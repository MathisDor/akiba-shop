<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $clientRole = Role::where('slug', 'client')->firstOrFail();

        $admin = User::updateOrCreate(
            ['email' => 'admin@akibashop.ga'],
            [
                'name' => 'Admin AkibaShop',
                'password' => 'password',
                'role_id' => $adminRole->id,
                'phone' => '+33600000001',
                'email_verified_at' => now(),
            ]
        );

        // Trusted seeder context — bypasses the mass-assignment guard to set
        // the super-admin flag, which is intentionally excluded from $fillable.
        $admin->forceFill(['is_super_admin' => true, 'is_active' => true])->save();

        User::updateOrCreate(
            ['email' => 'jean.marc@example.com'],
            [
                'name' => 'Jean-Marc Obiang',
                'password' => 'password',
                'role_id' => $clientRole->id,
                'phone' => '+33612000000',
                'email_verified_at' => now(),
            ]
        );
    }
}
