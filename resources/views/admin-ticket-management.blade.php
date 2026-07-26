<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Service - Ticket Management (Admin View)</title>
    <script src="{{ asset('vendor/tailwind.js') }}"></script>
    <style> body { font-family: 'Segoe UI', 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#F4F7FE] text-slate-800 antialiased flex h-screen overflow-hidden relative">

    <!-- 1. SIDEBAR -->
    @include('partials.sidebar')

    <!-- MAIN INTERFACE CONTAINER -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- 2. TOP NAVBAR -->
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-8 shrink-0">
            <div class="text-xs text-slate-400 font-medium">Dashboard &nbsp;&gt;&nbsp; <span class="text-slate-700 font-semibold">Tickets</span></div>
            <div class="w-96 relative">
                <input type="text" id="search-input" oninput="filterTickets()" placeholder="Search tickets, customers..." class="w-full bg-[#F4F7FE] border border-slate-200 rounded-full pl-9 pr-4 py-1.5 text-xs outline-none focus:border-blue-500 transition">
                <span class="absolute left-3 top-2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
            </div>
            <div class="flex items-center gap-4">
                <!-- Notifications Bell -->
                <div class="relative">
                    <button onclick="toggleNotificationPanel(event)" id="notif-btn" class="relative text-slate-400 hover:text-slate-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                      <span id="notif-badge" class="hidden absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center"></span>
                    </button>

                    <!-- Notifications Panel -->
                    <div id="notif-panel" class="hidden absolute right-0 top-10 w-80 bg-white rounded-xl border border-slate-200 shadow-lg z-50 max-h-96 flex flex-col">
                        <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-800">Notifications</span>
                            <span class="text-[10px] text-slate-400">{{ count($tickets) }} total</span>
                        </div>
                        <div class="overflow-y-auto flex-1">
                            @forelse($tickets as $ticket)
                                <div data-notif-id="{{ $ticket->id }}" onclick="openTicketFromNotif({{ $ticket->id }})" class="px-4 py-2.5 border-b border-slate-50 hover:bg-slate-50/70 transition flex items-start gap-2.5 cursor-pointer">
                                    <div class="w-7 h-7 rounded-full {{ $ticket->avatar_bg }} flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">
                                        {{ $ticket->initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[11px] text-slate-700 leading-snug">
                                            <span class="font-semibold text-slate-900">{{ $ticket->name }}</span> created a new ticket
                                            <span class="font-semibold">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        </p>
                                        <p class="text-[10px] text-slate-400 truncate">{{ $ticket->subject }}</p>
                                        <p class="text-[9px] text-slate-400 mt-0.5">{{ $ticket->created }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-[11px] text-slate-400">No notifications yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button onclick="toggleProfileMenu(event)" id="profile-btn" class="flex items-center gap-3 border-l border-slate-200 pl-4 cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-[#1A2B6D] flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="text-left">
                            <div class="text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-slate-400">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div id="profile-menu" class="hidden absolute right-0 top-12 w-48 bg-white rounded-xl border border-slate-200 shadow-lg z-50 py-1.5 text-xs">
                        <button type="button" onclick="openEditProfileModal()" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-slate-600 hover:bg-slate-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            My Profile
                        </button>
                        <button type="button" onclick="openAccountSettingsModal()" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-slate-600 hover:bg-slate-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Account Settings
                        </button>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form action="/logout" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-red-500 hover:bg-red-50 transition font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- 3. MAIN CONTENT AREA -->
        <main class="flex-1 p-8 overflow-y-auto">
            
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Support Ticket Management</h1>
                    <p class="text-xs text-slate-400 mt-1">Manage customer support requests, monitor ticket progress, assign agents, and resolve issues efficiently.</p>
                </div>
                <div class="flex gap-2">
                    <!-- Tinanggal ang Filter at Export dito, naiwan ang New Ticket na malinis -->
                    <button onclick="openNewTicketModal()" class="bg-[#00CB92] text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-[#00B582] transition shadow-sm active:scale-95">+ New Ticket</button>
                </div>
            </div>

            <!-- 4. METRICS CARDS -->
            <div class="grid grid-cols-4 gap-4 mb-6">
                @foreach($metrics as $metric)
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-between relative overflow-hidden">
                        <div>
                            <span class="text-[11px] font-medium text-slate-400 block mb-1">{{ $metric['label'] }}</span>
                            <span class="text-xl font-bold text-slate-800">{{ $metric['value'] }}</span>
                        </div>
                        <span class="absolute top-4 right-4 text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-[#00CB92] border border-emerald-100">
                            {{ $metric['change'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- 5. TABLE CONTAINER -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                
                <div class="px-6 py-3 border-b border-slate-100 flex justify-between items-center bg-white">
                    <div id="status-tabs" class="flex gap-2 text-xs font-medium text-slate-400">
                        <!-- Nilagyan ng id="all-tab-btn" para ma-control ng View All button mamaya -->
                        <button id="all-tab-btn" onclick="filterStatus('ALL', this)" class="tab-btn px-2.5 py-1 rounded bg-[#00CB92] text-white font-semibold transition-all">All</button>
                        <button onclick="filterStatus('OPEN', this)" class="tab-btn px-2.5 py-1 rounded hover:text-slate-600 transition-all">Open</button>
                        <button onclick="filterStatus('PENDING', this)" class="tab-btn px-2.5 py-1 rounded hover:text-slate-600 transition-all">Pending</button>
                        <button onclick="filterStatus('IN PROGRESS', this)" class="tab-btn px-2.5 py-1 rounded hover:text-slate-600 transition-all">In Progress</button>
                        <button onclick="filterStatus('RESOLVED', this)" class="tab-btn px-2.5 py-1 rounded hover:text-slate-600 transition-all">Resolved</button>
                        <button onclick="filterStatus('CLOSED', this)" class="tab-btn px-2.5 py-1 rounded hover:text-slate-600 transition-all">Closed</button>
                    </div>
                    <!-- Ginawang dynamic at clickable ang View All button para i-reset ang filter -->
                    <button onclick="filterStatus('ALL', document.getElementById('all-tab-btn'))" class="text-xs text-blue-500 font-semibold hover:underline bg-transparent border-none p-0 cursor-pointer">View All &rarr;</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] uppercase font-bold tracking-wider text-slate-400">
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Ticket ID</th>
                                <th class="px-6 py-3">Subject</th>
                                <th class="px-6 py-3">Category</th>
                                <th class="px-6 py-3">Assigned Agent</th>
                                <th class="px-6 py-3">Priority</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Created</th>
                                <th class="px-6 py-3 text-right">Last Updated</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-100 text-slate-600">
                            @foreach($tickets as $ticket)
                            <tr data-status="{{ $ticket->status }}"
                                data-description="{{ $ticket->description }}"
                                data-search="{{ strtolower($ticket->customer_name . ' ' . $ticket->customer_email . ' ' . $ticket->subject . ' TK-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT) . ' ' . $ticket->category) }}"
                                data-id="{{ $ticket->id }}"
                                onclick="openDrawer('#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}', '{{ $ticket->name }}', '{{ $ticket->email }}', '{{ $ticket->initials }}', '{{ $ticket->subject }}', '{{ $ticket->category }}', '{{ $ticket->priority }}', '{{ $ticket->status }}', '{{ $ticket->avatar_bg }}', {{ $ticket->id }}, '{{ $ticket->agent }}', '{{ $ticket->agent_initials }}', '{{ $ticket->agent_bg }}', this)"
                                class="ticket-row hover:bg-slate-50/50 transition-colors cursor-pointer">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full {{ $ticket->avatar_bg }} flex items-center justify-center font-bold text-[10px]">
                                            {{ $ticket->initials }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900 text-xs">{{ $ticket->name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $ticket->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-3.5 font-medium text-slate-400">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</td>
                                
                                <td class="px-6 py-3.5">
                                    <div class="font-semibold text-slate-900">{{ $ticket->subject }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5"># {{ $ticket->sub_subject }}</div>
                                </td>
                                
                                <td class="px-6 py-3.5">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        📁 {{ $ticket->category }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full {{ $ticket->agent_bg }} flex items-center justify-center font-bold text-[8px]">
                                            {{ $ticket->agent_initials }}
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $ticket->agent }}</span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold border tracking-wider
                                        {{ $ticket->priority == 'CRITICAL' ? 'bg-red-50 text-red-600 border-red-200' : '' }}
                                        {{ $ticket->priority == 'HIGH' ? 'bg-orange-50 text-orange-600 border-orange-200' : '' }}
                                        {{ $ticket->priority == 'MEDIUM' ? 'bg-amber-50 text-amber-600 border-amber-200' : '' }}
                                        {{ $ticket->priority == 'LOW' ? 'bg-slate-100 text-slate-500 border-slate-200' : '' }}">
                                        {{ $ticket->priority }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold tracking-wider
                                        {{ $ticket->status == 'OPEN' ? 'bg-blue-50 text-blue-600 border border-blue-100' : '' }}
                                        {{ $ticket->status == 'IN PROGRESS' ? 'bg-purple-50 text-purple-600 border border-purple-100' : '' }}
                                        {{ $ticket->status == 'PENDING' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                        {{ $ticket->status == 'RESOLVED' ? 'bg-green-50 text-green-600 border border-green-100' : '' }}
                                        {{ $ticket->status == 'CLOSED' ? 'bg-slate-100 text-slate-400 border border-slate-200' : '' }}
                                        {{ $ticket->status == 'CANCELLED' ? 'bg-red-50 text-red-500 border border-red-100' : '' }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-3.5 text-slate-400 font-medium">{{ $ticket->created }}</td>
                                <td class="px-6 py-3.5 text-right text-slate-400 font-medium">{{ $ticket->updated }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </main>
    </div>

    <!-- 6. TICKET DETAIL DRAWER -->
    <div id="ticket-drawer" class="fixed inset-y-0 right-0 w-[380px] bg-white shadow-2xl border-l border-slate-200 z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col justify-between p-6">

        <form id="ticket-edit-form" method="POST" action="" class="flex flex-col justify-between flex-1 overflow-hidden">
            @csrf
            @method('PUT')
            <input type="hidden" name="customer_name" id="field-customer_name">
            <input type="hidden" name="customer_email" id="field-customer_email">

            <div class="overflow-y-auto flex-1 pr-1">
                <div class="flex justify-between items-start border-b border-slate-100 pb-4">
                    <div>
                        <h2 id="drawer-id" class="text-sm font-bold text-slate-900">Ticket #TK-2847</h2>
                        <p class="text-[10px] text-slate-400 mt-0.5">Opened Jan 15, 2026</p>
                    </div>
                    <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-slate-600 text-sm font-bold bg-slate-100 w-6 h-6 rounded-full flex items-center justify-center">✕</button>
                </div>

                <div class="flex gap-2 mt-4 items-center flex-wrap">
                    <select name="priority" id="drawer-priority" onchange="adminChangePriority()" class="px-2 py-1 rounded text-[9px] font-bold border tracking-wider bg-red-50 text-red-600 border-red-200 outline-none cursor-pointer">
                        <option value="CRITICAL">CRITICAL</option>
                        <option value="HIGH">HIGH</option>
                        <option value="MEDIUM">MEDIUM</option>
                        <option value="LOW">LOW</option>
                    </select>
                    <select name="status" id="drawer-status" onchange="adminChangeStatus()" class="px-2 py-1 rounded text-[9px] font-bold tracking-wider bg-blue-50 text-blue-600 border border-blue-100 outline-none cursor-pointer">
                        <option value="OPEN">OPEN</option>
                        <option value="PENDING">PENDING</option>
                        <option value="IN PROGRESS">IN PROGRESS</option>
                        <option value="RESOLVED">RESOLVED</option>
                        <option value="CLOSED">CLOSED</option>
                        <option value="CANCELLED">CANCELLED</option>
                    </select>
                    <span id="drawer-waiting-badge" class="hidden px-2 py-0.5 rounded text-[9px] font-bold tracking-wider bg-amber-50 text-amber-600 border border-amber-100 animate-pulse">⏳ Waiting for your reply</span>
                </div>

                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mt-5 mb-2">Customer</p>
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex items-center gap-3">
                    <div id="drawer-initials" class="w-8 h-8 rounded-full bg-[#E0E7FF] text-[#4F46E5] flex items-center justify-center font-bold text-xs">SJ</div>
                    <div>
                        <div id="drawer-name" class="text-xs font-bold text-slate-800">Sarah Johnson</div>
                        <div id="drawer-email" class="text-[10px] text-slate-400">sarah@techcorp.com</div>
                        <div class="text-[9px] text-slate-400 font-medium">TechCorp Inc.</div>
                    </div>
                </div>

                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mt-5 mb-1">Subject</p>
                <input type="text" name="subject" id="drawer-subject-input" class="w-full text-xs font-semibold text-slate-800 bg-slate-50/50 p-2.5 rounded-lg border border-slate-100 outline-none focus:border-blue-500 transition">

                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mt-4 mb-1">Description</p>
                <textarea name="description" id="drawer-subject-desc" rows="3" class="w-full text-xs text-slate-600 leading-relaxed bg-slate-50/50 p-2.5 rounded-lg border border-slate-100 outline-none focus:border-blue-500 transition resize-none"></textarea>

                <!-- LIVE CHAT / CONVERSATION -->
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mt-5 mb-2">Conversation</p>
                <div id="drawer-chat-messages" class="space-y-3 bg-slate-50/60 border border-slate-100 rounded-xl p-3 max-h-64 overflow-y-auto">
                    <!-- chat bubbles injected here by JS -->
                </div>

                <!-- Reply composer (Admin/Agent POV) -->
                <div class="mt-3 flex items-end gap-2">
                    <textarea id="chat-input" rows="2" placeholder="I-type ang reply mo bilang agent..." class="flex-1 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition resize-none"></textarea>
                    <button type="button" onclick="sendChatMessage(event)" class="bg-[#00CB92] text-white w-9 h-9 shrink-0 rounded-xl text-xs font-semibold hover:bg-[#00B582] transition flex items-center justify-center">
                        ↩
                    </button>
                </div>

                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mt-5 mb-2">Assigned Agent</p>
                <div class="flex items-center justify-between bg-slate-50 p-2 rounded-lg border border-slate-100 text-xs gap-2">
                    <div class="flex items-center gap-2 flex-1">
                        <div id="drawer-agent-initials" class="w-6 h-6 rounded-full bg-[#E0F2FE] text-[#0369A1] flex items-center justify-center font-bold text-[10px] shrink-0">MC</div>
                        <select name="agent_id" id="drawer-agent-select" onchange="adminChangeAgent()" class="font-semibold text-slate-700 bg-transparent outline-none flex-1 cursor-pointer">
                            @foreach ($agents as $agentOption)
                                <option value="{{ $agentOption->id }}" data-name="{{ $agentOption->name }}" data-initials="{{ $agentOption->initials ?? '' }}">{{ $agentOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="text-[10px] font-medium text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100 shrink-0">⭐ 4.9</span>
                </div>

                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mt-5 mb-2">Details</p>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-2 text-[11px] mb-4">
                    <div class="flex justify-between items-center"><span class="text-slate-400">Category:</span>
                        <select name="category" id="drawer-category" onchange="adminChangeCategory()" class="font-semibold text-slate-700 bg-transparent outline-none text-right cursor-pointer">
                            <option value="Billing">Billing</option>
                            <option value="Auth">Account / Login</option>
                            <option value="Technical">Technical</option>
                            <option value="Feature">Feature Request</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="space-y-2 border-t border-slate-100 pt-4 bg-white">
                <button type="submit" class="w-full bg-[#00CB92] text-white py-2 rounded-xl text-xs font-semibold hover:bg-[#00B582] transition flex items-center justify-center gap-1.5">
                    <span>💾</span> Save Changes
                </button>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="quickSetStatus('RESOLVED')" class="w-full border border-green-200 text-green-600 py-2 rounded-xl text-xs font-semibold hover:bg-green-50 transition">
                        ✓ Resolve
                    </button>
                    <button type="button" onclick="quickSetStatus('CLOSED')" class="w-full border border-slate-200 text-slate-500 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 transition">
                        Close Ticket
                    </button>
                </div>
                <button type="button" onclick="deleteCurrentTicket()" class="w-full border border-red-200 text-red-500 py-2 rounded-xl text-xs font-semibold hover:bg-red-50 transition">
                    🗑 Delete Ticket
                </button>
            </div>
        </form>
    </div>

    <!-- Hidden delete form (submitted dynamically via JS for the ticket currently open in the drawer) -->
    <form id="ticket-delete-form" method="POST" action="" class="hidden">
        @csrf
        @method('DELETE')
    </form>


    <!-- 7. NEW INTERACTIVE MODAL FOR "+ NEW TICKET" -->
    <div id="new-ticket-modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl w-[450px] shadow-2xl border border-slate-100 overflow-hidden transform scale-95 transition-transform duration-300">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Create New Support Ticket</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Fill out customer details and request specifications.</p>
                </div>
                <button onclick="closeNewTicketModal()" class="text-slate-400 hover:text-slate-600 font-bold bg-slate-200/60 w-6 h-6 rounded-full flex items-center justify-center text-xs">✕</button>
            </div>
            
            <!-- Modal Body Form -->
            <form action="{{ route('tickets.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Customer Name</label>
                    <input type="text" name="customer_name" required placeholder="e.g. Juan Dela Cruz" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Email Address</label>
                    <input type="email" name="customer_email" required placeholder="e.g. juan@example.com" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Category</label>
                        <select name="category" required class="w-full border border-slate-200 bg-white rounded-xl px-2 py-2 text-xs outline-none focus:border-blue-500 transition">
                            <option value="Auth">Auth</option>
                            <option value="Billing">Billing</option>
                            <option value="Technical">Technical</option>
                            <option value="Feature">Feature</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Priority Level</label>
                        <select name="priority" required class="w-full border border-slate-200 bg-white rounded-xl px-2 py-2 text-xs outline-none focus:border-blue-500 transition">
                            <option value="LOW" class="text-blue-500 font-semibold">LOW</option>
                            <option value="MEDIUM" class="text-amber-500 font-semibold">MEDIUM</option>
                            <option value="HIGH" class="text-orange-500 font-semibold">HIGH</option>
                            <option value="CRITICAL" class="text-red-500 font-semibold">CRITICAL</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Assigned Agent</label>
                    <select name="agent_id" class="w-full border border-slate-200 bg-white rounded-xl px-2 py-2 text-xs outline-none focus:border-blue-500 transition">
                        <option value="">Unassigned</option>
                        @foreach ($agents as $agentOption)
                            <option value="{{ $agentOption->id }}">{{ $agentOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Subject Issue</label>
                    <input type="text" name="subject" required placeholder="Brief statement of the concern" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Full Description</label>
                    <textarea name="description" rows="3" placeholder="Describe the error or support request detailedly..." class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition resize-none"></textarea>
                </div>

                <!-- Modal Footer Buttons -->
                <div class="border-t border-slate-100 pt-4 flex gap-2 justify-end text-xs font-semibold">
                    <button type="button" onclick="closeNewTicketModal()" class="border border-slate-200 text-slate-500 px-4 py-2 rounded-xl hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit" class="bg-[#00CB92] text-white px-5 py-2 rounded-xl hover:bg-[#00B582] transition shadow-sm">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 7B. EDIT PROFILE MODAL -->
    <div id="edit-profile-modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl w-[420px] shadow-2xl border border-slate-100 overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Edit Profile</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Update your personal information.</p>
                </div>
                <button type="button" onclick="closeEditProfileModal()" class="text-slate-400 hover:text-slate-600 font-bold bg-slate-200/60 w-6 h-6 rounded-full flex items-center justify-center text-xs">✕</button>
            </div>

            <form action="/profile" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 rounded-full bg-[#1A2B6D] flex items-center justify-center text-white text-sm font-bold">TF</div>
                    <button type="button" class="text-[10px] font-semibold text-blue-500 border border-blue-200 hover:bg-blue-50 px-2.5 py-1.5 rounded-lg transition">Change Photo</button>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Full Name</label>
                    <input type="text" name="name" value="Timoty Filoteo" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Email Address</label>
                    <input type="email" name="email" value="timoty@company.com" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Job Title</label>
                    <input type="text" name="role" value="Ticket Manager" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>

                <div class="border-t border-slate-100 pt-4 flex gap-2 justify-end text-xs font-semibold">
                    <button type="button" onclick="closeEditProfileModal()" class="border border-slate-200 text-slate-500 px-4 py-2 rounded-xl hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit" class="bg-[#00CB92] text-white px-5 py-2 rounded-xl hover:bg-[#00B582] transition shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 7C. ACCOUNT SETTINGS MODAL -->
    <div id="account-settings-modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl w-[420px] shadow-2xl border border-slate-100 overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Account Settings</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Manage your account security and preferences.</p>
                </div>
                <button type="button" onclick="closeAccountSettingsModal()" class="text-slate-400 hover:text-slate-600 font-bold bg-slate-200/60 w-6 h-6 rounded-full flex items-center justify-center text-xs">✕</button>
            </div>

            <form action="/account" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Current Password</label>
                    <input type="password" name="current_password" placeholder="Enter current password" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">New Password</label>
                    <input type="password" name="new_password" placeholder="Enter new password" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" placeholder="Re-enter new password" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>

                <div class="border-t border-slate-100 pt-3 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-slate-700">Email Notifications</div>
                        <div class="text-[10px] text-slate-400">Get notified when a new ticket is created</div>
                    </div>
                    <input type="checkbox" name="email_notifications" checked class="w-4 h-4 accent-[#00CB92]">
                </div>

                <div class="border-t border-slate-100 pt-4 flex gap-2 justify-end text-xs font-semibold">
                    <button type="button" onclick="closeAccountSettingsModal()" class="border border-slate-200 text-slate-500 px-4 py-2 rounded-xl hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit" class="bg-[#00CB92] text-white px-5 py-2 rounded-xl hover:bg-[#00B582] transition shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 8. APP SCRIPTS -->
    <script>
        // ==============================
        // CHAT / CONVERSATION SYSTEM
        // ==============================
        // ==============================
        // NOTIFICATION READ-TRACKING
        // ==============================
        const NOTIF_READ_KEY = 'notif_read_ticket_ids';

        function getReadIds() {
            try {
                return JSON.parse(localStorage.getItem(NOTIF_READ_KEY)) || [];
            } catch {
                return [];
            }
        }

        function saveReadIds(ids) {
            localStorage.setItem(NOTIF_READ_KEY, JSON.stringify(ids));
        }

        function getAllNotifIds() {
            return [...document.querySelectorAll('#notif-panel [data-notif-id]')]
                .map(el => el.dataset.notifId);
        }

        function updateNotifBadge() {
            const allIds = getAllNotifIds();
            const readIds = getReadIds();
            const unreadCount = allIds.filter(id => !readIds.includes(id)).length;

            const badge = document.getElementById('notif-badge');
            if (unreadCount > 0) {
                badge.innerText = unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        function markAllNotifsAsRead() {
            saveReadIds(getAllNotifIds());
            updateNotifBadge();
        }

        document.addEventListener('DOMContentLoaded', updateNotifBadge);

        let currentTicketKey = null;
        let currentAgentName = 'Unassigned';
        let currentAgentInitials = '--';
        let currentAgentBg = 'bg-[#E0F2FE] text-[#0369A1]';
        let currentCustomerName = 'Customer';

        async function loadMessages(ticketId) {
            const res = await fetch(`/tickets/${ticketId}/messages`);
            const messages = await res.json();
            renderChatMessages(messages);
        }

        async function sendChatMessage(event) {
            event.preventDefault();
            if (!currentTicketKey) return;

            const input = document.getElementById('chat-input');
            const body = input.value.trim();
            if (!body) return;

            await fetch(`/tickets/${currentTicketKey}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ body })
            });

            input.value = '';
            loadMessages(currentTicketKey);

            // Update the status badge locally to match what the backend just set
            const currentStatus = document.getElementById('drawer-status').value;
            if (currentStatus === 'OPEN' || currentStatus === 'PENDING') {
                setTicketStatus(currentTicketKey, 'IN PROGRESS');
            }
        }

        function renderChatMessages(messages) {
            const container = document.getElementById('drawer-chat-messages');
            container.innerHTML = '';

            messages.forEach(msg => {
                const isAdmin = msg.sender_role === 'admin';
                const bubble = document.createElement('div');
                bubble.className = 'flex ' + (isAdmin ? 'justify-end' : 'justify-start');
                bubble.innerHTML = `
                    <div class="max-w-[80%]">
                        <div class="flex items-center gap-1.5 mb-0.5 ${isAdmin ? 'justify-end' : ''}">
                            <span class="text-[9px] font-semibold ${isAdmin ? 'text-[#00875F]' : 'text-slate-600'}">${msg.sender_name}</span>
                        </div>
                        <div class="text-[11px] px-3 py-2 rounded-2xl leading-snug ${isAdmin ? 'bg-[#00CB92] text-white rounded-tr-sm' : 'bg-white border border-slate-200 text-slate-700 rounded-tl-sm'}">
                            ${escapeHtml(msg.body)}
                        </div>
                    </div>`;
                container.appendChild(bubble);
            });

            container.scrollTop = container.scrollHeight;
        }


        function escapeHtml(str) {
            const div = document.createElement('div');
            div.innerText = str;
            return div.innerHTML;
        }

        // ==============================
        // ADMIN EDIT ACTIONS
        // ==============================
        function adminChangeStatus() {
            const status = document.getElementById('drawer-status').value;
            setTicketStatus(currentTicketKey, status);
        }

        function adminChangePriority() {
            const priority = document.getElementById('drawer-priority').value;
            const select = document.getElementById('drawer-priority');
            select.className = 'px-2 py-1 rounded text-[9px] font-bold border tracking-wider outline-none cursor-pointer ' + priorityClasses(priority);

            const row = document.querySelector(`.ticket-row[data-id="${currentTicketKey}"]`);
            if (row) {
                const cell = row.querySelector('td:nth-child(6) span');
                if (cell) {
                    cell.innerText = priority;
                    cell.className = 'px-2 py-0.5 rounded text-[9px] font-bold border tracking-wider ' + priorityClasses(priority);
                }
            }
        }

        function adminChangeCategory() {
            const category = document.getElementById('drawer-category').value;
            const row = document.querySelector(`.ticket-row[data-id="${currentTicketKey}"]`);
            if (row) {
                const cell = row.querySelector('td:nth-child(4) span');
                if (cell) cell.innerHTML = '📁 ' + category;
            }
        }

        function adminChangeAgent() {
            const select = document.getElementById('drawer-agent-select');
            const selectedOption = select.options[select.selectedIndex];
            const agentName = selectedOption.dataset.name;       // tamang pangalan na ngayon
            const initials = selectedOption.dataset.initials || '';

            currentAgentName = agentName;
            currentAgentInitials = initials;
            document.getElementById('drawer-agent-initials').innerText = initials;

            const row = document.querySelector(`.ticket-row[data-id="${currentTicketKey}"]`);
            if (row) {
                const agentCell = row.querySelector('td:nth-child(5) span.font-medium');
                const agentInitialsCell = row.querySelector('td:nth-child(5) .rounded-full');
                if (agentCell) agentCell.innerText = agentName;
                if (agentInitialsCell) agentInitialsCell.innerText = initials;
            }
        }

        function priorityClasses(priority) {
            if (priority === 'CRITICAL') return 'bg-red-50 text-red-600 border-red-200';
            if (priority === 'HIGH') return 'bg-orange-50 text-orange-600 border-orange-200';
            if (priority === 'MEDIUM') return 'bg-amber-50 text-amber-600 border-amber-200';
            return 'bg-slate-100 text-slate-500 border-slate-200';
        }

        function setTicketStatus(ticketId, newStatus) {
            const sSelect = document.getElementById('drawer-status');
            sSelect.value = newStatus;
            sSelect.className = 'px-2 py-1 rounded text-[9px] font-bold tracking-wider outline-none cursor-pointer ' + statusClasses(newStatus);

            const row = document.querySelector(`.ticket-row[data-id="${ticketId}"]`);
            if (row) {
                row.setAttribute('data-status', newStatus);
                const statusCell = row.querySelector('td:nth-child(7) span');
                if (statusCell) {
                    statusCell.innerText = newStatus;
                    statusCell.className = 'px-2 py-0.5 rounded text-[9px] font-bold tracking-wider ' + statusClasses(newStatus);
                }
                filterTickets();
            }
        }

        function statusClasses(status) {
            if (status === 'OPEN') return 'bg-blue-50 text-blue-600 border border-blue-100';
            if (status === 'IN PROGRESS') return 'bg-purple-50 text-purple-600 border border-purple-100';
            if (status === 'PENDING') return 'bg-amber-50 text-amber-600 border border-amber-100';
            if (status === 'RESOLVED') return 'bg-green-50 text-green-600 border border-green-100';
            if (status === 'CANCELLED') return 'bg-red-50 text-red-500 border border-red-100';
            return 'bg-slate-100 text-slate-400 border border-slate-200';
        }

        // Search Bar Filter Function
        let currentStatusFilter = 'ALL';

        function filterTickets() {
            const query = document.getElementById('search-input').value.trim().toLowerCase();
            const rows = document.querySelectorAll('.ticket-row');

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status').trim().toUpperCase();
                const rowSearch = row.getAttribute('data-search') || '';

                const matchesStatus = (currentStatusFilter === 'ALL' || rowStatus === currentStatusFilter);
                const matchesSearch = (query === '' || rowSearch.includes(query));

                row.style.display = (matchesStatus && matchesSearch) ? '' : 'none';
            });
        }

        // Filter Rows Function
        function filterStatus(status, element) {
            currentStatusFilter = status;

            const tabs = document.querySelectorAll('.tab-btn');
            tabs.forEach(tab => {
                tab.classList.remove('bg-[#00CB92]', 'text-white', 'font-semibold');
                tab.classList.add('text-slate-400', 'hover:text-slate-600');
            });
            
            if (element) {
                element.classList.remove('text-slate-400', 'hover:text-slate-600');
                element.classList.add('bg-[#00CB92]', 'text-white', 'font-semibold');
            }

            filterTickets();
        }

        // Ticket Detail Drawer Actions
        function openDrawer(id, name, email, initials, subject, category, priority, status, avatarBg, ticketId, agentName, agentInitials, agentBg, rowEl) {
            // FIX: set the form actions dynamically based on the ticket being opened
            var baseUrl = "{{ url('/customer-service/ticket-management') }}";
            document.getElementById('ticket-edit-form').action = baseUrl + '/' + ticketId;
            document.getElementById('ticket-delete-form').action = baseUrl + '/' + ticketId;

            document.getElementById('drawer-id').innerText = 'Ticket ' + id;
            document.getElementById('drawer-name').innerText = name;
            document.getElementById('drawer-email').innerText = email;
            document.getElementById('drawer-initials').innerText = initials;
            document.getElementById('drawer-subject-desc').value = rowEl ? rowEl.dataset.description : '';
            document.getElementById('drawer-initials').className = `w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs ${avatarBg}`;

            // Keep hidden fields in sync so the update request has customer_name/email too
            document.getElementById('field-customer_name').value = name;
            document.getElementById('field-customer_email').value = email;

            // Subject + description fields
            document.getElementById('drawer-subject-input').value = subject;

            // Category select
            const catSelect = document.getElementById('drawer-category');
            if ([...catSelect.options].some(o => o.value === category)) {
                catSelect.value = category;
            }

            // Priority select
            const pSelect = document.getElementById('drawer-priority');
            pSelect.value = priority;
            pSelect.className = 'px-2 py-1 rounded text-[9px] font-bold border tracking-wider outline-none cursor-pointer ' + priorityClasses(priority);

            // Status select
            const sSelect = document.getElementById('drawer-status');
            sSelect.value = status;
            sSelect.className = 'px-2 py-1 rounded text-[9px] font-bold tracking-wider outline-none cursor-pointer ' + statusClasses(status);

            // Update the Assigned Agent select + remember it for chat replies
            currentAgentName = agentName || 'Unassigned';
            currentAgentInitials = agentInitials || '--';
            currentAgentBg = agentBg || 'bg-[#E0F2FE] text-[#0369A1]';
            currentCustomerName = name;
            const agentSelect = document.getElementById('drawer-agent-select');
            if ([...agentSelect.options].some(o => o.value === currentAgentName)) {
                agentSelect.value = currentAgentName;
            }
            document.getElementById('drawer-agent-initials').innerText = currentAgentInitials;
            document.getElementById('drawer-agent-initials').className = `w-6 h-6 rounded-full flex items-center justify-center font-bold text-[10px] shrink-0 ${currentAgentBg}`;

            // Load chat for this specific ticket
            currentTicketKey = ticketId;
            loadMessages(currentTicketKey);

            document.getElementById('ticket-drawer').classList.remove('translate-x-full');
        }

        function closeDrawer() {
            document.getElementById('ticket-drawer').classList.add('translate-x-full');
        }

        // FIX: implementations for the quick-action buttons in the drawer footer
        function quickSetStatus(status) {
            if (!currentTicketKey) return;
            setTicketStatus(currentTicketKey, status);
            document.getElementById('ticket-edit-form').requestSubmit();
        }

        function deleteCurrentTicket() {
            if (!currentTicketKey) return;
            if (confirm('Sigurado ka bang gusto mong i-delete ang ticket na ito?')) {
                document.getElementById('ticket-delete-form').submit();
            }
        }

        // New Ticket Modal Actions
        function openNewTicketModal() {
            const modal = document.getElementById('new-ticket-modal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild.classList.remove('scale-95');
            }, 20);
        }

        function closeNewTicketModal() {
            const modal = document.getElementById('new-ticket-modal');
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Generic modal open/close helper (used by Edit Profile & Account Settings)
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild.classList.remove('scale-95');
            }, 20);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Edit Profile Modal Actions
        function openEditProfileModal() {
            document.getElementById('profile-menu').classList.add('hidden');
            openModal('edit-profile-modal');
        }
        function closeEditProfileModal() {
            closeModal('edit-profile-modal');
        }

        // Account Settings Modal Actions
        function openAccountSettingsModal() {
            document.getElementById('profile-menu').classList.add('hidden');
            openModal('account-settings-modal');
        }
        function closeAccountSettingsModal() {
            closeModal('account-settings-modal');
        }

        // Profile Dropdown Menu Actions
        function toggleProfileMenu(event) {
            event.stopPropagation();
            document.getElementById('notif-panel').classList.add('hidden');
            const menu = document.getElementById('profile-menu');
            menu.classList.toggle('hidden');
        }

        // Notification Panel Actions
        function openTicketFromNotif(ticketId) {
            // isara muna ang notif panel
            document.getElementById('notif-panel').classList.add('hidden');

            // hanapin ang kaukulang row sa table at i-trigger ang click nito
            // (para magamit ulit ang existing openDrawer logic, kasama na ang description mula sa row)
            const row = document.querySelector(`.ticket-row[data-id="${ticketId}"]`);
            if (row) {
                row.click();
            } else {
                alert('Hindi mahanap ang ticket na ito sa kasalukuyang listahan (baka na-filter).');
            }
        }

        function toggleNotificationPanel(event) {
            event.stopPropagation();
            document.getElementById('profile-menu').classList.add('hidden');
            const panel = document.getElementById('notif-panel');
            panel.classList.toggle('hidden');

            if (!panel.classList.contains('hidden')) {
                markAllNotifsAsRead();
            }
        }

        // Close profile menu / notif panel kapag nag-click sa labas nito
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('profile-menu');
            const btn = document.getElementById('profile-btn');
            if (!menu.classList.contains('hidden') && !menu.contains(event.target) && !btn.contains(event.target)) {
                menu.classList.add('hidden');
            }

            const panel = document.getElementById('notif-panel');
            const notifBtn = document.getElementById('notif-btn');
            if (!panel.classList.contains('hidden') && !panel.contains(event.target) && !notifBtn.contains(event.target)) {
                panel.classList.add('hidden');
            }
        });

        // FIX: Enter to send chat message (previously referenced a non-existent
        // 'chat-reply-form' which would throw an error). Now directly calls
        // sendChatMessage with a synthetic event.
        document.addEventListener('DOMContentLoaded', function() {
            const chatInput = document.getElementById('chat-input');
            if (chatInput) {
                chatInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        sendChatMessage(e);
                    }
                });
            }
        });
    </script>

</body>
</html>
