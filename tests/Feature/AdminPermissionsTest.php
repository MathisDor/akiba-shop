<?php

namespace Tests\Feature;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class AdminPermissionsTest extends TestCase
{
    use RefreshDatabase, SeedsShopData;

    public function test_an_admin_with_only_the_moderation_permission_can_reach_listings_but_not_users(): void
    {
        $admin = $this->makeAdmin();
        $admin->permissions()->attach(Permission::where('slug', 'listings.moderate')->value('id'));

        $this->actingAs($admin, 'admin')->get('/admin/annonces')->assertOk();
        $this->actingAs($admin, 'admin')->get('/admin/utilisateurs')->assertForbidden();
    }

    public function test_a_regular_admin_cannot_reach_admin_management_but_a_super_admin_can(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin')->get('/admin/administrateurs')->assertForbidden();

        $superAdmin = $this->makeAdmin();
        $superAdmin->forceFill(['is_super_admin' => true])->save();
        $this->actingAs($superAdmin, 'admin')->get('/admin/administrateurs')->assertOk();
    }

    public function test_a_deactivated_admin_is_logged_out_and_redirected(): void
    {
        $admin = $this->makeAdmin();
        $admin->forceFill(['is_active' => false])->save();

        $response = $this->actingAs($admin, 'admin')->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }
}
