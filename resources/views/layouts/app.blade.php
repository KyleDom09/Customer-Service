<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Customer Service Dashboard')</title>

    {{-- Tailwind --}}
    @vite('resources/css/app.css')

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    {{-- Export libraries (SheetJS for Excel, jsPDF for PDF) --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Navbar --}}
            @include('partials.navbar')

            {{-- Page Content --}}
            <main class="flex-1 p-8">
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

            // Trigger fade-in
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
            });

            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

    @stack('scripts')
</body>
</html>