<header class="bg-white border-b border-gray-200 px-8 py-5 flex items-center justify-between">

    {{-- Page Title --}}
    <div class="flex flex-col gap-1">
        @hasSection('breadcrumb')
            <p class="text-sm text-gray-400">
                <a href="{{ url('/customer-service/dashboard') }}" class="hover:text-navy hover:underline">Dashboard</a>
                <span class="mx-1">&gt;</span>
                <span>@yield('breadcrumb')</span>
            </p>
        @endif

        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-navy">@yield('page-title', 'Customer Service Dashboard')</h1>
        </div>
    </div>

    {{-- Right Side Actions --}}
    <div class="flex items-center gap-6">

        {{-- Notification Bell --}}
        @php
            $notifDotColors = [
                'CRITICAL' => 'bg-red-500',
                'HIGH' => 'bg-orange-400',
                'MEDIUM' => 'bg-yellow-400',
                'LOW' => 'bg-green-500',
                'SUCCESS' => 'bg-green-600',
            ];
            $notifTextColors = [
                'CRITICAL' => 'text-navy',
                'HIGH' => 'text-navy',
                'MEDIUM' => 'text-navy',
                'LOW' => 'text-navy',
                'SUCCESS' => 'text-navy',
            ];
            $navbarNotifs = \App\Models\ActivityLog::whereNull('read_at')->orderByDesc('logged_at')->take(5)->get();
            $unreadCount = \App\Models\ActivityLog::whereNull('read_at')->count();
        @endphp

        <div class="relative">
            <button id="notifBtn" onclick="toggleNotifications()" class="relative w-11 h-11 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-navy transition group">
                <svg class="w-7 h-7 group-hover:scale-105 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                <span id="notifBadge" class="absolute top-1 right-1.5 flex items-center justify-center min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full ring-2 ring-white {{ $unreadCount === 0 ? 'hidden' : '' }}">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            </button>

            {{-- Dropdown --}}
            <div id="notifMenu" class="hidden absolute right-0 mt-3 w-96 bg-white border border-gray-200 rounded-2xl shadow-xl z-50 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-navy to-navy-dark">
                    <div>
                        <h3 class="font-bold text-white text-sm">Notifications</h3>
                        <p class="text-blue-200 text-xs mt-0.5">{{ $unreadCount }} unread</p>
                    </div>
                    <button onclick="markAllRead()" class="text-xs font-semibold text-white bg-white/15 hover:bg-white/25 px-3 py-1.5 rounded-lg transition">
                        Mark all read
                    </button>
                </div>

                <div id="notifList" class="max-h-96 overflow-y-auto divide-y divide-gray-50">

                    @php
                        $notifIcons = [
                            'CRITICAL' => ['bg' => 'bg-red-50', 'text' => 'text-red-500', 'icon' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z'],
                            'HIGH' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-500', 'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z'],
                            'MEDIUM' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-500', 'icon' => 'M12 6v6l4 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                            'LOW' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'icon' => 'M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z'],
                            'SUCCESS' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                        ];
                    @endphp

                    @forelse ($navbarNotifs as $notif)
                        @php $style = $notifIcons[$notif->severity] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-400', 'icon' => 'M12 9v3.75m0 3.75h.008v.008H12v-.008Z']; @endphp
                        <a href="{{ url('/customer-service/logs') }}" class="notif-item group flex gap-3 px-5 py-3.5 hover:bg-gray-50 transition relative">
                            <span class="absolute left-0 top-0 bottom-0 w-1 bg-navy rounded-r"></span>
                            <div class="w-9 h-9 rounded-full {{ $style['bg'] }} {{ $style['text'] }} flex items-center justify-center flex-shrink-0">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $style['icon'] }}" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-navy font-medium leading-snug">{{ $notif->description }}</p>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-xs text-gray-400">{{ $notif->logged_at->diffForHumans() }}</span>
                                    <span class="text-gray-300">&bull;</span>
                                    <span class="text-xs font-medium {{ $style['text'] }}">{{ $notif->target_id }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-10 text-center">
                            <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                            <p class="text-gray-400 text-sm">You're all caught up!</p>
                        </div>
                    @endforelse

                </div>

                <a href="{{ url('/customer-service/logs') }}" class="flex items-center justify-center gap-1 py-3 text-sm font-semibold text-navy hover:bg-gray-50 border-t border-gray-100 transition">
                    View All Notifications
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Admin Profile --}}
        <div class="relative">
            <button id="profileMenuBtn" type="button" class="flex items-center gap-3 hover:bg-gray-50 rounded-xl px-2 py-1.5 transition">
                <div class="text-right">
                    <p class="text-navy font-semibold text-sm leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-gray-400 text-xs leading-tight">{{ auth()->user()->role ?? 'Administrator' }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-navy flex items-center justify-center text-white font-semibold text-sm shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </button>

            <div id="profileMenuDropdown" class="hidden absolute right-0 mt-3 w-56 bg-white border border-gray-200 rounded-2xl shadow-xl z-50 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <p class="text-navy font-semibold text-sm truncate">{{ auth()->user()->name }}</p>
                    <p class="text-gray-400 text-xs truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-5 py-3 text-sm text-red-500 hover:bg-red-50 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H3" />
                        </svg>
                        Log out
                    </button>
                </form>
            </div>
        </div>

    </div>

</header>

<script>

    function toggleNotifications() {
        document.getElementById('notifMenu').classList.toggle('hidden');
    }

    function markAllRead() {
        fetch('/customer-service/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('notifList').innerHTML = '<p class="px-4 py-6 text-center text-gray-400 text-sm">No new notifications.</p>';
                document.getElementById('notifBadge').classList.add('hidden');
                showToast('All notifications marked as read.', 'info');
            }
        })
        .catch(() => {
            showToast('Something went wrong.', 'error');
        });
    }

    document.addEventListener('click', function (event) {
        const notifMenu = document.getElementById('notifMenu');
        const notifBtn = document.getElementById('notifBtn');
        if (notifMenu && notifBtn && !notifMenu.contains(event.target) && !notifBtn.contains(event.target)) {
            notifMenu.classList.add('hidden');
        }

        const profileMenu = document.getElementById('profileMenuDropdown');
        const profileBtn = document.getElementById('profileMenuBtn');
        if (profileMenu && profileBtn && !profileMenu.contains(event.target) && !profileBtn.contains(event.target)) {
            profileMenu.classList.add('hidden');
        }
    });

    document.getElementById('profileMenuBtn')?.addEventListener('click', function (event) {
        event.stopPropagation();
        document.getElementById('profileMenuDropdown').classList.toggle('hidden');
    });
</script>