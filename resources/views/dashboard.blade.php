@extends('layouts.app')

@section('title', 'Customer Service Dashboard')
@section('page-title', 'Customer Service Dashboard')

@section('content')

    {{-- Summary Cards --}}
    <div class="grid grid-cols-4 gap-6 mb-6">

        {{-- Total Tickets --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-navy" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5m6-15l7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-navy mb-1">{{ number_format($totalTickets) }}</p>
            <p class="text-gray-500 text-sm font-medium mb-1">Total Tickets</p>
            <p class="text-gray-400 text-xs">vs last month</p>
        </div>

        {{-- Open Tickets --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-9 h-9 rounded-lg bg-orange-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3h9L21 7.5m-18 0v11.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V7.5m-18 0h18" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-navy mb-1">{{ number_format($openTickets) }}</p>
            <p class="text-gray-500 text-sm font-medium mb-1">Open Tickets</p>
            <p class="text-gray-400 text-xs">increase</p>
        </div>

        {{-- Closed Tickets --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-navy mb-1">{{ number_format($closedTickets) }}</p>
            <p class="text-gray-500 text-sm font-medium mb-1">Closed Tickets</p>
            <p class="text-gray-400 text-xs">resolved</p>
        </div>

        {{-- Avg Response Time --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-navy" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-navy mb-1">{{ $avgResponseTime }}m</p>
            <p class="text-gray-500 text-sm font-medium mb-1">Avg Response Time</p>
            <p class="text-gray-400 text-xs">faster</p>
        </div>

    </div>

    {{-- Agent Performance + Recent Activity --}}
    <div class="grid grid-cols-3 gap-6 mb-6">

        {{-- Agent Performance Overview --}}
        <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-navy">Agent Performance Overview</h2>
                <a href="{{ url('/customer-service/agents') }}" class="text-green-500 text-sm font-semibold flex items-center gap-1 hover:underline">
                    View All
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>
            </div>

            <div class="space-y-5">

                @foreach ($agents as $agent)
                    <div class="flex items-center gap-4">
                        @if ($agent['img'])
                            <img src="{{ $agent['img'] }}" alt="{{ $agent['name'] }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full bg-navy flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                {{ strtoupper(substr($agent['name'], 0, 1)) }}
                            </div>
                        @endif
                        <span class="w-28 text-navy font-medium text-sm flex-shrink-0">{{ $agent['name'] }}</span>
                        <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $agent['top'] ? 'bg-green-500' : 'bg-navy' }}" style="width: {{ $agent['percent'] }}%"></div>
                        </div>
                        <span class="w-10 text-right font-bold text-sm {{ $agent['top'] ? 'text-green-500' : 'text-navy' }}">{{ $agent['percent'] }}%</span>
                    </div>
                @endforeach

            </div>
        </div>

        {{-- Recent Activity Log --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-navy mb-5">Recent Activity Log</h2>

        <div class="space-y-4">
            @foreach ($recentLogs as $log)
                @php
                    $dotColor = match($log->severity) {
                        'CRITICAL' => 'bg-red-500',
                        'HIGH' => 'bg-orange-400',
                        'MEDIUM' => 'bg-yellow-400',
                        'LOW' => 'bg-green-500',
                        'SUCCESS' => 'bg-green-600',
                        default => 'bg-gray-300',
                    };
                @endphp
                <div class="flex gap-3">
                    <span class="w-2 h-2 mt-1.5 rounded-full {{ $dotColor }} flex-shrink-0"></span>
                    <div>
                        <p class="text-sm text-navy">{{ $log->description }}</p>
                        <p class="text-xs text-gray-400">{{ $log->logged_at->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>

            <a href="{{ url('/customer-service/logs') }}" class="block w-full mt-5 py-2.5 border border-navy text-navy font-semibold text-sm rounded-lg hover:bg-navy hover:text-white transition text-center">
                View Full Log
            </a>
        </div>

    </div>

    {{-- Ticket Volume Trend + CSAT --}}
    <div class="grid grid-cols-3 gap-6 items-stretch">

        {{-- Ticket Volume Trend --}}
        <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-navy">Ticket Volume Trend</h2>
                <div class="flex items-center gap-5 text-sm">
                    <span class="flex items-center gap-2 text-gray-500">
                        <span class="w-3 h-0.5 bg-navy inline-block"></span> Tickets Received
                    </span>
                    <span class="flex items-center gap-2 text-gray-500">
                        <span class="w-3 h-0.5 bg-green-500 inline-block"></span> Tickets Resolved
                    </span>
                </div>
            </div>
            <div class="h-80" style="height: 320px; min-height: 320px;">
                <canvas id="ticketVolumeChart"></canvas>
            </div>
        </div>

        {{-- Customer Satisfaction --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-navy mb-4">Customer Satisfaction (CSAT)</h2>

            <div class="text-center mb-2">
                <p class="text-4xl font-bold text-navy">{{ $overallCsat }}</p>
                <p class="text-gray-400 text-sm mb-2">/ 5.0</p>
                <div class="flex justify-center gap-1 text-yellow-400 mb-1">
                    @php
                        $fullStars = floor($overallCsat);
                        $decimal = $overallCsat - $fullStars;
                        $hasHalfStar = $decimal >= 0.25 && $decimal < 0.75;
                        if ($decimal >= 0.75) $fullStars++;
                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                    @endphp

                    {{-- Full Stars --}}
                    @for ($i = 0; $i < $fullStars; $i++)
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 0 0 .95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 0 0-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.367-2.446a1 1 0 0 0-1.176 0l-3.367 2.446c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 0 0-.364-1.118L2.83 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 0 0 .95-.69l1.286-3.958Z" />
                        </svg>
                    @endfor

                    {{-- Half Star --}}
                    @if ($hasHalfStar)
                        <svg class="w-4 h-4" viewBox="0 0 20 20">
                            <defs>
                                <linearGradient id="halfStar">
                                    <stop offset="50%" stop-color="currentColor" />
                                    <stop offset="50%" stop-color="#e5e7eb" />
                                </linearGradient>
                            </defs>
                            <path fill="url(#halfStar)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 0 0 .95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 0 0-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.367-2.446a1 1 0 0 0-1.176 0l-3.367 2.446c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 0 0-.364-1.118L2.83 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 0 0 .95-.69l1.286-3.958Z" />
                        </svg>
                    @endif

                    {{-- Empty Stars --}}
                    @for ($i = 0; $i < $emptyStars; $i++)
                        <svg class="w-4 h-4" fill="#e5e7eb" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 0 0 .95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 0 0-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.367-2.446a1 1 0 0 0-1.176 0l-3.367 2.446c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 0 0-.364-1.118L2.83 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 0 0 .95-.69l1.286-3.958Z" />
                        </svg>
                    @endfor
                </div>
                <p class="text-gray-400 text-xs mb-4">Based on 124 customer reviews this week</p>
            </div>

            <div class="relative w-40 h-40 mx-auto mb-4">
                <canvas id="csatDoughnutChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-xl font-bold text-navy">{{ $excellentPercent }}%</span>
                    <span class="text-xs text-gray-400">Excellent</span>
                </div>
            </div>

            <p class="text-sm font-semibold text-navy mb-3">Satisfaction Breakdown</p>
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Excellent</span>
                    <span class="font-semibold text-green-500">{{ $excellentPercent }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-navy"></span> Good</span>
                    <span class="font-semibold text-navy">{{ $goodPercent }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-orange-400"></span> Fair</span>
                    <span class="font-semibold text-orange-400">{{ $fairPercent }}%</span>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>

    // Dynamic datasets per period, galing sa database (Ticket model)
    const dashboardData = @json($dashboardPeriods);

    let ticketVolumeChart = null;

    function updateDashboardStats(period) {
        const data = dashboardData?.[period];
        if (!data) return;

        const statOpenTickets = document.getElementById('statOpenTickets');
        const statPendingTickets = document.getElementById('statPendingTickets');
        const statResolvedTickets = document.getElementById('statResolvedTickets');
        const labelOpenTickets = document.getElementById('labelOpenTickets');
        const labelPendingTickets = document.getElementById('labelPendingTickets');
        const labelResolvedTickets = document.getElementById('labelResolvedTickets');
        const headingResolvedTickets = document.getElementById('headingResolvedTickets');

        if (statOpenTickets) statOpenTickets.textContent = data.stats?.open ?? 0;
        if (statPendingTickets) statPendingTickets.textContent = data.stats?.pending ?? 0;
        if (statResolvedTickets) statResolvedTickets.textContent = data.stats?.resolved ?? 0;

        if (labelOpenTickets) labelOpenTickets.textContent = data.cardLabels?.open ?? '';
        if (labelPendingTickets) labelPendingTickets.textContent = data.cardLabels?.pending ?? '';
        if (labelResolvedTickets) labelResolvedTickets.textContent = data.cardLabels?.resolved ?? '';
        if (headingResolvedTickets) headingResolvedTickets.textContent = data.cardLabels?.resolvedHeading ?? '';

        if (!ticketVolumeChart) return;

        ticketVolumeChart.data.labels = data.labels ?? [];
        ticketVolumeChart.data.datasets[0].data = data.received ?? [];
        ticketVolumeChart.data.datasets[1].data = data.resolvedLine ?? [];
        ticketVolumeChart.update();
    }

    window.addEventListener('DOMContentLoaded', function() {
        const ticketCanvas = document.getElementById('ticketVolumeChart');
        const csatCanvas = document.getElementById('csatDoughnutChart');

        if (!window.Chart || !ticketCanvas || !csatCanvas) {
            return;
        }

        const initialData = dashboardData?.Today ?? Object.values(dashboardData)[0] ?? null;
        if (!initialData) return;

        ticketVolumeChart = new Chart(ticketCanvas, {
            type: 'line',
            data: {
                labels: initialData.labels ?? [],
                datasets: [
                    {
                        label: 'Tickets Received',
                        data: initialData.received ?? [],
                        borderColor: '#1E3A8A',
                        backgroundColor: 'rgba(30, 58, 138, 0.08)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#1E3A8A',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                    },
                    {
                        label: 'Tickets Resolved',
                        data: initialData.resolvedLine ?? [],
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.08)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#22c55e',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                    x: { grid: { display: false } }
                }
            }
        });

        new Chart(csatCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Excellent', 'Good', 'Fair'],
                datasets: [{
                    data: [{{ $excellentPercent }}, {{ $goodPercent }}, {{ $fairPercent }}],
                    backgroundColor: ['#22c55e', '#1E3A8A', '#fb923c'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });
    });
</script>
@endpush