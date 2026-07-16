<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Agent;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        // 1. Kukunin ang lahat ng tickets mula sa database (bago papuntang luma)
        //    Kasama na ang naka-assign na agent (relational, hindi na plain string)
        $tickets = Ticket::with('agentModel')->orderBy('created_at', 'desc')->get();

        // 2. Dynamic na kalkulasyon ng metrics base sa data sa database
        $metrics = [
            [
                'label' => 'Total Tickets',
                'value' => number_format(Ticket::count()),
                'change' => '+ 12.5%',
            ],
            [
                'label' => 'Open Tickets',
                'value' => number_format(Ticket::where('status', 'OPEN')->count()),
                'change' => '▲ 8.2%',
            ],
            [
                'label' => 'Closed Tickets',
                'value' => number_format(Ticket::where('status', 'CLOSED')->count()),
                'change' => '▲ 15.2%',
            ],
            [
                'label' => 'Avg Response Time',
                'value' => '18m',
                'change' => '▲ 22%',
            ],
        ];

        // 4. Kukunin ang listahan ng agents para sa "Assigned Agent" dropdown
        $agents = Agent::orderBy('name')->get();

        // 5. Ipapasa ang mga nakuha nating data sa blade view natin
        return view('Ticketmanagement', compact('tickets', 'metrics', 'agents'));
    }

    // Ito ang bagong method para i-save ang bagong ticket sa database
    public function store(Request $request)
    {
        // Suriin muna ang mga natanggap na data bago i-save
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'category'       => 'required|string|max:255',
            'agent_id'       => 'nullable|exists:agents,id',
            'priority'       => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
            'subject'        => 'required|string|max:255',
            'description'    => 'nullable|string',
        ]);

        // Gumawa ng natatanging (unique) ticket number, hal. TCK-000123
        $validated['ticket_number'] = 'TCK-' . str_pad((string) (Ticket::max('id') + 1), 6, '0', STR_PAD_LEFT);

        // Default na status ng bagong ticket
        $validated['status'] = 'OPEN';

        Ticket::create($validated);

        return redirect()->route('ticketmanagement')->with('success', 'Matagumpay na naidagdag ang bagong ticket!');
    }

    // Ito ang method para i-update ang isang existing na ticket
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'category'       => 'required|string|max:255',
            'agent_id'       => 'nullable|exists:agents,id',
            'priority'       => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
            'status'         => 'required|in:OPEN,PENDING,IN PROGRESS,RESOLVED,CLOSED',
            'subject'        => 'required|string|max:255',
            'description'    => 'nullable|string',
        ]);

        $ticket->update($validated);

        return redirect()->route('ticketmanagement')->with('success', 'Matagumpay na na-update ang ticket!');
    }

    // Ito ang method para i-delete ang isang ticket
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('ticketmanagement')->with('success', 'Matagumpay na na-delete ang ticket!');
    }
}