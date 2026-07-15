<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Article::create([
            'title' => 'Upcoming System Maintenance',
            'description' => "Stay informed about scheduled platform downtime.  Common Issue: Unsure when the system might be unavailable for quarterly updates.  Try this to fix: Check the 'News' banner on the dashboard. Maintenance windows are posted 48 hours in advance for all users. Pro-Tip: If you have active sync processes running, save your work locally 1 hour before the scheduled time.",
            'is_new' => false,
        ]);

        Article::create([
            'title' => 'Understanding SLA Response Times',
            'description' => "Know when to expect a reply from support. Critical: 2 hrs, High: 12 hrs, Standard: 24 hrs. Common Issue: Unsure about how long it takes to get a response on a support ticket. Try this to fix: Visit 'SLA Tracking' to see our current response targets based on priority levels (Critical: 2 hrs, High: 12 hrs, Standard: 24 hrs). Pro-Tip: Always attach a ticket ID when referencing open cases to speed up the lookup.",
            'is_new' => false,
        ]);

        Article::create([
            'title' => 'Resetting a Locked Account',
            'description' => "Self-service steps for account security lockouts. Common Issue: Account locked due to multiple incorrect login attempts. Try this to fix: Wait 15 minutes for the automated reset. Use the 'Forgot Password' link on the login page to verify your identity via your registered corporate email. Pro-Tip: Check your spam folder if the verification code does not arrive within 3 minutes.",
            'is_new' => false,
        ]);
    }
}
