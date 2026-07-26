<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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

    // =====================================================================
    // NEW: User-facing view — makikita LANG ng naka-login na customer ang
    // sarili niyang communication history (filtered gamit ang user_id).
    // =====================================================================
    public function myCommunications()
    {
        $communications = DB::table('communications')
            ->where('user_id', auth()->id())
            ->latest('id')
            ->get();

        return view('user-communications', compact('communications'));
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

        $agent = Agent::find($validated['agent_id']);

        DB::table('communications')->insert([
            'user_id'        => auth()->id(),
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
            'resolved_at'    => null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'date'     => 'nullable|string|max:255',
            'type'     => 'required|string|max:255',
            'subject'  => 'nullable|string|max:255',
            'staff'    => 'nullable|string|max:255',
            'status'   => 'required|string|max:255',
            'priority' => 'required|string|max:255',
        ]);

        $communication = DB::table('communications')->where('id', $id)->first();

        if (!$communication) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $updateData = [
            'date'       => $validated['date'] ?? $communication->date,
            'type'       => $validated['type'],
            'subject'    => $validated['subject'] ?? $communication->subject,
            'staff'      => $validated['staff'] ?? $communication->staff,
            'status'     => $validated['status'],
            'priority'   => $validated['priority'],
            'updated_at' => now(),
        ];

        $wasUnresolved = !in_array($communication->status, ['completed', 'resolved']);
        $isNowResolved = in_array($validated['status'], ['completed', 'resolved']);

        if ($wasUnresolved && $isNowResolved) {
            // First time this moves into a resolved state — stamp resolved_at
            // and calculate real elapsed time since creation.
            $resolvedAt = now();
            $updateData['resolved_at'] = $resolvedAt;
            $updateData['resp_time'] = $this->formatDuration(
                Carbon::parse($communication->created_at)->diffInMinutes($resolvedAt)
            );
        } elseif (!$isNowResolved) {
            // Reopened or still pending/cancelled — clear resolved data
            $updateData['resolved_at'] = null;
            $updateData['resp_time'] = 'Pending';
        }

        DB::table('communications')->where('id', $id)->update($updateData);

        return response()->json(['success' => true]);
    }

    private function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes . 'm';
        }
        $hours = intdiv($minutes, 60);
        $remMinutes = $minutes % 60;
        return $remMinutes > 0 ? "{$hours}h {$remMinutes}m" : "{$hours}h";
    }

    public function dashboardHistory()
    {
        $totalCommunications = DB::table('communications')->count();

        $pendingCount = DB::table('communications')->where('status', 'pending')->count();
        $pendingRate = $totalCommunications > 0
            ? round(($pendingCount / $totalCommunications) * 100, 1)
            : 0;

        $cancelledCount = DB::table('communications')->where('status', 'cancelled')->count();
        $cancellationRate = $totalCommunications > 0
            ? round(($cancelledCount / $totalCommunications) * 100, 1)
            : 0;

        $newCustomersThisWeek = DB::table('communications')
            ->select('customer_email')
            ->groupBy('customer_email')
            ->havingRaw('MIN(created_at) >= ?', [now()->startOfWeek()])
            ->get()
            ->count();

        $totalAudience = DB::table('communications')
            ->distinct()
            ->count('customer_email');

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

                $percent = $row->total > 0
                    ? round((($row->total - $pendingForStaff) / $row->total) * 100)
                    : 0;

                return [
                    'name'    => $row->staff,
                    'total'   => $row->total,
                    'percent' => $percent,
                ];
            });

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