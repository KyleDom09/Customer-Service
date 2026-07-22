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
        //    (naghahambing ng linggong ito kumpara sa nakaraang linggo)
        $thisWeekStart = now()->startOfWeek();
        $thisWeekEnd = now()->endOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();

        // Total Tickets: bilang ng tickets na ginawa this week vs last week
        $totalThisWeek = Ticket::whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])->count();
        $totalLastWeek = Ticket::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();

        // Open Tickets: bilang ng OPEN tickets na ginawa this week vs last week
        $openThisWeek = Ticket::where('status', 'OPEN')->whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])->count();
        $openLastWeek = Ticket::where('status', 'OPEN')->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();

        // Closed Tickets: bilang ng CLOSED tickets na na-update this week vs last week
        $closedThisWeek = Ticket::where('status', 'CLOSED')->whereBetween('updated_at', [$thisWeekStart, $thisWeekEnd])->count();
        $closedLastWeek = Ticket::where('status', 'CLOSED')->whereBetween('updated_at', [$lastWeekStart, $lastWeekEnd])->count();

        // Avg Response Time: this week vs last week (base sa tickets na ginawa nung panahong 'yon)
        $avgThisWeek = Ticket::whereNotNull('response_minutes')->whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])->avg('response_minutes') ?? 0;
        $avgLastWeek = Ticket::whereNotNull('response_minutes')->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->avg('response_minutes') ?? 0;

        $metrics = [
            [
                'label' => 'Total Tickets',
                'value' => number_format(Ticket::count()),
                'change' => $this->formatChange($totalThisWeek, $totalLastWeek),
            ],
            [
                'label' => 'Open Tickets',
                'value' => number_format(Ticket::where('status', 'OPEN')->count()),
                'change' => $this->formatChange($openThisWeek, $openLastWeek),
            ],
            [
                'label' => 'Closed Tickets',
                'value' => number_format(Ticket::where('status', 'CLOSED')->count()),
                'change' => $this->formatChange($closedThisWeek, $closedLastWeek),
            ],
            [
                'label' => 'Avg Response Time',
                'value' => ((int) round(Ticket::whereNotNull('response_minutes')->avg('response_minutes') ?? 0)) . 'm',
                'change' => $this->formatChange($avgThisWeek, $avgLastWeek),
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

    // Ito ang method para sa Ticket Analytics page (real data mula sa database)
    public function analytics()
    {
        // Bilang ng tickets kada status
        $statusCounts = Ticket::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Bilang ng tickets kada priority
        $priorityCounts = Ticket::selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority');

        // Bilang ng tickets kada agent (workload distribution)
        $agentCounts = Ticket::with('agentModel')
            ->selectRaw('agent_id, COUNT(*) as total')
            ->whereNotNull('agent_id')
            ->groupBy('agent_id')
            ->get()
            ->map(function ($row) {
                return [
                    'name'  => $row->agentModel->name ?? 'Unassigned',
                    'total' => $row->total,
                ];
            });

        return view('Ticket-Analytics', compact('statusCounts', 'priorityCounts', 'agentCounts'));
    }

    // Helper: kinukwenta ang % pagbabago sa pagitan ng dalawang bilang
    // (linggong ito kumpara sa nakaraang linggo), tapos ini-format
    // bilang string na tulad ng "▲ 12.5%" o "▼ 8.2%"
    private function formatChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? '▲ 100%' : '— 0%';
        }

        $percent = (($current - $previous) / $previous) * 100;
        $arrow = $percent > 0 ? '▲' : ($percent < 0 ? '▼' : '—');

        return $arrow . ' ' . number_format(abs($percent), 1) . '%';
    }
}