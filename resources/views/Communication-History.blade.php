<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Customer Service Dashboard</title>
<script src="{{ asset('vendor/tailwind.js') }}"></script>
<script>
  tailwind.config = {
    darkMode: 'class',
    theme: {
      extend: {
        colors: {
          brand: '#1E3A8A',
          accent: '#10B981',
        }
      }
    }
  };
</script>
<script src="{{ asset('vendor/lucide.min.js') }}"></script>
<script src="{{ asset('vendor/xlsx.min.js') }}"></script>
<script src="{{ asset('vendor/jspdf.min.js') }}"></script>
<script src="{{ asset('vendor/jspdf-autotable.min.js') }}"></script>
<style>
  body { font-family: 'Segoe UI', 'Poppins', sans-serif; }
  ::-webkit-scrollbar { height: 6px; width: 6px; }
  ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
  .dark ::-webkit-scrollbar-thumb { background: #475569; }
  .sidebar-transition { transition: transform 0.3s ease-in-out, width 0.2s ease-in-out; }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .stat-card-fade {
    animation: fadeInUp 0.4s ease-out both;
  }
  .stat-card-fade:nth-child(1) { animation-delay: 0s; }
  .stat-card-fade:nth-child(2) { animation-delay: 0.05s; }
  .stat-card-fade:nth-child(3) { animation-delay: 0.1s; }
  .stat-card-fade:nth-child(4) { animation-delay: 0.15s; }

  .table-row-fx { position: relative; }
  .table-row-fx:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.06);
  }
  .dark .table-row-fx:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
  }
</style>
</head>
<body class="bg-[#F8FAFC] dark:bg-[#121212] text-slate-800 dark:text-slate-100 transition-colors h-screen overflow-hidden">

<!-- Mobile overlay -->
<div id="overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

