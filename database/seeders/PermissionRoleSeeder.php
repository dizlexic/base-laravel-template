<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::factory()->create([
            'name' => 'admin',
        ]);

        Role::factory()->create([
            'name' => 'user',
        ]);

        Role::factory()->create([
            'name' => 'moderator',
        ]);
    }
}
