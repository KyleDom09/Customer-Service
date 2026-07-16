<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index($filter = 'all')
    {
        $query = ActivityLog::query()->orderByDesc('logged_at');

        if ($filter !== 'all') {
            $query->where('type', $filter);
        }

        $logs = $query->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'type' => $log->type,
                'event' => $log->event,
                'time' => $log->logged_at->diffForHumans(),
                'target' => $log->target_id,
                'desc' => $log->description,
                'by' => $log->performed_by,
                'severity' => $log->severity,
            ];
        })->toArray();

        $totalLogsRecorded = ActivityLog::count();
        $criticalEscalations = ActivityLog::where('severity', 'CRITICAL')->count();
        $automatedSystemActions = ActivityLog::where('performed_by', 'System Workflow')->count();

        $maxTicketNumber = ActivityLog::where('target_id', 'like', 'Ticket #%')
            ->get()
            ->map(fn ($log) => (int) str_replace('Ticket #', '', $log->target_id))
            ->max();

        $nextTicketNumber = 'Ticket #' . (($maxTicketNumber ?? 999) + 1);

        $agentNames = Agent::pluck('name');

        return view('logs', [
            'filter' => $filter,
            'filteredLogs' => $logs,
            'totalLogsRecorded' => $totalLogsRecorded,
            'criticalEscalations' => $criticalEscalations,
            'automatedSystemActions' => $automatedSystemActions,
            'nextTicketNumber' => $nextTicketNumber,
            'agentNames' => $agentNames,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_id' => 'required|string|max:255',
            'type_event' => 'required|string',
            'description' => 'required|string',
            'performed_by' => 'required|string|max:255',
            'severity' => 'required|string|max:50',
        ]);

        if (str_starts_with($validated['target_id'], 'Ticket #')) {
            $newNumber = (int) str_replace('Ticket #', '', $validated['target_id']);
            $maxNumber = ActivityLog::where('target_id', 'like', 'Ticket #%')
                ->get()
                ->map(fn ($log) => (int) str_replace('Ticket #', '', $log->target_id))
                ->max();

            if ($maxNumber && $newNumber <= $maxNumber) {
                return back()->withErrors(['target_id' => 'Ticket number must be higher than the current highest ticket.']);
            }
        }

        [$type, $event] = explode('|', $validated['type_event']);

        ActivityLog::create([
            'target_id' => $validated['target_id'],
            'type' => $type,
            'event' => $event,
            'description' => $validated['description'],
            'performed_by' => $validated['performed_by'],
            'severity' => $validated['severity'],
            'logged_at' => now(),
        ]);

        return redirect('/customer-service/logs');
    }

    public function update(Request $request, ActivityLog $activityLog)
    {
        $validated = $request->validate([
            'target_id' => 'required|string|max:255',
            'type_event' => 'required|string',
            'description' => 'required|string',
            'performed_by' => 'required|string|max:255',
            'severity' => 'required|string|max:50',
        ]);

        [$type, $event] = explode('|', $validated['type_event']);

        $activityLog->update([
            'target_id' => $validated['target_id'],
            'type' => $type,
            'event' => $event,
            'description' => $validated['description'],
            'performed_by' => $validated['performed_by'],
            'severity' => $validated['severity'],
        ]);

        return redirect('/customer-service/logs');
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();

        return redirect('/customer-service/logs');
    }

    public function markAllRead()
    {
        ActivityLog::whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}