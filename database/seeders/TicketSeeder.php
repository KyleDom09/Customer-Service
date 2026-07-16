<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\Agent;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        // Kunin ang mga totoong agent mula sa agents table (base sa pangalan)
        $agents = Agent::pluck('id', 'name');

        $tickets = [
            ['ticket_number'=>'TK-2847','customer_name'=>'Sarah Johnson','customer_email'=>'sarah@techcorp.com','subject'=>'Unable to login','sub_subject'=>'Authentication','category'=>'Auth','agent'=>'Kyle Dominick','priority'=>'CRITICAL','status'=>'OPEN'],
            ['ticket_number'=>'TK-2846','customer_name'=>'James Williams','customer_email'=>'james@example.com','subject'=>'Payment Failed','sub_subject'=>'Billing','category'=>'Billing','agent'=>'Taylee','priority'=>'HIGH','status'=>'IN PROGRESS'],
            ['ticket_number'=>'TK-2845','customer_name'=>'Emma Davis','customer_email'=>'emma@design.io','subject'=>'Feature: dark mode','sub_subject'=>'Feature','category'=>'Feature','agent'=>'Benju','priority'=>'LOW','status'=>'PENDING'],
            ['ticket_number'=>'TK-2844','customer_name'=>'Robert Martinez','customer_email'=>'robert@corp.com','subject'=>'App crashes on startup','sub_subject'=>'Bug','category'=>'Bug','agent'=>'Jake','priority'=>'CRITICAL','status'=>'OPEN'],
            ['ticket_number'=>'TK-2843','customer_name'=>'Jennifer Lee','customer_email'=>'jlee@startup.co','subject'=>'Dashboard loads slow','sub_subject'=>'Performance','category'=>'Perf','agent'=>'Kyle Dominick','priority'=>'HIGH','status'=>'IN PROGRESS'],
            ['ticket_number'=>'TK-2842','customer_name'=>'David Wilson','customer_email'=>'david@agency.co','subject'=>'PDF export broken','sub_subject'=>'Technical','category'=>'Technical','agent'=>'Taylee','priority'=>'MEDIUM','status'=>'RESOLVED'],
            ['ticket_number'=>'TK-2841','customer_name'=>'Amanda Thompson','customer_email'=>'amanda@tag.com','subject'=>'Slack integration issue','sub_subject'=>'Integration','category'=>'Integration','agent'=>'Benju','priority'=>'HIGH','status'=>'IN PROGRESS'],
            ['ticket_number'=>'TK-2840','customer_name'=>'Christopher Harris','customer_email'=>'chris@corp.com','subject'=>'Invoice not received','sub_subject'=>'Billing','category'=>'Billing','agent'=>'Jake','priority'=>'MEDIUM','status'=>'CLOSED'],
            ['ticket_number'=>'TK-2839','customer_name'=>'Michelle Young','customer_email'=>'michelle@tech.com','subject'=>'Cannot reset password','sub_subject'=>'Auth','category'=>'Auth','agent'=>'Marcus','priority'=>'LOW','status'=>'RESOLVED'],
        ];

        foreach ($tickets as $ticket) {
            $agentName = $ticket['agent'];
            unset($ticket['agent']);

            $ticket['agent_id'] = $agents[$agentName] ?? null;

            Ticket::create($ticket);
        }
    }
}