<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Enums\ReportStatus;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\Role;
use App\Support\GabonPhone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Concerns\SeedsMarketplaceData;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase, SeedsMarketplaceData, SeedsShopData;

    public function test_public_pages_render(): void
    {
        $listing = $this->makeListing($this->makeClient());

        foreach (['/', '/annonces', '/categories', '/categories/vehicules', '/annonce/'.$listing->slug, '/vendeur/'.$listing->user_id, '/cgu', '/sitemap.xml'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/annonces?q=Corolla&ville=libreville')->assertOk()->assertSee('Toyota Corolla 2014');
    }

    public function test_pending_and_suspended_listings_are_hidden_from_visitors(): void
    {
        $pending = $this->makeListing($this->makeClient(), ['title' => 'Annonce en attente', 'status' => ListingStatus::EnAttente]);
        $suspendedSeller = $this->makeClient(['is_active' => false]);
        $hidden = $this->makeListing($suspendedSeller, ['title' => 'Vendeur suspendu']);

        $this->get('/annonces')->assertDontSee('Annonce en attente')->assertDontSee('Vendeur suspendu');
        $this->get('/annonce/'.$pending->slug)->assertNotFound();
        $this->get('/annonce/'.$hidden->slug)->assertNotFound();

        // The owner can preview their own pending listing.
        $this->actingAs($pending->user)->get('/annonce/'.$pending->slug)->assertOk();
    }

    public function test_a_member_publishes_a_listing_that_waits_for_moderation(): void
    {
        Storage::fake('public');
        $seller = $this->makeClient();

        $response = $this->actingAs($seller)->post('/publier', $this->listingPayload([
            'photos' => [UploadedFile::fake()->image('voiture.png', 2000, 1500)],
        ]));

        $response->assertRedirect(route('account.listings'));

        $listing = Listing::firstOrFail();
        $this->assertSame(ListingStatus::EnAttente, $listing->status);
        $this->assertSame(8900000, $listing->price);
        $this->assertSame('077123456', $listing->contact_phone);
        // Only fields belonging to the category are kept.
        $this->assertSame(['marque' => 'Hyundai', 'annee' => '2017'], $listing->specs);
        $this->assertCount(1, $listing->images);
        Storage::disk('public')->assertExists($listing->images->first()->path);

        [$width] = getimagesizefromstring(Storage::disk('public')->get($listing->images->first()->path));
        $this->assertLessThanOrEqual(config('marketplace.image_max_dimension'), $width);
    }

    public function test_publishing_requires_a_sub_category_and_a_gabonese_phone(): void
    {
        $parent = ListingCategory::where('slug', 'vehicules')->value('id');

        $this->actingAs($this->makeClient())
            ->post('/publier', $this->listingPayload(['listing_category_id' => $parent, 'contact_phone' => '12']))
            ->assertSessionHasErrors(['listing_category_id', 'contact_phone']);

        $this->assertSame(0, Listing::count());
    }

    public function test_guests_cannot_publish(): void
    {
        $this->get('/publier')->assertRedirect(route('login'));
    }

    public function test_editing_a_published_listing_sends_it_back_to_moderation(): void
    {
        $listing = $this->makeListing($seller = $this->makeClient());

        $this->actingAs($seller)
            ->put('/compte/annonces/'.$listing->slug, $this->listingPayload(['title' => 'Toyota Corolla 2014 — prix revu']))
            ->assertRedirect(route('account.listings'));

        $this->assertSame(ListingStatus::EnAttente, $listing->fresh()->status);
    }

    public function test_members_cannot_edit_someone_elses_listing(): void
    {
        $listing = $this->makeListing($this->makeClient());

        $this->actingAs($this->makeClient())
            ->put('/compte/annonces/'.$listing->slug, $this->listingPayload())
            ->assertNotFound();
    }

    public function test_favorites_toggle(): void
    {
        $listing = $this->makeListing($this->makeClient());
        $member = $this->makeClient();

        $this->actingAs($member)->postJson('/annonce/'.$listing->slug.'/favori')->assertJson(['favorited' => true]);
        $this->actingAs($member)->get('/compte/favoris')->assertSee('Toyota Corolla 2014');
        $this->actingAs($member)->postJson('/annonce/'.$listing->slug.'/favori')->assertJson(['favorited' => false]);
    }

    public function test_visitors_can_report_a_listing_and_admins_can_remove_it(): void
    {
        $listing = $this->makeListing($this->makeClient());

        $this->post('/annonce/'.$listing->slug.'/signaler', ['reason' => 'arnaque', 'details' => 'Demande un dépôt Airtel Money.'])
            ->assertSessionHasNoErrors();

        $report = $listing->reports()->firstOrFail();
        $admin = $this->makeAdmin();
        $admin->forceFill(['is_super_admin' => true])->save();

        $this->actingAs($admin, 'admin')
            ->patch('/admin/signalements/'.$report->id, ['status' => 'traite', 'remove_listing' => '1'])
            ->assertRedirect();

        $this->assertSame(ReportStatus::Traite, $report->fresh()->status);
        $this->assertSame(ListingStatus::Refusee, $listing->fresh()->status);
    }

    public function test_admin_approves_a_pending_listing(): void
    {
        $listing = $this->makeListing($this->makeClient(), ['status' => ListingStatus::EnAttente, 'published_at' => null, 'expires_at' => null]);
        $admin = $this->makeAdmin();
        $admin->forceFill(['is_super_admin' => true])->save();

        $this->actingAs($admin, 'admin')->get('/admin/annonces')->assertOk()->assertSee($listing->title);
        $this->actingAs($admin, 'admin')->patch('/admin/annonces/'.$listing->id.'/approuver')->assertRedirect();

        $listing->refresh();
        $this->assertSame(ListingStatus::Publiee, $listing->status);
        $this->assertNotNull($listing->published_at);
        $this->assertTrue($listing->expires_at->isFuture());
    }

    public function test_members_can_sign_in_with_their_phone_number(): void
    {
        $member = $this->makeClient(['phone' => '074112233', 'password' => 'secret-pass']);

        $this->post('/connexion', ['login' => '+241 74 11 22 33', 'password' => 'secret-pass'])->assertRedirect();
        $this->assertAuthenticatedAs($member);
    }

    public function test_suspended_members_cannot_sign_in(): void
    {
        $member = $this->makeClient(['password' => 'secret-pass', 'is_active' => false]);

        $this->post('/connexion', ['login' => $member->email, 'password' => 'secret-pass'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_registration_normalises_the_phone_number(): void
    {
        Role::firstOrCreate(['slug' => 'client'], ['name' => 'Client']);

        $this->post('/inscription', [
            'name' => 'Aurélie Mba',
            'email' => 'aurelie@example.com',
            'phone' => '+241 077 12 34 56',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
            'terms' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'aurelie@example.com', 'phone' => '077123456']);
    }

    public function test_gabon_phone_helper(): void
    {
        $this->assertSame('077123456', GabonPhone::normalize('077 12 34 56'));
        $this->assertSame('077123456', GabonPhone::normalize('+241 77 12 34 56'));
        $this->assertSame('07123456', GabonPhone::normalize('00241 07 12 34 56'));
        $this->assertNull(GabonPhone::normalize('12345'));
        $this->assertSame('077 12 34 56', GabonPhone::format('077123456'));
        $this->assertSame('https://wa.me/241077123456', GabonPhone::whatsappUrl('077123456'));
    }

    public function test_old_shop_urls_redirect(): void
    {
        $this->get('/femme')->assertRedirect(route('categories.show', 'mode-beaute'));
        $this->get('/catalogue')->assertRedirect('/annonces');
    }
}
