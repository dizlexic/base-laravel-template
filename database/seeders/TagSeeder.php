<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seed_user = User::factory()->create([
            'first_name' => 'Tag',
            'last_name' => 'Seeder',
            'email' => 'not.email',
            'password' => bcrypt(Str::random(32)),
        ]);

        Tag::factory()->count(200)->create([
            'created_by' => $seed_user->id,
        ]);
    }
}
