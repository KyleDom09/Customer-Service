<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\ActivityLog;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $agents = Agent::all()
            ->map(function ($agent) {
                return [
                    'name' => $agent->name,
                    'percent' => (int) round($agent->total_resolved / max($agent->total_assigned, 1) * 100),
                    'img' => $agent->avatar ?: 'https://i.pravatar.cc/150?img=' . (($agent->id % 70) + 1),
                ];
            })
            ->sortByDesc('percent')
            ->take(5)
            ->values()
            ->map(function ($agent, $index) {
                $agent['top'] = $index === 0;
                return $agent;
            })
            ->toArray();

        $recentLogs = ActivityLog::orderByDesc('logged_at')->take(4)->get();

        // CSAT computation base sa Agent csat_score
        $allAgentsForCsat = Agent::all();
        $overallCsat = $allAgentsForCsat->count() > 0
            ? round($allAgentsForCsat->avg('csat_score'), 1)
            : 0;

        $excellentCount = $allAgentsForCsat->where('csat_score', '>=', 4.5)->count();
        $goodCount = $allAgentsForCsat->whereBetween('csat_score', [3.5, 4.49])->count();
        $fairCount = $allAgentsForCsat->where('csat_score', '<', 3.5)->count();
        $totalAgentsForCsat = max($allAgentsForCsat->count(), 1);

        $excellentPercent = round(($excellentCount / $totalAgentsForCsat) * 100);
        $goodPercent = round(($goodCount / $totalAgentsForCsat) * 100);
        $fairPercent = round(($fairCount / $totalAgentsForCsat) * 100);

        // Ticket summary counts (matched sa Ticket Management submodule metrics)
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'OPEN')->count();
        $closedTickets = Ticket::where('status', 'CLOSED')->count();
        $avgResponseTime = (int) round(Ticket::whereNotNull('response_minutes')->avg('response_minutes') ?? 0);

        $pendingTickets = Ticket::where('status', 'PENDING')->count();

        // Build dynamic period data (Today / This Week / This Month)
        $todayResolved = Ticket::where('status', 'RESOLVED')->whereDate('resolved_at', today())->count();

        $weekResolved = Ticket::where('status', 'RESOLVED')->whereBetween('resolved_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $monthResolved = Ticket::where('status', 'RESOLVED')->whereMonth('resolved_at', now()->month)->whereYear('resolved_at', now()->year)->count();

        // Chart: last 5 days (Today), last 4 weeks (This Week), last 6 months (This Month)
        $last5Days = collect(range(4, 0))->map(fn ($i) => now()->subDays($i));
        $dailyLabels = $last5Days->map(fn ($d) => $d->format('D'))->toArray();
        $dailyReceived = $last5Days->map(fn ($d) => Ticket::whereDate('created_at', $d)->count())->toArray();
        $dailyResolved = $last5Days->map(fn ($d) => Ticket::where('status', 'RESOLVED')->whereDate('resolved_at', $d)->count())->toArray();

        $last4Weeks = collect(range(3, 0))->map(fn ($i) => now()->subWeeks($i));
        $weeklyLabels = $last4Weeks->map(fn ($w, $idx) => 'Week ' . ($idx + 1))->toArray();
        $weeklyReceived = $last4Weeks->map(fn ($w) => Ticket::whereBetween('created_at', [$w->copy()->startOfWeek(), $w->copy()->endOfWeek()])->count())->toArray();
        $weeklyResolved = $last4Weeks->map(fn ($w) => Ticket::where('status', 'RESOLVED')->whereBetween('resolved_at', [$w->copy()->startOfWeek(), $w->copy()->endOfWeek()])->count())->toArray();

        $last6Months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));
        $monthlyLabels = $last6Months->map(fn ($m) => $m->format('M'))->toArray();
        $monthlyReceived = $last6Months->map(fn ($m) => Ticket::whereMonth('created_at', $m->month)->whereYear('created_at', $m->year)->count())->toArray();
        $monthlyResolved = $last6Months->map(fn ($m) => Ticket::where('status', 'RESOLVED')->whereMonth('resolved_at', $m->month)->whereYear('resolved_at', $m->year)->count())->toArray();

        $dashboardPeriods = [
            'Today' => [
                'stats' => ['open' => $openTickets, 'pending' => $pendingTickets, 'resolved' => $todayResolved],
                'labels' => $dailyLabels,
                'received' => $dailyReceived,
                'resolvedLine' => $dailyResolved,
                'cardLabels' => [
                    'open' => 'Active tickets',
                    'pending' => 'Awaiting response',
                    'resolved' => 'Closed today',
                    'resolvedHeading' => 'RESOLVED TODAY',
                ],
            ],
            'This Week' => [
                'stats' => ['open' => $openTickets, 'pending' => $pendingTickets, 'resolved' => $weekResolved],
                'labels' => $weeklyLabels,
                'received' => $weeklyReceived,
                'resolvedLine' => $weeklyResolved,
                'cardLabels' => [
                    'open' => 'Active tickets this week',
                    'pending' => 'Awaiting response',
                    'resolved' => 'Closed this week',
                    'resolvedHeading' => 'RESOLVED THIS WEEK',
                ],
            ],
            'This Month' => [
                'stats' => ['open' => $openTickets, 'pending' => $pendingTickets, 'resolved' => $monthResolved],
                'labels' => $monthlyLabels,
                'received' => $monthlyReceived,
                'resolvedLine' => $monthlyResolved,
                'cardLabels' => [
                    'open' => 'Active tickets this month',
                    'pending' => 'Awaiting response',
                    'resolved' => 'Closed this month',
                    'resolvedHeading' => 'RESOLVED THIS MONTH',
                ],
            ],
        ];

        return view('dashboard', [
            'agents' => $agents,
            'recentLogs' => $recentLogs,
            'overallCsat' => $overallCsat,
            'excellentPercent' => $excellentPercent,
            'goodPercent' => $goodPercent,
            'fairPercent' => $fairPercent,
            'totalTickets' => $totalTickets,
            'openTickets' => $openTickets,
            'closedTickets' => $closedTickets,
            'avgResponseTime' => $avgResponseTime,
            'dashboardPeriods' => $dashboardPeriods,
        ]);
    }
}