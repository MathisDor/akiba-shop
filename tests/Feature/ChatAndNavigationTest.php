<?php

namespace Tests\Feature;

use App\Models\Conversation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\SeedsMarketplaceData;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class ChatAndNavigationTest extends TestCase
{
    use RefreshDatabase, SeedsMarketplaceData, SeedsShopData;

    public function test_visitors_get_no_bottom_bar_and_members_do(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Navigation principale');

        $member = $this->makeClient();
        $this->actingAs($member)->get('/annonces')->assertOk()
            ->assertSee('Navigation principale')
            ->assertSee('Chat');
    }

    public function test_signed_in_members_are_sent_to_the_listings_from_guest_pages(): void
    {
        $this->actingAs($this->makeClient())->get('/connexion')->assertRedirect(route('listings.index'));
        $this->get('/tableau-de-bord')->assertRedirect('/annonces');
    }

    public function test_guests_cannot_reach_the_chat(): void
    {
        $this->get('/messages')->assertRedirect(route('login'));
    }

    public function test_a_buyer_messages_a_seller_and_the_seller_replies(): void
    {
        $seller = $this->makeClient();
        $buyer = $this->makeClient();
        $listing = $this->makeListing($seller);

        $this->actingAs($buyer)
            ->post('/annonce/'.$listing->slug.'/contacter', ['body' => 'Toujours disponible ?'])
            ->assertRedirect();

        $conversation = Conversation::firstOrFail();
        $this->assertSame([$buyer->id, $seller->id], [$conversation->buyer_id, $conversation->seller_id]);
        $this->assertSame(1, $seller->unreadMessagesCount());

        // Opening the thread marks it read.
        $this->actingAs($seller)->get('/messages/'.$conversation->id)->assertOk()->assertSee('Toujours disponible ?');
        $this->assertSame(0, $seller->unreadMessagesCount());

        $this->actingAs($seller)
            ->postJson('/messages/'.$conversation->id, ['body' => 'Oui, passez demain.'])
            ->assertOk()
            ->assertJsonPath('message.mine', true);

        $firstId = $conversation->messages()->value('id');
        $this->actingAs($buyer)
            ->getJson('/messages/'.$conversation->id.'/nouveaux?after='.$firstId)
            ->assertOk()
            ->assertJsonCount(1, 'messages')
            ->assertJsonPath('messages.0.body', 'Oui, passez demain.')
            ->assertJsonPath('messages.0.mine', false);

        // A second message from the same buyer reuses the conversation.
        $this->actingAs($buyer)->post('/annonce/'.$listing->slug.'/contacter', ['body' => 'Merci !']);
        $this->assertSame(1, Conversation::count());
    }

    public function test_outsiders_cannot_read_a_conversation(): void
    {
        $listing = $this->makeListing($this->makeClient());
        $buyer = $this->makeClient();
        $this->actingAs($buyer)->post('/annonce/'.$listing->slug.'/contacter', ['body' => 'Bonjour']);
        $conversation = Conversation::firstOrFail();

        $outsider = $this->makeClient();
        $this->actingAs($outsider)->get('/messages/'.$conversation->id)->assertNotFound();
        $this->actingAs($outsider)->postJson('/messages/'.$conversation->id, ['body' => 'Hey'])->assertNotFound();
        $this->actingAs($outsider)->getJson('/messages/'.$conversation->id.'/nouveaux')->assertNotFound();
    }

    public function test_sellers_cannot_message_their_own_listing(): void
    {
        $seller = $this->makeClient();
        $listing = $this->makeListing($seller);

        $this->actingAs($seller)->post('/annonce/'.$listing->slug.'/contacter', ['body' => 'Test']);
        $this->assertSame(0, Conversation::count());
    }
}
