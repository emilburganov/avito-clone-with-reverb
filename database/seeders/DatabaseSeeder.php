<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->create([
            'email' => 'admin@admin.ru',
            'password' => 'admin123',
            'is_admin' => true,
        ]);

        User::query()->create([
            'email' => 'admin@admin.ru',
            'password' => 'admin',
            'is_admin' => true,
        ]);

        Category::query()->create([
            'name' => 'Одежда',
        ]);

        Category::query()->create([
            'name' => 'Электроника',
        ]);
    }
}
