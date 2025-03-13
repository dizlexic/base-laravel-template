<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    protected $developmentSeeders = [
        DevelopmentSeeder::class,
    ];

    protected $productionSeeders = [
        PermissionRoleSeeder::class,
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $seeders = $this->productionSeeders;
        } else {
            $seeders = array_merge($this->productionSeeders, $this->developmentSeeders);
        }

        foreach ($seeders as $seeder) {
            $this->call($seeder);
        }
    }
}