<div class="flex h-screen overflow-hidden">

  <!-- Sidebar -->
  <div class="shrink-0">
    @include('partials.sidebar')
  </div>

  <!-- Main -->
  <div class="flex-1 min-w-0 flex flex-col overflow-hidden">

    <!-- Top Navbar -->
    <header class="h-20 bg-white dark:bg-[#1e1e1e] flex items-center justify-between px-4 sm:px-8 sticky top-0 z-20 border-b border-slate-100 dark:border-slate-700 transition-colors">
      <button id="menuBtn" class="lg:hidden text-slate-600 dark:text-slate-300">
        <i data-lucide="menu" class="w-6 h-6"></i>
      </button>

      <div class="hidden md:flex flex-1 justify-center px-4">
        <div class="relative w-full max-w-md">
          <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
          <input type="text" placeholder="Search..." class="w-full bg-[#F8FAFC] dark:bg-[#121212] border border-[#E5E7EB] dark:border-slate-700 rounded-full py-2.5 pl-10 pr-4 text-sm placeholder-slate-400 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
        </div>
      </div>

      <div class="flex items-center gap-4 sm:gap-5">
        <button id="themeToggleBtn" type="button" class="relative text-slate-500 dark:text-slate-300 hover:text-slate-700 dark:hover:text-white">
          <i data-lucide="sun" id="themeIconSun" class="w-5 h-5 hidden"></i>
          <i data-lucide="moon" id="themeIconMoon" class="w-5 h-5"></i>
        </button>
        <button class="relative text-slate-500 dark:text-slate-300 hover:text-slate-700 dark:hover:text-white">
          <i data-lucide="bell" class="w-5 h-5"></i>
          <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-[#10B981] rounded-full"></span>
        </button>
        <div class="relative" id="profileMenuWrapper">
          <div class="flex items-center gap-2 cursor-pointer" id="profileMenuBtn">
            @if (auth()->user()->avatar ?? false)
              <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover">
            @else
              <div class="w-9 h-9 rounded-full bg-[#1E3A8A] text-white flex items-center justify-center font-semibold text-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
              </div>
            @endif
            <span class="hidden sm:block text-sm font-semibold text-slate-700 dark:text-slate-100">
              {{ auth()->user()->name }}
            </span>
            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 hidden sm:block"></i>
          </div>

          <!-- Dropdown -->
          <div id="profileMenuDropdown" class="hidden absolute right-0 mt-3 w-48 bg-white dark:bg-[#1e1e1e] border border-slate-100 dark:border-slate-700 rounded-xl shadow-lg py-2 z-30">
            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700">
              <p class="text-sm font-semibold text-slate-700 dark:text-slate-100 truncate">{{ auth()->user()->name }}</p>
              <p class="text-xs text-slate-400 dark:text-slate-500 truncate">{{ auth()->user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 flex items-center gap-2">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Log out
              </button>
            </form>
          </div>
        </div>
      </div>
    </header>

    <main class="flex-1 p-4 sm:p-8 overflow-y-auto">

      <!-- Page Header -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-2">
        <div>
          <h2 class="text-2xl sm:text-3xl font-bold text-[#1E3A8A] dark:text-blue-300 leading-tight">
            Communication<br>
            History
          </h2>
          <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Manage all customer interactions in one place.</p>
        </div>
        <div class="flex items-center gap-3">
          <button id="exportBtnHeader" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-[#CBD5E1] dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            <i data-lucide="download" class="w-4 h-4"></i>
            Export
          </button>
          <button id="newCommBtn" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors shadow-sm shadow-emerald-200">
            <i data-lucide="plus" class="w-4 h-4"></i>
            New Communication
          </button>
        </div>
      </div>

      <!-- Live Date & Time -->
      <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500 mb-6">
        <i data-lucide="calendar-clock" class="w-4 h-4"></i>
        <span id="liveDateTime">Loading date &amp; time...</span>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        <!-- Card 1: Total Communications -->
        <div class="stat-card-fade bg-white dark:bg-[#1e1e1e] rounded-2xl shadow-sm p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
          <div class="flex items-start justify-between">
            <div class="w-11 h-11 rounded-xl bg-[#1E3A8A]/10 flex items-center justify-center shrink-0">
              <i data-lucide="layers" class="w-5 h-5 text-[#1E3A8A] dark:text-blue-300"></i>
            </div>
            <span id="statTotalTrend" class="flex items-center gap-1 text-xs font-semibold text-[#10B981] bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-full">
              <i data-lucide="arrow-up-right" class="w-3 h-3"></i>0%
            </span>
          </div>
          <p id="statTotalLogs" class="text-3xl font-bold text-[#1E3A8A] dark:text-blue-300 mt-4">0</p>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Total Communications</p>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">All communication records</p>
        </div>

        <!-- Card 2: Pending Responses -->
        <button id="statPendingCard" type="button" class="stat-card-fade text-left bg-white dark:bg-[#1e1e1e] rounded-2xl shadow-sm p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 cursor-pointer">
          <div class="flex items-start justify-between">
            <div class="w-11 h-11 rounded-xl bg-[#F59E0B]/10 flex items-center justify-center shrink-0">
              <i data-lucide="clock" class="w-5 h-5 text-[#F59E0B]"></i>
            </div>
            <span id="statPendingTrend" class="flex items-center gap-1 text-xs font-semibold text-red-500 bg-red-50 dark:bg-red-500/10 px-2 py-1 rounded-full">
              <i data-lucide="arrow-down-right" class="w-3 h-3"></i>0%
            </span>
          </div>
          <p id="statPending" class="text-3xl font-bold text-[#1E3A8A] dark:text-blue-300 mt-4">0</p>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Pending Responses</p>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Awaiting a reply</p>
        </button>

        <!-- Card 3: Today's Communications -->
        <button id="statTodayCard" type="button" class="stat-card-fade text-left bg-white dark:bg-[#1e1e1e] rounded-2xl shadow-sm p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 cursor-pointer">
          <div class="flex items-start justify-between">
            <div class="w-11 h-11 rounded-xl bg-[#10B981]/10 flex items-center justify-center shrink-0">
              <i data-lucide="calendar-check-2" class="w-5 h-5 text-[#10B981]"></i>
            </div>
            <span id="statTodayTrend" class="flex items-center gap-1 text-xs font-semibold text-[#10B981] bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-full">
              <i data-lucide="arrow-up-right" class="w-3 h-3"></i>0%
            </span>
          </div>
          <p id="statToday" class="text-3xl font-bold text-[#1E3A8A] dark:text-blue-300 mt-4">0</p>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Today's Communications</p>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Logs created today</p>
        </button>

        <!-- Card 4: Average Response Time -->
        <div class="stat-card-fade bg-white dark:bg-[#1e1e1e] rounded-2xl shadow-sm p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
          <div class="flex items-start justify-between">
            <div class="w-11 h-11 rounded-xl bg-[#1E3A8A]/10 flex items-center justify-center shrink-0">
              <i data-lucide="timer" class="w-5 h-5 text-[#1E3A8A] dark:text-blue-300"></i>
            </div>
            <span id="statAvgRespTrend" class="flex items-center gap-1 text-xs font-semibold text-[#10B981] bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-full">
              <i data-lucide="arrow-down-right" class="w-3 h-3"></i>0hrs
            </span>
          </div>
          <p id="statAvgResponseTime" class="text-3xl font-bold text-[#1E3A8A] dark:text-blue-300 mt-4">–</p>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Average Response Time</p>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Based on resolved logs</p>
        </div>

      </div>

      <!-- Recent Activity + Upcoming Follow-ups widgets removed -->

      <!-- Communication History -->
      <div class="bg-white dark:bg-[#1e1e1e] rounded-3xl shadow-sm overflow-hidden transition-colors">
        <div class="p-6 sm:p-8 pb-4">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">Communication History</h3>
              <p class="text-sm text-slate-400 dark:text-slate-500 mt-1"><span id="showingCount">0</span> of <span id="totalCount">0</span> total records</p>
            </div>
            <button id="exportBtnTable" class="flex items-center gap-2 px-4 py-2 rounded-xl border border-[#CBD5E1] dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors self-start sm:self-auto">
              <i data-lucide="file-down" class="w-4 h-4"></i>
              Export CSV
            </button>
          </div>

          <!-- Filter Tabs -->
          <div class="flex items-center gap-2 mt-6 overflow-x-auto pb-1">
            <button data-filter="all" class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-semibold bg-[#10B981] text-white transition-colors">All</button>
            <button data-filter="completed" class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">Completed</button>
            <button data-filter="pending" class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">Pending</button>
            <button data-filter="resolved" class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">Resolved</button>
            <button data-filter="cancelled" class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">Cancelled</button>
          </div>

          <!-- Modern Filter Toolbar -->
          <div class="mt-4 bg-[#F8FAFC] dark:bg-[#121212] rounded-2xl p-4 flex flex-wrap items-center gap-3">

            <div class="relative flex-1 min-w-[200px]">
              <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
              <input id="customerSearchInput" type="text" placeholder="Search customer, email, subject, staff..." class="w-full bg-white dark:bg-[#1e1e1e] border border-[#E5E7EB] dark:border-slate-700 rounded-xl pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
            </div>

            <select id="staffFilter" class="border border-[#E5E7EB] dark:border-slate-700 bg-white dark:bg-[#1e1e1e] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
              <option value="">All Staff</option>
            </select>
            <select id="channelFilter" class="border border-[#E5E7EB] dark:border-slate-700 bg-white dark:bg-[#1e1e1e] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
              <option value="">All Channels</option>
              <option value="mail">Email</option>
              <option value="phone">Phone</option>
              <option value="chat">Live Chat</option>
              <option value="whatsapp">WhatsApp</option>
              <option value="messenger">Messenger</option>
              <option value="sms">SMS</option>
            </select>
            <select id="priorityFilter" class="border border-[#E5E7EB] dark:border-slate-700 bg-white dark:bg-[#1e1e1e] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
              <option value="">All Priorities</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>

            <div class="flex items-center gap-2">
              <input id="startDateFilter" type="date" class="border border-[#E5E7EB] dark:border-slate-700 bg-white dark:bg-[#1e1e1e] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
              <span class="text-slate-400 text-sm">to</span>
              <input id="endDateFilter" type="date" class="border border-[#E5E7EB] dark:border-slate-700 bg-white dark:bg-[#1e1e1e] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
            </div>

            <button id="clearFiltersBtn" type="button" class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
              <i data-lucide="x-circle" class="w-4 h-4"></i>
              Clear Filters
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full min-w-[900px] text-sm">
            <thead>
              <tr class="bg-[#F8FAFC] dark:bg-[#121212] text-slate-400 dark:text-slate-500 text-xs uppercase tracking-wide">
                <th class="text-left font-semibold px-8 py-3">ID</th>
                <th class="text-left font-semibold px-4 py-3">Customer</th>
                <th class="text-left font-semibold px-4 py-3">Date/Time</th>
                <th class="text-left font-semibold px-4 py-3">Channel</th>
                <th class="text-left font-semibold px-4 py-3">Subject</th>
                <th class="text-left font-semibold px-4 py-3">Staff</th>
                <th class="text-left font-semibold px-4 py-3">Status</th>
                <th class="text-left font-semibold px-4 py-3">Priority</th>
                <th class="text-left font-semibold px-8 py-3">Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody" class="divide-y divide-slate-100 dark:divide-slate-700">
              <!-- rows injected by JS -->
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-6 sm:px-8 py-5 border-t border-slate-100 dark:border-slate-700">
          <p class="text-xs text-slate-400 dark:text-slate-500 order-2 sm:order-1">
            Showing <span id="pageRangeStart">0</span>–<span id="pageRangeEnd">0</span> of <span id="pageTotalCount">0</span>
          </p>
          <div id="paginationControls" class="flex items-center gap-1 order-1 sm:order-2">
            <!-- injected by JS -->
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- View Details Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div id="modalOverlay" class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white dark:bg-[#1e1e1e] w-full max-w-6xl max-h-[92vh] rounded-3xl shadow-xl flex flex-col overflow-hidden">

    <!-- Header -->
    <div class="flex items-start justify-between px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
      <div class="flex items-start gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center shrink-0">
          <i data-lucide="message-square" class="w-5 h-5 text-[#10B981]"></i>
        </div>
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h3 id="modalCaseId" class="text-xl font-bold text-slate-800 dark:text-slate-100"></h3>
            <span id="modalStatus" class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide"></span>
            <span id="modalPriorityBadge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide"></span>
          </div>
          <p id="modalSubtitle" class="text-sm text-slate-400 dark:text-slate-500 mt-1"></p>
        </div>
      </div>
      <div class="flex items-center gap-3 shrink-0">
        <button id="modalPrintBtn" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" title="Print">
          <i data-lucide="printer" class="w-5 h-5"></i>
        </button>
        <button id="modalCloseBtn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
    </div>

    <!-- Body: 3 columns -->
    <div class="flex-1 overflow-y-auto grid grid-cols-1 lg:grid-cols-[280px_1fr_280px]">

      <!-- Left column -->
      <div class="border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-slate-700 p-6 space-y-6">

        <div>
          <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-3">Customer Profile</p>
          <div class="flex items-center gap-3 mb-3">
            <div id="modalAvatar" class="w-11 h-11 rounded-full flex items-center justify-center text-white text-sm font-semibold shrink-0"></div>
            <div class="min-w-0">
              <p id="modalName" class="font-semibold text-slate-800 dark:text-slate-100 truncate"></p>
            </div>
          </div>
          <div class="space-y-2 text-sm">
            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
              <i data-lucide="mail" class="w-4 h-4 shrink-0"></i>
              <span id="modalEmail" class="truncate"></span>
            </div>
          </div>
        </div>

        <div class="pt-6 border-t border-slate-100 dark:border-slate-700">
          <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-3">Case Overview</p>
          <div class="space-y-4 text-sm">
            <div>
              <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Channel</p>
              <p id="modalChannel" class="font-medium text-slate-700 dark:text-slate-200 inline-flex items-center gap-1.5"></p>
            </div>
            <div>
              <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Assigned Staff</p>
              <p id="modalStaff" class="font-medium text-slate-700 dark:text-slate-200"></p>
            </div>
            <div>
              <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Response Time</p>
              <p id="modalResp" class="font-medium text-[#10B981] inline-flex items-center gap-1"></p>
            </div>
          </div>
        </div>

        <div class="pt-6 border-t border-slate-100 dark:border-slate-700">
          <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-3">Attachments</p>
          <p class="text-sm text-slate-400 dark:text-slate-500">No attachments on this record.</p>
        </div>

      </div>

      <!-- Center column: chat + notes -->
      <div class="p-6 space-y-6 border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-slate-700">
        <div>
          <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-3">Communication Thread</p>
          <div id="modalChat" class="space-y-3"></div>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
          <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-3 flex items-center gap-1.5">
            <i data-lucide="lock" class="w-3.5 h-3.5"></i> Internal Staff Notes
          </p>
          <div id="modalNotesList" class="space-y-2 mb-3"></div>
          <textarea id="modalNoteInput" rows="2" placeholder="Type a private note for other staff members..." class="w-full border border-[#E5E7EB] dark:border-slate-700 bg-[#F8FAFC] dark:bg-[#121212] text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20 resize-none"></textarea>
          <div class="flex justify-end mt-2">
            <button id="modalPostNoteBtn" type="button" class="px-4 py-2 rounded-xl bg-slate-800 dark:bg-slate-700 text-white text-xs font-semibold hover:bg-slate-900 dark:hover:bg-slate-600 transition-colors">Post Note</button>
          </div>
          <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-2">Notes are visible for this session only — not yet saved to the database.</p>
        </div>
      </div>

      <!-- Right column -->
      <div class="p-6 space-y-6">
        <div>
          <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-3">Activity Timeline</p>
          <div id="modalTimeline" class="relative pl-6 space-y-4">
            <div class="absolute left-[7px] top-1 bottom-1 w-px bg-slate-200 dark:bg-slate-700"></div>
          </div>
        </div>

        <div class="pt-6 border-t border-slate-100 dark:border-slate-700">
          <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-3">Follow-up</p>
          <p id="modalFollowUp" class="text-sm text-slate-500 dark:text-slate-400"></p>
        </div>
      </div>

    </div>

    <!-- Footer -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-6 sm:px-8 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0">
      <div class="flex items-center gap-3 w-full sm:w-auto">
        <button id="modalEditCaseBtn" type="button" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-[#CBD5E1] dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
          <i data-lucide="pencil" class="w-4 h-4"></i> Edit Case
        </button>
        <button id="modalForwardBtn" type="button" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-[#CBD5E1] dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
          <i data-lucide="forward" class="w-4 h-4"></i> Forward
        </button>
      </div>
      <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
        <button id="modalCloseCaseBtn" type="button" class="px-4 py-2.5 rounded-xl border border-[#CBD5E1] dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Close Case</button>
        <button id="modalResolveBtn" type="button" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
          <i data-lucide="check" class="w-4 h-4"></i> Mark as Resolved
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Edit Row Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div id="editOverlay" class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white dark:bg-[#1e1e1e] w-full max-w-lg rounded-3xl shadow-xl flex flex-col overflow-hidden">

    <div class="relative flex items-center justify-center px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-700">
      <div class="text-center">
        <p class="text-xl font-bold text-slate-800 dark:text-slate-100">View Communication</p>
        <p id="editCustomerLabel" class="text-base font-medium text-slate-500 dark:text-slate-400 mt-0.5"></p>
      </div>
      <button id="editCloseBtn" class="absolute right-6 sm:right-8 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <p class="px-6 sm:px-8 pt-4 text-xs text-slate-400 dark:text-slate-500">This record is read-only and cannot be edited.</p>

    <form id="editForm" class="px-6 sm:px-8 py-6 space-y-4">

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-base font-semibold text-slate-600 dark:text-slate-300 mb-1.5 block">Date</label>
          <input id="editDate" type="text" disabled placeholder="e.g. Jun 22" class="w-full border border-[#E5E7EB] dark:border-slate-700 bg-slate-50 dark:bg-[#161616] text-slate-500 dark:text-slate-400 placeholder-slate-400 dark:placeholder-slate-500 rounded-xl px-3.5 py-2.5 text-base focus:outline-none cursor-not-allowed">
        </div>
        <div>
          <label class="text-base font-semibold text-slate-600 dark:text-slate-300 mb-1.5 block">Type</label>
          <select id="editType" disabled class="w-full border border-[#E5E7EB] dark:border-slate-700 bg-slate-50 dark:bg-[#161616] text-slate-500 dark:text-slate-400 rounded-xl px-3.5 py-2.5 text-base focus:outline-none cursor-not-allowed">
            <option value="mail">Email</option>
            <option value="phone">Phone</option>
            <option value="chat">Chat</option>
          </select>
        </div>
      </div>

      <div>
        <label class="text-base font-semibold text-slate-600 dark:text-slate-300 mb-1.5 block">Subject</label>
        <input id="editSubject" type="text" disabled class="w-full border border-[#E5E7EB] dark:border-slate-700 bg-slate-50 dark:bg-[#161616] text-slate-500 dark:text-slate-400 rounded-xl px-3.5 py-2.5 text-base focus:outline-none cursor-not-allowed">
      </div>

      <div>
        <label class="text-base font-semibold text-slate-600 dark:text-slate-300 mb-1.5 block">Staff</label>
        <input id="editStaff" type="text" disabled class="w-full border border-[#E5E7EB] dark:border-slate-700 bg-slate-50 dark:bg-[#161616] text-slate-500 dark:text-slate-400 rounded-xl px-3.5 py-2.5 text-base focus:outline-none cursor-not-allowed">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-base font-semibold text-slate-600 dark:text-slate-300 mb-1.5 block">Status</label>
          <select id="editStatus" disabled class="w-full border border-[#E5E7EB] dark:border-slate-700 bg-slate-50 dark:bg-[#161616] text-slate-500 dark:text-slate-400 rounded-xl px-3.5 py-2.5 text-base focus:outline-none cursor-not-allowed">
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
            <option value="resolved">Resolved</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label class="text-base font-semibold text-slate-600 dark:text-slate-300 mb-1.5 block">Priority</label>
          <select id="editPriority" disabled class="w-full border border-[#E5E7EB] dark:border-slate-700 bg-slate-50 dark:bg-[#161616] text-slate-500 dark:text-slate-400 rounded-xl px-3.5 py-2.5 text-base focus:outline-none cursor-not-allowed">
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
      </div>

      <div>
        <label class="text-base font-semibold text-slate-600 dark:text-slate-300 mb-1.5 block">Resp Time</label>
        <input id="editResp" type="text" readonly disabled placeholder="Calculated automatically" class="w-full border border-[#E5E7EB] dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-base bg-slate-50 dark:bg-[#161616] text-slate-500 dark:text-slate-400 focus:outline-none cursor-not-allowed">
      </div>

      <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" id="editDoneBtn" class="px-5 py-2.5 rounded-xl bg-[#1E3A8A] text-white text-sm font-semibold hover:bg-[#16305f] transition-colors">Done</button>
      </div>

    </form>
  </div>
</div>

<!-- New Communication Modal -->
<div id="newModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div id="newOverlay" class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white dark:bg-[#1e1e1e] w-full max-w-lg max-h-[90vh] rounded-3xl shadow-xl flex flex-col overflow-hidden">

    <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-700">
      <p class="font-semibold text-slate-800 dark:text-slate-100">New Communication</p>
      <button id="newCloseBtn" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form
        id="newForm"
        action="{{ route('communication.store') }}"
        method="POST"
        class="px-6 sm:px-8 py-6 space-y-4 overflow-y-auto"
    >
        @csrf

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-xs font-medium text-slate-500 mb-1 block">Customer Name</label>
          <input id="newCustomerName" required type="text" placeholder="e.g. John Carter" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
        </div>
        <div>
          <label class="text-xs font-medium text-slate-500 mb-1 block">Email</label>
          <input id="newCustomerEmail" required type="email" placeholder="e.g. john@email.com" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-xs font-medium text-slate-500 mb-1 block">Date</label>
          <input id="newDate" type="text" placeholder="e.g. Jul 06" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
        </div>
        <div>
          <label class="text-xs font-medium text-slate-500 mb-1 block">Type</label>
          <select id="newType" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
            <option value="mail">Email</option>
            <option value="phone">Phone</option>
            <option value="chat">Chat</option>
          </select>
        </div>
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Subject</label>
        <input id="newSubject" required type="text" placeholder="e.g. Billing Inquiry" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Assigned Agent</label>
        <select id="newAgentId" required class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
          <option value="">Select an agent</option>
          @foreach($agents as $agent)
            <option value="{{ $agent->id }}">{{ $agent->name }} ({{ $agent->role }})</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Related Ticket (optional)</label>
        <select id="newTicketId" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
          <option value="">No ticket</option>
          @foreach($tickets as $ticket)
            <option value="{{ $ticket->id }}">{{ $ticket->ticket_number }} — {{ $ticket->subject }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-xs font-medium text-slate-500 mb-1 block">Status</label>
          <select id="newStatus" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
            <option value="pending" selected>Pending</option>
            <option value="completed">Completed</option>
            <option value="resolved">Resolved</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label class="text-xs font-medium text-slate-500 mb-1 block">Priority</label>
          <select id="newPriority" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
            <option value="low">Low</option>
          </select>
        </div>
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Resp Time</label>
        <input id="newResp" type="text" placeholder="e.g. Pending" value="Pending" readonly class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm bg-slate-50 text-slate-500 focus:outline-none">
      </div>

      <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" id="newCancelBtn" class="px-4 py-2.5 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">Cancel</button>
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">Add Communication</button>
      </div>

    </form>
  </div>
</div>

<!-- Add Follow-up Modal -->
<div id="followUpModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div id="followUpOverlay" class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white dark:bg-[#1e1e1e] w-full max-w-md rounded-3xl shadow-xl flex flex-col overflow-hidden">

    <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-700">
      <p class="font-semibold text-slate-800 dark:text-slate-100">Add Follow-up</p>
      <button id="followUpCloseBtn" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form id="followUpForm" class="px-6 sm:px-8 py-6 space-y-4">

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Communication Record</label>
        <select id="followUpRecordId" required class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
          <option value="">Select a customer / record</option>
        </select>
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Follow-up Date</label>
        <input id="followUpDateInput" required type="datetime-local" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Priority</label>
        <select id="followUpPriorityInput" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
          <option value="high">High</option>
          <option value="medium" selected>Medium</option>
          <option value="low">Low</option>
        </select>
      </div>

      <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" id="followUpCancelBtn" class="px-4 py-2.5 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">Cancel</button>
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">Schedule Follow-up</button>
      </div>

    </form>
  </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div id="exportOverlay" class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white dark:bg-[#1e1e1e] w-full max-w-2xl max-h-[90vh] rounded-3xl shadow-xl flex flex-col overflow-hidden">

    <!-- ===== Screen 1: Export Options ===== -->
    <div id="exportOptionsScreen">
      <div class="flex items-start justify-between px-6 sm:px-8 py-6 border-b border-slate-100 dark:border-slate-700">
        <div class="flex items-start gap-4">
          <div class="w-11 h-11 rounded-xl bg-[#1E3A8A] flex items-center justify-center shrink-0">
            <i data-lucide="download" class="w-5 h-5 text-white"></i>
          </div>
          <div>
            <h3 class="text-lg font-bold text-slate-800">Export Communication History</h3>
            <p class="text-sm text-slate-400 mt-0.5">Choose the format and options for your export.</p>
          </div>
        </div>
        <button id="exportCloseBtn" class="text-slate-400 hover:text-slate-600 shrink-0">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <div class="px-6 sm:px-8 py-6 space-y-6 overflow-y-auto max-h-[60vh]">

        <div>
          <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Export Format</p>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            <button type="button" data-format="csv" class="format-card relative border-2 border-[#10B981] bg-emerald-50/40 rounded-2xl p-4 text-left transition-colors">
              <span class="format-check absolute top-3 right-3 w-5 h-5 rounded-full bg-[#10B981] flex items-center justify-center">
                <i data-lucide="check" class="w-3 h-3 text-white"></i>
              </span>
              <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center mb-3">
                <i data-lucide="file-text" class="w-5 h-5 text-green-600"></i>
              </div>
              <p class="font-bold text-slate-800 text-sm">CSV</p>
              <p class="text-xs text-slate-400 mt-1">Best for CSV and Google Sheets.</p>
            </button>

            <button type="button" data-format="excel" class="format-card relative border-2 border-slate-200 rounded-2xl p-4 text-left transition-colors hover:border-slate-300">
              <span class="format-check absolute top-3 right-3 w-5 h-5 rounded-full bg-[#10B981] hidden items-center justify-center">
                <i data-lucide="check" class="w-3 h-3 text-white"></i>
              </span>
              <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center mb-3">
                <i data-lucide="file-spreadsheet" class="w-5 h-5 text-green-600"></i>
              </div>
              <p class="font-bold text-slate-800 text-sm">Excel</p>
              <p class="text-xs text-slate-400 mt-1">Formatted spreadsheet with styles.</p>
            </button>

            <button type="button" data-format="pdf" class="format-card relative border-2 border-slate-200 rounded-2xl p-4 text-left transition-colors hover:border-slate-300">
              <span class="format-check absolute top-3 right-3 w-5 h-5 rounded-full bg-[#10B981] hidden items-center justify-center">
                <i data-lucide="check" class="w-3 h-3 text-white"></i>
              </span>
              <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center mb-3">
                <i data-lucide="file-type" class="w-5 h-5 text-red-500"></i>
              </div>
              <p class="font-bold text-slate-800 text-sm">PDF</p>
              <p class="text-xs text-slate-400 mt-1">Professional printable report.</p>
            </button>

          </div>
        </div>

        <div>
          <label class="text-sm font-semibold text-slate-600 mb-2 block">Export Date Range</label>
          <select id="exportDateRange" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
            <option>Last 7 Days</option>
            <option selected>Last 30 Days</option>
            <option>Last 90 Days</option>
            <option>All Time</option>
          </select>
        </div>

        <div>
          <p class="text-sm font-semibold text-slate-600 mb-3">Include in Export</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-slate-600">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" id="incEmail" checked class="w-4 h-4 rounded accent-[#10B981]"> Include Customer Email
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" id="incStaff" checked class="w-4 h-4 rounded accent-[#10B981]"> Include Staff Name
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" id="incStatus" checked class="w-4 h-4 rounded accent-[#10B981]"> Include Status
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" id="incPriority" checked class="w-4 h-4 rounded accent-[#10B981]"> Include Priority
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" id="incResp" checked class="w-4 h-4 rounded accent-[#10B981]"> Include Response Time
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" id="incNotes" class="w-4 h-4 rounded accent-[#10B981]"> Include Communication Notes
            </label>
          </div>
        </div>

        <div>
          <label class="text-sm font-semibold text-slate-600 mb-2 block">Sort By</label>
          <select id="exportSortBy" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
            <option selected>Newest First</option>
            <option>Oldest First</option>
            <option>Priority (High to Low)</option>
            <option>Customer Name (A–Z)</option>
          </select>
        </div>

      </div>

      <div class="flex items-center justify-end gap-3 px-6 sm:px-8 py-5 border-t border-slate-100">
        <button type="button" id="exportCancelBtn" class="px-5 py-2.5 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">Cancel</button>
        <button type="button" id="exportGoBtn" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
          <i data-lucide="download" class="w-4 h-4"></i> Export
        </button>
      </div>
    </div>

    <!-- ===== Screen 2: Preview ===== -->
    <div id="exportPreviewScreen" class="hidden">
      <div class="flex items-center justify-between px-6 sm:px-8 py-6 border-b border-slate-100">
        <div>
          <h3 id="previewTitle" class="text-lg font-bold text-slate-800">CSV Export Preview</h3>
          <p class="text-sm text-slate-400 mt-0.5">Preview first few rows</p>
        </div>
        <button id="previewCloseBtn" class="text-slate-400 hover:text-slate-600">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <div class="px-6 sm:px-8 py-5 overflow-x-auto max-h-[50vh]">
        <table class="w-full text-xs sm:text-sm min-w-[700px]">
          <thead>
            <tr class="text-slate-400 uppercase text-[10px] sm:text-xs border-b border-slate-100">
              <th class="text-left font-semibold py-2 pr-3">#</th>
              <th class="text-left font-semibold py-2 pr-3">Customer</th>
              <th class="text-left font-semibold py-2 pr-3">Email</th>
              <th class="text-left font-semibold py-2 pr-3">Date</th>
              <th class="text-left font-semibold py-2 pr-3">Type</th>
              <th class="text-left font-semibold py-2 pr-3">Subject</th>
              <th class="text-left font-semibold py-2 pr-3">Status</th>
              <th class="text-left font-semibold py-2 pr-3">Resp Time</th>
            </tr>
          </thead>
          <tbody id="previewTableBody" class="divide-y divide-slate-50"></tbody>
        </table>
      </div>

      <div class="px-6 sm:px-8 py-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-[#F8FAFC] text-xs sm:text-sm text-slate-500">
        <span>File Name: <span id="previewFileName" class="font-medium text-slate-700"></span></span>
        <span>Size: <span id="previewFileSize" class="font-medium text-slate-700"></span></span>
        <span>Records: <span id="previewRecordCount" class="font-medium text-slate-700"></span></span>
      </div>

      <div class="flex items-center justify-end gap-3 px-6 sm:px-8 py-5 border-t border-slate-100">
        <button type="button" id="previewBackBtn" class="px-5 py-2.5 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">Back</button>
        <button type="button" id="previewDownloadBtn" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
          <i data-lucide="download" class="w-4 h-4"></i> <span id="previewDownloadLabel">Download CSV</span>
        </button>
      </div>
    </div>

  </div>
</div>

<script>
  // ================= Theme Toggle (Light / Dark) =================
  function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    localStorage.setItem('theme', theme);
    document.getElementById('themeIconSun').classList.toggle('hidden', theme !== 'dark');
    document.getElementById('themeIconMoon').classList.toggle('hidden', theme === 'dark');
  }

  const savedTheme = localStorage.getItem('theme') || 'light';
  applyTheme(savedTheme);

  document.getElementById('themeToggleBtn').addEventListener('click', () => {
    const isDark = document.documentElement.classList.contains('dark');
    applyTheme(isDark ? 'light' : 'dark');
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

  lucide.createIcons();

  // Pull real data from the database via $communications passed from the controller
  const rows = <?php
    $avatarColors = ['#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444'];
    $i = 0;
    function getPhpInitials($name) {
        $name = $name ?: 'NA';
        $parts = preg_split('/\s+/', trim($name));
        $initials = strtoupper(substr($parts[0], 0, 1));
        if (count($parts) > 1) {
            $initials .= strtoupper(substr(end($parts), 0, 1));
        }
        return $initials;
    }
    echo json_encode(array_map(function ($c) use ($avatarColors, &$i) {
        $color = $avatarColors[$i % count($avatarColors)];
        $i++;
            $ticketAgent = $c->ticket?->agentModel?->name ?? null;
        $relationAgent = $c->agent?->name ?? null;
        $staffName = $ticketAgent ?: $relationAgent ?: $c->staff ?: '';

        $ticketStatus = $c->ticket?->status ?? null;
        $ticketPriority = $c->ticket?->priority ?? null;

        return [
            'id'       => $c->id,
            'initials' => getPhpInitials($c->customer_name ?? null),
            'color'    => $color,
            'name'     => $c->customer_name ?? 'Unknown',
            'email'    => $c->customer_email ?? '',
            'date'     => $c->date ?? '',
            'type'     => $c->type ?? 'mail',
            'subject'  => $c->subject ?? '',
            'staff'    => $staffName,
            'status'   => $c->status ?? 'pending',
            'ticketStatus' => $ticketStatus,
            'priority' => $ticketPriority ? strtolower($ticketPriority) : ($c->priority ?? 'medium'),
            'resp'     => $c->resp_time ?? '',
            'followUpDate'     => $c->follow_up_date ?? null,
            'followUpPriority' => $c->follow_up_priority ?? null,
        ];
    }, isset($communications) ? $communications->all() : []));
  ?>;

  const followUps = <?php
    echo json_encode(array_map(function ($f) {
        return [
            'id'       => $f->id,
            'name'     => $f->customer_name ?? 'Unknown',
            'date'     => $f->follow_up_date,
            'priority' => $f->follow_up_priority ?? 'medium',
        ];
    }, isset($followUps) ? $followUps->all() : []));
  ?>;

  const typeIcon = {
    mail: 'mail',
    email: 'mail',
    phone: 'phone',
    chat: 'message-circle',
    livechat: 'message-circle',
    whatsapp: 'message-square',
    messenger: 'send',
    sms: 'smartphone',
  };
  const typeLabel = {
    mail: 'Email',
    email: 'Email',
    phone: 'Phone',
    chat: 'Live Chat',
    livechat: 'Live Chat',
    whatsapp: 'WhatsApp',
    messenger: 'Messenger',
    sms: 'SMS',
  };

  const statusStyles = {
    open: 'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400',
    completed: 'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400',
    resolved: 'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400',
    pending: 'bg-yellow-100 dark:bg-yellow-500/20 text-yellow-600 dark:text-yellow-400',
    in_progress: 'bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400',
    closed: 'bg-slate-100 dark:bg-slate-600 text-slate-500 dark:text-slate-300',
    escalated: 'bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400',
    cancelled: 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
  };
  const statusLabel = {
    open: 'Open',
    completed: 'Resolved',
    resolved: 'Resolved',
    pending: 'Pending',
    in_progress: 'In Progress',
    closed: 'Closed',
    cancelled: 'Cancelled',
  };

  const priorityStyles = {
    critical: 'bg-red-50 dark:bg-red-500/20 text-red-600 dark:text-red-400',
    high: 'bg-red-100 dark:bg-red-500/20 text-red-500 dark:text-red-400',
    medium: 'bg-orange-100 dark:bg-orange-500/20 text-orange-500 dark:text-orange-400',
    low: 'bg-slate-200 dark:bg-slate-600 text-slate-500 dark:text-slate-300',
  };

  function formatFollowUpDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr.replace(' ', 'T'));
    if (isNaN(d)) return dateStr;
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  }

  // Upcoming Follow-ups widget removed (UI simplified)

  function normalizeStatus(s) {
    if (!s) return '';
    return s.toString().trim().toLowerCase().replace(/\s+/g, '_');
  }

  function rowStatus(row) {
    return normalizeStatus(row.ticketStatus || row.status || '');
  }

  function label(s) { if (!s) return 'N/A'; return s.charAt(0).toUpperCase() + s.slice(1); }
  function statusText(s) { return statusLabel[normalizeStatus(s)] || label(s); }
  function typeText(t) { return typeLabel[t] || label(t); }

  const chatHistory = {
    'James Davidson': [
      { from: 'customer', text: 'Hi, I need help reviewing my Q3 financial statement.', time: '9:02 AM' },
      { from: 'staff', text: 'Sure! I can pull that up for you now.', time: '9:03 AM' },
      { from: 'staff', text: 'I\'ve sent the Q3 review to your email, let me know if anything looks off.', time: '9:15 AM' },
      { from: 'customer', text: 'Got it, thanks! Everything looks good.', time: '11:18 AM' },
    ],
    'Sarah Mitchell': [
      { from: 'customer', text: 'I\'d like to upgrade my account plan.', time: '10:30 AM' },
      { from: 'staff', text: 'Happy to help! Let me check available plans for you.', time: '10:32 AM' },
    ],
    'Robert Kim': [
      { from: 'customer', text: 'Can you walk me through my investment portfolio?', time: '2:00 PM' },
      { from: 'staff', text: 'Of course, give me a moment to pull up your details.', time: '2:01 PM' },
      { from: 'staff', text: 'Your portfolio is up 4.2% this quarter overall.', time: '2:10 PM' },
      { from: 'customer', text: 'That\'s great to hear, thank you!', time: '2:45 PM' },
    ],
  };

  const defaultChat = [
    { from: 'customer', text: 'Hello, I have a question about my recent request.', time: '9:00 AM' },
    { from: 'staff', text: 'Hi! Thanks for reaching out, let me look into that.', time: '9:05 AM' },
  ];

  let currentFilter = 'all';
  let editingId = null;
  let currentFilteredRows = [];
  let currentPage = 1;
  const PAGE_SIZE = 10;

  // Matches PHP's now()->format('M d') used when saving new communications
  function getTodayPHPStyle() {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const d = new Date();
    return `${months[d.getMonth()]} ${String(d.getDate()).padStart(2, '0')}`;
  }

  function parseRowDate(dateStr) {
    if (!dateStr) return null;
    const parsed = new Date(`${dateStr} ${new Date().getFullYear()}`);
    return isNaN(parsed) ? null : parsed;
  }

  // Renders whatever filtered array is passed in — stores it and resets to page 1
  function renderFilteredRows(filtered) {
    currentFilteredRows = filtered;
    currentPage = 1;
    renderPage();
  }

  function renderPage() {
    const totalFiltered = currentFilteredRows.length;
    const totalPages = Math.max(1, Math.ceil(totalFiltered / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;

    const startIdx = (currentPage - 1) * PAGE_SIZE;
    const pageRows = currentFilteredRows.slice(startIdx, startIdx + PAGE_SIZE);

    document.getElementById('showingCount').textContent = totalFiltered;
    document.getElementById('totalCount').textContent = rows.length;
    document.getElementById('pageRangeStart').textContent = totalFiltered ? startIdx + 1 : 0;
    document.getElementById('pageRangeEnd').textContent = Math.min(startIdx + PAGE_SIZE, totalFiltered);
    document.getElementById('pageTotalCount').textContent = totalFiltered;

    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = pageRows.map(r => `
      <tr class="table-row-fx hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all duration-200">
        <td class="px-8 py-4 text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">#COM-${String(r.id).padStart(3, '0')}</td>
        <td class="px-4 py-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-semibold shrink-0" style="background:${r.color}">${r.initials}</div>
            <div>
              <p class="font-semibold text-slate-700 dark:text-slate-100">${r.name}</p>
              <p class="text-xs text-slate-400 dark:text-slate-500">${r.email}</p>
            </div>
          </div>
        </td>
        <td class="px-4 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">${r.date}</td>
        <td class="px-4 py-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">
          <span class="inline-flex items-center gap-1.5">
            <i data-lucide="${typeIcon[r.type] || 'mail'}" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i>
            <span class="text-xs">${typeText(r.type)}</span>
          </span>
        </td>
        <td class="px-4 py-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">${r.subject}</td>
        <td class="px-4 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">${r.staff}</td>
        <td class="px-4 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap transition-colors ${statusStyles[normalizeStatus(r.ticketStatus || r.status || '')]}">${statusText(r.ticketStatus || r.status || '')}</span></td>
        <td class="px-4 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap transition-colors ${priorityStyles[(r.priority||'').toLowerCase()] || priorityStyles.low}">${label(r.priority)}</span></td>
        <td class="px-8 py-4">
          <div class="flex items-center gap-2">
            <button class="view-btn w-8 h-8 flex items-center justify-center rounded-lg border border-[#10B981] text-[#10B981] hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors" data-name="${r.name}" title="View">
              <i data-lucide="eye" class="w-4 h-4"></i>
            </button>
            <button class="edit-btn w-8 h-8 flex items-center justify-center rounded-lg border border-[#CBD5E1] dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors" data-id="${r.id}" title="Edit">
              <i data-lucide="pencil" class="w-4 h-4"></i>
            </button>
          </div>
        </td>
      </tr>
    `).join('');

    document.querySelectorAll('.view-btn').forEach(btn => {
      btn.addEventListener('click', () => openViewModal(btn.dataset.name));
    });
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', () => openEditModal(btn.dataset.id));
    });

    renderPaginationControls(totalPages);
    lucide.createIcons();
  }

  // Builds Previous / page numbers (with ellipsis for long ranges) / Next
  function renderPaginationControls(totalPages) {
    const container = document.getElementById('paginationControls');
    const btnBase = 'min-w-[36px] h-9 px-2 rounded-lg text-sm font-medium transition-colors';
    const inactive = 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700';
    const active = 'bg-[#10B981] text-white font-semibold shadow-sm shadow-emerald-200';
    const disabled = 'text-slate-300 dark:text-slate-600 cursor-not-allowed';

    let pagesToShow = [];
    if (totalPages <= 7) {
      pagesToShow = Array.from({ length: totalPages }, (_, i) => i + 1);
    } else {
      pagesToShow = [1];
      if (currentPage > 3) pagesToShow.push('...');
      for (let p = Math.max(2, currentPage - 1); p <= Math.min(totalPages - 1, currentPage + 1); p++) pagesToShow.push(p);
      if (currentPage < totalPages - 2) pagesToShow.push('...');
      pagesToShow.push(totalPages);
    }

    const pageButtons = pagesToShow.map(p =>
      p === '...'
        ? `<span class="min-w-[36px] h-9 flex items-center justify-center text-slate-300 dark:text-slate-600 text-sm">…</span>`
        : `<button type="button" class="page-btn ${btnBase} ${p === currentPage ? active : inactive}" data-page="${p}">${p}</button>`
    ).join('');

    container.innerHTML = `
      <button type="button" id="prevPageBtn" class="${btnBase} ${currentPage === 1 ? disabled : inactive}" ${currentPage === 1 ? 'disabled' : ''}>
        <i data-lucide="chevron-left" class="w-4 h-4"></i>
      </button>
      ${pageButtons}
      <button type="button" id="nextPageBtn" class="${btnBase} ${currentPage === totalPages ? disabled : inactive}" ${currentPage === totalPages ? 'disabled' : ''}>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
      </button>
    `;

    document.querySelectorAll('.page-btn').forEach(btn => {
      btn.addEventListener('click', () => { currentPage = parseInt(btn.dataset.page, 10); renderPage(); });
    });
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    if (currentPage > 1) prevBtn.addEventListener('click', () => { currentPage--; renderPage(); });
    if (currentPage < totalPages) nextBtn.addEventListener('click', () => { currentPage++; renderPage(); });
  }

  // Combines the active status tab + staff/channel/priority/search/date dropdowns, then renders
  function applyAllFilters() {
    const staff = document.getElementById('staffFilter').value;
    const channel = document.getElementById('channelFilter').value;
    const priority = document.getElementById('priorityFilter').value;
    const search = document.getElementById('customerSearchInput').value.trim().toLowerCase();
    const start = document.getElementById('startDateFilter').value ? new Date(document.getElementById('startDateFilter').value) : null;
    const end = document.getElementById('endDateFilter').value ? new Date(document.getElementById('endDateFilter').value) : null;

    let filtered = currentFilter === 'all' ? rows.slice()
      : currentFilter === 'today' ? rows.filter(r => r.date === getTodayPHPStyle())
      : rows.filter(r => rowStatus(r) === currentFilter);

    if (staff) filtered = filtered.filter(r => r.staff === staff);
    if (channel) filtered = filtered.filter(r => r.type === channel);
    if (priority) filtered = filtered.filter(r => r.priority === priority);
    if (start) filtered = filtered.filter(r => { const d = parseRowDate(r.date); return d && d >= start; });
    if (end) filtered = filtered.filter(r => { const d = parseRowDate(r.date); return d && d <= end; });

    if (search) {
      filtered = filtered.filter(r =>
        (r.name && r.name.toLowerCase().includes(search)) ||
        (r.email && r.email.toLowerCase().includes(search)) ||
        (r.subject && r.subject.toLowerCase().includes(search)) ||
        (r.staff && r.staff.toLowerCase().includes(search)) ||
        (typeText(r.type) && typeText(r.type).toLowerCase().includes(search)) ||
        (statusText(r.ticketStatus || r.status || '') && statusText(r.ticketStatus || r.status || '').toLowerCase().includes(search)) ||
        (r.priority && r.priority.toLowerCase().includes(search))
      );
    }

    renderFilteredRows(filtered);
  }

  function setActiveFilterTab(filterValue) {
    currentFilter = filterValue;
    document.querySelectorAll('.filter-tab').forEach(b => {
      b.classList.remove('bg-[#10B981]', 'text-white', 'font-semibold');
      b.classList.add('bg-slate-100', 'text-slate-500', 'font-medium');
    });
    const matchingTab = document.querySelector(`.filter-tab[data-filter="${filterValue}"]`);
    if (matchingTab) {
      matchingTab.classList.remove('bg-slate-100', 'text-slate-500', 'font-medium');
      matchingTab.classList.add('bg-[#10B981]', 'text-white', 'font-semibold');
    }
    applyAllFilters();
  }

  function populateStaffFilter() {
    const staffSet = [...new Set(rows.map(r => r.staff).filter(Boolean))];
    const select = document.getElementById('staffFilter');
    staffSet.forEach(name => {
      const opt = document.createElement('option');
      opt.value = name;
      opt.textContent = name;
      select.appendChild(opt);
    });
  }

  // Parses resp_time strings like "1h 30m", "45m", "2h" into minutes. Returns null for "Pending"/unparseable.
  function parseRespMinutes(resp) {
    if (!resp || resp === 'Pending') return null;
    const hMatch = resp.match(/(\d+)\s*h/);
    const mMatch = resp.match(/(\d+)\s*m/);
    if (!hMatch && !mMatch) return null;
    const hours = hMatch ? parseInt(hMatch[1], 10) : 0;
    const mins = mMatch ? parseInt(mMatch[1], 10) : 0;
    return hours * 60 + mins;
  }

  function trendBadge(el, pct, higherIsBetter = true) {
    if (el === null) return;
    const positive = pct >= 0;
    const good = higherIsBetter ? positive : !positive;
    el.className = `flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full ${
      good ? 'text-[#10B981] bg-emerald-50 dark:bg-emerald-500/10' : 'text-red-500 bg-red-50 dark:bg-red-500/10'
    }`;
    el.innerHTML = `<i data-lucide="${positive ? 'arrow-up-right' : 'arrow-down-right'}" class="w-3 h-3"></i>${Math.abs(pct).toFixed(1)}%`;
  }

  function updateStats() {
    const total = rows.length;
    const pendingCount = rows.filter(r => r.status === 'pending').length;
    const todayCount = rows.filter(r => r.date === getTodayPHPStyle()).length;

    // Windows: last 7 days vs prior 7 days, based on parsed row dates
    const now = new Date();
    const sevenDaysAgo = new Date(now); sevenDaysAgo.setDate(now.getDate() - 7);
    const fourteenDaysAgo = new Date(now); fourteenDaysAgo.setDate(now.getDate() - 14);

    const withDates = rows.map(r => ({ ...r, _d: parseRowDate(r.date) })).filter(r => r._d);
    const recentWindow = withDates.filter(r => r._d >= sevenDaysAgo && r._d <= now);
    const priorWindow = withDates.filter(r => r._d >= fourteenDaysAgo && r._d < sevenDaysAgo);

    const pctChange = (curr, prev) => prev === 0 ? (curr > 0 ? 100 : 0) : ((curr - prev) / prev) * 100;

    // Total trend: volume of records created recent week vs prior week
    const totalTrend = pctChange(recentWindow.length, priorWindow.length);

    // Pending trend: pending count recent vs prior
    const pendingTrend = pctChange(
      recentWindow.filter(r => r.status === 'pending').length,
      priorWindow.filter(r => r.status === 'pending').length
    );

    // Today trend: today's count vs yesterday's count
    const yesterday = new Date(now); yesterday.setDate(now.getDate() - 1);
    const yesterdayStr = getPHPStyleDate(yesterday);
    const yesterdayCount = rows.filter(r => r.date === yesterdayStr).length;
    const todayTrend = pctChange(todayCount, yesterdayCount);

    // Average response time from resolved/completed records with a parseable resp_time
    const resolvedMinutes = rows
      .filter(r => {
        const s = rowStatus(r);
        return s === 'completed' || s === 'resolved';
      })
      .map(r => parseRespMinutes(r.resp))
      .filter(m => m !== null);
    const avgMinutes = resolvedMinutes.length
      ? resolvedMinutes.reduce((a, b) => a + b, 0) / resolvedMinutes.length
      : null;

    const recentResolvedMinutes = recentWindow
      .filter(r => {
        const s = rowStatus(r);
        return s === 'completed' || s === 'resolved';
      })
      .map(r => parseRespMinutes(r.resp)).filter(m => m !== null);
    const priorResolvedMinutes = priorWindow
      .filter(r => {
        const s = rowStatus(r);
        return s === 'completed' || s === 'resolved';
      })
      .map(r => parseRespMinutes(r.resp)).filter(m => m !== null);
    const recentAvg = recentResolvedMinutes.length ? recentResolvedMinutes.reduce((a,b)=>a+b,0)/recentResolvedMinutes.length : null;
    const priorAvg = priorResolvedMinutes.length ? priorResolvedMinutes.reduce((a,b)=>a+b,0)/priorResolvedMinutes.length : null;
    const avgRespTrendHrs = (recentAvg !== null && priorAvg !== null) ? (recentAvg - priorAvg) / 60 : null;

    document.getElementById('statTotalLogs').textContent = total.toLocaleString();
    document.getElementById('statPending').textContent = pendingCount;
    document.getElementById('statToday').textContent = todayCount;
    document.getElementById('statAvgResponseTime').textContent = avgMinutes !== null
      ? (avgMinutes / 60).toFixed(1) + ' hrs'
      : '–';

    trendBadge(document.getElementById('statTotalTrend'), totalTrend, true);
    trendBadge(document.getElementById('statPendingTrend'), pendingTrend, false);
    trendBadge(document.getElementById('statTodayTrend'), todayTrend, true);

    const avgTrendEl = document.getElementById('statAvgRespTrend');
    if (avgRespTrendHrs !== null) {
      const improved = avgRespTrendHrs <= 0;
      avgTrendEl.className = `flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full ${
        improved ? 'text-[#10B981] bg-emerald-50 dark:bg-emerald-500/10' : 'text-red-500 bg-red-50 dark:bg-red-500/10'
      }`;
      avgTrendEl.innerHTML = `<i data-lucide="${improved ? 'arrow-down-right' : 'arrow-up-right'}" class="w-3 h-3"></i>${Math.abs(avgRespTrendHrs).toFixed(1)}hrs`;
    } else {
      avgTrendEl.innerHTML = `<i data-lucide="minus" class="w-3 h-3"></i>N/A`;
      avgTrendEl.className = 'flex items-center gap-1 text-xs font-semibold text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded-full';
    }

    lucide.createIcons();
  }

  // Same "M d" formatting PHP uses, for an arbitrary Date object
  function getPHPStyleDate(d) {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return `${months[d.getMonth()]} ${String(d.getDate()).padStart(2, '0')}`;
  }

  // Recent Activity widget removed (UI simplified)

  populateStaffFilter();
  updateStats();
  applyAllFilters();
  // Recent Activity and Upcoming Follow-ups widgets removed

  document.getElementById('statPendingCard').addEventListener('click', () => setActiveFilterTab('pending'));
  document.getElementById('statTodayCard').addEventListener('click', () => setActiveFilterTab('today'));

  ['staffFilter', 'channelFilter', 'priorityFilter', 'startDateFilter', 'endDateFilter'].forEach(id => {
    document.getElementById(id).addEventListener('change', applyAllFilters);
  });

  let searchDebounce;
  document.getElementById('customerSearchInput').addEventListener('input', () => {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(applyAllFilters, 200);
  });

  document.getElementById('clearFiltersBtn').addEventListener('click', () => {
    document.getElementById('staffFilter').value = '';
    document.getElementById('channelFilter').value = '';
    document.getElementById('priorityFilter').value = '';
    document.getElementById('customerSearchInput').value = '';
    document.getElementById('startDateFilter').value = '';
    document.getElementById('endDateFilter').value = '';
    applyAllFilters();
  });

  document.querySelectorAll('.filter-tab').forEach(btn => {
    btn.addEventListener('click', () => setActiveFilterTab(btn.dataset.filter));
  });

  // ================= Live Date & Time =================
  function updateLiveDateTime() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    const timeStr = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true });
    document.getElementById('liveDateTime').textContent = `${dateStr} — ${timeStr}`;
  }
  updateLiveDateTime();
  setInterval(updateLiveDateTime, 1000);

  // ---- View Modal logic ----
  const viewModal = document.getElementById('viewModal');
  const sessionNotes = {}; // in-memory only, per-row id — not persisted to backend
  let currentViewRow = null;

  function openViewModal(name) {
    const r = rows.find(row => row.name === name);
    if (!r) return;
    currentViewRow = r;

    document.getElementById('modalCaseId').textContent = `#COM-${String(r.id).padStart(3, '0')}`;
    document.getElementById('modalSubtitle').textContent = `${r.subject || 'Communication Thread'} — ${r.date}`;

    const statusEl = document.getElementById('modalStatus');
    statusEl.textContent = statusText(r.ticketStatus || r.status || '');
    statusEl.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide ' + statusStyles[rowStatus(r)];

    const priorityEl = document.getElementById('modalPriorityBadge');
    priorityEl.textContent = label(r.priority) + ' Priority';
    priorityEl.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide ' + (priorityStyles[(r.priority||'').toLowerCase()] || priorityStyles.low);

    document.getElementById('modalAvatar').textContent = r.initials;
    document.getElementById('modalAvatar').style.background = r.color;
    document.getElementById('modalName').textContent = r.name;
    document.getElementById('modalEmail').textContent = r.email || 'No email on file';

    document.getElementById('modalChannel').innerHTML = `<i data-lucide="${typeIcon[r.type] || 'mail'}" class="w-3.5 h-3.5 text-slate-400"></i> ${typeText(r.type)}`;
    document.getElementById('modalStaff').textContent = r.staff || 'Unassigned';
    document.getElementById('modalResp').innerHTML = r.resp && r.resp !== 'Pending'
      ? `<i data-lucide="zap" class="w-3.5 h-3.5"></i> ${r.resp}`
      : `<span class="text-slate-400 dark:text-slate-500 font-normal">Pending</span>`;

    // Chat thread
    const chat = chatHistory[r.name] || defaultChat;
    document.getElementById('modalChat').innerHTML = chat.map(m => `
      <div class="flex ${m.from === 'staff' ? 'justify-end' : 'justify-start'}">
        <div class="max-w-[80%] ${m.from === 'staff' ? 'bg-[#1E3A8A] text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200'} rounded-2xl px-4 py-2.5 text-sm">
          <p>${m.text}</p>
          <p class="text-[10px] mt-1 ${m.from === 'staff' ? 'text-blue-200' : 'text-slate-400 dark:text-slate-400'}">${m.time}</p>
        </div>
      </div>
    `).join('');

    // Internal notes (session-only)
    renderModalNotes(r.id);

    // Activity timeline — built only from real fields we have
    const timelineSteps = [
      { label: 'Logged', sub: `${r.date} · ${r.staff || 'System'}`, color: 'bg-blue-500' },
    ];
    if (r.staff) timelineSteps.push({ label: `Assigned to ${r.staff}`, sub: 'Staff', color: 'bg-slate-300 dark:bg-slate-600' });
    if (r.status === 'in_progress') timelineSteps.push({ label: 'In Progress', sub: r.staff || '', color: 'bg-purple-400' });
    if (r.status === 'escalated') timelineSteps.push({ label: 'Escalated', sub: r.staff || '', color: 'bg-purple-500' });
    if (r.status === 'completed' || r.status === 'resolved') timelineSteps.push({ label: 'Resolved', sub: r.staff || '', color: 'bg-[#10B981]' });
    if (r.status === 'cancelled') timelineSteps.push({ label: 'Cancelled', sub: r.staff || '', color: 'bg-red-500' });

    document.getElementById('modalTimeline').innerHTML = `<div class="absolute left-[7px] top-1 bottom-1 w-px bg-slate-200 dark:bg-slate-700"></div>` +
      timelineSteps.map(s => `
        <div class="relative">
          <span class="absolute -left-6 top-1 w-3 h-3 rounded-full ${s.color} ring-4 ring-white dark:ring-[#1e1e1e]"></span>
          <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">${s.label}</p>
          <p class="text-xs text-slate-400 dark:text-slate-500">${s.sub}</p>
        </div>
      `).join('');

    // Follow-up (only shown if a real follow-up exists for this customer)
    const fu = followUps.find(f => f.name === r.name);
    document.getElementById('modalFollowUp').textContent = fu
      ? `${formatFollowUpDate(fu.date)} — ${label(fu.priority)} priority`
      : 'No upcoming follow-up scheduled.';

    viewModal.classList.remove('hidden');
    viewModal.classList.add('flex');
    lucide.createIcons();
  }

  function renderModalNotes(id) {
    const notes = sessionNotes[id] || [];
    const list = document.getElementById('modalNotesList');
    list.innerHTML = notes.length
      ? notes.map(n => `<div class="bg-[#F8FAFC] dark:bg-[#121212] rounded-xl px-3 py-2 text-sm text-slate-600 dark:text-slate-300">${n}</div>`).join('')
      : '';
  }

  document.getElementById('modalPostNoteBtn').addEventListener('click', () => {
    if (!currentViewRow) return;
    const input = document.getElementById('modalNoteInput');
    const text = input.value.trim();
    if (!text) return;
    if (!sessionNotes[currentViewRow.id]) sessionNotes[currentViewRow.id] = [];
    sessionNotes[currentViewRow.id].push(text);
    input.value = '';
    renderModalNotes(currentViewRow.id);
  });

  document.getElementById('modalPrintBtn').addEventListener('click', () => window.print());

  document.getElementById('modalEditCaseBtn').addEventListener('click', () => {
    if (!currentViewRow) return;
    closeViewModal();
    openEditModal(currentViewRow.id);
  });

  document.getElementById('modalForwardBtn').addEventListener('click', () => {
    if (!currentViewRow) return;
    const subject = encodeURIComponent(`Fwd: ${currentViewRow.subject || 'Communication'} (#COM-${String(currentViewRow.id).padStart(3, '0')})`);
    window.location.href = `mailto:?subject=${subject}`;
  });

  async function updateCaseStatus(status) {
    if (!currentViewRow) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    try {
      const res = await fetch(`/customer-service/communication-history/${currentViewRow.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({
          date: currentViewRow.date,
          type: currentViewRow.type,
          subject: currentViewRow.subject,
          staff: currentViewRow.staff,
          status: status,
          priority: currentViewRow.priority,
        }),
      });
      if (!res.ok) throw new Error('Update failed');
      window.location.reload();
    } catch (err) {
      alert('Something went wrong updating this case. Please try again.');
      console.error(err);
    }
  }

  document.getElementById('modalResolveBtn').addEventListener('click', () => updateCaseStatus('resolved'));
  document.getElementById('modalCloseCaseBtn').addEventListener('click', () => updateCaseStatus('cancelled'));

  function closeViewModal() {
    viewModal.classList.add('hidden');
    viewModal.classList.remove('flex');
    currentViewRow = null;
  }

  document.getElementById('modalCloseBtn').addEventListener('click', closeViewModal);
  document.getElementById('modalOverlay').addEventListener('click', closeViewModal);

  // ---- Edit Modal logic (now persists to the database) ----
  const editModal = document.getElementById('editModal');

  function openEditModal(id) {
    const r = rows.find(row => row.id == id);
    if (!r) return;

    editingId = id;

    document.getElementById('editCustomerLabel').textContent = r.name;
    document.getElementById('editDate').value = r.date;
    document.getElementById('editType').value = r.type;
    document.getElementById('editSubject').value = r.subject;
    document.getElementById('editStaff').value = r.staff;
    document.getElementById('editStatus').value = r.status;
    document.getElementById('editPriority').value = r.priority;
    document.getElementById('editResp').value = r.resp;

    editModal.classList.remove('hidden');
    editModal.classList.add('flex');
  }

  function closeEditModal() {
    editModal.classList.add('hidden');
    editModal.classList.remove('flex');
    editingId = null;
  }

  document.getElementById('editCloseBtn').addEventListener('click', closeEditModal);
  document.getElementById('editDoneBtn').addEventListener('click', closeEditModal);
  document.getElementById('editOverlay').addEventListener('click', closeEditModal);

  // View-only modal: fields are disabled in the markup and there is no
  // save/submit handler here on purpose — records can no longer be edited
  // from this popup. The PUT /communication-history/{id} route/controller
  // method still exists server-side but nothing in this view calls it.
  document.getElementById('editForm').addEventListener('submit', (e) => e.preventDefault());

  // ---- New Communication Modal logic ----
  const newModal = document.getElementById('newModal');

  function openNewModal() {
    document.getElementById('newForm').reset();
    document.getElementById('newResp').value = 'Pending';
    newModal.classList.remove('hidden');
    newModal.classList.add('flex');
  }

  function closeNewModal() {
    newModal.classList.add('hidden');
    newModal.classList.remove('flex');
  }

  document.getElementById('newCommBtn').addEventListener('click', openNewModal);
  document.getElementById('newCloseBtn').addEventListener('click', closeNewModal);
  document.getElementById('newCancelBtn').addEventListener('click', closeNewModal);
  document.getElementById('newOverlay').addEventListener('click', closeNewModal);

  document.getElementById('newForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const payload = {
      customer_name: document.getElementById('newCustomerName').value.trim(),
      customer_email: document.getElementById('newCustomerEmail').value.trim(),
      date: document.getElementById('newDate').value.trim() || 'Jul 06',
      type: document.getElementById('newType').value,
      subject: document.getElementById('newSubject').value.trim(),
      agent_id: document.getElementById('newAgentId').value,
      ticket_id: document.getElementById('newTicketId').value || null,
      status: document.getElementById('newStatus').value,
      priority: document.getElementById('newPriority').value,
      resp_time: 'Pending',
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    try {
      const res = await fetch('/customer-service/communication-history/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(payload),
      });

      if (!res.ok) throw new Error('Save failed');

      closeNewModal();
      window.location.reload();
    } catch (err) {
      alert('Something went wrong saving this communication. Please try again.');
      console.error(err);
    }
  });

  // ---- Add Follow-up Modal logic ----
  const followUpModal = document.getElementById('followUpModal');

  function populateFollowUpRecordSelect() {
    const select = document.getElementById('followUpRecordId');
    select.innerHTML = '<option value="">Select a customer / record</option>' +
      rows.map(r => `<option value="${r.id}">${r.name} — ${r.subject}</option>`).join('');
  }

  function openFollowUpModal() {
    document.getElementById('followUpForm').reset();
    populateFollowUpRecordSelect();
    followUpModal.classList.remove('hidden');
    followUpModal.classList.add('flex');
  }

  function closeFollowUpModal() {
    followUpModal.classList.add('hidden');
    followUpModal.classList.remove('flex');
  }

  // Add follow-up button removed from UI; modal still available if needed
  document.getElementById('followUpCloseBtn').addEventListener('click', closeFollowUpModal);
  document.getElementById('followUpCancelBtn').addEventListener('click', closeFollowUpModal);
  document.getElementById('followUpOverlay').addEventListener('click', closeFollowUpModal);

  document.getElementById('followUpForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = document.getElementById('followUpRecordId').value;
    const r = rows.find(row => row.id == id);
    if (!r) { alert('Please select a record.'); return; }

    const payload = {
      // update() requires type/status/priority — reuse the record's current values
      // so this call only changes the follow-up fields.
      type: r.type,
      status: r.status,
      priority: r.priority,
      follow_up_date: document.getElementById('followUpDateInput').value.replace('T', ' '),
      follow_up_priority: document.getElementById('followUpPriorityInput').value,
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    try {
      const res = await fetch(`/customer-service/communication-history/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(payload),
      });

      if (!res.ok) throw new Error('Save failed');

      closeFollowUpModal();
      window.location.reload();
    } catch (err) {
      alert('Something went wrong scheduling this follow-up. Please try again.');
      console.error(err);
    }
  });

  // Mobile sidebar toggle
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const menuBtn = document.getElementById('menuBtn');

  function openSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
  }
  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  }
  if (menuBtn) menuBtn.addEventListener('click', openSidebar);
  if (overlay) overlay.addEventListener('click', closeSidebar);

  // ================= Export Modal =================
  const exportModal = document.getElementById('exportModal');
  const exportOptionsScreen = document.getElementById('exportOptionsScreen');
  const exportPreviewScreen = document.getElementById('exportPreviewScreen');
  let selectedFormat = 'csv';

  function openExportModal() {
    exportOptionsScreen.classList.remove('hidden');
    exportPreviewScreen.classList.add('hidden');
    exportModal.classList.remove('hidden');
    exportModal.classList.add('flex');
  }

  function closeExportModal() {
    exportModal.classList.add('hidden');
    exportModal.classList.remove('flex');
  }

  document.getElementById('exportBtnHeader').addEventListener('click', openExportModal);
  document.getElementById('exportBtnTable').addEventListener('click', openExportModal);
  document.getElementById('exportCloseBtn').addEventListener('click', closeExportModal);
  document.getElementById('exportCancelBtn').addEventListener('click', closeExportModal);
  document.getElementById('exportOverlay').addEventListener('click', closeExportModal);
  document.getElementById('previewCloseBtn').addEventListener('click', closeExportModal);

  document.querySelectorAll('.format-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.format-card').forEach(c => {
        c.classList.remove('border-[#10B981]', 'bg-emerald-50/40');
        c.classList.add('border-slate-200');
        c.querySelector('.format-check').classList.add('hidden');
        c.querySelector('.format-check').classList.remove('flex');
      });
      card.classList.remove('border-slate-200');
      card.classList.add('border-[#10B981]', 'bg-emerald-50/40');
      card.querySelector('.format-check').classList.remove('hidden');
      card.querySelector('.format-check').classList.add('flex');
      selectedFormat = card.dataset.format;
    });
  });

  function buildExportData() {
    const includeEmail = document.getElementById('incEmail').checked;
    const includeStaff = document.getElementById('incStaff').checked;
    const includeStatus = document.getElementById('incStatus').checked;
    const includePriority = document.getElementById('incPriority').checked;
    const includeResp = document.getElementById('incResp').checked;

    const sortBy = document.getElementById('exportSortBy').value;
    let data = [...rows];

    if (sortBy === 'Oldest First') data.reverse();
    else if (sortBy === 'Priority (High to Low)') {
      const order = { high: 0, medium: 1, low: 2 };
      data.sort((a, b) => order[a.priority] - order[b.priority]);
    } else if (sortBy === 'Customer Name (A–Z)') {
      data.sort((a, b) => a.name.localeCompare(b.name));
    }

    return data.map(r => {
      const row = { Customer: r.name };
      if (includeEmail) row.Email = r.email;
      row.Date = r.date;
      row.Type = typeText(r.type);
      row.Subject = r.subject;
      if (includeStaff) row.Staff = r.staff;
      if (includeStatus) row.Status = statusText(r.ticketStatus || r.status || '');
      if (includePriority) row.Priority = label(r.priority);
      if (includeResp) row['Resp Time'] = r.resp;
      return row;
    });
  }

  function estimateFileSize(rowCount, format) {
    const perRowKB = format === 'pdf' ? 1.4 : format === 'excel' ? 0.9 : 0.4;
    return Math.max(8, Math.round(rowCount * perRowKB)) + ' KB';
  }

  document.getElementById('exportGoBtn').addEventListener('click', () => {
    const data = buildExportData();
    const totalRecords = rows.length;
    const previewRows = rows.slice(0, 9);

    const tbody = document.getElementById('previewTableBody');
    tbody.innerHTML = previewRows.map((r, i) => `
      <tr>
        <td class="py-2 pr-3 text-slate-400">${i + 1}</td>
        <td class="py-2 pr-3 font-medium text-slate-700 whitespace-nowrap">${r.name}</td>
        <td class="py-2 pr-3 text-slate-500 whitespace-nowrap">${r.email}</td>
        <td class="py-2 pr-3 text-slate-500 whitespace-nowrap">${r.date}</td>
        <td class="py-2 pr-3 text-slate-500 whitespace-nowrap">${typeText(r.type)}</td>
        <td class="py-2 pr-3 text-slate-600 whitespace-nowrap">${r.subject}</td>
        <td class="py-2 pr-3"><span class="px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold whitespace-nowrap ${statusStyles[rowStatus(r)]}">${statusText(r.ticketStatus || r.status || '')}</span></td>
        <td class="py-2 pr-3 text-slate-500 whitespace-nowrap">${r.resp}</td>
      </tr>
    `).join('');

    const ext = selectedFormat === 'excel' ? 'xlsx' : selectedFormat;
    const fileName = `communication_history_${new Date().getFullYear()}.${ext}`;

    document.getElementById('previewTitle').textContent = `${selectedFormat.toUpperCase()} Export Preview`;
    document.getElementById('previewFileName').textContent = fileName;
    document.getElementById('previewFileSize').textContent = estimateFileSize(totalRecords, selectedFormat);
    document.getElementById('previewRecordCount').textContent = totalRecords;
    document.getElementById('previewDownloadLabel').textContent = `Download ${selectedFormat.toUpperCase()}`;

    exportOptionsScreen.classList.add('hidden');
    exportPreviewScreen.classList.remove('hidden');
    lucide.createIcons();
  });

  document.getElementById('previewBackBtn').addEventListener('click', () => {
    exportPreviewScreen.classList.add('hidden');
    exportOptionsScreen.classList.remove('hidden');
  });

  function downloadBlob(blob, filename) {
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  }

  function exportAsCSV(data, fileName) {
    if (!data.length) return;
    const headers = Object.keys(data[0]);
    const csvRows = [
      headers.join(','),
      ...data.map(row => headers.map(h => `"${String(row[h] ?? '').replace(/"/g, '""')}"`).join(',')),
    ];
    const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
    downloadBlob(blob, fileName);
  }

  function exportAsExcel(data, fileName) {
    const ws = XLSX.utils.json_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Communication History');
    XLSX.writeFile(wb, fileName);
  }

  function exportAsPDF(data, fileName) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape' });
    doc.setFontSize(14);
    doc.text('Customer Communication History', 14, 15);

    const headers = data.length ? Object.keys(data[0]) : [];
    const body = data.map(row => headers.map(h => String(row[h] ?? '')));

    doc.autoTable({
      head: [headers],
      body: body,
      startY: 22,
      styles: { fontSize: 8 },
      headStyles: { fillColor: [30, 58, 138] },
    });

    doc.save(fileName);
  }

  document.getElementById('previewDownloadBtn').addEventListener('click', () => {
    const data = buildExportData();
    const ext = selectedFormat === 'excel' ? 'xlsx' : selectedFormat;
    const fileName = `communication_history_${new Date().getFullYear()}.${ext}`;

    if (selectedFormat === 'csv') exportAsCSV(data, fileName);
    else if (selectedFormat === 'excel') exportAsExcel(data, fileName);
    else if (selectedFormat === 'pdf') exportAsPDF(data, fileName);

    closeExportModal();
  });
</script>

</body>
</html>