<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Concerns\SeedsMarketplaceData;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class ShopProfileTest extends TestCase
{
    use RefreshDatabase, SeedsMarketplaceData, SeedsShopData;

    private function shopPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Boutique Mbolo',
            'bio' => 'Vêtements et accessoires importés, nouveaux arrivages chaque semaine.',
            'province' => 'estuaire',
            'city' => 'Libreville',
            'address' => 'Louis, face à la pharmacie',
            'delivers' => '1',
            'delivery_note' => 'Libreville et Owendo sous 24 h',
            'opening_hours' => 'Lun – Sam, 8 h – 19 h',
        ], $overrides);
    }

    public function test_a_member_customises_their_storefront(): void
    {
        Storage::fake('public');
        $member = $this->makeClient();

        $this->actingAs($member)->put('/compte/boutique', $this->shopPayload([
            'avatar' => UploadedFile::fake()->image('logo.png', 900, 900),
            'cover' => UploadedFile::fake()->image('cover.png', 3000, 1000),
        ]))->assertRedirect(route('account.shop.edit'));

        $member->refresh();
        $this->assertSame('Boutique Mbolo', $member->name);
        $this->assertTrue($member->delivers);
        $this->assertSame('Lun – Sam, 8 h – 19 h', $member->opening_hours);
        Storage::disk('public')->assertExists([$member->avatar_path, $member->cover_path]);

        [$avatarWidth] = getimagesizefromstring(Storage::disk('public')->get($member->avatar_path));
        $this->assertLessThanOrEqual(400, $avatarWidth);

        // Seen by another member (owners don't get contact buttons on their own shop).
        $this->makeListing($member);
        $this->actingAs($this->makeClient())->get('/vendeur/'.$member->id)->assertOk()
            ->assertSee('Boutique Mbolo')
            ->assertSee('Vêtements et accessoires importés')
            ->assertSee('Libreville et Owendo sous 24 h')
            ->assertSee($member->avatarUrl(), false)
            ->assertSee('wa.me/241077123456', false);
    }

    public function test_photos_can_be_removed(): void
    {
        Storage::fake('public');
        $member = $this->makeClient();
        $this->actingAs($member)->put('/compte/boutique', $this->shopPayload(['avatar' => UploadedFile::fake()->image('logo.png')]));
        $path = $member->refresh()->avatar_path;

        $this->actingAs($member)->put('/compte/boutique', $this->shopPayload(['remove_avatar' => '1']));

        $this->assertNull($member->refresh()->avatar_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_non_images_are_rejected_and_delivery_can_be_off(): void
    {
        $member = $this->makeClient();

        $this->actingAs($member)
            ->put('/compte/boutique', $this->shopPayload(['avatar' => UploadedFile::fake()->create('cv.pdf', 10)]))
            ->assertSessionHasErrors('avatar');

        $this->actingAs($member)->put('/compte/boutique', $this->shopPayload(['delivers' => '0']));
        $this->assertFalse($member->refresh()->delivers);
        $this->get('/vendeur/'.$member->id)->assertSee('Remise en main propre uniquement');
    }

    public function test_the_storefront_page_requires_sign_in_to_edit(): void
    {
        $this->get('/compte/boutique')->assertRedirect(route('login'));
    }
}
