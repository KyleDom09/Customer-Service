<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BillingItem;

class BillingItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BillingItem::create([
            'title' => 'Double Charging',
            'icon' => 'fa-file-invoice-dollar',
            'problem' => 'Why am I seeing two identical charges on my bank statement?',
            'steps' => [
                'Check if one of the charges is marked as Pending (this is a temporary authorization hold).',
                'Wait 24 to 48 hours for your bank to clear and remove the duplicate temporary hold.',
                'If both charges remain active after 2 days, contact support with your receipt.'
            ],
            'is_new' => false,
        ]);

        BillingItem::create([
            'title' => 'Card Declining',
            'icon' => 'fa-exclamation-circle',
            'problem' => 'Why is my card being declined when I try to make a payment?',
            'steps' => [
                'Verify that your card details, billing address, and expiration date are entered correctly.',
                'Check with your bank to ensure the transaction is not blocked for security reasons.',
                'If the issue continues, contact support with the error message and payment attempt time.'
            ],
            'is_new' => false,
        ]);

        BillingItem::create([
            'title' => 'Update Tax Information',
            'icon' => 'fa-id-card',
            'problem' => 'How can I check if my requested refund has been processed?',
            'steps' => [
                'Check your transaction history for a green line item showing a negative balance credit.',
                'Allow 5 to 10 standard business days for your bank to post the credit to your card.',
                'If the credit does not show up after 10 days, contact support with the refund ID.'
            ],
            'is_new' => false,
        ]);
    }
}
