<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Agent;
use App\Models\Ticket;

class CommunicationController extends Controller
{
    public function index()
    {
        $communications = DB::table('communications')
            ->latest('id')
            ->get();

        $agents = Agent::orderBy('name')->get();
        $tickets = Ticket::orderBy('ticket_number')->get();

        return view('Communication-History', compact('communications', 'agents', 'tickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'type'           => 'required|string|max:255',
            'subject'        => 'required|string|max:255',
            'agent_id'       => 'required|exists:agents,id',
            'ticket_id'      => 'nullable|exists:tickets,id',
            'status'         => 'required|string|max:255',
            'priority'       => 'required|string|max:255',
        ]);

        // Look up the agent's name to keep the "staff" text column filled too
        $agent = Agent::find($validated['agent_id']);

        DB::table('communications')->insert([
            'customer_name'  => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'date'           => now()->format('M d'),
            'type'           => $validated['type'],
            'subject'        => $validated['subject'],
            'staff'          => $agent->name ?? '',
            'agent_id'       => $validated['agent_id'],
            'ticket_id'      => $validated['ticket_id'] ?? null,
            'status'         => $validated['status'],
            'priority'       => $validated['priority'],
            'resp_time'      => 'Pending',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return response()->json(['success' => true]);
    }
}