@extends('layouts.app')

@section('title', 'System Activity Log')
@section('page-title', 'System Activity Log')
@section('breadcrumb', 'Recent Activity Log')

@section('content')

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-6 mb-6">

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-600 text-sm font-medium">Total Logs Recorded</p>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-navy" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-navy mb-1">{{ $totalLogsRecorded }}</p>
            <p class="text-gray-400 text-sm">Total logs recorded in the system</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-600 text-sm font-medium">Critical Escalations</p>
                <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-navy mb-1">{{ $criticalEscalations }}</p>
            <p class="text-gray-400 text-sm">Requires immediate supervisor review</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-600 text-sm font-medium">Automated System Actions</p>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-navy mb-1">{{ $automatedSystemActions }}</p>
            <p class="text-gray-400 text-sm">Triggers handled by background workflows</p>
        </div>

    </div>

    {{-- Detailed System Event Log --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-navy">Detailed System Event Log</h2>
            <div class="flex items-center gap-3 text-gray-400">
                <div class="relative">
                    <button id="exportBtn" onclick="toggleExportMenu()" class="hover:text-navy transition" title="Export">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 12m0 0 4.5-4.5M12 12V3" />
                        </svg>
                    </button>

                    <div id="exportMenu" class="hidden absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg py-1.5 z-50">
                        <button onclick="exportLogs('csv')" class="w-full flex items-center gap-2 text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Export as CSV
                        </button>
                        <button onclick="exportLogs('excel')" class="w-full flex items-center gap-2 text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Export as Excel
                        </button>
                        <button onclick="exportLogs('pdf')" class="w-full flex items-center gap-2 text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Export as PDF
                        </button>
                    </div>
                </div>
                <div class="relative">
                    <button id="moreOptionsBtn" onclick="toggleMoreOptions()" class="hover:text-navy transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                        </svg>
                    </button>

                    <div id="moreOptionsMenu" class="hidden absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg py-1.5 z-50">
                        <button onclick="refreshLogs()" class="w-full flex items-center gap-2 text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Refresh
                        </button>
                        <button onclick="printLogs()" class="w-full flex items-center gap-2 text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search + Tabs + Add Log --}}
        <div class="flex items-center justify-between mb-5 gap-4">
            <div class="relative flex-1 max-w-xs">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text" id="logSearch" onkeyup="filterLogs()" placeholder="Search {{ $filter === 'all' ? 'logs' : str_replace('-', ' ', $filter) }}..." class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-navy/20">
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1 bg-gray-50 rounded-full p-1">
                    @php
                        $tabs = [
                            'all' => 'All Events',
                            'escalations' => 'Escalations',
                            'status-updates' => 'Status Updates',
                            'sla-alerts' => 'SLA Alerts',
                            'user-creation' => 'User Creation',
                        ];
                    @endphp

                    @foreach ($tabs as $key => $label)
                        <a href="{{ url('/customer-service/logs/' . $key) }}"
                        class="px-4 py-1.5 rounded-full text-sm font-medium transition
                                {{ $filter === $key ? 'bg-blue-50 text-navy shadow-sm' : 'text-gray-500 hover:text-navy' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <button onclick="openAddLogModal()" class="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-navy text-white text-sm font-medium hover:bg-navy-dark transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Log
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 text-xs uppercase tracking-wide border-b border-gray-100">
                        <th class="pb-3 font-semibold">Timestamp</th>
                        <th class="pb-3 font-semibold">Event Type</th>
                        <th class="pb-3 font-semibold">Target ID</th>
                        <th class="pb-3 font-semibold">Description</th>
                        <th class="pb-3 font-semibold">Performed By</th>
                        <th class="pb-3 font-semibold">Severity</th>
                        <th class="pb-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="logTableBody">

                    @php
                        $eventColors = [
                            'Escalation' => 'bg-red-50 text-red-500',
                            'Status Update' => 'bg-blue-50 text-blue-500',
                            'Creation' => 'bg-green-50 text-green-500',
                            'SLA Alert' => 'bg-orange-50 text-orange-500',
                            'SLA Breach' => 'bg-red-50 text-red-500',
                            'Assignment' => 'bg-purple-50 text-purple-500',
                            'Resolution' => 'bg-green-50 text-green-500',
                            'User Creation' => 'bg-green-50 text-green-500',
                        ];

                        $severityColors = [
                            'CRITICAL' => 'bg-red-500 text-white',
                            'HIGH' => 'bg-orange-400 text-white',
                            'MEDIUM' => 'bg-yellow-400 text-white',
                            'LOW' => 'bg-green-500 text-white',
                            'SUCCESS' => 'bg-green-600 text-white',
                        ];
                    @endphp

                    @forelse ($filteredLogs as $log)
                        <tr class="log-row border-b border-gray-50 hover:bg-gray-50/50" data-desc="{{ strtolower($log['desc']) }}" data-target="{{ strtolower($log['target']) }}" data-by="{{ strtolower($log['by']) }}">
                            <td class="py-4 text-gray-500">{{ $log['time'] }}</td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold {{ $eventColors[$log['event']] ?? 'bg-gray-50 text-gray-500' }}">
                                    {{ $log['event'] }}
                                </span>
                            </td>
                            <td class="py-4">
                                <a href="javascript:void(0)" onclick="openLogModal('{{ $log['time'] }}', '{{ $log['event'] }}', '{{ $log['target'] }}', '{{ addslashes($log['desc']) }}', '{{ $log['by'] }}', '{{ $log['severity'] }}')" class="text-navy font-semibold hover:underline">{{ $log['target'] }}</a>
                            </td>
                            <td class="py-4 text-gray-600">{{ $log['desc'] }}</td>
                            <td class="py-4 text-navy">{{ $log['by'] }}</td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $severityColors[$log['severity']] ?? 'bg-gray-400 text-white' }}">
                                    {{ $log['severity'] }}
                                </span>
                            </td>
                            <td class="py-4">
                                <button onclick="event.stopPropagation(); openEditLogModal({{ $log['id'] }}, '{{ $log['target'] }}', '{{ $log['type'] }}', '{{ $log['event'] }}', '{{ addslashes($log['desc']) }}', '{{ $log['by'] }}', '{{ $log['severity'] }}')" class="text-gray-400 hover:text-navy transition p-1.5 rounded-lg hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">No log entries found for this filter.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Footer: Showing + Pagination --}}
        <div class="flex items-center justify-between mt-5">
            <p class="text-sm text-gray-400" id="logPageInfo">Showing 1 to {{ count($filteredLogs) }} of {{ count($filteredLogs) }} entries</p>
            <div class="flex items-center gap-2">
                <button id="logPrevBtn" onclick="changeLogPage(-1)" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded-lg text-gray-400 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>

                <div id="logPageNumbers" class="flex items-center gap-2"></div>

                <button id="logNextBtn" onclick="changeLogPage(1)" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded-lg text-gray-400 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </div>

    </div>

    {{-- Log Detail Modal --}}
    <div id="logModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeLogModal()">
        <div class="bg-white rounded-xl w-full max-w-md p-6 pt-10 relative">
            <button onclick="closeLogModal()" class="absolute top-3 right-3 text-gray-400 hover:text-navy p-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="flex items-center justify-between mb-4">
                <span id="modalLogEvent" class="px-2.5 py-1 rounded-md text-xs font-semibold"></span>
                <span id="modalLogSeverity" class="px-2.5 py-1 rounded-md text-xs font-bold mr-6"></span>
            </div>

            <h3 id="modalLogTarget" class="text-lg font-bold text-navy mb-2"></h3>
            <p id="modalLogDesc" class="text-gray-600 text-sm mb-5"></p>

            <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Timestamp</span>
                    <span id="modalLogTime" class="text-navy font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Performed By</span>
                    <span id="modalLogBy" class="text-navy font-medium"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Log Modal --}}
    <div id="addLogModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeAddLogModal()">
        <div class="bg-white rounded-xl w-full max-w-md p-6 relative">
            <button onclick="closeAddLogModal()" class="absolute top-4 right-4 text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="text-lg font-bold text-navy mb-4">Add New Log</h3>

            @if ($errors->any() && old('form') === 'add-log')
                <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-xs text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>&bull; {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/customer-service/logs') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="form" value="add-log">

                <div>
                    <label class="text-xs font-semibold text-gray-500">Target ID <span class="text-gray-400 font-normal">(auto-generated)</span></label>
                    <input type="text" name="target_id" value="{{ $nextTicketNumber }}" readonly class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500">Event Type</label>
                    <select name="type_event" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                        <option value="escalations|Escalation">Escalation</option>
                        <option value="status-updates|Status Update">Status Update</option>
                        <option value="status-updates|Assignment">Assignment</option>
                        <option value="status-updates|Resolution">Resolution</option>
                        <option value="creation|Creation">Creation</option>
                        <option value="sla-alerts|SLA Alert">SLA Alert</option>
                        <option value="sla-alerts|SLA Breach">SLA Breach</option>
                        <option value="user-creation|User Creation">User Creation</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500">Description</label>
                    <textarea name="description" rows="3" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20"></textarea>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500">Performed By</label>
                    <select name="performed_by" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                        <option value="System Workflow">System Workflow</option>
                        <option value="Admin">Admin</option>
                        <option value="Customer Guest">Customer Guest</option>
                        @foreach ($agentNames as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500">Severity</label>
                    <select name="severity" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                        <option value="CRITICAL">Critical</option>
                        <option value="HIGH">High</option>
                        <option value="MEDIUM">Medium</option>
                        <option value="LOW">Low</option>
                        <option value="SUCCESS">Success</option>
                    </select>
                </div>

                <button type="submit" class="w-full mt-4 py-2.5 bg-navy text-white font-semibold text-sm rounded-lg hover:bg-navy-dark transition">
                    Add Log
                </button>
            </form>
        </div>
    </div>

    {{-- Edit Log Modal --}}
    <div id="editLogModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeEditLogModal()">
        <div class="bg-white rounded-xl w-full max-w-md p-6 relative">
            <button onclick="closeEditLogModal()" class="absolute top-4 right-4 text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="text-lg font-bold text-navy mb-4">Edit Log</h3>

            @if ($errors->any() && old('form') === 'edit-log')
                <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-xs text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>&bull; {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="editLogForm" action="" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="form" value="edit-log">

                <div>
                    <label class="text-xs font-semibold text-gray-500">Target ID</label>
                    <input type="text" name="target_id" id="editLogTarget" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500">Event Type</label>
                    <select name="type_event" id="editLogTypeEvent" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                        <option value="escalations|Escalation">Escalation</option>
                        <option value="status-updates|Status Update">Status Update</option>
                        <option value="status-updates|Assignment">Assignment</option>
                        <option value="status-updates|Resolution">Resolution</option>
                        <option value="creation|Creation">Creation</option>
                        <option value="sla-alerts|SLA Alert">SLA Alert</option>
                        <option value="sla-alerts|SLA Breach">SLA Breach</option>
                        <option value="user-creation|User Creation">User Creation</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500">Description</label>
                    <textarea name="description" id="editLogDescription" rows="3" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20"></textarea>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500">Performed By</label>
                    <select name="performed_by" id="editLogBy" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                        <option value="System Workflow">System Workflow</option>
                        <option value="Admin">Admin</option>
                        <option value="Customer Guest">Customer Guest</option>
                        @foreach ($agentNames as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500">Severity</label>
                    <select name="severity" id="editLogSeverity" required class="w-full mt-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy/20">
                        <option value="CRITICAL">Critical</option>
                        <option value="HIGH">High</option>
                        <option value="MEDIUM">Medium</option>
                        <option value="LOW">Low</option>
                        <option value="SUCCESS">Success</option>
                    </select>
                </div>

                <button type="submit" class="w-full mt-4 py-2.5 bg-navy text-white font-semibold text-sm rounded-lg hover:bg-navy-dark transition">
                    Save Changes
                </button>
            </form>

            <form id="deleteLogForm" action="" method="POST" class="mt-2" onsubmit="return confirm('Are you sure you want to delete this log entry? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2.5 border border-red-500 text-red-500 font-semibold text-sm rounded-lg hover:bg-red-500 hover:text-white transition">
                    Delete Log
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function toggleMoreOptions() {
        document.getElementById('moreOptionsMenu').classList.toggle('hidden');
    }

    function refreshLogs() {
        document.getElementById('moreOptionsMenu').classList.add('hidden');
        location.reload();
    }

    function printLogs() {
        document.getElementById('moreOptionsMenu').classList.add('hidden');
        window.print();
    }

    function filterLogs() {
        const query = document.getElementById('logSearch').value.toLowerCase();
        const rows = document.querySelectorAll('.log-row');

        rows.forEach(row => {
            const desc = row.dataset.desc;
            const target = row.dataset.target;
            const by = row.dataset.by;
            const matches = desc.includes(query) || target.includes(query) || by.includes(query);
            row.dataset.hiddenBySearch = matches ? 'false' : 'true';
        });

        logCurrentPage = 1;
        renderLogPagination();
    }

    // Log Pagination
    let logCurrentPage = 1;
    const logsPerPage = 4;

    function renderLogPagination() {
        const allRows = Array.from(document.querySelectorAll('#logTableBody .log-row'));
        const visibleRows = allRows.filter(row => row.dataset.hiddenBySearch !== 'true');

        const totalPages = Math.max(1, Math.ceil(visibleRows.length / logsPerPage));
        if (logCurrentPage > totalPages) logCurrentPage = totalPages;

        allRows.forEach(row => row.style.display = 'none');

        const start = (logCurrentPage - 1) * logsPerPage;
        const end = start + logsPerPage;
        const pageRows = visibleRows.slice(start, end);
        pageRows.forEach(row => row.style.display = '');

        document.getElementById('logPageInfo').textContent =
            `Showing ${visibleRows.length === 0 ? 0 : start + 1} to ${Math.min(end, visibleRows.length)} of ${visibleRows.length} entries`;

        const pageNumbersDiv = document.getElementById('logPageNumbers');
        pageNumbersDiv.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = i === logCurrentPage
                ? 'w-8 h-8 flex items-center justify-center text-sm font-semibold bg-green-500 text-white rounded-lg'
                : 'w-8 h-8 flex items-center justify-center text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-lg transition';
            btn.onclick = () => { logCurrentPage = i; renderLogPagination(); };
            pageNumbersDiv.appendChild(btn);
        }

        document.getElementById('logPrevBtn').disabled = logCurrentPage === 1;
        document.getElementById('logNextBtn').disabled = logCurrentPage === totalPages;
    }

    function changeLogPage(direction) {
        logCurrentPage += direction;
        renderLogPagination();
    }

    const eventColorMap = {
        'Escalation': 'bg-red-50 text-red-500',
        'Status Update': 'bg-blue-50 text-blue-500',
        'Creation': 'bg-green-50 text-green-500',
        'SLA Alert': 'bg-orange-50 text-orange-500',
        'SLA Breach': 'bg-red-50 text-red-500',
        'Assignment': 'bg-purple-50 text-purple-500',
        'Resolution': 'bg-green-50 text-green-500',
        'User Creation': 'bg-green-50 text-green-500',
    };

    const severityColorMap = {
        'CRITICAL': 'bg-red-500 text-white',
        'HIGH': 'bg-orange-400 text-white',
        'MEDIUM': 'bg-yellow-400 text-white',
        'LOW': 'bg-green-500 text-white',
        'SUCCESS': 'bg-green-600 text-white',
    };

    function openLogModal(time, event, target, desc, by, severity) {
        document.getElementById('modalLogTime').textContent = time;
        document.getElementById('modalLogTarget').textContent = target;
        document.getElementById('modalLogDesc').textContent = desc;
        document.getElementById('modalLogBy').textContent = by;

        const eventEl = document.getElementById('modalLogEvent');
        eventEl.textContent = event;
        eventEl.className = 'px-2.5 py-1 rounded-md text-xs font-semibold ' + (eventColorMap[event] || 'bg-gray-50 text-gray-500');

        const sevEl = document.getElementById('modalLogSeverity');
        sevEl.textContent = severity;
        sevEl.className = 'px-2.5 py-1 rounded-md text-xs font-bold ' + (severityColorMap[severity] || 'bg-gray-400 text-white');

        document.getElementById('logModal').classList.remove('hidden');
    }

    function closeLogModal() {
        document.getElementById('logModal').classList.add('hidden');
    }

    // Add Log Modal
    function openAddLogModal() {
        document.getElementById('addLogModal').classList.remove('hidden');
    }

    function closeAddLogModal() {
        document.getElementById('addLogModal').classList.add('hidden');
    }

    // Edit Log Modal
    function openEditLogModal(id, target, type, event, desc, by, severity) {
        const form = document.getElementById('editLogForm');
        form.action = `/customer-service/logs/${id}`;

        document.getElementById('deleteLogForm').action = `/customer-service/logs/${id}`;
        document.getElementById('editLogTarget').value = target;
        document.getElementById('editLogTypeEvent').value = `${type}|${event}`;
        document.getElementById('editLogDescription').value = desc;
        document.getElementById('editLogBy').value = by;
        document.getElementById('editLogSeverity').value = severity;

        document.getElementById('editLogModal').classList.remove('hidden');
    }

    function closeEditLogModal() {
        document.getElementById('editLogModal').classList.add('hidden');
    }

    function toggleExportMenu() {
        document.getElementById('exportMenu').classList.toggle('hidden');
    }

    function getVisibleLogRows() {
        const rows = document.querySelectorAll('#logTableBody .log-row');
        const data = [];
        rows.forEach(row => {
            if (row.style.display === 'none') return;
            const cells = row.querySelectorAll('td');
            data.push({
                time: cells[0].textContent.trim(),
                event: cells[1].textContent.trim(),
                target: cells[2].textContent.trim(),
                desc: cells[3].textContent.trim(),
                by: cells[4].textContent.trim(),
                severity: cells[5].textContent.trim(),
            });
        });
        return data;
    }

    function exportLogs(format) {
        document.getElementById('exportMenu').classList.add('hidden');
        const data = getVisibleLogRows();

        if (data.length === 0) {
            showToast('No data available to export.', 'error');
            return;
        }

        const filename = `system-activity-log-${new Date().toISOString().slice(0, 10)}`;
        const headers = ['Timestamp', 'Event Type', 'Target ID', 'Description', 'Performed By', 'Severity'];

        if (format === 'csv') {
            const escape = (val) => `"${String(val).replace(/"/g, '""')}"`;
            const rows = [headers.join(',')];
            data.forEach(log => {
                rows.push([log.time, log.event, log.target, log.desc, log.by, log.severity].map(escape).join(','));
            });
            const blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
            downloadBlob(blob, `${filename}.csv`);
            showToast('CSV file exported successfully!', 'success');

        } else if (format === 'excel') {
            const wsData = [headers, ...data.map(log => [log.time, log.event, log.target, log.desc, log.by, log.severity])];
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Activity Log');
            XLSX.writeFile(wb, `${filename}.xlsx`);
            showToast('Excel file exported successfully!', 'success');

        } else if (format === 'pdf') {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape' });
            doc.setFontSize(14);
            doc.text('System Activity Log', 14, 15);
            doc.autoTable({
                startY: 20,
                head: [headers],
                body: data.map(log => [log.time, log.event, log.target, log.desc, log.by, log.severity]),
                styles: { fontSize: 8 },
                headStyles: { fillColor: [30, 58, 138] },
            });
            doc.save(`${filename}.pdf`);
            showToast('PDF file exported successfully!', 'success');
        }
    }

    function downloadBlob(blob, filename) {
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    document.addEventListener('click', function (event) {
        const exportMenu = document.getElementById('exportMenu');
        const exportBtn = document.getElementById('exportBtn');
        if (exportMenu && exportBtn && !exportMenu.contains(event.target) && !exportBtn.contains(event.target)) {
            exportMenu.classList.add('hidden');
        }

        const moreMenu = document.getElementById('moreOptionsMenu');
        const moreBtn = document.getElementById('moreOptionsBtn');
        if (moreMenu && moreBtn && !moreMenu.contains(event.target) && !moreBtn.contains(event.target)) {
            moreMenu.classList.add('hidden');
        }
    });

    document.addEventListener('DOMContentLoaded', renderLogPagination);
</script>
@endpush

@if ($errors->any() && old('form') === 'add-log')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            openAddLogModal();
        });
    </script>
@endif

@if ($errors->any() && old('form') === 'edit-log')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('editLogModal').classList.remove('hidden');
        });
    </script>
@endif