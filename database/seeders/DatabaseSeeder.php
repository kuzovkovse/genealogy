<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\HistoricalFactsSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 👤 Тестовый пользователь
        |--------------------------------------------------------------------------
        */
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 🏛 Исторические факты
        |--------------------------------------------------------------------------
        */
        $this->call([
            HistoricalFactsSeeder::class,
        ]);
    }
}
