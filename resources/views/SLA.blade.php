<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Service Level Agreement Performance - Optimized Layout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    </style>
</head>
<body class="bg-[#1e3a8a] text-slate-900 font-sans h-screen w-screen overflow-hidden p-0 m-0"
      x-data="{ 
        rulesConfigOpen: false,
        ruleEditorOpen: false,
        datePickerOpen: false,
        escalationModalOpen: false,
        ticketModalOpen: false,
        
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
        
        selectedDate: {{ $calendar->date ?? 6 }},
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
        
        rulesList: {{ json_encode($rules) }},
        
        newRuleName: '',
        newRuleResponse: '',
        newRuleResolution: '',
        
        init() {
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
        },

        graphPathLine() { 
            return 'M 0 ' + this.graphPoints[0] + ' Q 15 ' + this.graphPoints[1] + ' 25 ' + this.graphPoints[2] + ' T 45 ' + this.graphPoints[3] + ' T 65 ' + this.graphPoints[4] + ' T 80 ' + this.graphPoints[5] + ' T 90 ' + this.graphPoints[6] + ' T 100 ' + this.graphPoints[7]; 
        },
        
        graphPathFill() { return this.graphPathLine() + ' L 100 100 L 0 100 Z'; },

        addNewRule() {
            if(this.newRuleName.trim() === '') return;
            
            let ruleData = {
                name: this.newRuleName,
                response: this.newRuleResponse || '&lt; 30m',
                resolution: this.newRuleResolution || '&lt; 4h',
                active: true
            };

            fetch('/customer-service/sla-tracking/rules', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify(ruleData)
            })
            .then(res => res.json())
            .then(data => {
                this.rulesList.push(data);
                this.newRuleName = '';
                this.newRuleResponse = '';
                this.newRuleResolution = '';
                this.ruleEditorOpen = false;
            });
        },

        deleteRule(id) {
            fetch('/customer-service/sla-tracking/rules/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            }).then(() => {
                this.rulesList = this.rulesList.filter(rule => rule.id !== id);
            });
        },

        toggleRule(rule) {
            rule.active = !rule.active;
            fetch('/customer-service/sla-tracking/rules/' + rule.id, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ active: rule.active })
            });
        },

        saveCalendar() {
            fetch('/customer-service/sla-tracking/calendar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    date: this.selectedDate,
                    month: this.selectedMonth,
                    year: this.selectedYear,
                    hour: this.selectedHour,
                    minute: this.selectedMinute,
                    ampm: this.selectedAmpm
                })
            });
        }
      }">

    <div class="w-screen h-screen flex flex-row relative select-none overflow-hidden">
        
        <aside class="w-64 bg-[#1e3a8a] text-white flex flex-col justify-between shrink-0 relative z-10 shadow-2xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-10 px-1">
                    <span class="font-bold text-lg tracking-wide text-white">Customer Service</span>
                    <span class="text-sm font-bold text-white">❯</span>
                </div>
                <nav class="space-y-3 text-[13px] font-medium">
                    <a href="#" class="flex items-center gap-4 py-2.5 px-3 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition">
                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center gap-4 py-2.5 px-3 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition">
                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        Ticket Management
                    </a>
                    <a href="#" class="flex items-center gap-4 py-2.5 px-3 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition">
                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Self-Service Portal
                    </a>
                    <a href="#" class="flex items-center gap-4 py-2.5 px-3 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition">
                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        Communication History
                    </a>
                    <div class="flex items-center justify-between py-3 px-3 bg-[#185c6a] text-teal-100 font-semibold rounded-xl mt-4 shadow-lg border border-[#217082]">
                        <div class="flex items-center gap-4">
                            <svg class="w-5 h-5 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            SLA Tracking
                        </div>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#10b981] mr-1"></span>
                    </div>
                </nav>
            </div>
            
            <div class="p-3 mx-4 mb-6 bg-[#263e8a] rounded-xl flex items-center gap-3 shadow-md border border-[#304a9e]/50">
                <div class="w-8 h-8 rounded-full bg-[#3a53a5] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div class="text-[11px]">
                    <p class="text-slate-400 font-medium leading-none">Support Status</p>
                    <p class="text-[#10b981] font-bold mt-1">Online</p>
                </div>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden relative bg-[#f4f6f9] rounded-tl-3xl shadow-inner">
            
            <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between shrink-0">
                <div class="relative w-80">
                    <input type="text" placeholder="Search tickets, customers..." class="w-full pl-9 pr-4 py-1.5 bg-slate-50 border border-slate-200 rounded-full text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-inner">
                    <span class="absolute left-3 top-2 text-slate-400 text-xs">🔍</span>
                </div>
                
                <h1 class="text-[#1e3a8a] font-extrabold text-sm tracking-wide">Service Level Agreement Performance</h1>
                
                <div class="flex items-center gap-4">
                    <button class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 text-xs flex items-center justify-center font-bold border border-slate-200/50">?</button>
                    <div class="relative cursor-pointer">
                        <span class="text-lg text-slate-600">🔔</span>
                        <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </div>
                    <div class="flex items-center gap-3 border-l pl-4 border-slate-200">
                        <div class="w-8 h-8 rounded-full bg-slate-300 overflow-hidden ring-2 ring-slate-100">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" class="w-full h-full object-cover">
                        </div>
                        <span class="text-[11px] font-black text-slate-700 tracking-wider">RODRIN</span>
                    </div>
                </div>
            </header>

            <div class="flex-1 p-6 flex flex-col gap-5 overflow-y-auto custom-scrollbar min-h-0">
                
                <div class="grid grid-cols-12 gap-5 items-stretch shrink-0 min-h-[280px]">
                    
                    <div class="col-span-4 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm flex flex-col justify-between overflow-hidden">
                        <div>
                            <h3 class="text-xs font-black text-slate-800 tracking-wider uppercase mb-3">SLA Rules Config</h3>
                            <div class="space-y-2 max-h-[160px] overflow-y-auto custom-scrollbar pr-1">
                                <template x-for="rule in rulesList" :key="rule.id">
                                    <div class="bg-slate-50/60 p-2.5 rounded-xl border border-slate-200/80 flex items-center justify-between hover:bg-slate-50 transition select-none">
                                        <div>
                                            <p class="text-xs font-bold text-slate-700 font-sans" x-text="rule.name"></p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Response <span x-html="rule.response"></span>, Resolution <span x-html="rule.resolution"></span></p>
                                        </div>
                                        <div class="flex flex-col items-center gap-0.5 cursor-pointer" @click="toggleRule(rule)">
                                            <span class="w-7 h-3.5 rounded-full p-0.5 flex items-center transition-colors duration-200"
                                                  :class="rule.active ? 'bg-[#10b981] justify-end' : 'bg-slate-300 justify-start'">
                                                <span class="w-2.5 h-2.5 bg-white rounded-full shadow-sm"></span>
                                            </span>
                                            <span class="text-[8px] font-bold tracking-wider mt-0.5 transition-colors duration-200"
                                                  :class="rule.active ? 'text-[#10b981]' : 'text-slate-400'"
                                                  x-text="rule.active ? 'ACTIVE' : 'INACTIVE'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="flex justify-end mt-3">
                            <button @click="rulesConfigOpen = true; ruleEditorOpen = false" class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold tracking-wider transition flex items-center gap-1 border border-slate-200 shadow-sm">
                                SLA CONFIG <span class="text-[10px] text-slate-400">❯</span>
                            </button>
                        </div>
                    </div>

                    <div class="col-span-4 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm flex flex-col justify-between overflow-hidden">
                        <h3 class="text-xs font-black text-slate-800 tracking-wider uppercase mb-3">Current SLA Compliance Monitor</h3>
                        <div class="grid grid-cols-12 gap-3 items-center bg-slate-50/30 p-2 rounded-xl border border-slate-100">
                            <div class="col-span-5 relative w-20 h-20 mx-auto rounded-full flex items-center justify-center shrink-0" style="background: conic-gradient(#10b981 0% 12%, #3b82f6 12% 32%, #8b5cf6 32% 100%);">
                                <div class="w-14 h-14 bg-white rounded-full flex flex-col items-center justify-center shadow-md">
                                    <span class="text-base font-black text-slate-800">92%</span>
                                </div>
                            </div>
                            <div class="col-span-7 text-[11px] space-y-1 font-medium text-slate-600">
                                <p class="text-slate-400 font-bold uppercase text-[9px] tracking-wider mb-1">At-Risk Countdown</p>
                                <div class="flex justify-between items-center"><span>🟡 Ticket #6125:</span> <span class="text-red-500 font-bold">12 mins left</span></div>
                                <div class="flex justify-between items-center"><span>🟡 Ticket #1254:</span> <span class="text-red-500 font-bold">13 mins left</span></div>
                                <div class="flex justify-between items-center"><span>🟡 Ticket #1254:</span> <span class="text-red-500 font-bold">12 mins left</span></div>
                                <div class="flex justify-between items-center"><span>🟡 Ticket #1254:</span> <span class="text-red-500 font-bold">12 mins left</span></div>
                            </div>
                        </div>
                        
                        <div class="mt-3 text-[11px] space-y-1 border-t pt-2 border-slate-100">
                            <span class="text-slate-400 font-bold text-[9px] uppercase tracking-wider block mb-1">Tickets Breached Today</span>
                            <div class="flex justify-between items-center text-slate-700">
                                <span class="font-medium flex items-center gap-1">🔴 Ticket #1201</span> <span class="text-[10px]">Overdue by: <b class="text-red-500 font-bold">45 mins</b></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-700">
                                <span class="font-medium flex items-center gap-1">🔴 Ticket #4121</span> <span class="text-[10px]">Overdue by: <b class="text-red-500 font-bold">50 mins</b></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-4 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm flex flex-col min-h-0 overflow-hidden">
                        <div class="mb-3 flex justify-between items-center cursor-pointer" @click="escalationModalOpen = true">
                            <h3 class="text-xs font-black text-slate-800 tracking-wider uppercase">Auto Escalation Log</h3>
                            <span class="text-slate-400 text-xs">❯</span>
                        </div>
                        
                        <div class="space-y-2 flex-1 overflow-y-auto pr-1 custom-scrollbar">
                            <div class="p-2.5 rounded-xl border bg-slate-50 border-slate-200/60 flex items-start gap-2.5 text-[11px]">
                                <span class="w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center shrink-0 font-bold text-[10px]">✕</span>
                                <div class="flex-1">
                                    <p class="text-slate-700 font-medium leading-snug">Traces a High Priority ticket that missed its mark, explicitly stating Escalated to Supervisor at 10:42 AM and noting (Resolution Target Breached)</p>
                                </div>
                            </div>
                            <div class="p-2.5 rounded-xl border bg-slate-50 border-slate-200/60 flex items-start gap-2.5 text-[11px]">
                                <span class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 font-bold text-[10px]">✓</span>
                                <div class="flex-1">
                                    <p class="text-slate-700 font-medium leading-snug">Traces a second High Priority ticket breaching at 10:42 AM and being handed off to a supervisor due to a (Resolution Target Breached)</p>
                                </div>
                            </div>
                            <div class="p-2.5 rounded-xl border bg-slate-50 border-slate-200/60 flex items-start gap-2.5 text-[11px]">
                                <span class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 font-bold text-[10px]">✓</span>
                                <div class="flex-1">
                                    <p class="text-slate-700 font-medium leading-snug">Displays a third High Priority ticket automated shift at 10:15 AM, explicitly marked as (Resolution Target Breached)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-5 items-stretch flex-1 min-h-[300px]">
                    <div class="col-span-5 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between overflow-hidden">
                        <div>
                            <h3 class="text-xs font-black text-slate-800 tracking-wide uppercase">Generate SLA Reports</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Overall SLA Attainment (Last 30 Days)</p>
                        </div>
                        <div class="flex-1 w-full relative mt-4 min-h-0 flex items-end transition-all duration-300">
                            <svg class="w-full h-32 overflow-visible" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <path :d="graphPathFill()" fill="url(#grad)" opacity="0.1" class="transition-all duration-500 ease-in-out"/>
                                <path :d="graphPathLine()" fill="none" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="transition-all duration-500 ease-in-out"/>
                                <defs>
                                    <linearGradient id="grad" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#10b981" />
                                        <stop offset="100%" stop-color="#fff" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="absolute left-0 top-0 bottom-0 text-[10px] font-bold text-slate-400 flex flex-col justify-between h-full pointer-events-none">
                                <span>100</span><span>80</span><span>60</span><span>40</span><span>20</span><span>0</span>
                            </div>
                        </div>
                        <div class="text-[9px] font-bold text-slate-400 flex justify-between px-1 mt-3 border-t pt-2 border-slate-100">
                            <span>Last 1</span><span>Day 5</span><span>Day 9</span><span>Day 10</span><span>Day 13</span><span>Day 18</span><span>Day 21</span><span>Last 30</span>
                        </div>
                    </div>

                    <div class="col-span-7 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between overflow-hidden">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-xs font-black text-slate-800 tracking-wide uppercase">Attainment By Ticket Priority</h3>
                            </div>
                            <button @click="datePickerOpen = true" class="px-3 py-1.5 bg-[#10b981] hover:bg-[#0e9f6e] text-white rounded-lg text-[11px] font-bold uppercase tracking-wider flex items-center gap-1 shadow-sm transition">
                                DATE PICKER <span class="text-[10px]">🗓</span>
                            </button>
                        </div>
                        
                        <div class="flex-1 flex items-end justify-around relative min-h-0 pt-4 px-6">
                            <div class="absolute left-0 top-0 bottom-0 text-[10px] font-bold text-slate-400 flex flex-col justify-between text-right pointer-events-none h-full pb-8">
                                <span>100</span><span>80</span><span>60</span><span>40</span><span>20</span><span>0</span>
                            </div>
                            
                            <div class="flex flex-col items-center justify-end h-full w-24 pb-6">
                                <div class="flex gap-1.5 items-end h-full w-full justify-center border-b border-slate-200">
                                    <div class="w-2.5 bg-blue-400/60 rounded-t-sm transition-all duration-500 ease-in-out" :style="`height: ${barHeights.high[0]}%`"></div>
                                    <div class="w-2.5 bg-blue-500 rounded-t-sm transition-all duration-500 ease-in-out" :style="`height: ${barHeights.high[1]}%`"></div>
                                    <div class="w-2.5 bg-blue-300/80 rounded-t-sm transition-all duration-500 ease-in-out" :style="`height: ${barHeights.high[2]}%`"></div>
                                </div>
                                <span class="text-[11px] font-bold text-slate-700 mt-2 tracking-wider">HIGH</span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-50 mt-1"></span>
                            </div>

                            <div class="flex flex-col items-center justify-end h-full w-24 pb-6">
                                <div class="flex gap-1.5 items-end h-full w-full justify-center border-b border-slate-200">
                                    <div class="w-2.5 bg-blue-400/60 rounded-t-sm transition-all duration-500 ease-in-out" :style="`height: ${barHeights.medium[0]}%`"></div>
                                    <div class="w-2.5 bg-blue-500 rounded-t-sm transition-all duration-500 ease-in-out" :style="`height: ${barHeights.medium[1]}%`"></div>
                                    <div class="w-2.5 bg-blue-300/80 rounded-t-sm transition-all duration-500 ease-in-out" :style="`height: ${barHeights.medium[2]}%`"></div>
                                </div>
                                <span class="text-[11px] font-bold text-slate-700 mt-2 tracking-wider">MEDIUM</span>
                                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 mt-1"></span>
                            </div>

                            <div class="flex flex-col items-center justify-end h-full w-24 pb-6">
                                <div class="flex gap-1.5 items-end h-full w-full justify-center border-b border-slate-200">
                                    <div class="w-2.5 bg-blue-400/60 rounded-t-sm transition-all duration-500 ease-in-out" :style="`height: ${barHeights.low[0]}%`"></div>
                                    <div class="w-2.5 bg-blue-500 rounded-t-sm transition-all duration-500 ease-in-out" :style="`height: ${barHeights.low[1]}%`"></div>
                                    <div class="w-2.5 bg-blue-300/80 rounded-t-sm transition-all duration-500 ease-in-out" :style="`height: ${barHeights.low[2]}%`"></div>
                                </div>
                                <span class="text-[11px] font-bold text-slate-700 mt-2 tracking-wider">LOW</span>
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500 mt-1"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <footer class="h-10 bg-white border-t border-slate-200 px-6 flex items-center justify-between shrink-0 text-[11px] text-slate-400 z-10">
                <p>&copy; 2026 Customer Operations Portal. All monitoring metrics active.</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-slate-600">Privacy Policy</a>
                    <a href="#" class="hover:text-slate-600">SLA Documentation</a>
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
                         
                         <div class="pt-5 pb-4 border-b border-slate-200/60 text-center bg-white rounded-t-[24px]">
                             <h3 class="text-[13px] font-black text-slate-800 tracking-wide">Ticket <span x-text="selectedTicket.id"></span></h3>
                             <p class="text-[10px] text-slate-400 mt-0.5">Opened <span x-text="selectedTicket.openedDate"></span></p>
                         </div>
                         
                         <div class="p-5 flex-1 flex flex-col gap-4">
                             <div class="flex gap-2">
                                 <span class="px-2.5 py-0.5 rounded-full border border-red-500/50 text-red-600 bg-red-50 text-[9px] font-bold tracking-wider" x-text="selectedTicket.tier"></span>
                                 <span class="px-2.5 py-0.5 rounded-full border border-blue-500/50 text-blue-600 bg-blue-50 text-[9px] font-bold tracking-wider" x-text="selectedTicket.status"></span>
                             </div>

                             <div>
                                 <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Customer</p>
                                 <div class="flex items-center gap-3 bg-white p-2.5 rounded-xl border border-slate-100 shadow-sm">
                                     <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold" x-text="selectedTicket.customer.initials"></div>
                                     <div class="flex-1">
                                         <p class="text-[11px] font-bold text-slate-700" x-text="selectedTicket.customer.name"></p>
                                         <p class="text-[9px] text-slate-400" x-text="selectedTicket.customer.email"></p>
                                     </div>
                                 </div>
                             </div>

                             <div>
                                 <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Description</p>
                                 <p class="text-[10px] text-slate-600 leading-relaxed bg-white p-3 rounded-xl border border-slate-100 shadow-sm" x-text="selectedTicket.description"></p>
                             </div>

                             <div>
                                 <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Timeline</p>
                                 <div class="space-y-4 relative border-l-2 border-slate-200 ml-3 pl-4 pt-1 pb-1">
                                     <template x-for="item in selectedTicket.timeline">
                                         <div class="relative">
                                             <span class="absolute -left-[23px] top-0 w-4 h-4 rounded-full flex items-center justify-center text-[7px] font-bold text-white shadow-sm ring-2 ring-slate-50" :class="item.type === 'customer' ? 'bg-blue-500' : 'bg-emerald-500'" x-text="item.initials"></span>
                                             <div class="flex justify-between items-baseline mb-1">
                                                 <span class="text-[10px] font-bold text-slate-700" x-text="item.user"></span>
                                                 <span class="text-[8px] text-slate-400 font-medium" x-text="item.time"></span>
                                             </div>
                                             <div class="p-2.5 rounded-xl text-[10px] text-slate-600 border" :class="item.type === 'customer' ? 'bg-white border-slate-200' : 'bg-emerald-50/50 border-emerald-100 text-emerald-800'" x-text="item.text"></div>
                                         </div>
                                     </template>
                                 </div>
                             </div>

                             <div>
                                 <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Assigned Agent</p>
                                 <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-100 shadow-sm">
                                     <div class="flex items-center gap-2.5">
                                         <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-bold shadow-inner">RT</div>
                                         <div>
                                             <p class="text-[10px] font-bold text-slate-700 leading-none mb-1">RJ Taylee</p>
                                             <p class="text-[8px] text-slate-400 uppercase tracking-wider font-semibold">Senior Support</p>
                                         </div>
                                     </div>
                                     <div class="text-[10px] font-bold text-slate-600 flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded-lg border border-yellow-100">
                                         <span class="text-yellow-500">★</span> 4.9
                                     </div>
                                 </div>
                             </div>

                             <div class="border-t border-slate-200/60 pt-4">
                                 <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Details</p>
                                 <div class="space-y-2 text-[10px] bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                                     <div class="flex justify-between items-center"><span class="text-slate-500 font-medium">Category</span> <span class="font-bold text-slate-700" x-text="selectedTicket.category"></span></div>
                                     <div class="flex justify-between items-center"><span class="text-slate-500 font-medium">Created</span> <span class="font-bold text-slate-700" x-text="selectedTicket.openedDate"></span></div>
                                     <div class="pt-1">
                                         <div class="flex justify-between items-center mb-1.5">
                                             <span class="text-slate-500 font-medium">SLA Status</span> 
                                             <span class="text-red-500 font-bold" x-text="selectedTicket.slaStatus"></span>
                                         </div>
                                         <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                             <div class="w-1/3 h-full bg-red-500 rounded-full shadow-inner"></div>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <div class="flex flex-col gap-2 pt-2">
                                 <button class="w-full py-2.5 bg-[#10b981] hover:bg-[#0e9f6e] text-white font-bold rounded-xl text-[11px] transition flex justify-center items-center gap-2 shadow-sm">
                                     <span class="text-[14px] leading-none">↩</span> Reply
                                 </button>
                                 <button @click="ticketModalOpen = false" class="w-full py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold rounded-xl text-[11px] transition flex justify-center items-center gap-2 shadow-sm">
                                     ✕ Close Ticket
                                 </button>
                             </div>
                         </div>
                     </div>

                     <div class="bg-white rounded-2xl w-[500px] shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[85vh] shrink-0" @click.stop>
                        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2 relative shrink-0">
                            <button @click="escalationModalOpen = false; ticketModalOpen = false" class="text-slate-800 font-bold text-sm hover:opacity-70 flex items-center gap-1">
                                <span>❮</span> Auto Escalation Log
                            </button>
                        </div>
                        <div class="p-5 overflow-y-auto custom-scrollbar flex-1 relative">
                            <div class="absolute left-[33px] top-8 bottom-8 w-[2px] bg-slate-200 z-0"></div>
                            <div class="space-y-6 relative z-10">
                                <div class="flex items-start gap-4">
                                    <span class="w-7 h-7 rounded-full bg-red-500 text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-sm relative z-10">✕</span>
                                    <div class="flex-1 bg-slate-50 p-3 rounded-xl border border-slate-200/60 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-semibold text-slate-700">Auto Escalation Log: [Tickets] to read....</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">12:49 AM</p>
                                        </div>
                                        <button @click="ticketModalOpen = true" class="px-3 py-1 text-xs border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition font-medium">View Ticket</button>
                                    </div>
                                </div>
                                
                                <div class="flex items-start gap-4 relative">
                                    <span class="w-7 h-7 rounded-full bg-gradient-to-b from-[#86efac] to-[#22c55e] border-2 border-white text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-sm z-10">✓</span>
                                    <div class="flex-1 p-1 pb-0 pt-0">
                                        <div>
                                            <p class="text-xs font-semibold text-slate-700">Auto Escalation Log: [Tickets] to read....</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">12:06 PM</p>
                                        </div>
                                        <div class="flex items-center gap-3 mt-3 ml-2">
                                            <svg class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            <div>
                                                <p class="text-[10px] font-bold text-slate-700 leading-none">Agent Assigned</p>
                                                <p class="text-[9px] text-slate-500 font-medium mt-0.5">Assigned to: Guzman</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 relative">
                                    <span class="w-7 h-7 rounded-full bg-gradient-to-b from-[#86efac] to-[#22c55e] border-2 border-white text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-sm z-10">✓</span>
                                    <div class="flex-1 p-1 pb-0 pt-0">
                                        <div>
                                            <p class="text-xs font-semibold text-slate-700">Auto Escalation Log: [Tickets] to read....</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">12:03 PM</p>
                                        </div>
                                        <div class="flex items-center gap-3 mt-3 ml-2">
                                            <svg class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            <div>
                                                <p class="text-[10px] font-bold text-slate-700 leading-none">Agent Assigned</p>
                                                <p class="text-[9px] text-slate-500 font-medium mt-0.5">Assigned to: Taylee</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                            <button @click="rulesConfigOpen = false" class="text-slate-800 font-black text-xs flex items-center gap-1.5 hover:opacity-80 uppercase tracking-wider">
                                <span>◀</span> SLA Rules Config
                            </button>
                            <button @click="ruleEditorOpen = !ruleEditorOpen" class="text-white text-xs font-bold bg-blue-600 hover:bg-blue-700 px-2.5 py-1.5 rounded-xl border border-blue-600 transition shadow-sm" x-text="ruleEditorOpen ? 'Hide Form' : '+ Add Rule Option'"></button>
                        </div>
                        
                        <div class="mb-3 bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-2.5" x-show="ruleEditorOpen" x-transition>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 block mb-0.5">Rule Designation Name</label>
                                <input type="text" x-model="newRuleName" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none" placeholder="Premium Tier Response">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 block mb-0.5">Response Target</label>
                                    <input type="text" x-model="newRuleResponse" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none" placeholder="&lt; 30m">
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 block mb-0.5">Resolution Target</label>
                                    <input type="text" x-model="newRuleResolution" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none" placeholder="&lt; 4h">
                                </div>
                            </div>
                            <button @click="addNewRule" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[11px] uppercase tracking-wider transition">Save Dynamic Rule</button>
                        </div>

                        <div class="space-y-2 max-h-[200px] overflow-y-auto pr-1 custom-scrollbar">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wide">Currently Assigned Platform Rules</span>
                            <template x-for="rule in rulesList" :key="rule.id">
                                <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-200 flex items-center justify-between select-none">
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800" x-text="rule.name"></h4>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Parameters: <span class="text-slate-600 font-semibold" x-html="rule.response + ' / ' + rule.resolution"></span></p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col items-center gap-0.5 cursor-pointer" @click="toggleRule(rule)">
                                            <span class="w-7 h-4 rounded-full p-0.5 flex items-center transition-colors duration-200"
                                                  :class="rule.active ? 'bg-[#10b981] justify-end' : 'bg-slate-300 justify-start'">
                                                <span class="w-3 h-3 bg-white rounded-full shadow"></span>
                                            </span>
                                            <span class="text-[8px] font-bold tracking-wider mt-0.5 transition-colors duration-200"
                                                  :class="rule.active ? 'text-[#10b981]' : 'text-slate-400'"
                                                  x-text="rule.active ? 'ACTIVE' : 'INACTIVE'"></span>
                                        </div>
                                        <button @click="deleteRule(rule.id)" class="text-red-400 hover:text-red-500 transition p-1 bg-white rounded-md border border-slate-200 shadow-sm" title="Delete Rule">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <button @click="rulesConfigOpen = false" class="w-full mt-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold uppercase transition border border-slate-200/60">
                        Close Matrix Window
                    </button>
                </div>
            </div>

            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md flex items-center justify-center z-50 p-12"
                 x-show="datePickerOpen" x-cloak x-transition>
                <div class="bg-white rounded-3xl w-full max-w-3xl h-[520px] shadow-2xl border border-slate-200 overflow-hidden flex flex-col md:flex-row">
                    
                    <div class="w-full md:w-72 bg-gradient-to-br from-[#10b981] to-[#0e9f6e] p-6 text-white flex flex-col justify-between">
                        <div>
                            <span class="text-xs uppercase tracking-widest font-black opacity-75 block">Baseline Configuration</span>
                            <h2 class="text-3xl font-extrabold mt-2 tracking-tight font-sans" x-text="currentMonthWord + ' ' + selectedDate"></h2>
                            <p class="text-xl font-light text-emerald-100 mt-1" x-text="selectedYear"></p>
                            
                            <div class="mt-8 bg-black/10 rounded-xl p-3.5 border border-white/10 shadow-inner">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-200 block">Configured Timeline Point</span>
                                <p class="text-2xl font-mono font-bold mt-1 flex items-center gap-1 text-white">
                                    <span x-text="selectedHour"></span>:<span x-text="selectedMinute"></span> 
                                    <span class="text-sm bg-white/20 px-1.5 py-0.5 rounded font-sans uppercase" x-text="selectedAmpm"></span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <button @click="saveCalendar(); datePickerOpen = false; randomizeGraph()" class="w-full py-2.5 bg-white text-emerald-800 font-black rounded-xl shadow hover:bg-emerald-50 transition uppercase text-xs tracking-wider">Confirm Point</button>
                        </div>
                    </div>

                    <div class="flex-1 p-6 flex flex-col justify-between bg-slate-50">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 h-full items-start">
                            
                            <div class="md:col-span-7 space-y-3">
                                <div class="flex justify-between items-center bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                                    <span class="font-black text-xs text-slate-800" x-text="currentMonthWord + ' ' + selectedYear"></span>
                                    <div class="flex gap-1">
                                        <button @click="changeMonth(-1)" class="w-6 h-6 bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 rounded transition">‹</button>
                                        <button @click="changeMonth(1)" class="w-6 h-6 bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 rounded transition">›</button>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-7 text-center text-slate-400 font-bold text-[10px] uppercase tracking-wider pb-1 border-b">
                                    <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
                                </div>
                                <div class="grid grid-cols-7 text-center gap-y-1.5 font-bold text-slate-700">
                                    <template x-for="day in daysInMonth" :key="day">
                                        <div @click="selectedDate = day; randomizeGraph()" 
                                             class="w-7 h-7 flex items-center justify-center mx-auto rounded-lg text-xs transition cursor-pointer"
                                             :class="selectedDate === day ? 'bg-[#10b981] text-white shadow' : 'hover:bg-white border border-transparent hover:border-slate-200'">
                                            <span x-text="day"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="md:col-span-5 space-y-3 border-l border-slate-200/80 pl-4 h-full flex flex-col justify-between">
                                <div>
                                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-2 font-mono">Select Baseline Time</h4>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <label class="text-[9px] font-bold text-slate-400 block mb-1 uppercase">Hour</label>
                                            <select x-model="selectedHour" class="w-full p-2 bg-white border border-slate-200 rounded-lg font-mono text-xs focus:outline-none font-bold">
                                                <template x-for="h in ['01','02','03','04','05','06','07','08','09','10','11','12']">
                                                    <option :value="h" x-text="h"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-[9px] font-bold text-slate-400 block mb-1 uppercase">Minute</label>
                                            <select x-model="selectedMinute" class="w-full p-2 bg-white border border-slate-200 rounded-lg font-mono text-xs focus:outline-none font-bold">
                                                <template x-for="m in ['00','15','30','42','45','50']">
                                                    <option :value="m" x-text="m + ' mins'"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-[9px] font-bold text-slate-400 block mb-1 uppercase">Meridiem</label>
                                            <div class="grid grid-cols-2 gap-1 p-0.5 bg-slate-200/60 rounded-lg">
                                                <button @click="selectedAmpm = 'AM'" class="py-1 text-[11px] font-bold rounded transition" :class="selectedAmpm === 'AM' ? 'bg-white text-slate-800 shadow' : 'text-slate-500'">AM</button>
                                                <button @click="selectedAmpm = 'PM'" class="py-1 text-[11px] font-bold rounded transition" :class="selectedAmpm === 'PM' ? 'bg-white text-slate-800 shadow' : 'text-slate-500'">PM</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button @click="datePickerOpen = false" class="w-full py-2 bg-slate-200 hover:bg-slate-300 font-bold text-slate-700 rounded-xl text-xs tracking-wider uppercase transition">
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