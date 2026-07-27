<aside id="sidebar" class="sidebar-transition w-64 bg-[#1E3A8A] flex flex-col min-h-screen shrink-0 relative">

    {{-- Brand + Collapse Toggle --}}
    <div class="px-6 py-7 flex items-center justify-between">
        <h1 id="sidebarBrandText" class="text-white text-xl font-bold whitespace-nowrap overflow-hidden">Customer Service</h1>
        <button id="sidebarCollapseBtn" type="button"
            class="text-blue-200 hover:text-white hover:bg-[#152a63] rounded-lg p-1.5 transition shrink-0">
            <svg id="sidebarCollapseIcon" class="w-5 h-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75 3 12l3.75 5.25M20.25 6.75 16.5 12l3.75 5.25" />
            </svg>
        </button>
    </div>

    {{-- Nav Items --}}
    <nav class="flex-1 px-4 space-y-2">

        {{-- Dashboard --}}
        <a href="{{ url('/customer-service/dashboard') }}"
        class="sidebar-link flex items-center justify-between px-4 py-3.5 rounded-lg transition
                {{ request()->is('customer-service/dashboard') || request()->is('customer-service/agents*') || request()->is('customer-service/logs*')
                        ? 'bg-white/5 text-white ring-1 ring-green-400/40 shadow-[0_0_10px_rgba(74,222,128,0.15)]'
                        : 'text-blue-200 hover:bg-[#152a63] hover:text-white' }}">
            <div class="flex items-center gap-3.5">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.25 3.75a.75.75 0 0 0-.75-.75C6.101 3 2.25 6.851 2.25 11.25S6.101 19.5 10.5 19.5c4.399 0 8.25-3.851 8.25-8.25a.75.75 0 0 0-.75-.75h-6.75V3.75Z" />
                    <path d="M13.5 3.75a.75.75 0 0 1 .75-.75 8.25 8.25 0 0 1 8.25 8.25.75.75 0 0 1-.75.75h-7.5a.75.75 0 0 1-.75-.75v-7.5Z" />
                </svg>
                <span class="sidebar-label font-medium whitespace-nowrap">Dashboard</span>
            </div>
        </a>

        {{-- Ticket Management --}}
        <a href="{{ url('/customer-service/ticket-management') }}"
        class="sidebar-link flex items-center justify-between px-4 py-3.5 rounded-lg transition
                {{ request()->is('customer-service/ticket-management*')
                        ? 'bg-white/5 text-white ring-1 ring-green-400/40 shadow-[0_0_10px_rgba(74,222,128,0.15)]'
                        : 'text-blue-200 hover:bg-[#152a63] hover:text-white' }}">
            <div class="flex items-center gap-3.5">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
                <span class="sidebar-label font-medium whitespace-nowrap">Ticket Management</span>
            </div>
        </a>

        {{-- Self-Service Portal --}}
        <a href="{{ url('/customer-service/self-service') }}"
        class="sidebar-link flex items-center justify-between px-4 py-3.5 rounded-lg transition
                {{ request()->is('customer-service/self-service*')
                        ? 'bg-white/5 text-white ring-1 ring-green-400/40 shadow-[0_0_10px_rgba(74,222,128,0.15)]'
                        : 'text-blue-200 hover:bg-[#152a63] hover:text-white' }}">
            <div class="flex items-center gap-3.5">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75 16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                </svg>
                <span class="sidebar-label font-medium whitespace-nowrap">Self-Service Portal</span>
            </div>
        </a>

        {{-- Communication History --}}
        <a href="{{ route('communication.index') }}"
        class="sidebar-link flex items-center justify-between px-4 py-3.5 rounded-lg transition
                {{ request()->is('customer-service/communication-history*')
                        ? 'bg-white/5 text-white ring-1 ring-green-400/40 shadow-[0_0_10px_rgba(74,222,128,0.15)]'
                        : 'text-blue-200 hover:bg-[#152a63] hover:text-white' }}">
            <div class="flex items-center gap-3.5">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                </svg>
                <span class="sidebar-label font-medium whitespace-nowrap">Communication History</span>
            </div>
        </a>

        {{-- SLA Tracking --}}
        <a href="{{ url('/customer-service/sla-tracking') }}"
        class="sidebar-link flex items-center justify-between px-4 py-3.5 rounded-lg transition
                {{ request()->is('customer-service/sla-tracking*')
                        ? 'bg-white/5 text-white ring-1 ring-green-400/40 shadow-[0_0_10px_rgba(74,222,128,0.15)]'
                        : 'text-blue-200 hover:bg-[#152a63] hover:text-white' }}">
            <div class="flex items-center gap-3.5">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                <span class="sidebar-label font-medium whitespace-nowrap">SLA Tracking</span>
            </div>
        </a>

    </nav>

    {{-- Support Status Footer --}}
    <div class="p-4">
        <div class="flex items-center gap-3 bg-[#152a63]/60 rounded-lg px-4 py-3.5">
            <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-6a9 9 0 0 1 18 0v6" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3v5ZM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3v5Z" />
                </svg>
            </div>
            <div class="sidebar-label whitespace-nowrap">
                <p class="text-blue-200 text-xs">Support Status</p>
                <p class="text-green-400 font-semibold text-sm">Online</p>
            </div>
        </div>
    </div>

</aside>

<style>
  /* Collapsed state: shrink width and hide labels/submenus */
  #sidebar.collapsed { width: 5.5rem; }
  #sidebar.collapsed .sidebar-label { display: none; }
  #sidebar.collapsed #sidebarCollapseIcon { transform: rotate(180deg); }
  #sidebar.collapsed .sidebar-link { justify-content: center; }
  #sidebar.collapsed nav > div > div > a,
  #sidebar.collapsed nav > div > div { justify-content: center; }
</style>

<script>

    // Collapse / expand sidebar, remembering the choice across page loads
    const sidebarEl = document.getElementById('sidebar');
    const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');

    function applySidebarState(collapsed) {
        sidebarEl.classList.toggle('collapsed', collapsed);
        localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    }

    applySidebarState(localStorage.getItem('sidebarCollapsed') === '1');

    sidebarCollapseBtn.addEventListener('click', () => {
        applySidebarState(!sidebarEl.classList.contains('collapsed'));
    });
</script>