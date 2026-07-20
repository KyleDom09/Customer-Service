<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SlaRule;

class SlaRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'name' => 'Critical Priority',
                'response' => 15,
                'resolution' => 240,
                'active' => true,
            ],
            [
                'name' => 'High Priority',
                'response' => 30,
                'resolution' => 480,
                'active' => true,
            ],
            [
                'name' => 'Medium Priority',
                'response' => 60,
                'resolution' => 1440,
                'active' => true,
            ],
            [
                'name' => 'Low Priority',
                'response' => 120,
                'resolution' => 2880,
                'active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            SlaRule::create($rule);
        }
    }
}
