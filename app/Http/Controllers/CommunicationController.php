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

    // Ito ang method para sa Dashboard History page (real data mula sa database)
    public function dashboardHistory()
    {
        $totalCommunications = DB::table('communications')->count();

        // Pending Rate: % ng communications na status = pending
        $pendingCount = DB::table('communications')->where('status', 'pending')->count();
        $pendingRate = $totalCommunications > 0
            ? round(($pendingCount / $totalCommunications) * 100, 1)
            : 0;

        // Cancellation Rate: % ng communications na status = cancelled
        $cancelledCount = DB::table('communications')->where('status', 'cancelled')->count();
        $cancellationRate = $totalCommunications > 0
            ? round(($cancelledCount / $totalCommunications) * 100, 1)
            : 0;

        // New Customers: distinct customer_email na unang lumabas this week
        $newCustomersThisWeek = DB::table('communications')
            ->select('customer_email')
            ->groupBy('customer_email')
            ->havingRaw('MIN(created_at) >= ?', [now()->startOfWeek()])
            ->get()
            ->count();

        // Audience Growth: total distinct customers overall (cumulative)
        $totalAudience = DB::table('communications')
            ->distinct()
            ->count('customer_email');

        // Top Staff: bilang ng communications kada staff, at % completed (gamit real statuses)
        $topStaff = DB::table('communications')
            ->select('staff', DB::raw('COUNT(*) as total'))
            ->whereNotNull('staff')
            ->where('staff', '!=', '')
            ->groupBy('staff')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $pendingForStaff = DB::table('communications')
                    ->where('staff', $row->staff)
                    ->where('status', 'pending')
                    ->count();

                // % ng hindi pending (ibig sabihin, natapos na ang usapan)
                $percent = $row->total > 0
                    ? round((($row->total - $pendingForStaff) / $row->total) * 100)
                    : 0;

                return [
                    'name'    => $row->staff,
                    'total'   => $row->total,
                    'percent' => $percent,
                ];
            });

        // Recent Communications: pinaka-huling 5 na naitala
        $recentCommunications = DB::table('communications')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('Dashboard-Communication', compact(
            'totalCommunications',
            'pendingRate',
            'cancellationRate',
            'newCustomersThisWeek',
            'totalAudience',
            'topStaff',
            'recentCommunications'
        ));
    }
}