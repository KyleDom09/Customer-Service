<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service - Ticket Management</title>
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
                <span class="absolute left-3 top-2 text-slate-400 text-xs">🔍</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative" id="profileMenuWrapper">
                    <div class="flex items-center gap-3 border-l border-slate-200 pl-4 cursor-pointer" id="profileMenuBtn">
                        <div class="w-8 h-8 rounded-full bg-[#1A2B6D] flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-slate-400">{{ auth()->user()->role ?? 'Ticket Manager' }}</div>
                        </div>
                    </div>
                    <div id="profileMenuDropdown" class="hidden absolute right-0 mt-3 w-48 bg-white border border-slate-200 rounded-xl shadow-lg py-2 z-30">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-xs font-semibold text-slate-700 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-xs text-red-500 hover:bg-red-50">
                                Log out
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
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-100 text-slate-600">
                            @foreach($tickets as $ticket)
                            <tr data-status="{{ $ticket->status }}"
                                data-search="{{ strtolower($ticket->customer_name . ' ' . $ticket->customer_email . ' ' . $ticket->subject . ' TK-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT) . ' ' . $ticket->category) }}"
                                onclick="openDrawer('#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}', {{ $ticket->id }}, '{{ $ticket->name }}', '{{ $ticket->email }}', '{{ $ticket->initials }}', '{{ $ticket->subject }}', '{{ $ticket->category }}', '{{ $ticket->priority }}', '{{ $ticket->status }}', '{{ $ticket->avatar_bg }}')"
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
                                        {{ $ticket->status == 'CLOSED' ? 'bg-slate-100 text-slate-400 border border-slate-200' : '' }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-3.5 text-slate-400 font-medium">{{ $ticket->created }}</td>
                                <td class="px-6 py-3.5 text-right text-slate-400 font-medium">{{ $ticket->updated }}</td>
                                <td class="px-6 py-3.5 text-right" onclick="event.stopPropagation();">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                            onclick="openEditModal({{ $ticket->id }}, '{{ addslashes($ticket->customer_name) }}', '{{ addslashes($ticket->customer_email) }}', '{{ addslashes($ticket->category) }}', '{{ $ticket->agent_id }}', '{{ $ticket->priority }}', '{{ $ticket->status }}', '{{ addslashes($ticket->subject) }}', '{{ addslashes($ticket->description) }}')"
                                            class="text-blue-500 hover:text-blue-700 text-[10px] font-semibold border border-blue-200 hover:bg-blue-50 px-2 py-1 rounded-lg transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Sigurado ka bang gusto mong i-delete ang ticket na ito?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-[10px] font-semibold border border-red-200 hover:bg-red-50 px-2 py-1 rounded-lg transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
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
        <div class="overflow-y-auto flex-1 pr-1">
            <div class="flex justify-between items-start border-b border-slate-100 pb-4">
                <div>
                    <h2 id="drawer-id" class="text-sm font-bold text-slate-900">Ticket #TK-2847</h2>
                    <p class="text-[10px] text-slate-400 mt-0.5">Opened Jan 15, 2026</p>
                </div>
                <button onclick="closeDrawer()" class="text-slate-400 hover:text-slate-600 text-sm font-bold bg-slate-100 w-6 h-6 rounded-full flex items-center justify-center">✕</button>
            </div>

            <div class="flex gap-2 mt-4">
                <span id="drawer-priority" class="px-2 py-0.5 rounded text-[9px] font-bold border tracking-wider bg-red-50 text-red-600 border-red-200">CRITICAL</span>
                <span id="drawer-status" class="px-2 py-0.5 rounded text-[9px] font-bold tracking-wider bg-blue-50 text-blue-600 border border-blue-100">OPEN</span>
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

            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mt-5 mb-1">Description</p>
            <p id="drawer-subject-desc" class="text-xs text-slate-600 leading-relaxed bg-slate-50/50 p-2.5 rounded-lg border border-slate-100">
                User is unable to login to their account. Error appears after entering credentials. Issue started after the recent system update.
            </p>

            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mt-5 mb-2">Timeline</p>
            <div id="drawer-timeline" class="space-y-3 pl-2 border-l-2 border-slate-100 ml-1">
                <div class="relative">
                    <span class="absolute -left-[13px] top-1 w-2 h-2 rounded-full bg-blue-500"></span>
                    <div class="flex justify-between text-[10px] font-semibold text-slate-700">
                        <span>Sarah Johnson</span> <span class="text-slate-400">10:23 AM</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">"I can't login at all, getting error 401"</p>
                </div>
                <div class="relative">
                    <span class="absolute -left-[13px] top-1 w-2 h-2 rounded-full bg-purple-500"></span>
                    <div class="flex justify-between text-[10px] font-semibold text-slate-700">
                        <span>Mike Chen (Agent)</span> <span class="text-slate-400">10:35 AM</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">"I'm looking into this, checking auth service logs"</p>
                </div>
                <div class="relative">
                    <span class="absolute -left-[13px] top-1 w-2 h-2 rounded-full bg-blue-500"></span>
                    <div class="flex justify-between text-[10px] font-semibold text-slate-700">
                        <span>Sarah Johnson</span> <span class="text-slate-400">11:02 AM</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">"Still not working, please help!"</p>
                </div>
            </div>

            <!-- Waiting-for-admin indicator -->
            <div id="drawer-waiting" class="hidden items-center gap-1.5 mt-2 text-[10px] text-slate-400">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                <span>Waiting for admin response...</span>
            </div>

            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mt-5 mb-2">Assigned Agent</p>
            <div class="flex items-center justify-between bg-slate-50 p-2 rounded-lg border border-slate-100 text-xs">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-[#E0F2FE] text-[#0369A1] flex items-center justify-center font-bold text-[10px]">MC</div>
                    <span class="font-semibold text-slate-700">Mike Chen</span>
                </div>
                <span class="text-[10px] font-medium text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">⭐ 4.9</span>
            </div>

            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mt-5 mb-2">Details</p>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-2 text-[11px] mb-4">
                <div class="flex justify-between"><span class="text-slate-400">Category:</span><span id="drawer-category" class="font-semibold text-slate-700">Authentication</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Created:</span><span class="font-semibold text-slate-700">Jan 15, 2026</span></div>
                <div>
                    <div class="flex justify-between text-red-500 font-semibold mb-1"><span>SLA Status:</span><span>2 hrs remaining</span></div>
                    <div class="w-full h-1 bg-slate-200 rounded-full overflow-hidden">
                        <div class="w-[20%] h-full bg-red-500"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reply box (hidden until "Reply" is clicked) -->
        <div id="drawer-reply-box" class="hidden border-t border-slate-100 pt-3 mb-2">
            <div class="flex items-end gap-2">
                <textarea id="drawer-reply-input" rows="2" placeholder="Type your reply..." class="flex-1 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition resize-none"></textarea>
                <button onclick="sendCustomerReply()" class="bg-[#1E3A8A] text-white w-9 h-9 rounded-xl flex items-center justify-center text-xs shrink-0 hover:bg-[#16296b] transition">➤</button>
            </div>
        </div>

        <div class="space-y-2 border-t border-slate-100 pt-4 bg-white">
            <button id="drawer-reply-toggle" onclick="toggleReplyBox()" class="w-full bg-[#00CB92] text-white py-2 rounded-xl text-xs font-semibold hover:bg-[#00B582] transition flex items-center justify-center gap-1.5">
                <span>↩</span> Reply
            </button>
            <button onclick="closeDrawer()" class="w-full border border-slate-200 text-slate-500 py-2 rounded-xl text-xs font-semibold hover:bg-slate-50 transition text-center">
                Close Ticket
            </button>
        </div>
    </div>

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

    <!-- 7B. EDIT TICKET MODAL -->
    <div id="edit-ticket-modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl w-[450px] shadow-2xl border border-slate-100 overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Edit Ticket</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Update the ticket details below.</p>
                </div>
                <button type="button" onclick="closeEditTicketModal()" class="text-slate-400 hover:text-slate-600 font-bold bg-slate-200/60 w-6 h-6 rounded-full flex items-center justify-center text-xs">✕</button>
            </div>

            <form id="edit-ticket-form" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Customer Name</label>
                    <input type="text" id="edit-customer_name" name="customer_name" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Email Address</label>
                    <input type="email" id="edit-customer_email" name="customer_email" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Category</label>
                        <select id="edit-category" name="category" required class="w-full border border-slate-200 bg-white rounded-xl px-2 py-2 text-xs outline-none focus:border-blue-500 transition">
                            <option value="Auth">Auth</option>
                            <option value="Billing">Billing</option>
                            <option value="Technical">Technical</option>
                            <option value="Feature">Feature</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Priority Level</label>
                        <select id="edit-priority" name="priority" required class="w-full border border-slate-200 bg-white rounded-xl px-2 py-2 text-xs outline-none focus:border-blue-500 transition">
                            <option value="LOW">LOW</option>
                            <option value="MEDIUM">MEDIUM</option>
                            <option value="HIGH">HIGH</option>
                            <option value="CRITICAL">CRITICAL</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Assigned Agent</label>
                        <select id="edit-agent_id" name="agent_id" class="w-full border border-slate-200 bg-white rounded-xl px-2 py-2 text-xs outline-none focus:border-blue-500 transition">
                            <option value="">Unassigned</option>
                            @foreach ($agents as $agentOption)
                                <option value="{{ $agentOption->id }}">{{ $agentOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status</label>
                        <select id="edit-status" name="status" required class="w-full border border-slate-200 bg-white rounded-xl px-2 py-2 text-xs outline-none focus:border-blue-500 transition">
                            <option value="OPEN">OPEN</option>
                            <option value="PENDING">PENDING</option>
                            <option value="IN PROGRESS">IN PROGRESS</option>
                            <option value="RESOLVED">RESOLVED</option>
                            <option value="CLOSED">CLOSED</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Subject Issue</label>
                    <input type="text" id="edit-subject" name="subject" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Full Description</label>
                    <textarea id="edit-description" name="description" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition resize-none"></textarea>
                </div>

                <div class="border-t border-slate-100 pt-4 flex gap-2 justify-end text-xs font-semibold">
                    <button type="button" onclick="closeEditTicketModal()" class="border border-slate-200 text-slate-500 px-4 py-2 rounded-xl hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit" class="bg-[#00CB92] text-white px-5 py-2 rounded-xl hover:bg-[#00B582] transition shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Chatbot Widget -->

    <div id="chatbotWidget" class="fixed bottom-6 right-6 z-50 flex flex-col items-end">

      <!-- Chat Panel -->
      <div id="chatPanel" class="hidden mb-4 w-[350px] sm:w-[380px] max-w-[90vw] h-[520px] max-h-[75vh] bg-white rounded-3xl shadow-2xl flex flex-col overflow-hidden border border-slate-100">

        <!-- Header -->
        <div class="bg-[#1E3A8A] px-5 py-4 flex items-center gap-3">
          <div class="relative w-10 h-10 rounded-full bg-white flex items-center justify-center shrink-0 overflow-hidden">
            <img src="/assets/chatbot-avatar.png" alt="Navi Avatar" class="w-full h-full object-cover" onerror="botAvatarFallback(this)">
          </div>
          <div class="flex-1">
            <p class="text-white text-sm font-semibold">Navi — Ticket Assistant</p>
            <p class="text-blue-200 text-xs flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> Online
            </p>
          </div>
          <button id="chatCloseBtn" class="text-blue-200 hover:text-white text-lg leading-none">
            &times;
          </button>
        </div>

        <!-- Messages -->
        <div id="chatMessages" class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-[#F8FAFC]"></div>

        <!-- Quick Options (dynamic) -->
        <div id="chatOptions" class="px-4 pb-2 flex flex-wrap gap-2"></div>

        <!-- Input -->
        <form id="chatForm" class="flex items-center gap-2 px-4 py-3 border-t border-slate-100 bg-white">
          <input id="chatInput" type="text" placeholder="Type your message..." autocomplete="off"
            class="flex-1 bg-[#F8FAFC] border border-[#E5E7EB] rounded-full px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
          <button type="submit" class="w-10 h-10 rounded-full bg-[#10B981] flex items-center justify-center text-white hover:bg-emerald-600 transition-colors shrink-0 text-sm">
            ➤
          </button>
        </form>
      </div>

      <!-- Floating Bubble Button -->
      <button id="chatToggleBtn" class="w-16 h-16 rounded-full bg-white shadow-xl flex items-center justify-center hover:scale-105 transition-transform relative overflow-hidden">
        <span class="absolute top-1 right-1 w-3.5 h-3.5 bg-[#10B981] rounded-full border-2 border-white z-10"></span>
        <img src="/assets/chatbot-avatar.png" alt="Chatbot" class="w-full h-full object-cover" onerror="botAvatarFallback(this)">
      </button>
    </div>

    <!-- 8. APP SCRIPTS -->
    <script>
        // Fallback: if /assets/chatbot-avatar.png is missing, show a built-in robot icon instead of a broken image
        function botAvatarFallback(imgEl) {
          imgEl.onerror = null;
          const wrapper = imgEl.parentElement;
          wrapper.innerHTML = `
            <svg viewBox="0 0 64 64" class="w-full h-full p-1.5">
              <rect x="6" y="16" width="52" height="32" rx="16" fill="#3B82F6"/>
              <rect x="9" y="19" width="46" height="26" rx="13" fill="#0B1220"/>
              <circle cx="24" cy="32" r="5.5" fill="#ffffff"/>
              <circle cx="40" cy="32" r="5.5" fill="#ffffff"/>
            </svg>`;
        }
        // Filter Rows Function
        function filterStatus(status, element) {
            const tabs = document.querySelectorAll('.tab-btn');
            tabs.forEach(tab => {
                tab.classList.remove('bg-[#00CB92]', 'text-white', 'font-semibold');
                tab.classList.add('text-slate-400', 'hover:text-slate-600');
            });
            
            if (element) {
                element.classList.remove('text-slate-400', 'hover:text-slate-600');
                element.classList.add('bg-[#00CB92]', 'text-white', 'font-semibold');
            }

            const rows = document.querySelectorAll('.ticket-row');
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status').trim().toUpperCase();
                if (status === 'ALL' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // ================= Ticket Detail Drawer =================
        let currentTicketId = null;
        let waitingTimeout = null;

        function openDrawer(id, ticketId, name, email, initials, subject, category, priority, status, avatarBg) {
            currentTicketId = ticketId;
            document.getElementById('drawer-id').innerText = 'Ticket ' + id;
            document.getElementById('drawer-name').innerText = name;
            document.getElementById('drawer-email').innerText = email;
            document.getElementById('drawer-initials').innerText = initials;
            document.getElementById('drawer-initials').className = `w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs ${avatarBg}`;
            document.getElementById('drawer-category').innerText = category;

            const pBadge = document.getElementById('drawer-priority');
            pBadge.innerText = priority;
            pBadge.className = 'px-2 py-0.5 rounded text-[9px] font-bold border tracking-wider ';
            if(priority === 'CRITICAL') pBadge.className += 'bg-red-50 text-red-600 border-red-200';
            else if(priority === 'HIGH') pBadge.className += 'bg-orange-50 text-orange-600 border-orange-200';
            else if(priority === 'MEDIUM') pBadge.className += 'bg-amber-50 text-amber-600 border-amber-200';
            else pBadge.className += 'bg-slate-100 text-slate-500 border-slate-200';

            const sBadge = document.getElementById('drawer-status');
            sBadge.innerText = status;
            sBadge.className = 'px-2 py-0.5 rounded text-[9px] font-bold tracking-wider ';
            if(status === 'OPEN') sBadge.className += 'bg-blue-50 text-blue-600 border border-blue-100';
            else if(status === 'IN PROGRESS') sBadge.className += 'bg-purple-50 text-purple-600 border border-purple-100';
            else if(status === 'PENDING') sBadge.className += 'bg-amber-50 text-amber-600 border border-amber-100';
            else if(status === 'RESOLVED') sBadge.className += 'bg-green-50 text-green-600 border border-green-100';
            else sBadge.className += 'bg-slate-100 text-slate-400 border border-slate-200';

            // reset reply UI state on open
            document.getElementById('drawer-reply-box').classList.add('hidden');
            document.getElementById('drawer-waiting').classList.add('hidden');
            document.getElementById('drawer-waiting').classList.remove('flex');
            if (waitingTimeout) clearTimeout(waitingTimeout);

            document.getElementById('ticket-drawer').classList.remove('translate-x-full');
        }

        function closeDrawer() {
            document.getElementById('ticket-drawer').classList.add('translate-x-full');
        }

        function toggleReplyBox() {
            const box = document.getElementById('drawer-reply-box');
            box.classList.toggle('hidden');
            if (!box.classList.contains('hidden')) {
                document.getElementById('drawer-reply-input').focus();
            }
        }

        function formatNowTime() {
            const now = new Date();
            let h = now.getHours();
            const m = now.getMinutes().toString().padStart(2, '0');
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12; if (h === 0) h = 12;
            return `${h}:${m} ${ampm}`;
        }

        function appendTimelineEntry(author, dotColorClass, message) {
            const timeline = document.getElementById('drawer-timeline');
            const entry = document.createElement('div');
            entry.className = 'relative';
            entry.innerHTML = `
                <span class="absolute -left-[13px] top-1 w-2 h-2 rounded-full ${dotColorClass}"></span>
                <div class="flex justify-between text-[10px] font-semibold text-slate-700">
                    <span>${author}</span> <span class="text-slate-400">${formatNowTime()}</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">"${message}"</p>
            `;
            timeline.appendChild(entry);
            timeline.scrollTop = timeline.scrollHeight;
        }

        // Sends the customer's reply, shows an auto-ack entry, then waits for the admin.
        // Optionally persist this to the backend via fetch() to your Laravel route,
        // e.g. POST /tickets/{id}/messages with { body: text }.
        function sendCustomerReply() {
            const input = document.getElementById('drawer-reply-input');
            const text = input.value.trim();
            if (!text) return;

            appendTimelineEntry(document.getElementById('drawer-name').innerText, 'bg-blue-500', text);
            input.value = '';
            document.getElementById('drawer-reply-box').classList.add('hidden');

            const waiting = document.getElementById('drawer-waiting');
            waiting.classList.remove('hidden');
            waiting.classList.add('flex');

            if (waitingTimeout) clearTimeout(waitingTimeout);
            waitingTimeout = setTimeout(() => {
                appendTimelineEntry('System', 'bg-slate-300', "Thanks for your message. An agent will respond shortly.");
            }, 600);

            // TODO (backend): replace the block above with a real API call, e.g.
            // fetch(`/customer-service/ticket-management/${currentTicketId}/messages`, {
            //   method: 'POST',
            //   headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            //   body: JSON.stringify({ body: text })
            // });
            // Then poll or use Laravel Echo/Pusher to detect the admin's real reply and
            // call hideWaitingIndicator() + appendTimelineEntry('Agent Name', 'bg-purple-500', reply) when it arrives.
        }

        function hideWaitingIndicator() {
            const waiting = document.getElementById('drawer-waiting');
            waiting.classList.add('hidden');
            waiting.classList.remove('flex');
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

        // Edit Ticket Modal Actions
        function openEditModal(id, name, email, category, agentId, priority, status, subject, description) {
            document.getElementById('edit-ticket-form').action = '/customer-service/ticket-management/' + id;
            document.getElementById('edit-customer_name').value = name;
            document.getElementById('edit-customer_email').value = email;
            document.getElementById('edit-category').value = category;
            document.getElementById('edit-agent_id').value = agentId;
            document.getElementById('edit-priority').value = priority;
            document.getElementById('edit-status').value = status;
            document.getElementById('edit-subject').value = subject;
            document.getElementById('edit-description').value = description;

            const modal = document.getElementById('edit-ticket-modal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild.classList.remove('scale-95');
            }, 20);
        }

        function closeEditTicketModal() {
            const modal = document.getElementById('edit-ticket-modal');
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // ================= Chatbot Widget (Navi) =================
        const chatPanel = document.getElementById('chatPanel');
        const chatToggleBtn = document.getElementById('chatToggleBtn');
        const chatCloseBtn = document.getElementById('chatCloseBtn');
        const chatMessages = document.getElementById('chatMessages');
        const chatOptions = document.getElementById('chatOptions');
        const chatForm = document.getElementById('chatForm');
        const chatInput = document.getElementById('chatInput');

        let chatStarted = false;

        function addBotMessage(text) {
          const div = document.createElement('div');
          div.className = 'flex justify-start';
          div.innerHTML = `
            <div class="max-w-[80%] bg-white border border-slate-100 rounded-2xl rounded-bl-sm px-4 py-2.5 text-sm text-slate-700 shadow-sm">
              ${text}
            </div>`;
          chatMessages.appendChild(div);
          chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function addUserMessage(text) {
          const div = document.createElement('div');
          div.className = 'flex justify-end';
          div.innerHTML = `
            <div class="max-w-[80%] bg-[#1E3A8A] text-white rounded-2xl rounded-br-sm px-4 py-2.5 text-sm">
              ${text}
            </div>`;
          chatMessages.appendChild(div);
          chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function showTyping() {
          const div = document.createElement('div');
          div.id = 'typingIndicator';
          div.className = 'flex justify-start';
          div.innerHTML = `
            <div class="bg-white border border-slate-100 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:0ms"></span>
              <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:150ms"></span>
              <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay:300ms"></span>
            </div>`;
          chatMessages.appendChild(div);
          chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function hideTyping() {
          const el = document.getElementById('typingIndicator');
          if (el) el.remove();
        }

        function botSay(text, delay = 700) {
          showTyping();
          return new Promise(resolve => {
            setTimeout(() => {
              hideTyping();
              addBotMessage(text);
              resolve();
            }, delay);
          });
        }

        function setOptions(options) {
          chatOptions.innerHTML = '';
          options.forEach(opt => {
            const btn = document.createElement('button');
            btn.textContent = opt.label;
            btn.className = 'px-3 py-2 rounded-full border border-[#1E3A8A]/20 text-[#1E3A8A] text-xs font-medium hover:bg-[#1E3A8A] hover:text-white transition-colors';
            btn.addEventListener('click', () => {
              addUserMessage(opt.label);
              clearOptions();
              setTimeout(() => opt.action(), 350);
            });
            chatOptions.appendChild(btn);
          });
        }

        function clearOptions() {
          chatOptions.innerHTML = '';
        }

        function askAnythingElse() {
          botSay('Is there anything else I can help you with? 😊', 600).then(() => {
            setOptions([
              { label: 'Yes, I have another question', action: showMainOptions },
              { label: 'No, that\'s all, thank you', action: endChat },
            ]);
          });
        }

        function endChat() {
          botSay(
            "You're welcome! If you need anything else, I'll be right here. Have a great day! 😊",
            600
          );
        }

        function showMainOptions() {
          botSay('No worries, just pick what you need below and I\'ll walk you through it:', 500).then(() => {
            setOptions([
              { label: '🎫 What is a ticket?', action: handleWhatIsTicket },
              { label: '📝 How do I create a ticket?', action: handleHowToCreate },
              { label: '📊 Check my ticket status', action: handleTicketStatus },
              { label: '❓ Something else', action: handleGeneralSupport },
            ]);
          });
        }

        function handleWhatIsTicket() {
          botSay(
            "Great question! A <span class='font-semibold'>support ticket</span> is simply a record of your issue or request " +
            "— like a login problem, a payment issue, or a feature request. Once it's created, our team can track it, " +
            "assign the right agent, and update you until it's resolved. Think of it as your direct line to getting help. 🎫"
          ).then(askAnythingElse);
        }

        function handleHowToCreate() {
          botSay(
            "Sure! Here's how to create a new ticket, step by step:"
          , 500).then(() => botSay(
            "1️⃣ Click the green <span class='font-semibold'>\"+ New Ticket\"</span> button at the top right of the page.<br>" +
            "2️⃣ Fill in your <span class='font-semibold'>Customer Name</span> and <span class='font-semibold'>Email Address</span>.<br>" +
            "3️⃣ Choose a <span class='font-semibold'>Category</span> (e.g. Billing, Bug, Feature) and set the <span class='font-semibold'>Priority Level</span>.<br>" +
            "4️⃣ Pick the <span class='font-semibold'>Assigned Agent</span> who will handle it.<br>" +
            "5️⃣ Add a short <span class='font-semibold'>Subject</span> and a <span class='font-semibold'>Full Description</span> explaining the issue.<br>" +
            "6️⃣ Click <span class='font-semibold'>Save</span> — and that's it, your ticket is submitted! ✅"
          , 900)).then(askAnythingElse);
        }

        function handleTicketStatus() {
          botSay(
            "You can check your ticket's progress right from this page — look at the <span class='font-semibold'>Status</span> column. " +
            "Tickets usually move through: <span class='font-semibold'>Open → Pending → In Progress → Resolved</span>. " +
            "You can also click any ticket row to open its full details, or use the tabs at the top to filter by status."
          ).then(askAnythingElse);
        }

        function handleGeneralSupport() {
          botSay(
            "No problem! You can type your question below and I'll do my best to help, or one of our support staff " +
            "can follow up with you directly. 🙂"
          ).then(askAnythingElse);
        }

        function handlePriorityQuestion() {
          botSay(
            "Priority levels help us know how urgent your issue is:<br>" +
            "🔴 <span class='font-semibold'>Critical</span> — major issue, needs immediate attention<br>" +
            "🟠 <span class='font-semibold'>High</span> — important, handled soon<br>" +
            "🟡 <span class='font-semibold'>Medium</span> — normal priority<br>" +
            "⚪ <span class='font-semibold'>Low</span> — minor issue, no rush"
          ).then(askAnythingElse);
        }

        function handleContactQuestion() {
          botSay(
            "You can reach our support team anytime through this dashboard, or use the 'New Ticket' button to submit " +
            "your request directly so an agent can follow up with you."
          ).then(askAnythingElse);
        }

        function handleFreeText(text) {
          const t = text.toLowerCase();

          if (t.includes('what is a ticket') || t.includes('what is ticket') || (t.includes('what') && t.includes('ticket'))) {
            handleWhatIsTicket();
          } else if (t.includes('how') && (t.includes('create') || t.includes('fill') || t.includes('submit') || t.includes('make'))) {
            handleHowToCreate();
          } else if (t.includes('status') || t.includes('progress') || t.includes('track')) {
            handleTicketStatus();
          } else if (t.includes('priority') || t.includes('urgent') || t.includes('critical')) {
            handlePriorityQuestion();
          } else if (t.includes('contact') || t.includes('reach') || t.includes('agent')) {
            handleContactQuestion();
          } else if (t.includes('thank') || t.includes('bye')) {
            endChat();
          } else {
            botSay(
              "Hmm, I might not have the exact answer to that one. 🙂 But here's how I can help — pick an option below, " +
              "or try asking in a different way!"
            ).then(() => {
              setOptions([
                { label: '🎫 What is a ticket?', action: handleWhatIsTicket },
                { label: '📝 How do I create a ticket?', action: handleHowToCreate },
                { label: '📊 Check my ticket status', action: handleTicketStatus },
              ]);
            });
          }
        }

        function openChat() {
          chatPanel.classList.remove('hidden');
          chatToggleBtn.classList.add('hidden');
          if (!chatStarted) {
            chatStarted = true;
            botSay(
              "Hello dear customer! 👋 I'm <span class='font-semibold'>Navi</span>, your friendly ticket assistant. What can I help you with today?",
              600
            ).then(showMainOptions);
          }
        }

        function closeChat() {
          chatPanel.classList.add('hidden');
          chatToggleBtn.classList.remove('hidden');
        }

        chatToggleBtn.addEventListener('click', openChat);
        chatCloseBtn.addEventListener('click', closeChat);

        chatForm.addEventListener('submit', (e) => {
          e.preventDefault();
          const text = chatInput.value.trim();
          if (!text) return;
          addUserMessage(text);
          chatInput.value = '';
          clearOptions();
          handleFreeText(text);
        });

        // ================= Profile Dropdown =================
        const profileBtn = document.getElementById('profileMenuBtn');
        const profileDropdown = document.getElementById('profileMenuDropdown');
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });
    </script>

</body>
</html>
