<?php
namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        Agent::insert([
            [
                'name' => 'Kyle Dominick',
                'role' => 'Senior Agent',
                'team' => 'Support',
                'active_status' => 'online',
                'total_assigned' => 45,
                'total_resolved' => 44,
                'avg_response_minutes' => 12,
                'csat_score' => 4.9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Benju',
                'role' => 'Support Agent',
                'team' => 'Support',
                'active_status' => 'online',
                'total_assigned' => 40,
                'total_resolved' => 34,
                'avg_response_minutes' => 18,
                'csat_score' => 4.6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jake',
                'role' => 'Support Agent',
                'team' => 'Support',
                'active_status' => 'away',
                'total_assigned' => 38,
                'total_resolved' => 27,
                'avg_response_minutes' => 24,
                'csat_score' => 4.2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Timoty',
                'role' => 'Senior Agent',
                'team' => 'Billing',
                'active_status' => 'online',
                'total_assigned' => 42,
                'total_resolved' => 36,
                'avg_response_minutes' => 15,
                'csat_score' => 4.7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Taylee',
                'role' => 'Support Agent',
                'team' => 'Billing',
                'active_status' => 'offline',
                'total_assigned' => 30,
                'total_resolved' => 17,
                'avg_response_minutes' => 32,
                'csat_score' => 3.9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marcus',
                'role' => 'Support Agent',
                'team' => 'Technical',
                'active_status' => 'online',
                'total_assigned' => 35,
                'total_resolved' => 30,
                'avg_response_minutes' => 19,
                'csat_score' => 4.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Filoteo',
                'role' => 'Support Agent',
                'team' => 'Technical',
                'active_status' => 'online',
                'total_assigned' => 33,
                'total_resolved' => 29,
                'avg_response_minutes' => 20,
                'csat_score' => 4.4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}