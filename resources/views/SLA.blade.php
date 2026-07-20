<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Service Level Agreement Performance - Optimized Layout</title>
    <script src="{{ asset('vendor/tailwind.js') }}"></script>
    <script defer src="{{ asset('vendor/alpine.min.js') }}"></script>
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
        @keyframes ring {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(15deg); }
            50% { transform: rotate(-15deg); }
            75% { transform: rotate(10deg); }
        }
        .animate-ring {
            animation: ring 0.5s ease-in-out 3;
        }
    </style>
</head>
<body class="bg-[#1e3a8a] text-slate-900 font-sans h-screen w-screen overflow-hidden p-0 m-0"
      x-data="{ 
        rulesConfigOpen: false,
        ruleEditorOpen: false,
        datePickerOpen: false,
        escalationModalOpen: false,
        ticketModalOpen: false,
        
        unreadNotifications: 2,
        notifyPulse: false,
        notificationsOpen: false,
        profileOpen: false,
        searchQuery: '',
        searchActive: false,
        slaCompliancePct: 92,
        
        notificationsList: [
            { id: 1, text: 'Ticket #4121 has breached SLA.', time: '10 mins ago', read: false },
            { id: 2, text: 'Ticket #6125 is at risk.', time: '25 mins ago', read: false }
        ],

        breachedTickets: [
            { id: '#1201', time: '45 mins' },
            { id: '#4121', time: '50 mins' }
        ],

        atRiskTickets: [
            { id: '#6125', time: '12 mins left', ticketId: 'TK-6125' },
            { id: '#1254', time: '13 mins left', ticketId: 'TK-1254' },
            { id: '#1255', time: '12 mins left', ticketId: 'TK-1255' },
            { id: '#1256', time: '12 mins left', ticketId: 'TK-1256' }
        ],

        escalationLogs: [
            { id: 1, type: 'fail', text: 'Traces a High Priority ticket that missed its mark, explicitly stating Escalated to Supervisor.', time: '10:42 AM', ticketId: '#TK-2847' },
            { id: 2, type: 'success', text: 'Traces a second High Priority ticket breaching and being handed off.', time: '10:42 AM', ticketId: '#TK-2848' },
            { id: 3, type: 'success', text: 'Displays a third High Priority ticket automated shift, marked as Breached.', time: '10:15 AM', ticketId: '#TK-2849' }
        ],

        selectedTicket: { 
            id: '#TK-2847', 
            openedDate: 'Jan 15, 2026',
            status: 'OPEN', 
            tier: 'CRITICAL', 
            customer: { name: 'Benju Guzman', email: 'benju@techcorp.com', initials: 'BG' },
            description: 'User is unable to login to their account. Error appears after entering credentials.',
            timeline: [
                { user: 'Benju Guzman', initials: 'BG', time: '10:23 AM', text: '“I can’t login at all, getting error 401”', type: 'customer' },
                { user: 'RJ Taylee', initials: 'RT', time: '10:35 AM', text: '“I’m looking into this, checking auth service logs”', type: 'agent' }
            ],
            category: 'Authentication',
            slaStatus: '2 hrs remaining'
        },
        
        selectedDate: {{ $calendar->date ?? 16 }},
        selectedMonth: {{ $calendar->month ?? 6 }},
        selectedYear: {{ $calendar->year ?? 2026 }},
        selectedHour: '{{ $calendar->hour ?? "02" }}',
        selectedMinute: '{{ $calendar->minute ?? "45" }}',
        selectedAmpm: '{{ $calendar->ampm ?? "PM" }}',
        
        currentMonthWord: '',
        daysInMonth: [],
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        
        graphPoints: [65, 50, 60, 68, 50, 25, 20, 35],
        
        barHeights: {
            high: [60, 95, 30],
            medium: [60, 90, 30],
            low: [60, 85, 30]
        },

        timelineDays: [
            { label: 'Last 1', b: ['#4211'], r: ['#1254', '#1255'] },
            { label: 'Day 5', b: [], r: ['#6125'] },
            { label: 'Day 9', b: ['#1201', '#4121'], r: [] },
            { label: 'Day 10', b: [], r: ['#7721'] },
            { label: 'Day 13', b: ['#9921'], r: ['#1123'] },
            { label: 'Day 18', b: [], r: [] },
            { label: 'Day 21', b: ['#2211'], r: ['#4412'] },
            { label: 'Last 30', b: [], r: ['#8812'] }
        ],
        hoveredDayIndex: null,
        
        rulesList: {{ json_encode($rules ?? []) }},
        
        newRuleName: '',
        newRuleResponse: '',
        newRuleResolution: '',
        
        init() {
            if(this.rulesList.length === 0) {
                this.rulesList = [{ id: 1, name: 'Standard T1 Response', response: '< 1h', resolution: '< 24h', active: true }];
            }
            this.generateCalendar();
        },

        generateCalendar() {
            this.currentMonthWord = this.monthNames[this.selectedMonth];
            let d = new Date(parseInt(this.selectedYear), parseInt(this.selectedMonth) + 1, 0);
            let totalDays = d.getDate();
            this.daysInMonth = Array.from({length: totalDays}, (_, i) => i + 1);
        },

        changeMonth(dir) {
            this.selectedMonth += dir;
            if (this.selectedMonth < 0) {
                this.selectedMonth = 11;
                this.selectedYear--;
            } else if (this.selectedMonth > 11) {
                this.selectedMonth = 0;
                this.selectedYear++;
            }
            this.generateCalendar();
            this.randomizeGraph();
        },

        randomizeGraph() {
            this.graphPoints = this.graphPoints.map(() => Math.floor(Math.random() * 50) + 20);
            ['high', 'medium', 'low'].forEach(tier => {
                this.barHeights[tier] = [
                    Math.floor(Math.random() * 40) + 30,
                    Math.floor(Math.random() * 40) + 60,
                    Math.floor(Math.random() * 30) + 10
                ];
            });
            this.slaCompliancePct = Math.floor(Math.random() * 20) + 80;

            this.breachedTickets = Array.from({length: Math.floor(Math.random() * 3)}, () => ({
                id: '#' + Math.floor(Math.random() * 9000 + 1000),
                time: Math.floor(Math.random() * 60 + 10) + ' mins'
            }));
            
            this.atRiskTickets = Array.from({length: Math.floor(Math.random() * 4) + 1}, () => ({
                id: '#' + Math.floor(Math.random() * 9000 + 1000),
                time: Math.floor(Math.random() * 25 + 5) + ' mins left',
                ticketId: 'TK-' + Math.floor(Math.random() * 9000 + 1000)
            }));

            this.timelineDays.forEach(day => {
                day.b = Array.from({length: Math.floor(Math.random() * 2)}, () => '#' + Math.floor(Math.random() * 9000 + 1000));
                day.r = Array.from({length: Math.floor(Math.random() * 3)}, () => '#' + Math.floor(Math.random() * 9000 + 1000));
            });
        },

        graphPathLine() { 
            return 'M 0 ' + this.graphPoints[0] + ' Q 15 ' + this.graphPoints[1] + ' 25 ' + this.graphPoints[2] + ' T 45 ' + this.graphPoints[3] + ' T 65 ' + this.graphPoints[4] + ' T 80 ' + this.graphPoints[5] + ' T 90 ' + this.graphPoints[6] + ' T 100 ' + this.graphPoints[7]; 
        },
        
        graphPathFill() { return this.graphPathLine() + ' L 100 100 L 0 100 Z'; },

        addNewRule() {
            if(this.newRuleName.trim() === '') return;
            
            let ruleData = {
                id: Date.now(),
                name: this.newRuleName,
                response: this.newRuleResponse || '< 30m',
                resolution: this.newRuleResolution || '< 4h',
                active: true
            };

            this.rulesList.push(ruleData);
            this.unreadNotifications++;
            this.notifyPulse = true;
            this.notificationsList.unshift({
                id: Date.now(),
                text: 'New SLA Rule Configured: ' + ruleData.name,
                time: 'Just now',
                read: false
            });
            
            setTimeout(() => this.notifyPulse = false, 1500);

            this.newRuleName = '';
            this.newRuleResponse = '';
            this.newRuleResolution = '';
            this.ruleEditorOpen = false;

            try {
                fetch('/rules', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify(ruleData)
                });
            } catch(e) {}
        },

        deleteRule(id) {
            this.rulesList = this.rulesList.filter(rule => rule.id !== id);
            try {
                fetch('/rules/' + id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                });
            } catch(e) {}
        },

        toggleRule(rule) {
            rule.active = !rule.active;
            try {
                fetch('/rules/' + rule.id, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ active: rule.active })
                });
            } catch(e) {}
        },

        openTicketFromLog(log) {
            this.selectedTicket.id = log.ticketId;
            this.escalationModalOpen = true;
            this.ticketModalOpen = true;
        },

        markNotificationsRead() {
            this.unreadNotifications = 0;
            this.notificationsList.forEach(n => n.read = true);
        }
      }">

    <div class="w-screen h-screen flex flex-row relative select-none overflow-hidden">
        
        @include('partials.sidebar')

        <main class="flex-1 flex flex-col overflow-hidden relative bg-[#f4f6f9] rounded-tl-3xl shadow-inner">
            
            <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between shrink-0 z-30 drop-shadow-sm">
                
                <div class="relative w-80" @click.away="searchActive = false">
                    <input type="text" x-model="searchQuery" @focus="searchActive = true" placeholder="Search tickets, customers..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-inner transition-all">
                    <span class="absolute left-3 top-2 text-slate-400 text-sm drop-shadow-sm">🔍</span>
                    
                    <div x-show="searchActive && searchQuery.length > 0" x-transition class="absolute top-full left-0 w-full mt-2 bg-white rounded-xl shadow-2xl border border-slate-200 p-2 z-50">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 px-2">Results for "<span x-text="searchQuery"></span>"</p>
                        <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-blue-50 text-[11px] font-medium text-slate-700 cursor-pointer transition shadow-sm">🔎 Search in Tickets</button>
                        <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-blue-50 text-[11px] font-medium text-slate-700 cursor-pointer transition shadow-sm">👤 Search in Customers</button>
                    </div>
                </div>
                
                <h1 class="text-[#1e3a8a] font-extrabold text-sm tracking-wide drop-shadow-sm">Service Level Agreement Performance</h1>
                
                <div class="flex items-center gap-4">
                    <button class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 hover:-translate-y-0.5 active:scale-95 text-slate-500 text-xs flex items-center justify-center font-bold border border-slate-200/50 cursor-pointer transition-all shadow-md">?</button>
                    
                    <div class="relative cursor-pointer" @click="notificationsOpen = !notificationsOpen; markNotificationsRead()" @click.away="notificationsOpen = false">
                        <div class="relative hover:scale-110 active:scale-95 transition-transform" :class="notifyPulse ? 'animate-ring' : ''">
                            <svg class="w-6 h-6 text-slate-500 hover:text-blue-600 transition-colors drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a3 3 0 11-5.714 0M3.124 7.5A8.969 8.969 0 015.292 3m13.416 0a8.969 8.969 0 012.168 4.5" />
                            </svg>
                            <span x-show="unreadNotifications > 0" x-text="unreadNotifications" x-transition class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 rounded-full border-2 border-white text-white text-[9px] font-bold flex items-center justify-center shadow-lg"></span>
                        </div>
                        
                        <div x-show="notificationsOpen" x-transition class="absolute top-full right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50 cursor-default">
                            <div class="p-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                <span class="text-xs font-black text-slate-800 uppercase tracking-wider drop-shadow-sm">Notifications</span>
                            </div>
                            <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                <template x-for="notif in notificationsList" :key="notif.id">
                                    <div class="p-3 border-b border-slate-50 hover:bg-slate-50 transition cursor-pointer flex flex-col gap-1">
                                        <p class="text-[11px] text-slate-700 font-medium leading-snug" :class="!notif.read ? 'font-bold drop-shadow-sm' : ''" x-text="notif.text"></p>
                                        <span class="text-[9px] text-slate-400" x-text="notif.time"></span>
                                    </div>
                                </template>
                            </div>
                            <button class="w-full py-2.5 text-center text-[10px] font-bold text-blue-600 hover:bg-blue-50 transition cursor-pointer shadow-inner">View All History</button>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="flex items-center gap-2.5 border-l pl-4 border-slate-200 cursor-pointer hover:bg-slate-50 transition-all p-1.5 rounded-xl shadow-sm" @click="profileOpen = !profileOpen" @click.away="profileOpen = false">
                            <div class="w-8 h-8 rounded-full bg-slate-300 overflow-hidden ring-2 ring-blue-100 shadow-md transition-transform duration-200" :class="profileOpen ? 'scale-105' : ''">
                                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" class="w-full h-full object-cover">
                            </div>
                            <div class="flex flex-col items-start leading-none">
                                <span class="text-[11px] font-bold text-slate-800 tracking-wide drop-shadow-sm">Rodrin</span>
                                <span class="text-[9px] text-slate-400 font-medium">Administrator</span>
                            </div>
                            <svg class="w-3 h-3 text-slate-400 transition-transform duration-200 drop-shadow-sm" :class="profileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        
                        <div x-show="profileOpen" x-transition class="absolute top-full right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden z-50">
                            <button class="w-full text-left px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 font-medium transition cursor-pointer flex items-center gap-2 drop-shadow-sm">⚙️ Account Settings</button>
                            <button class="w-full text-left px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 font-medium transition cursor-pointer flex items-center gap-2 drop-shadow-sm">🌙 Dark Mode</button>
                            <div class="border-t border-slate-100"></div>
                            <button class="w-full text-left px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 font-bold transition cursor-pointer flex items-center gap-2 drop-shadow-sm">🚪 Sign Out</button>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 p-6 flex flex-col gap-5 overflow-y-auto custom-scrollbar min-h-0 z-10">
                
                <div class="grid grid-cols-12 gap-5 items-stretch shrink-0 min-h-[280px]">
                    
                    <div class="col-span-4 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-lg flex flex-col justify-between overflow-hidden relative transition-transform hover:-translate-y-0.5">
                        <div>
                            <h3 class="text-xs font-black text-slate-800 tracking-wider uppercase mb-3 drop-shadow-sm">SLA Rules Config</h3>
                            <div class="space-y-2 max-h-[160px] overflow-y-auto custom-scrollbar pr-1">
                                <template x-for="rule in rulesList" :key="rule.id">
                                    <div class="bg-slate-50/60 p-2.5 rounded-xl border border-slate-200/80 flex items-center justify-between hover:bg-slate-50 transition select-none cursor-pointer group shadow-sm hover:shadow" @click="toggleRule(rule)">
                                        <div>
                                            <p class="text-xs font-bold text-slate-700 font-sans group-hover:text-blue-700 transition drop-shadow-sm" x-text="rule.name"></p>
                                            <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Response <span x-html="rule.response"></span>, Resolution <span x-html="rule.resolution"></span></p>
                                        </div>
                                        <div class="flex items-center gap-2 cursor-pointer active:scale-95 transition-transform" @click="toggleRule(rule)">
                                            <span class="text-[8px] font-bold tracking-wider transition-colors duration-200 drop-shadow-sm"
                                                  :class="rule.active ? 'text-[#10b981]' : 'text-slate-400'"
                                                  x-text="rule.active ? 'ON' : 'OFF'"></span>
                                            <button type="button" class="relative inline-flex h-4 w-8 shrink-0 cursor-pointer rounded-full border border-transparent transition-colors duration-200 ease-in-out focus:outline-none shadow-sm"
                                                    :class="rule.active ? 'bg-[#10b981] shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300'">
                                                <span class="pointer-events-none inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"
                                                      :class="rule.active ? 'translate-x-4' : 'translate-x-0'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="flex justify-end mt-3">
                            <button @click="rulesConfigOpen = true; ruleEditorOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold tracking-wider cursor-pointer hover:-translate-y-0.5 active:scale-95 active:translate-y-0 transition-all flex items-center gap-1 border border-slate-200 shadow-md">
                                SLA CONFIG <span class="text-[10px] text-slate-400">❯</span>
                            </button>
                        </div>
                    </div>

                    <div class="col-span-4 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-lg flex flex-col justify-between overflow-hidden transition-transform hover:-translate-y-0.5">
                        <h3 class="text-xs font-black text-slate-800 tracking-wider uppercase mb-3 drop-shadow-sm">Current SLA Compliance Monitor</h3>
                        <div class="grid grid-cols-12 gap-3 items-center bg-slate-50/30 p-2 rounded-xl border border-slate-100 shadow-inner">
                            <div class="col-span-5 relative w-20 h-20 mx-auto rounded-full flex items-center justify-center shrink-0 transition-all duration-700 shadow-[0_5px_20px_rgba(16,185,129,0.4)] ring-4 ring-white" 
                                 :style="`background: conic-gradient(#10b981 0% ${slaCompliancePct}%, #cbd5e1 ${slaCompliancePct}% 100%);`">
                                <div class="w-14 h-14 bg-white rounded-full flex flex-col items-center justify-center shadow-lg">
                                    <span class="text-lg font-extrabold text-emerald-600 drop-shadow-sm" x-text="slaCompliancePct + '%'"></span>
                                </div>
                            </div>
                            
                            <div class="col-span-7 text-[11px] space-y-1 font-medium text-slate-600 max-h-[80px] overflow-y-auto custom-scrollbar">
                                <p class="text-slate-400 font-bold uppercase text-[9px] tracking-wider mb-1 sticky top-0 bg-slate-50/90 backdrop-blur z-10 py-0.5 drop-shadow-sm">At-Risk Countdown</p>
                                <template x-for="ticket in atRiskTickets" :key="ticket.id">
                                    <div class="flex justify-between items-center group cursor-pointer bg-white hover:bg-slate-100 p-1.5 rounded-lg border border-slate-100 shadow-sm transition-all hover:shadow" @click="openTicketFromLog(ticket)">
                                        <span class="flex items-center gap-1.5 text-slate-700">
                                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse ring-2 ring-amber-100 inline-block shrink-0 shadow-[0_0_5px_rgba(251,191,36,0.8)]"></span>
                                            <span class="font-bold drop-shadow-sm" x-text="ticket.id"></span>
                                        </span> 
                                        <span class="text-red-500 font-extrabold group-hover:scale-105 transition-transform drop-shadow-sm" x-text="ticket.time"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <div class="mt-3 text-[11px] space-y-1.5 border-t pt-2 border-slate-100 max-h-[60px] overflow-y-auto custom-scrollbar">
                            <span class="text-slate-400 font-bold text-[9px] uppercase tracking-wider block mb-1 drop-shadow-sm">Tickets Breached Today</span>
                            <template x-for="ticket in breachedTickets" :key="ticket.id">
                                <div class="flex justify-between items-center text-slate-700 cursor-pointer bg-white border border-rose-100 hover:bg-red-50 p-1.5 rounded-lg transition-all shadow-sm hover:shadow group">
                                    <span class="font-bold flex items-center gap-2 drop-shadow-sm">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 ring-2 ring-rose-100 inline-block shrink-0 shadow-[0_0_5px_rgba(244,63,94,0.8)]"></span>
                                        Ticket <span x-text="ticket.id"></span>
                                    </span> 
                                    <span class="text-[10px] text-slate-500 font-medium">Overdue by: <b class="text-red-600 font-extrabold group-hover:scale-105 transition-transform inline-block drop-shadow-sm" x-text="ticket.time"></b></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="col-span-4 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-lg flex flex-col min-h-0 overflow-hidden transition-transform hover:-translate-y-0.5">
                        <button class="mb-3 flex justify-between items-center cursor-pointer hover:opacity-70 active:scale-95 transition-all w-full text-left" @click="escalationModalOpen = true">
                            <h3 class="text-xs font-black text-slate-800 tracking-wider uppercase drop-shadow-sm">Auto Escalation Log</h3>
                            <span class="text-slate-500 text-xs bg-slate-100 px-2 py-0.5 rounded-md font-bold shadow-sm">View All ❯</span>
                        </button>
                        
                        <div class="space-y-2.5 flex-1 overflow-y-auto pr-1 custom-scrollbar">
                            <template x-for="log in escalationLogs" :key="log.id">
                                <div class="p-3 rounded-xl border bg-white hover:bg-slate-50 hover:shadow-md transition-all border-slate-200 shadow-sm flex items-center gap-2.5 text-[11px] group cursor-pointer" @click="openTicketFromLog(log)">
                                    <span class="w-5 h-5 rounded-full text-white flex items-center justify-center shrink-0 font-bold text-[10px] shadow-md" :class="log.type === 'fail' ? 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]' : 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]'" x-text="log.type === 'fail' ? '✕' : '✓'"></span>
                                    <div class="flex-1">
                                        <p class="text-slate-700 font-bold leading-snug line-clamp-2 drop-shadow-sm" x-text="log.text"></p>
                                    </div>
                                    <button @click.stop="openTicketFromLog(log)" class="shrink-0 px-2.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 rounded-lg text-[9px] font-bold uppercase tracking-wider transition-all transform active:scale-95 cursor-pointer shadow-sm">
                                        View
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-5 items-stretch flex-1 min-h-[300px]">
                    <div class="col-span-5 bg-white border border-slate-200 rounded-2xl p-5 shadow-lg flex flex-col justify-between overflow-hidden relative group transition-transform hover:-translate-y-0.5">
                        <div>
                            <h3 class="text-xs font-black text-slate-800 tracking-wide uppercase drop-shadow-sm">Generate SLA Reports</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5 font-bold">Overall SLA Attainment (Last 30 Days)</p>
                        </div>

                        <div class="flex-1 w-full relative mt-4 min-h-0 flex items-end transition-all duration-300">
                            <svg class="w-full h-32 overflow-visible" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <path :d="graphPathFill()" fill="url(#grad)" opacity="0.15" class="transition-all duration-500 ease-in-out"/>
                                <path :d="graphPathLine()" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="transition-all duration-500 ease-in-out drop-shadow-[0_5px_5px_rgba(16,185,129,0.6)]"/>
                                <defs>
                                    <linearGradient id="grad" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#10b981" />
                                        <stop offset="100%" stop-color="#fff" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="absolute left-0 top-0 bottom-0 text-[10px] font-extrabold text-slate-400 flex flex-col justify-between h-full pointer-events-none drop-shadow-sm">
                                <span>100</span><span>80</span><span>60</span><span>40</span><span>20</span><span>0</span>
                            </div>
                        </div>
                        
                        <div class="text-[9px] font-extrabold text-slate-400 flex justify-between px-1 mt-3 border-t pt-2 border-slate-100 relative">
                            <template x-for="(day, index) in timelineDays" :key="index">
                                <div class="relative group/day cursor-pointer" @mouseenter="hoveredDayIndex = index" @mouseleave="hoveredDayIndex = null">
                                    <span class="hover:text-[#10b981] hover:scale-110 transition-all inline-block drop-shadow-sm" x-text="day.label"></span>
                                    
                                    <div x-show="hoveredDayIndex === index" x-transition.opacity.duration.200ms class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 w-[180px] bg-slate-900/95 backdrop-blur-md border border-slate-700 shadow-2xl rounded-xl p-3 z-50 pointer-events-none" style="display: none;">
                                        <h4 class="text-[10px] font-black text-emerald-400 uppercase tracking-wider border-b border-slate-700 pb-1 mb-2 drop-shadow-sm" x-text="day.label + ' Metrics'"></h4>
                                        <div class="flex flex-col gap-1.5">
                                            <div>
                                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Breached</span>
                                                <div class="flex flex-wrap gap-1">
                                                    <template x-if="day.b.length === 0"><span class="text-slate-500 text-[9px] font-bold">None</span></template>
                                                    <template x-for="id in day.b"><span class="text-rose-400 font-bold text-[9px] bg-rose-900/30 px-1 rounded shadow-sm border border-rose-800/50" x-text="id"></span></template>
                                                </div>
                                            </div>
                                            <div class="border-t border-slate-700 pt-1.5">
                                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">At-Risk</span>
                                                <div class="flex flex-wrap gap-1">
                                                    <template x-if="day.r.length === 0"><span class="text-slate-500 text-[9px] font-bold">None</span></template>
                                                    <template x-for="id in day.r"><span class="text-amber-400 font-bold text-[9px] bg-amber-900/30 px-1 rounded shadow-sm border border-amber-800/50" x-text="id"></span></template>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-slate-900/95 border-b border-r border-slate-700 transform rotate-45"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="col-span-7 bg-white border border-slate-200 rounded-2xl p-5 shadow-lg flex flex-col justify-between overflow-hidden relative group transition-transform hover:-translate-y-0.5">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-xs font-black text-slate-800 tracking-wide uppercase drop-shadow-sm">Attainment By Ticket Priority</h3>
                            </div>
                            <button @click="datePickerOpen = true" class="px-3 py-1.5 bg-[#10b981] hover:bg-[#0e9f6e] text-white rounded-lg text-[11px] font-extrabold uppercase tracking-wider flex items-center gap-1 shadow-[0_4px_10px_rgba(16,185,129,0.4)] transition-all hover:-translate-y-0.5 active:translate-y-0 active:scale-95 cursor-pointer relative z-30">
                                DATE PICKER <span class="text-[10px]">🗓</span>
                            </button>
                        </div>
                        
                        <div class="flex-1 flex items-end justify-around relative min-h-0 pt-4 px-6">
                            <div class="absolute left-0 top-0 bottom-0 text-[10px] font-extrabold text-slate-400 flex flex-col justify-between text-right pointer-events-none h-full pb-8 drop-shadow-sm">
                                <span>100</span><span>80</span><span>60</span><span>40</span><span>20</span><span>0</span>
                            </div>
                            
                            <div class="flex flex-col items-center justify-end h-full w-24 pb-6">
                                <div class="flex gap-1.5 items-end h-full w-full justify-center border-b border-slate-200 relative z-10">
                                    <div class="w-3 bg-gradient-to-t from-blue-500 to-blue-300 rounded-t-sm transition-all duration-500 ease-in-out hover:from-blue-400 hover:to-blue-200 cursor-pointer shadow-[0_-3px_8px_rgba(59,130,246,0.4)] border-t border-white/40" :style="`height: ${barHeights.high[0]}%`"></div>
                                    <div class="w-3 bg-gradient-to-t from-blue-700 to-blue-500 rounded-t-sm transition-all duration-500 ease-in-out hover:from-blue-600 hover:to-blue-400 cursor-pointer shadow-[0_-3px_12px_rgba(37,99,235,0.6)] border-t border-white/40" :style="`height: ${barHeights.high[1]}%`"></div>
                                    <div class="w-3 bg-gradient-to-t from-blue-400 to-blue-200 rounded-t-sm transition-all duration-500 ease-in-out hover:from-blue-300 hover:to-blue-100 cursor-pointer shadow-[0_-3px_6px_rgba(96,165,250,0.4)] border-t border-white/40" :style="`height: ${barHeights.high[2]}%`"></div>
                                </div>
                                <span class="text-[11px] font-extrabold text-slate-700 mt-2 tracking-wider drop-shadow-sm">HIGH</span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 mt-1 shadow-[0_0_5px_rgba(52,211,153,0.8)]"></span>
                            </div>

                            <div class="flex flex-col items-center justify-end h-full w-24 pb-6">
                                <div class="flex gap-1.5 items-end h-full w-full justify-center border-b border-slate-200 relative z-10">
                                    <div class="w-3 bg-gradient-to-t from-blue-500 to-blue-300 rounded-t-sm transition-all duration-500 ease-in-out hover:from-blue-400 hover:to-blue-200 cursor-pointer shadow-[0_-3px_8px_rgba(59,130,246,0.4)] border-t border-white/40" :style="`height: ${barHeights.medium[0]}%`"></div>
                                    <div class="w-3 bg-gradient-to-t from-blue-700 to-blue-500 rounded-t-sm transition-all duration-500 ease-in-out hover:from-blue-600 hover:to-blue-400 cursor-pointer shadow-[0_-3px_12px_rgba(37,99,235,0.6)] border-t border-white/40" :style="`height: ${barHeights.medium[1]}%`"></div>
                                    <div class="w-3 bg-gradient-to-t from-blue-400 to-blue-200 rounded-t-sm transition-all duration-500 ease-in-out hover:from-blue-300 hover:to-blue-100 cursor-pointer shadow-[0_-3px_6px_rgba(96,165,250,0.4)] border-t border-white/40" :style="`height: ${barHeights.medium[2]}%`"></div>
                                </div>
                                <span class="text-[11px] font-extrabold text-slate-700 mt-2 tracking-wider drop-shadow-sm">MEDIUM</span>
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 mt-1 shadow-[0_0_5px_rgba(251,191,36,0.8)]"></span>
                            </div>

                            <div class="flex flex-col items-center justify-end h-full w-24 pb-6">
                                <div class="flex gap-1.5 items-end h-full w-full justify-center border-b border-slate-200 relative z-10">
                                    <div class="w-3 bg-gradient-to-t from-blue-500 to-blue-300 rounded-t-sm transition-all duration-500 ease-in-out hover:from-blue-400 hover:to-blue-200 cursor-pointer shadow-[0_-3px_8px_rgba(59,130,246,0.4)] border-t border-white/40" :style="`height: ${barHeights.low[0]}%`"></div>
                                    <div class="w-3 bg-gradient-to-t from-blue-700 to-blue-500 rounded-t-sm transition-all duration-500 ease-in-out hover:from-blue-600 hover:to-blue-400 cursor-pointer shadow-[0_-3px_12px_rgba(37,99,235,0.6)] border-t border-white/40" :style="`height: ${barHeights.low[1]}%`"></div>
                                    <div class="w-3 bg-gradient-to-t from-blue-400 to-blue-200 rounded-t-sm transition-all duration-500 ease-in-out hover:from-blue-300 hover:to-blue-100 cursor-pointer shadow-[0_-3px_6px_rgba(96,165,250,0.4)] border-t border-white/40" :style="`height: ${barHeights.low[2]}%`"></div>
                                </div>
                                <span class="text-[11px] font-extrabold text-slate-700 mt-2 tracking-wider drop-shadow-sm">LOW</span>
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mt-1 shadow-[0_0_5px_rgba(244,63,94,0.8)]"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <footer class="h-10 bg-white border-t border-slate-200 px-6 flex items-center justify-between shrink-0 text-[11px] text-slate-400 z-10 shadow-inner">
                <p class="font-bold drop-shadow-sm">&copy; 2026 Customer Operations Portal. All monitoring metrics active.</p>
                <div class="flex gap-4 font-bold">
                    <a href="#" class="hover:text-slate-600 hover:underline cursor-pointer drop-shadow-sm">Privacy Policy</a>
                    <a href="#" class="hover:text-slate-600 hover:underline cursor-pointer drop-shadow-sm">SLA Documentation</a>
                </div>
            </footer>

            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center z-50"
                 x-show="escalationModalOpen" x-cloak x-transition>
                 
                 <div class="flex items-start justify-center gap-6 w-full max-w-5xl px-4" @click.away="escalationModalOpen = false; ticketModalOpen = false">
                     
                     <div class="bg-slate-50 rounded-[24px] w-[340px] shadow-2xl border border-slate-200 flex flex-col max-h-[85vh] overflow-y-auto custom-scrollbar shrink-0"
                          x-show="ticketModalOpen"
                          x-transition:enter="transition ease-out duration-300"
                          x-transition:enter-start="opacity-0 -translate-x-8 scale-95"
                          x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                          x-transition:leave="transition ease-in duration-200"
                          x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                          x-transition:leave-end="opacity-0 -translate-x-8 scale-95"
                          @click.stop>
                         
                         <div class="pt-5 pb-4 border-b border-slate-200/60 text-center bg-white rounded-t-[24px] shadow-sm">
                             <h3 class="text-[13px] font-black text-slate-800 tracking-wide drop-shadow-sm">Ticket <span x-text="selectedTicket.id"></span></h3>
                             <p class="text-[10px] text-slate-500 font-bold mt-0.5">Opened <span x-text="selectedTicket.openedDate"></span></p>
                         </div>
                         
                         <div class="p-5 flex-1 flex flex-col gap-4">
                             <div class="flex gap-2">
                                 <span class="px-2.5 py-0.5 rounded-full border border-red-500/50 text-red-600 bg-red-50 text-[9px] font-extrabold tracking-wider shadow-sm" x-text="selectedTicket.tier"></span>
                                 <span class="px-2.5 py-0.5 rounded-full border border-blue-500/50 text-blue-600 bg-blue-50 text-[9px] font-extrabold tracking-wider shadow-sm" x-text="selectedTicket.status"></span>
                             </div>

                             <div>
                                 <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-2 drop-shadow-sm">Customer</p>
                                 <div class="flex items-center gap-3 bg-white p-2.5 rounded-xl border border-slate-100 shadow-md cursor-pointer hover:bg-slate-50 transition">
                                     <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-black shadow-inner" x-text="selectedTicket.customer.initials"></div>
                                     <div class="flex-1">
                                         <p class="text-[11px] font-extrabold text-slate-700 drop-shadow-sm" x-text="selectedTicket.customer.name"></p>
                                         <p class="text-[9px] text-slate-500 font-bold" x-text="selectedTicket.customer.email"></p>
                                     </div>
                                 </div>
                             </div>

                             <div>
                                 <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-2 drop-shadow-sm">Description</p>
                                 <p class="text-[10px] font-bold text-slate-600 leading-relaxed bg-white p-3 rounded-xl border border-slate-100 shadow-md" x-text="selectedTicket.description"></p>
                             </div>

                             <div>
                                 <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-2 drop-shadow-sm">Timeline</p>
                                 <div class="space-y-4 relative border-l-2 border-slate-200 ml-3 pl-4 pt-1 pb-1">
                                     <template x-for="item in selectedTicket.timeline">
                                         <div class="relative cursor-pointer hover:scale-[1.02] transition-transform">
                                             <span class="absolute -left-[23px] top-0 w-4 h-4 rounded-full flex items-center justify-center text-[7px] font-black text-white shadow-md ring-2 ring-slate-50" :class="item.type === 'customer' ? 'bg-blue-500' : 'bg-emerald-500'" x-text="item.initials"></span>
                                             <div class="flex justify-between items-baseline mb-1">
                                                 <span class="text-[10px] font-extrabold text-slate-700 drop-shadow-sm" x-text="item.user"></span>
                                                 <span class="text-[8px] text-slate-500 font-extrabold" x-text="item.time"></span>
                                             </div>
                                             <div class="p-2.5 rounded-xl text-[10px] font-bold text-slate-600 border shadow-sm" :class="item.type === 'customer' ? 'bg-white border-slate-200' : 'bg-emerald-50/50 border-emerald-100 text-emerald-800'" x-text="item.text"></div>
                                         </div>
                                     </template>
                                 </div>
                             </div>

                             <div>
                                 <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-2 drop-shadow-sm">Assigned Agent</p>
                                 <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-100 shadow-md cursor-pointer hover:bg-slate-50 transition">
                                     <div class="flex items-center gap-2.5">
                                         <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-black shadow-inner">RT</div>
                                         <div>
                                             <p class="text-[10px] font-extrabold text-slate-700 leading-none mb-1 drop-shadow-sm">RJ Taylee</p>
                                             <p class="text-[8px] text-slate-500 uppercase tracking-wider font-extrabold">Senior Support</p>
                                         </div>
                                     </div>
                                     <div class="text-[10px] font-black text-slate-600 flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded-lg border border-yellow-100 shadow-sm">
                                         <span class="text-yellow-500 drop-shadow-sm">★</span> 4.9
                                     </div>
                                 </div>
                             </div>

                             <div class="border-t border-slate-200/60 pt-4">
                                 <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-2 drop-shadow-sm">Details</p>
                                 <div class="space-y-2 text-[10px] bg-white p-3 rounded-xl border border-slate-100 shadow-md cursor-pointer hover:bg-slate-50 transition">
                                     <div class="flex justify-between items-center"><span class="text-slate-500 font-extrabold">Category</span> <span class="font-black text-slate-700 drop-shadow-sm" x-text="selectedTicket.category"></span></div>
                                     <div class="flex justify-between items-center"><span class="text-slate-500 font-extrabold">Created</span> <span class="font-black text-slate-700 drop-shadow-sm" x-text="selectedTicket.openedDate"></span></div>
                                     <div class="pt-1">
                                         <div class="flex justify-between items-center mb-1.5">
                                             <span class="text-slate-500 font-extrabold">SLA Status</span> 
                                             <span class="text-red-500 font-black drop-shadow-sm" x-text="selectedTicket.slaStatus"></span>
                                         </div>
                                         <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                             <div class="w-1/3 h-full bg-red-500 rounded-full shadow-[0_0_5px_rgba(239,68,68,0.8)]"></div>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <div class="flex flex-col gap-2 pt-2">
                                 <button class="w-full py-2.5 bg-[#10b981] hover:bg-[#0e9f6e] text-white font-black rounded-xl text-[11px] transition-all active:scale-95 cursor-pointer flex justify-center items-center gap-2 shadow-[0_4px_10px_rgba(16,185,129,0.4)]">
                                     <svg class="w-4 h-4 text-white drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                     </svg>
                                     <span class="drop-shadow-sm">Reply</span>
                                 </button>
                                 <button @click="ticketModalOpen = false" class="w-full py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-black rounded-xl text-[11px] transition-all active:scale-95 cursor-pointer flex justify-center items-center gap-2 shadow-md">
                                     <span class="drop-shadow-sm">✕ Close Ticket</span>
                                 </button>
                             </div>
                         </div>
                     </div>

                     <div class="bg-white rounded-2xl w-[500px] shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[85vh] shrink-0" @click.stop>
                        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2 relative shrink-0 shadow-sm z-10">
                            <button @click="escalationModalOpen = false; ticketModalOpen = false" class="text-slate-800 font-black text-sm hover:opacity-70 active:scale-95 transition-all flex items-center gap-1 cursor-pointer drop-shadow-sm">
                                <span>❮</span> Auto Escalation Log
                            </button>
                        </div>
                        <div class="p-5 overflow-y-auto custom-scrollbar flex-1 relative">
                            <div class="absolute left-[33px] top-8 bottom-8 w-[2px] bg-slate-200 z-0"></div>
                            <div class="space-y-6 relative z-10">
                                <template x-for="log in escalationLogs" :key="log.id">
                                    <div class="flex items-start gap-4 relative group cursor-pointer" @click="openTicketFromLog(log)">
                                        <span class="w-7 h-7 rounded-full border-2 border-white text-white flex items-center justify-center shrink-0 font-black text-xs shadow-md z-10" :class="log.type === 'fail' ? 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]' : 'bg-gradient-to-b from-[#86efac] to-[#22c55e] shadow-[0_0_8px_rgba(34,197,94,0.5)]'" x-text="log.type === 'fail' ? '✕' : '✓'"></span>
                                        <div class="flex-1 bg-white p-3 rounded-xl border border-slate-200/60 flex items-center justify-between group-hover:bg-slate-50 group-hover:border-slate-300 transition-all shadow-md">
                                            <div>
                                                <p class="text-xs font-bold text-slate-800 w-[230px] truncate drop-shadow-sm" x-text="log.text"></p>
                                                <p class="text-[10px] text-slate-500 font-bold mt-0.5" x-text="log.time"></p>
                                            </div>
                                            <button @click.stop="openTicketFromLog(log)" class="px-3 py-1.5 text-xs border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all font-black uppercase tracking-wider transform active:scale-95 cursor-pointer shadow-sm">View</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                     </div>
                 </div>
            </div>

            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center z-40"
                 x-show="rulesConfigOpen" x-cloak x-transition>
                <div class="bg-white rounded-2xl w-[480px] shadow-2xl border border-slate-200 p-5 flex flex-col justify-between"
                     @click.away="if (!ruleEditorOpen) rulesConfigOpen = false">
                    <div>
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100 mb-3">
                            <button @click="rulesConfigOpen = false" class="text-slate-800 font-black text-xs flex items-center gap-1.5 hover:opacity-80 uppercase tracking-wider cursor-pointer active:scale-95 transition-all drop-shadow-sm">
                                <span>◀</span> SLA Rules Config
                            </button>
                            <button @click="ruleEditorOpen = !ruleEditorOpen" class="text-white text-xs font-black bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-xl border border-blue-600 transition-all active:scale-95 cursor-pointer shadow-[0_4px_10px_rgba(37,99,235,0.4)] drop-shadow-sm" x-text="ruleEditorOpen ? 'Hide Form' : '+ Add Rule Option'"></button>
                        </div>
                        
                        <div class="mb-3 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs space-y-3 shadow-inner" x-show="ruleEditorOpen" x-transition>
                            <div>
                                <label class="text-[10px] font-extrabold text-slate-500 block mb-1 uppercase tracking-wider drop-shadow-sm">Rule Designation Name</label>
                                <input type="text" x-model="newRuleName" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-sm" placeholder="Premium Tier Response">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[10px] font-extrabold text-slate-500 block mb-1 uppercase tracking-wider drop-shadow-sm">Response Target</label>
                                    <input type="text" x-model="newRuleResponse" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-sm" placeholder="< 30m">
                                </div>
                                <div>
                                    <label class="text-[10px] font-extrabold text-slate-500 block mb-1 uppercase tracking-wider drop-shadow-sm">Resolution Target</label>
                                    <input type="text" x-model="newRuleResolution" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-sm" placeholder="< 4h">
                                </div>
                            </div>
                            <button @click="addNewRule" class="w-full py-2.5 mt-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-lg text-[11px] uppercase tracking-wider transition-all active:scale-95 cursor-pointer shadow-[0_4px_10px_rgba(5,150,105,0.4)] drop-shadow-sm">Save Dynamic Rule</button>
                        </div>

                        <div class="space-y-2 max-h-[200px] overflow-y-auto pr-1 custom-scrollbar">
                            <span class="text-[10px] uppercase font-extrabold text-slate-400 block tracking-wide drop-shadow-sm">Currently Assigned Platform Rules</span>
                            <template x-for="rule in rulesList" :key="rule.id">
                                <div class="p-3 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between select-none transition-all shadow-md">
                                    <div>
                                        <h4 class="text-xs font-black text-slate-800 drop-shadow-sm" x-text="rule.name"></h4>
                                        <p class="text-[11px] text-slate-500 font-bold mt-0.5">Parameters: <span class="text-slate-700 font-black" x-html="rule.response + ' / ' + rule.resolution"></span></p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-2 cursor-pointer active:scale-95 transition-transform" @click="toggleRule(rule)">
                                            <span class="text-[8px] font-extrabold tracking-wider transition-colors duration-200 drop-shadow-sm"
                                                  :class="rule.active ? 'text-[#10b981]' : 'text-slate-400'"
                                                  x-text="rule.active ? 'ON' : 'OFF'"></span>
                                            <button type="button" class="relative inline-flex h-4 w-8 shrink-0 cursor-pointer rounded-full border border-transparent transition-colors duration-200 ease-in-out focus:outline-none shadow-sm"
                                                    :class="rule.active ? 'bg-[#10b981] shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-300'">
                                                <span class="pointer-events-none inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"
                                                      :class="rule.active ? 'translate-x-4' : 'translate-x-0'"></span>
                                            </button>
                                        </div>
                                        <button @click="deleteRule(rule.id)" class="text-red-500 hover:text-white hover:bg-red-500 transition-all p-1.5 bg-white rounded-md border border-slate-200 shadow-sm active:scale-95 cursor-pointer font-black" title="Delete Rule">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <button @click="rulesConfigOpen = false" class="w-full mt-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-black uppercase transition-all active:scale-95 border border-slate-200/60 cursor-pointer shadow-md drop-shadow-sm">
                        Close Matrix Window
                    </button>
                </div>
            </div>

            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md flex items-center justify-center z-50 p-12"
                 x-show="datePickerOpen" x-cloak x-transition>
                <div class="bg-white rounded-3xl w-full max-w-3xl h-[520px] shadow-2xl border border-slate-200 overflow-hidden flex flex-col md:flex-row">
                    
                    <div class="w-full md:w-72 bg-gradient-to-br from-[#10b981] to-[#0e9f6e] p-6 text-white flex flex-col justify-between shadow-inner">
                        <div>
                            <span class="text-xs uppercase tracking-widest font-black opacity-90 block drop-shadow-md">Baseline Configuration</span>
                            <h2 class="text-3xl font-black mt-2 tracking-tight font-sans drop-shadow-md" x-text="currentMonthWord + ' ' + selectedDate"></h2>
                            <p class="text-xl font-bold text-emerald-100 mt-1 drop-shadow-md" x-text="selectedYear"></p>
                            
                            <div class="mt-8 bg-black/20 rounded-xl p-3.5 border border-white/20 shadow-inner">
                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-200 block drop-shadow-sm">Configured Timeline Point</span>
                                <p class="text-2xl font-mono font-black mt-1 flex items-center gap-1 text-white drop-shadow-md">
                                    <span x-text="selectedHour"></span>:<span x-text="selectedMinute"></span> 
                                    <span class="text-sm bg-white/20 px-1.5 py-0.5 rounded font-sans uppercase font-black" x-text="selectedAmpm"></span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <button @click="datePickerOpen = false" class="w-full py-3 bg-white text-emerald-800 font-black rounded-xl shadow-[0_5px_15px_rgba(0,0,0,0.2)] hover:bg-emerald-50 transition-all uppercase text-xs tracking-wider active:scale-95 cursor-pointer drop-shadow-sm">Confirm Point</button>
                        </div>
                    </div>

                    <div class="flex-1 p-6 flex flex-col justify-between bg-slate-50">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 h-full items-start">
                            
                            <div class="md:col-span-7 space-y-3">
                                <div class="flex justify-between items-center bg-white p-2.5 rounded-xl border border-slate-200 shadow-md">
                                    <span class="font-black text-xs text-slate-800 ml-2 drop-shadow-sm" x-text="currentMonthWord + ' ' + selectedYear"></span>
                                    <div class="flex gap-1">
                                        <button @click="changeMonth(-1)" class="w-7 h-7 bg-slate-100 hover:bg-slate-200 text-sm font-black text-slate-800 rounded-lg transition-all active:scale-95 cursor-pointer shadow-sm">‹</button>
                                        <button @click="changeMonth(1)" class="w-7 h-7 bg-slate-100 hover:bg-slate-200 text-sm font-black text-slate-800 rounded-lg transition-all active:scale-95 cursor-pointer shadow-sm">›</button>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-7 text-center text-slate-400 font-extrabold text-[10px] uppercase tracking-wider pb-1 border-b border-slate-200 drop-shadow-sm">
                                    <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
                                </div>
                                <div class="grid grid-cols-7 text-center gap-y-1.5 font-bold text-slate-700">
                                    <template x-for="day in daysInMonth" :key="day">
                                        <div @click="selectedDate = day; randomizeGraph()" 
                                             class="w-8 h-8 flex items-center justify-center mx-auto rounded-lg text-xs font-black transition-all cursor-pointer transform active:scale-95 hover:-translate-y-0.5 hover:shadow-md"
                                             :class="selectedDate === day ? 'bg-[#10b981] text-white shadow-[0_4px_10px_rgba(16,185,129,0.5)] drop-shadow-sm' : 'hover:bg-white border border-transparent hover:border-slate-200 shadow-sm'">
                                            <span x-text="day"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="md:col-span-5 space-y-3 border-l border-slate-200/80 pl-5 h-full flex flex-col justify-between">
                                <div>
                                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-3 font-mono drop-shadow-sm">Select Baseline Time</h4>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-[9px] font-extrabold text-slate-400 block mb-1 uppercase tracking-wider drop-shadow-sm">Hour</label>
                                            <select x-model="selectedHour" class="w-full p-2 bg-white border border-slate-200 rounded-lg font-mono text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/50 font-black shadow-md cursor-pointer hover:bg-slate-50 transition">
                                                <template x-for="h in ['01','02','03','04','05','06','07','08','09','10','11','12']">
                                                    <option :value="h" x-text="h"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-[9px] font-extrabold text-slate-400 block mb-1 uppercase tracking-wider drop-shadow-sm">Minute</label>
                                            <select x-model="selectedMinute" class="w-full p-2 bg-white border border-slate-200 rounded-lg font-mono text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/50 font-black shadow-md cursor-pointer hover:bg-slate-50 transition">
                                                <template x-for="m in ['00','15','30','42','45','50']">
                                                    <option :value="m" x-text="m + ' mins'"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-[9px] font-extrabold text-slate-400 block mb-1 uppercase tracking-wider drop-shadow-sm">Meridiem</label>
                                            <div class="grid grid-cols-2 gap-1 p-1 bg-slate-200/80 rounded-xl shadow-inner">
                                                <button @click="selectedAmpm = 'AM'" class="py-1.5 text-[11px] font-black rounded-lg transition-all active:scale-95 cursor-pointer" :class="selectedAmpm === 'AM' ? 'bg-white text-slate-800 shadow-md drop-shadow-sm' : 'text-slate-500 hover:text-slate-700'">AM</button>
                                                <button @click="selectedAmpm = 'PM'" class="py-1.5 text-[11px] font-black rounded-lg transition-all active:scale-95 cursor-pointer" :class="selectedAmpm === 'PM' ? 'bg-white text-slate-800 shadow-md drop-shadow-sm' : 'text-slate-500 hover:text-slate-700'">PM</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button @click="datePickerOpen = false" class="w-full py-2.5 bg-slate-200 hover:bg-slate-300 font-black text-slate-800 rounded-xl text-xs tracking-wider uppercase transition-all active:scale-95 cursor-pointer shadow-md drop-shadow-sm">
                                    Dismiss
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </main>
    </div>
</body>
</html>