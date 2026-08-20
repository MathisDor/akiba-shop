<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase, SeedsShopData;

    public function test_guests_are_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_a_client_logged_into_the_web_guard_cannot_reach_the_admin_dashboard(): void
    {
        $client = $this->makeClient();

        // Authenticated on the "web" guard (customer session) — not the "admin" guard.
        $response = $this->actingAs($client, 'web')->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_an_admin_user_can_reach_the_dashboard(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')->get('/admin');

        $response->assertOk();
    }

    public function test_an_admin_account_cannot_log_into_the_customer_facing_web_guard(): void
    {
        $admin = $this->makeAdmin(['password' => 'password1234']);

        $response = $this->post('/connexion', [
            'email' => $admin->email,
            'password' => 'password1234',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }
}
