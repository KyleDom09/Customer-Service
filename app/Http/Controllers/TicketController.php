<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index()
    {
        // 1. Kukunin ang lahat ng tickets mula sa database (bago papuntang luma)
        //    Kasama na ang naka-assign na agent (relational, hindi na plain string)
        $tickets = Ticket::with('agentModel')->orderBy('created_at', 'desc')->get();

        // 2. Metrics (nasa reusable helper na para magamit din ng adminIndex())
        $metrics = $this->buildMetrics();

        // 3. Kukunin ang listahan ng agents para sa "Assigned Agent" dropdown
        $agents = Agent::orderBy('name')->get();

        // 4. Ipapasa ang mga nakuha nating data sa blade view natin
        return view('Ticketmanagement', compact('tickets', 'metrics', 'agents'));
    }

    // Ito ang Admin View — parehong data lang sa index(), pero ipapadala
    // sa admin-ticket-management blade (may edit-all + reply-as-agent).
    public function adminIndex()
    {
        $tickets = Ticket::with('agentModel')->orderBy('created_at', 'desc')->get();
        $metrics = $this->buildMetrics();
        $agents  = Agent::orderBy('name')->get();

        return view('admin-ticket-management', compact('tickets', 'metrics', 'agents'));
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

        $ticket = Ticket::create($validated);

        // Auto-log this ticket creation into Communication History,
        // so agents can see the full conversation trail without
        // digging through the Ticket Management page separately.
        $this->logCommunication($ticket, 'New ticket created: ' . $ticket->ticket_number . ' — ' . $ticket->subject);

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

        $previousStatus = $ticket->status;

        $ticket->update($validated);

        // If the status actually changed, log it as a new Communication
        // History entry so there's a visible trail of what happened.
        if ($previousStatus !== $validated['status']) {
            $this->logCommunication($ticket, 'Ticket ' . $ticket->ticket_number . ' status changed: ' . $previousStatus . ' → ' . $validated['status']);
        }

        return redirect()->route('ticketmanagement')->with('success', 'Matagumpay na na-update ang ticket!');
    }

    // Helper: nag-iinsert ng bagong row sa communications table tuwing
    // may bagong ticket na nagawa o nag-iba ang status ng isang ticket.
    // Ginawa itong sarili niyang method para reusable sa store() at update(),
    // at para consistent ang format sa CommunicationController.
    //
    // NOTE: the communications.type column is a strict ENUM('mail','phone','chat')
    // and priority is ENUM('high','medium','low') — there's no "system"/"CRITICAL"
    // option, so system-generated ticket events are logged as type 'mail' with
    // the actual event description placed in 'subject' instead, and priority is
    // lowercased + CRITICAL is downgraded to 'high' since it isn't a valid value here.
    private function logCommunication(Ticket $ticket, string $note)
    {
        $agentName = $ticket->agent_id
            ? (Agent::find($ticket->agent_id)->name ?? '')
            : '';

        // Map ticket status -> communication status so it fits the same
        // filters already used on the Communication History page.
        $statusMap = [
            'OPEN'         => 'pending',
            'PENDING'      => 'pending',
            'IN PROGRESS'  => 'pending',
            'RESOLVED'     => 'resolved',
            'CLOSED'       => 'resolved',
        ];
        $commStatus = $statusMap[$ticket->status] ?? 'pending';

        // communications.priority only allows high/medium/low (lowercase),
        // so CRITICAL tickets get logged as 'high' — the closest valid fit.
        $priorityMap = [
            'CRITICAL' => 'high',
            'HIGH'     => 'high',
            'MEDIUM'   => 'medium',
            'LOW'      => 'low',
        ];
        $commPriority = $priorityMap[$ticket->priority] ?? 'medium';

        $respTime = 'Pending';
        $resolvedAt = null;

        if ($commStatus === 'resolved') {
            $resolvedAt = now();
            $minutes = $ticket->created_at->diffInMinutes($resolvedAt);
            $respTime = $minutes < 60
                ? $minutes . 'm'
                : intdiv($minutes, 60) . 'h ' . ($minutes % 60) . 'm';
        }

        DB::table('communications')->insert([
            'customer_name'  => $ticket->customer_name,
            'customer_email' => $ticket->customer_email,
            'date'           => now()->format('M d'),
            'type'           => 'mail',
            'subject'        => $note,
            'staff'          => $agentName,
            'agent_id'       => $ticket->agent_id,
            'ticket_id'      => $ticket->id,
            'status'         => $commStatus,
            'priority'       => $commPriority,
            'resp_time'      => $respTime,
            'resolved_at'    => $resolvedAt,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
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

    // Helper: kinukwenta ang metrics cards (Total/Open/Closed/Avg Response)
    // Ginawang sariling method ito para reusable sa index() at adminIndex(),
    // hindi na kailangang kopyahin ang code nang dalawang beses.
    private function buildMetrics()
    {
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

        return [
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