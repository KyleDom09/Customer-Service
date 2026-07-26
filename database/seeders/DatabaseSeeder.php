<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Regular customer/user account — para sa testing ng /my-dashboard view
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
            'role'  => 'user',
        ]);

        // Admin account — para sa testing ng Customer Service Dashboard (admin view)
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $this->call([
            AgentSeeder::class,
            ActivityLogSeeder::class,
            TicketSeeder::class,
            ArticleSeeder::class,
            BillingItemSeeder::class,
            CommunicationSeeder::class,
        ]);
    }
}