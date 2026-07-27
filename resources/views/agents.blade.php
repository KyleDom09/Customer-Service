@extends('layouts.app')

@section('title', 'Agent Performance Directory')
@section('page-title', 'Agent Performance Directory')
@section('breadcrumb', 'Agent Performance Overview')

@section('content')

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-6 mb-6">

        {{-- Total Active Agents --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-500 text-xs font-semibold tracking-wide">TOTAL ACTIVE AGENTS</p>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-navy" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-navy mb-1">{{ $totalActiveAgents }}</p>
            <p class="text-gray-400 text-sm">Currently active &amp; online</p>
        </div>

        {{-- Team Avg Efficiency --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-500 text-xs font-semibold tracking-wide">TEAM AVG. EFFICIENCY</p>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-navy" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-navy mb-1">{{ $teamAvgEfficiency }}%</p>
            <p class="text-green-500 text-sm font-medium">+2.5% improvement this week</p>
        </div>

        {{-- Top Performer Today --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-500 text-xs font-semibold tracking-wide">TOP PERFORMER TODAY</p>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-green-500 mb-1">{{ $topPerformer->name ?? 'N/A' }}</p>
            <p class="text-gray-400 text-sm">{{ $topPerformer->csat_score ?? 0 }}/5.0 CSAT Score</p>
        </div>

    </div>

    {{-- Detailed Agent Metrics Table --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-navy">Detailed Agent Metrics</h2>

            <div class="flex items-center gap-3">
                {{-- Search --}}
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" id="agentSearch" onkeyup="filterAgents()" placeholder="Search agent..." class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg w-56 focus:outline-none focus:ring-2 focus:ring-navy/20">
                </div>

                {{-- Role Filter Dropdown --}}
                <div class="relative">
                    <button id="teamFilterBtn" onclick="toggleTeamFilter()" class="flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                        <span id="teamFilterLabel">All Roles</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div id="teamFilterMenu" class="hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg py-1.5 z-50">
                        <button onclick="selectTeamFilter('All Roles')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">All Roles</button>
                        <button onclick="selectTeamFilter('Support Agent')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Support Agent</button>
                        <button onclick="selectTeamFilter('Senior Agent')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Senior Agent</button>
                    </div>
                </div>

                {{-- Add New Agent Button --}}
                <button onclick="openAddAgentModal()" class="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-navy text-white text-sm font-medium hover:bg-navy-dark transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add New Agent
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 text-xs uppercase tracking-wide border-b border-gray-100">
                        <th class="pb-3 font-semibold cursor-pointer select-none hover:text-navy" onclick="sortAgents('name')">
                            <span class="flex items-center gap-1">Agent Name <span id="sortIcon-name"></span></span>
                        </th>
                        <th class="pb-3 font-semibold">Active Status</th>
                        <th class="pb-3 font-semibold cursor-pointer select-none hover:text-navy" onclick="sortAgents('assigned')">
                            <span class="flex items-center gap-1">Total Assigned <span id="sortIcon-assigned"></span></span>
                        </th>
                        <th class="pb-3 font-semibold cursor-pointer select-none hover:text-navy" onclick="sortAgents('resolved')">
                            <span class="flex items-center gap-1">Total Resolved <span id="sortIcon-resolved"></span></span>
                        </th>
                        <th class="pb-3 font-semibold cursor-pointer select-none hover:text-navy" onclick="sortAgents('response')">
                            <span class="flex items-center gap-1">Avg. Response Time <span id="sortIcon-response"></span></span>
                        </th>
                        <th class="pb-3 font-semibold cursor-pointer select-none hover:text-navy" onclick="sortAgents('csat')">
                            <span class="flex items-center gap-1">CSAT Score <span id="sortIcon-csat"></span></span>
                        </th>
                        <th class="pb-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="agentTableBody">

                    @php
                        $statusColors = [
                            'Online' => 'bg-green-50 text-green-600',
                            'Away' => 'bg-orange-50 text-orange-500',
                            'Offline' => 'bg-gray-100 text-gray-500',
                        ];
                        $dotColors = [
                            'Online' => 'bg-green-500',
                            'Away' => 'bg-orange-400',
                            'Offline' => 'bg-gray-400',
                        ];
                    @endphp

                    @foreach ($agents as $agent)
                       <tr class="agent-row border-b border-gray-50 hover:bg-gray-50/50 cursor-pointer"
                            data-name="{{ strtolower($agent['name']) }}"
                            data-role="{{ strtolower($agent['role']) }}"
                            data-assigned="{{ $agent['assigned'] }}"
                            data-resolved="{{ $agent['resolved'] }}"
                            data-response-min="{{ (int) filter_var($agent['response'], FILTER_SANITIZE_NUMBER_INT) }}"
                            data-csat="{{ $agent['csat'] }}"
                            onclick="openAgentModal('{{ $agent['name'] }}', '{{ $agent['role'] }}', '{{ $agent['status'] }}', {{ $agent['assigned'] }}, {{ $agent['resolved'] }}, '{{ $agent['response'] }}', {{ $agent['csat'] }}, '{{ $agent['img'] ?? '' }}')">
                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    @if ($agent['img'])
                                        <img src="{{ $agent['img'] }}" alt="{{ $agent['name'] }}" class="w-9 h-9 rounded-full object-cover">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-navy flex items-center justify-center text-white text-xs font-semibold">
                                            {{ strtoupper(substr($agent['name'], 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-navy">{{ $agent['name'] }}</p>
                                        <p class="text-gray-400 text-xs">{{ $agent['role'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$agent['status']] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$agent['status']] }}"></span>
                                    {{ $agent['status'] }}
                                </span>
                            </td>
                            <td class="py-4 text-navy">{{ $agent['assigned'] }}</td>
                            <td class="py-4 text-navy">{{ $agent['resolved'] }}</td>
                            <td class="py-4 text-navy">{{ $agent['response'] }}</td>
                            <td class="py-4">
                                @php
                                    $fullStars = floor($agent['csat']);
                                    $decimal = $agent['csat'] - $fullStars;
                                    $hasHalf = false;

                                    if ($decimal >= 0.6) {
                                        $fullStars++;
                                    } elseif ($decimal > 0) {
                                        $hasHalf = true;
                                    }

                                    $gradientId = 'halfStarAgent' . $loop->index;
                                @endphp
                                <div class="flex items-center gap-1">
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $fullStars)
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 0 0 .95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 0 0-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.367-2.446a1 1 0 0 0-1.176 0l-3.367 2.446c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 0 0-.364-1.118L2.83 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 0 0 .95-.69l1.286-3.958Z" />
                                                </svg>
                                            @elseif ($hasHalf && $i == $fullStars + 1)
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20">
                                                    <defs>
                                                        <linearGradient id="{{ $gradientId }}">
                                                            <stop offset="50%" stop-color="currentColor" />
                                                            <stop offset="50%" stop-color="#e5e7eb" />
                                                        </linearGradient>
                                                    </defs>
                                                    <path fill="url(#{{ $gradientId }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 0 0 .95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 0 0-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.367-2.446a1 1 0 0 0-1.176 0l-3.367 2.446c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 0 0-.364-1.118L2.83 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 0 0 .95-.69l1.286-3.958Z" />
                                                </svg>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="#e5e7eb" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 0 0 .95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 0 0-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.367-2.446a1 1 0 0 0-1.176 0l-3.367 2.446c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 0 0-.364-1.118L2.83 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 0 0 .95-.69l1.286-3.958Z" />
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="font-semibold text-navy ml-1">{{ $agent['csat'] }}<span class="text-gray-400 font-normal">/5.0</span></span>
                                </div>
                            </td>
                            <td class="py-4">
                                <button onclick="event.stopPropagation(); openEditAgentModal({{ $agent['id'] }}, '{{ $agent['name'] }}', '{{ $agent['role'] }}', '{{ strtolower($agent['status']) }}', {{ $agent['assigned'] }}, {{ $agent['resolved'] }}, {{ (int) filter_var($agent['response'], FILTER_SANITIZE_NUMBER_INT) }}, {{ $agent['csat'] }})" class="text-gray-400 hover:text-navy transition p-1.5 rounded-lg hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        {{-- Footer: Showing + Pagination --}}
        <div class="flex items-center justify-between mt-5">
            <p class="text-sm text-gray-400" id="agentPageInfo">Showing 1 to 3 of {{ count($agents) }} agents</p>
            <div class="flex items-center gap-2" id="agentPagination">
                <button id="agentPrevBtn" onclick="changeAgentPage(-1)" class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition flex items-center gap-1 disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    Previous
                </button>

                <div id="agentPageNumbers" class="flex items-center gap-2"></div>

                <button id="agentNextBtn" onclick="changeAgentPage(1)" class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition flex items-center gap-1 disabled:opacity-40 disabled:cursor-not-allowed">
                    Next
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </div>

    </div>

@endsection

{{-- Agent Profile Modal --}}
<div id="agentModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeAgentModal()">
    <div class="bg-white rounded-xl w-full max-w-sm p-6 relative">
        <button onclick="closeAgentModal()" class="absolute top-4 right-4 text-gray-400 hover:text-navy">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex flex-col items-center text-center mb-5">
            <img id="modalAgentImg" src="" alt="" class="w-16 h-16 rounded-full object-cover mb-3 hidden">
            <div id="modalAgentInitial" class="w-16 h-16 rounded-full bg-navy text-white text-xl font-bold flex items-center justify-center mb-3 hidden"></div>
            <h3 id="modalAgentName" class="text-lg font-bold text-navy"></h3>
            <p id="modalAgentRole" class="text-gray-400 text-sm"></p>
            <span id="modalAgentStatus" class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"></span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-center border-t border-gray-100 pt-4">
            <div>
                <p class="text-xl font-bold text-navy" id="modalAgentAssigned"></p>
                <p class="text-xs text-gray-400">Total Assigned</p>
            </div>
            <div>
                <p class="text-xl font-bold text-navy" id="modalAgentResolved"></p>
                <p class="text-xs text-gray-400">Total Resolved</p>
            </div>
            <div>
                <p class="text-xl font-bold text-navy" id="modalAgentResponse"></p>
                <p class="text-xs text-gray-400">Avg. Response</p>
            </div>
            <div>
                <p class="text-xl font-bold text-navy" id="modalAgentCsat"></p>
                <p class="text-xs text-gray-400">CSAT Score</p>
            </div>
        </div>
    </div>
</div>

{{-- Add Agent Modal --}}
<div id="addAgentModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeAddAgentModal()">
    <div class="bg-white rounded-xl w-full max-w-md p-6 relative">
        <button onclick="closeAddAgentModal()" class="absolute top-4 right-4 text-gray-400 hover:text-navy">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <h3 class="text-lg font-bold text-navy mb-4">Add New Agent</h3>

        @if ($errors->any() && old('form') === 'add-agent')
            <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                <ul class="text-xs text-red-600 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>&bull; {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/customer-service/agents') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="form" value="add-agent">

            <div>
                <label class="text-xs font-semibold text-gray-500">Name</label>
                <input type="text" name="name" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-500">Role</label>
                <select name="role" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                    <option value="Senior Agent">Senior Agent</option>
                    <option value="Support Agent">Support Agent</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-500">Active Status</label>
                <select name="active_status" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                    <option value="away">Away</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-gray-500">Total Assigned</label>
                    <input type="number" name="total_assigned" value="0" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500">Total Resolved</label>
                    <input type="number" name="total_resolved" value="0" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-gray-500">Avg Response (min)</label>
                    <input type="number" name="avg_response_minutes" value="0" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500">CSAT Score</label>
                    <input type="number" step="0.1" min="0" max="5" name="csat_score" value="0" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                </div>
            </div>

            <button type="submit" class="w-full mt-4 py-2.5 bg-navy text-white font-semibold text-sm rounded-lg hover:bg-navy-dark transition">
                Add Agent
            </button>
        </form>
    </div>
</div>

{{-- Edit Agent Modal --}}
<div id="editAgentModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeEditAgentModal()">
    <div class="bg-white rounded-xl w-full max-w-md p-6 relative">
        <button onclick="closeEditAgentModal()" class="absolute top-4 right-4 text-gray-400 hover:text-navy">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <h3 class="text-lg font-bold text-navy mb-4">Edit Agent</h3>

        @if ($errors->any() && old('form') === 'edit-agent')
            <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                <ul class="text-xs text-red-600 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>&bull; {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="editAgentForm" action="" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <input type="hidden" name="form" value="edit-agent">

            <div>
                <label class="text-xs font-semibold text-gray-500">Name</label>
                <input type="text" name="name" id="editAgentName" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-500">Role</label>
                <select name="role" id="editAgentRole" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                    <option value="Senior Agent">Senior Agent</option>
                    <option value="Support Agent">Support Agent</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-500">Active Status</label>
                <select name="active_status" id="editAgentStatus" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                    <option value="away">Away</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-gray-500">Total Assigned</label>
                    <input type="number" name="total_assigned" id="editAgentAssigned" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500">Total Resolved</label>
                    <input type="number" name="total_resolved" id="editAgentResolved" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-gray-500">Avg Response (min)</label>
                    <input type="number" name="avg_response_minutes" id="editAgentResponse" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500">CSAT Score</label>
                    <input type="number" step="0.1" min="0" max="5" name="csat_score" id="editAgentCsat" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                </div>
            </div>

            <button type="submit" class="w-full mt-4 py-2.5 bg-navy text-white font-semibold text-sm rounded-lg hover:bg-navy-dark transition">
                Save Changes
            </button>
        </form>

        <form id="deleteAgentForm" action="" method="POST" class="mt-2" onsubmit="return confirm('Are you sure you want to delete this agent? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full py-2.5 border border-red-500 text-red-500 font-semibold text-sm rounded-lg hover:bg-red-500 hover:text-white transition">
                Delete Agent
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentSortKey = null;
    let currentSortDir = 'asc';

    function sortAgents(key) {
        const tbody = document.getElementById('agentTableBody');
        const rows = Array.from(tbody.querySelectorAll('.agent-row'));

        // Kung parehong column ulit ang pinindot, i-reverse yung direction
        if (currentSortKey === key) {
            currentSortDir = currentSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortKey = key;
            currentSortDir = 'asc';
        }

        const getValue = (row) => {
            if (key === 'name') return row.dataset.name;
            if (key === 'assigned') return parseInt(row.dataset.assigned);
            if (key === 'resolved') return parseInt(row.dataset.resolved);
            if (key === 'response') return parseInt(row.dataset.responseMin);
            if (key === 'csat') return parseFloat(row.dataset.csat);
        };

        rows.sort((a, b) => {
            const valA = getValue(a);
            const valB = getValue(b);
            if (valA < valB) return currentSortDir === 'asc' ? -1 : 1;
            if (valA > valB) return currentSortDir === 'asc' ? 1 : -1;
            return 0;
        });

        rows.forEach(row => tbody.appendChild(row));

        // I-update yung sort icons
        ['name', 'assigned', 'resolved', 'response', 'csat'].forEach(k => {
            const icon = document.getElementById('sortIcon-' + k);
            if (!icon) return;
            icon.textContent = (k === currentSortKey) ? (currentSortDir === 'asc' ? '▲' : '▼') : '';
        });

        agentCurrentPage = 1;
        renderAgentPagination();
    }

    function toggleTeamFilter() {
        document.getElementById('teamFilterMenu').classList.toggle('hidden');
    }
    function selectTeamFilter(value) {
        document.getElementById('teamFilterLabel').textContent = value;
        document.getElementById('teamFilterMenu').classList.add('hidden');

        const rows = document.querySelectorAll('.agent-row');
        const query = value.toLowerCase();

        rows.forEach(row => {
            const role = row.dataset.role;
            const matches = query === 'all roles' || role === query;
            row.dataset.hiddenBySearch = matches ? 'false' : 'true';
        });

        document.getElementById('agentSearch').value = '';
        agentCurrentPage = 1;
        renderAgentPagination();
    }
    
    const statusColorMap = {
        'Online': 'bg-green-50 text-green-600',
        'Away': 'bg-orange-50 text-orange-500',
        'Offline': 'bg-gray-100 text-gray-500',
    };

    function openAgentModal(name, role, status, assigned, resolved, response, csat, img) {
        document.getElementById('modalAgentName').textContent = name;
        document.getElementById('modalAgentRole').textContent = role;
        document.getElementById('modalAgentAssigned').textContent = assigned;
        document.getElementById('modalAgentResolved').textContent = resolved;
        document.getElementById('modalAgentResponse').textContent = response;
        document.getElementById('modalAgentCsat').textContent = csat + '/5.0';

        const statusEl = document.getElementById('modalAgentStatus');
        statusEl.textContent = status;
        statusEl.className = 'mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium ' + (statusColorMap[status] || 'bg-gray-100 text-gray-500');

        const imgEl = document.getElementById('modalAgentImg');
        const initialEl = document.getElementById('modalAgentInitial');
        if (img) {
            imgEl.src = img;
            imgEl.classList.remove('hidden');
            initialEl.classList.add('hidden');
        } else {
            imgEl.classList.add('hidden');
            initialEl.textContent = name.charAt(0).toUpperCase();
            initialEl.classList.remove('hidden');
        }

        document.getElementById('agentModal').classList.remove('hidden');
    }

    function closeAgentModal() {
        document.getElementById('agentModal').classList.add('hidden');
    }

    // Add Agent Modal
    function openAddAgentModal() {
        document.getElementById('addAgentModal').classList.remove('hidden');
    }

    function closeAddAgentModal() {
        document.getElementById('addAgentModal').classList.add('hidden');
    }

    // Edit Agent Modal
    function openEditAgentModal(id, name, role, status, assigned, resolved, responseMinutes, csat) {
        const form = document.getElementById('editAgentForm');
        form.action = `/customer-service/agents/${id}`;

        document.getElementById('deleteAgentForm').action = `/customer-service/agents/${id}`;
        document.getElementById('editAgentName').value = name;
        document.getElementById('editAgentRole').value = role;
        document.getElementById('editAgentStatus').value = status;
        document.getElementById('editAgentAssigned').value = assigned;
        document.getElementById('editAgentResolved').value = resolved;
        document.getElementById('editAgentResponse').value = responseMinutes;
        document.getElementById('editAgentCsat').value = csat;

        document.getElementById('editAgentModal').classList.remove('hidden');
    }

    function closeEditAgentModal() {
        document.getElementById('editAgentModal').classList.add('hidden');
    }

    document.addEventListener('click', function (event) {
        const menu = document.getElementById('teamFilterMenu');
        const btn = document.getElementById('teamFilterBtn');
        if (menu && btn && !menu.contains(event.target) && !btn.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });

    function filterAgents() {
        const query = document.getElementById('agentSearch').value.toLowerCase();
        const rows = document.querySelectorAll('.agent-row');

        rows.forEach(row => {
            const name = row.dataset.name;
            const role = row.dataset.role;
            const matches = name.includes(query) || role.includes(query);
            row.dataset.hiddenBySearch = matches ? 'false' : 'true';
        });

        agentCurrentPage = 1;
        renderAgentPagination();
    }

        // Agent Pagination
    let agentCurrentPage = 1;
    const agentsPerPage = 7;

    function renderAgentPagination() {
        const allRows = Array.from(document.querySelectorAll('#agentTableBody .agent-row'));
        const visibleRows = allRows.filter(row => row.dataset.hiddenBySearch !== 'true');

        const totalPages = Math.max(1, Math.ceil(visibleRows.length / agentsPerPage));
        if (agentCurrentPage > totalPages) agentCurrentPage = totalPages;

        allRows.forEach(row => row.style.display = 'none');

        const start = (agentCurrentPage - 1) * agentsPerPage;
        const end = start + agentsPerPage;
        const pageRows = visibleRows.slice(start, end);
        pageRows.forEach(row => row.style.display = '');

        document.getElementById('agentPageInfo').textContent =
            `Showing ${visibleRows.length === 0 ? 0 : start + 1} to ${Math.min(end, visibleRows.length)} of ${visibleRows.length} agents`;

        const pageNumbersDiv = document.getElementById('agentPageNumbers');
        pageNumbersDiv.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = i === agentCurrentPage
                ? 'w-8 h-8 flex items-center justify-center text-sm font-semibold bg-green-500 text-white rounded-lg'
                : 'w-8 h-8 flex items-center justify-center text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-lg transition';
            btn.onclick = () => { agentCurrentPage = i; renderAgentPagination(); };
            pageNumbersDiv.appendChild(btn);
        }

        document.getElementById('agentPrevBtn').disabled = agentCurrentPage === 1;
        document.getElementById('agentNextBtn').disabled = agentCurrentPage === totalPages;
    }

    function changeAgentPage(direction) {
        agentCurrentPage += direction;
        renderAgentPagination();
    }

    document.addEventListener('DOMContentLoaded', renderAgentPagination);
</script>
@endpush

@if ($errors->any() && old('form') === 'add-agent')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            openAddAgentModal();
        });
    </script>
@endif

@if ($errors->any() && old('form') === 'edit-agent')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('editAgentModal').classList.remove('hidden');
        });
    </script>
@endif