<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Customer Service Dashboard')</title>

    {{-- Tailwind (CDN, hindi na kailangan ng Vite build) --}}
    <script src="{{ asset('vendor/tailwind.js') }}"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#1E3A8A',
                        'navy-dark': '#152a63',
                    },
                    fontFamily: {
                        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
                    },
                },
            },
        };
    </script>

    {{-- Chart.js --}}
    <script src="{{ asset('vendor/chart.min.js') }}"></script>

    {{-- Export libraries (SheetJS for Excel, jsPDF for PDF) --}}
    <script src="{{ asset('vendor/xlsx.min.js') }}"></script>
    <script src="{{ asset('vendor/jspdf.min.js') }}"></script>
    <script src="{{ asset('vendor/jspdf-autotable.min.js') }}"></script>
</head>
<body class="bg-gray-50 font-sans antialiased h-screen overflow-hidden">

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Navbar --}}
            @include('partials.navbar')

            {{-- Page Content --}}
            <main class="flex-1 p-8 overflow-y-auto">
                @yield('content')
            </main>

        </div>

    </div>
    
    {{-- Toast Notification Container --}}
    <div id="toastContainer" class="fixed bottom-6 right-6 z-[100] flex flex-col gap-3"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');

            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-navy',
            };

            const icons = {
                success: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
                error: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>',
                info: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>',
            };

            const toast = document.createElement('div');
            toast.className = `flex items-center gap-3 ${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg min-w-[280px] max-w-sm transition-all duration-300 opacity-0 translate-y-2`;
            toast.innerHTML = `${icons[type]}<span class="text-sm font-medium flex-1">${message}</span>`;

            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

    @stack('scripts')
</body>
</html>