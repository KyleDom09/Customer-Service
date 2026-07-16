<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Communication;

class CommunicationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'customer_name' => 'James Davidson',
                'customer_email' => 'james@email.com',
                'date' => 'Jun 22',
                'type' => 'mail',
                'subject' => 'Q3 Financial Review',
                'staff' => 'Alex Chen',
                'status' => 'completed',
                'priority' => 'high',
                'resp_time' => '2h 15m',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Sarah Mitchell',
                'customer_email' => 'sarah@techcorp.com',
                'date' => 'Jun 22',
                'type' => 'phone',
                'subject' => 'Account Upgrade Req',
                'staff' => 'Maria Lopez',
                'status' => 'pending',
                'priority' => 'medium',
                'resp_time' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Robert Kim',
                'customer_email' => 'rkim@invest.com',
                'date' => 'Jun 21',
                'type' => 'chat',
                'subject' => 'Investment Portfolio',
                'staff' => 'James Park',
                'status' => 'resolved',
                'priority' => 'low',
                'resp_time' => '45m',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Alice Thompson',
                'customer_email' => 'alice@corp.com',
                'date' => 'Jun 21',
                'type' => 'mail',
                'subject' => 'Monthly Statement',
                'staff' => 'Emma Wilson',
                'status' => 'completed',
                'priority' => 'medium',
                'resp_time' => '1h 30m',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Michael Brown',
                'customer_email' => 'mbrown@fin.com',
                'date' => 'Jun 20',
                'type' => 'phone',
                'subject' => 'Loan Application',
                'staff' => 'Alex Chen',
                'status' => 'pending',
                'priority' => 'high',
                'resp_time' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Lisa Chen',
                'customer_email' => 'lchen@bank.com',
                'date' => 'Jun 20',
                'type' => 'mail',
                'subject' => 'Credit Card Dispute',
                'staff' => 'Maria Lopez',
                'status' => 'cancelled',
                'priority' => 'low',
                'resp_time' => 'N/A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($data as $row) {
            Communication::create($row);
        }
    }
}