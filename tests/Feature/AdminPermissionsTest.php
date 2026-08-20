<?php

namespace Tests\Feature;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class AdminPermissionsTest extends TestCase
{
    use RefreshDatabase, SeedsShopData;

    public function test_an_admin_with_only_the_products_permission_can_reach_products_but_not_orders(): void
    {
        $admin = $this->makeAdmin();
        $permission = Permission::create(['slug' => 'products.manage', 'name' => 'Produits', 'group' => 'Boutique']);
        $admin->permissions()->attach($permission->id);

        $this->actingAs($admin, 'admin')->get('/admin/produits')->assertOk();
        $this->actingAs($admin, 'admin')->get('/admin/commandes')->assertForbidden();
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
