<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        ActivityLog::insert([
            [
                'target_id' => 'Ticket #1004',
                'type' => 'escalations',
                'event' => 'Escalation',
                'description' => 'Ticket escalated to Tier 2 Support due to response delay',
                'performed_by' => 'System Workflow',
                'severity' => 'CRITICAL',
                'logged_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_id' => 'Ticket #1002',
                'type' => 'status-updates',
                'event' => 'Status Update',
                'description' => 'Updated ticket status from Open to Pending',
                'performed_by' => 'Dominick',
                'severity' => 'MEDIUM',
                'logged_at' => now()->subMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_id' => 'Ticket #1008',
                'type' => 'user-creation',
                'event' => 'Creation',
                'description' => 'New support ticket created via Self-Service Portal',
                'performed_by' => 'Customer Guest',
                'severity' => 'LOW',
                'logged_at' => now()->subMinutes(45),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_id' => 'Ticket #1001',
                'type' => 'sla-alerts',
                'event' => 'SLA Alert',
                'description' => 'SLA warning generated: ticket expiring in 15 mins',
                'performed_by' => 'System Workflow',
                'severity' => 'HIGH',
                'logged_at' => now()->subHour(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_id' => 'Ticket #1003',
                'type' => 'status-updates',
                'event' => 'Assignment',
                'description' => 'Assigned ticket handling responsibility to Timothy',
                'performed_by' => 'Admin Profile',
                'severity' => 'LOW',
                'logged_at' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_id' => 'Ticket #998',
                'type' => 'status-updates',
                'event' => 'Resolution',
                'description' => 'Status changed to Closed after customer confirmation',
                'performed_by' => 'Kyle Dominick',
                'severity' => 'SUCCESS',
                'logged_at' => now()->subHours(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_id' => 'Ticket #1015',
                'type' => 'escalations',
                'event' => 'Escalation',
                'description' => 'Critical database lag escalated to infrastructure supervisor',
                'performed_by' => 'System Workflow',
                'severity' => 'CRITICAL',
                'logged_at' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_id' => 'Ticket #992',
                'type' => 'sla-alerts',
                'event' => 'SLA Breach',
                'description' => 'Response target breached for priority billing concern',
                'performed_by' => 'System Workflow',
                'severity' => 'CRITICAL',
                'logged_at' => now()->subHours(4),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_id' => 'Client #4102',
                'type' => 'user-creation',
                'event' => 'User Creation',
                'description' => 'Portal access generated and activation email dispatched',
                'performed_by' => 'System Workflow',
                'severity' => 'LOW',
                'logged_at' => now()->subHours(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}