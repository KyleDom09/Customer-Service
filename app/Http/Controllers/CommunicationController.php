<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Communication;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommunicationController extends Controller
{
    /**
     * Main Communication History page (admin/agent view).
     */
    public function index()
    {
        $communications = Communication::with(['agent', 'ticket.agentModel'])
            ->orderByDesc('id')
            ->get();

        // No follow-up table/columns exist yet on the communications schema,
        // so this stays empty for now — the view already handles an empty
        // collection gracefully ("No upcoming follow-up scheduled").
        $followUps = collect();

        $agents = Agent::orderBy('name')->get();
        $tickets = Ticket::orderByDesc('id')->get();

        return view('communication-history', compact(
            'communications',
            'followUps',
            'agents',
            'tickets'
        ));
    }

    /**
     * Store a new communication record (New Communication modal).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name'  => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'date'           => ['nullable', 'string', 'max:50'],
            'type'           => ['required', 'string', 'in:mail,phone,chat'],
            'subject'        => ['required', 'string', 'max:255'],
            'agent_id'       => ['required', 'exists:agents,id'],
            'ticket_id'      => ['nullable', 'exists:tickets,id'],
            'status'         => ['required', 'string', 'in:pending,completed,resolved,cancelled'],
            'priority'       => ['required', 'string', 'in:low,medium,high'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $agent = Agent::find($data['agent_id']);

        $communication = Communication::create([
            'customer_name'  => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'date'           => $data['date'] ?? now()->format('M d'),
            'type'           => $data['type'],
            'subject'        => $data['subject'],
            'staff'          => $agent?->name,
            'agent_id'       => $agent?->id,
            'ticket_id'      => $data['ticket_id'] ?? null,
            'status'         => $data['status'],
            'priority'       => $data['priority'],
            'resp_time'      => 'Pending',
        ]);

        return response()->json(['communication' => $communication], 201);
    }

    /**
     * Update an existing communication record.
     * (Currently unused by the view — the Edit popup was locked to
     * view-only — but the route/endpoint stays available.)
     */
    public function update(Request $request, $id)
    {
        $communication = Communication::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date'     => ['nullable', 'string', 'max:50'],
            'type'     => ['required', 'string', 'in:mail,phone,chat'],
            'subject'  => ['required', 'string', 'max:255'],
            'staff'    => ['nullable', 'string', 'max:255'],
            'status'   => ['required', 'string', 'in:pending,completed,resolved,cancelled'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Auto-calculate resp_time once the case is marked completed/resolved.
        if (in_array($data['status'], ['completed', 'resolved']) && !$communication->resolved_at) {
            $data['resolved_at'] = now();
            $data['resp_time'] = $communication->created_at
                ? $communication->created_at->diffForHumans(now(), true)
                : 'Just now';
        }

        $communication->update($data);

        return response()->json(['communication' => $communication]);
    }

    /**
     * Recent communication activity feed for the main Dashboard page.
     */
    public function dashboardHistory()
    {
        $recent = Communication::with(['agent', 'ticket.agentModel'])
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return response()->json(['recent' => $recent]);
    }
}