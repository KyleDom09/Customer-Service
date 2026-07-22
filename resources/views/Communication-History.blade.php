<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Customer Service Dashboard</title>
<script src="{{ asset('vendor/tailwind.js') }}"></script>
<script src="{{ asset('vendor/lucide.min.js') }}"></script>
<script src="{{ asset('vendor/xlsx.min.js') }}"></script>
<script src="{{ asset('vendor/jspdf.min.js') }}"></script>
<script src="{{ asset('vendor/jspdf-autotable.min.js') }}"></script>
<style>
  body { font-family: 'Segoe UI', 'Poppins', sans-serif; }
  ::-webkit-scrollbar { height: 6px; width: 6px; }
  ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
  .sidebar-transition { transition: transform 0.3s ease-in-out; }
</style>
</head>
<body class="bg-[#F8FAFC] text-slate-800">

<!-- Mobile overlay -->
<div id="overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

<div class="flex min-h-screen">

  <!-- Sidebar -->
  @include('partials.sidebar')

  <!-- Main -->
  <div class="flex-1 min-w-0">

    <!-- Top Navbar -->
    <header class="h-20 bg-white flex items-center justify-between px-4 sm:px-8 sticky top-0 z-20 border-b border-slate-100">
      <button id="menuBtn" class="lg:hidden text-slate-600">
        <i data-lucide="menu" class="w-6 h-6"></i>
      </button>

      <div class="hidden md:flex flex-1 justify-center px-4">
        <div class="relative w-full max-w-md">
          <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
          <input type="text" placeholder="Search..." class="w-full bg-[#F8FAFC] border border-[#E5E7EB] rounded-full py-2.5 pl-10 pr-4 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
        </div>
      </div>

      <div class="flex items-center gap-4 sm:gap-5">
        <button class="relative text-slate-500 hover:text-slate-700">
          <i data-lucide="bell" class="w-5 h-5"></i>
          <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-[#10B981] rounded-full"></span>
        </button>
        <div class="flex items-center gap-2 cursor-pointer">
          <img src="/assets/benju.jpg" alt="Benju Guzman" class="w-9 h-9 rounded-full object-cover">
          <span class="hidden sm:block text-sm font-semibold text-slate-700">Benju Guzman</span>
          <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 hidden sm:block"></i>
        </div>
      </div>
    </header>

    <main class="p-4 sm:p-8">

      <!-- Page Header -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-2">
        <div>
          <h2 class="text-2xl sm:text-3xl font-bold text-[#1E3A8A]">Customer Communication History</h2>
          <p class="text-slate-500 text-sm mt-1">Manage all customer interactions in one place.</p>
        </div>
        <div class="flex items-center gap-3">
          <button id="exportBtnHeader" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">
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
      <div class="flex items-center gap-2 text-sm text-slate-400 mb-6">
        <i data-lucide="calendar-clock" class="w-4 h-4"></i>
        <span id="liveDateTime">Loading date &amp; time...</span>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
            <i data-lucide="layers" class="w-6 h-6 text-[#10B981]"></i>
          </div>
          <div>
            <p class="text-sm text-slate-500">Total Logs</p>
            <p id="statTotalLogs" class="text-2xl font-bold text-[#1E3A8A]">0</p>
            <p class="text-xs text-slate-400 mt-0.5">All time records</p>
          </div>
        </div>

        <button id="statPendingCard" type="button" class="text-left bg-white rounded-2xl shadow-sm p-6 flex items-center gap-4 hover:shadow-md transition-shadow cursor-pointer">
          <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center shrink-0">
            <i data-lucide="hourglass" class="w-6 h-6 text-[#F59E0B]"></i>
          </div>
          <div>
            <p class="text-sm text-slate-500">Pending</p>
            <p id="statPending" class="text-2xl font-bold text-[#1E3A8A]">0</p>
            <p class="text-xs text-slate-400 mt-0.5">Awaiting response</p>
          </div>
        </button>

        <button id="statTodayCard" type="button" class="text-left bg-white rounded-2xl shadow-sm p-6 flex items-center gap-4 hover:shadow-md transition-shadow cursor-pointer">
          <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
            <i data-lucide="calendar-check-2" class="w-6 h-6 text-[#10B981]"></i>
          </div>
          <div>
            <p class="text-sm text-slate-500">Today</p>
            <p id="statToday" class="text-2xl font-bold text-[#1E3A8A]">0</p>
            <p class="text-xs text-slate-400 mt-0.5">Logs added today</p>
          </div>
        </button>

        <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
            <i data-lucide="check-circle-2" class="w-6 h-6 text-[#10B981]"></i>
          </div>
          <div>
            <p class="text-sm text-slate-500">Completed</p>
            <p id="statCompletedRate" class="text-2xl font-bold text-[#1E3A8A]">0%</p>
            <p class="text-xs text-slate-400 mt-0.5">Resolution rate</p>
          </div>
        </div>

      </div>

      <!-- Communication History -->
      <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 pb-4">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h3 class="text-xl font-bold text-slate-800">Communication History</h3>
              <p class="text-sm text-slate-400 mt-1"><span id="showingCount">0</span> of <span id="totalCount">0</span> total records</p>
            </div>
            <button id="exportBtnTable" class="flex items-center gap-2 px-4 py-2 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors self-start sm:self-auto">
              <i data-lucide="file-down" class="w-4 h-4"></i>
              Export CSV
            </button>
          </div>

          <!-- Filter Tabs -->
          <div class="flex items-center gap-2 mt-6 overflow-x-auto pb-1">
            <button data-filter="all" class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-semibold bg-[#10B981] text-white transition-colors">All</button>
            <button data-filter="completed" class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">Completed</button>
            <button data-filter="pending" class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">Pending</button>
            <button data-filter="resolved" class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">Resolved</button>
            <button data-filter="cancelled" class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">Cancelled</button>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full min-w-[900px] text-sm">
            <thead>
              <tr class="bg-[#F8FAFC] text-slate-400 text-xs uppercase tracking-wide">
                <th class="text-left font-semibold px-8 py-3">Customer</th>
                <th class="text-left font-semibold px-4 py-3">Date</th>
                <th class="text-left font-semibold px-4 py-3">Type</th>
                <th class="text-left font-semibold px-4 py-3">Subject</th>
                <th class="text-left font-semibold px-4 py-3">Staff</th>
                <th class="text-left font-semibold px-4 py-3">Status</th>
                <th class="text-left font-semibold px-4 py-3">Priority</th>
                <th class="text-left font-semibold px-4 py-3">Resp Time</th>
                <th class="text-left font-semibold px-8 py-3">Action</th>
              </tr>
            </thead>
            <tbody id="tableBody" class="divide-y divide-slate-100">
              <!-- rows injected by JS -->
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- View Details Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div id="modalOverlay" class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white w-full max-w-2xl max-h-[85vh] rounded-3xl shadow-xl flex flex-col overflow-hidden">

    <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div id="modalAvatar" class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-semibold"></div>
        <div>
          <p id="modalName" class="font-semibold text-slate-800"></p>
          <p id="modalEmail" class="text-xs text-slate-400"></p>
        </div>
      </div>
      <button id="modalCloseBtn" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <div class="overflow-y-auto px-6 sm:px-8 py-6 space-y-8">

      <!-- Transaction Summary -->
      <div>
        <h4 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-3">Transaction</h4>
        <div class="bg-[#F8FAFC] rounded-2xl p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
          <div><p class="text-slate-400 text-xs mb-1">Subject</p><p id="modalSubject" class="font-medium text-slate-700"></p></div>
          <div><p class="text-slate-400 text-xs mb-1">Staff</p><p id="modalStaff" class="font-medium text-slate-700"></p></div>
          <div><p class="text-slate-400 text-xs mb-1">Status</p><span id="modalStatus" class="px-2 py-0.5 rounded-full text-xs font-semibold"></span></div>
          <div><p class="text-slate-400 text-xs mb-1">Resp Time</p><p id="modalResp" class="font-medium text-slate-700"></p></div>
        </div>
      </div>

      <!-- Chat History -->
      <div>
        <h4 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-3">Chat History</h4>
        <div id="modalChat" class="space-y-3"></div>
      </div>

    </div>
  </div>
</div>

<!-- Edit Row Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div id="editOverlay" class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white w-full max-w-lg rounded-3xl shadow-xl flex flex-col overflow-hidden">

    <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100">
      <div>
        <p class="font-semibold text-slate-800">Edit Communication</p>
        <p id="editCustomerLabel" class="text-xs text-slate-400"></p>
      </div>
      <button id="editCloseBtn" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form id="editForm" class="px-6 sm:px-8 py-6 space-y-4">

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-xs font-medium text-slate-500 mb-1 block">Date</label>
          <input id="editDate" type="text" placeholder="e.g. Jun 22" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
        </div>
        <div>
          <label class="text-xs font-medium text-slate-500 mb-1 block">Type</label>
          <select id="editType" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
            <option value="mail">Email</option>
            <option value="phone">Phone</option>
            <option value="chat">Chat</option>
          </select>
        </div>
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Subject</label>
        <input id="editSubject" type="text" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Staff</label>
        <input id="editStaff" type="text" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-xs font-medium text-slate-500 mb-1 block">Status</label>
          <select id="editStatus" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
            <option value="resolved">Resolved</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label class="text-xs font-medium text-slate-500 mb-1 block">Priority</label>
          <select id="editPriority" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Resp Time</label>
        <input id="editResp" type="text" placeholder="e.g. 2h 15m or Pending" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
      </div>

      <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" id="editCancelBtn" class="px-4 py-2.5 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">Cancel</button>
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">Save changes</button>
      </div>

    </form>
  </div>
</div>

<!-- New Communication Modal -->
<div id="newModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div id="newOverlay" class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white w-full max-w-lg max-h-[90vh] rounded-3xl shadow-xl flex flex-col overflow-hidden">

    <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100">
      <p class="font-semibold text-slate-800">New Communication</p>
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
        <input id="newResp" type="text" placeholder="e.g. Pending" value="Pending" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
      </div>

      <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" id="newCancelBtn" class="px-4 py-2.5 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">Cancel</button>
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">Add Communication</button>
      </div>

    </form>
  </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div id="exportOverlay" class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white w-full max-w-2xl max-h-[90vh] rounded-3xl shadow-xl flex flex-col overflow-hidden">

    <!-- ===== Screen 1: Export Options ===== -->
    <div id="exportOptionsScreen">
      <div class="flex items-start justify-between px-6 sm:px-8 py-6 border-b border-slate-100">
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
        <button type="button" id="exportCancelBtn" class="px-5 py-2.5 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">Cancel</button>
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
        <button type="button" id="previewBackBtn" class="px-5 py-2.5 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">Back</button>
        <button type="button" id="previewDownloadBtn" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
          <i data-lucide="download" class="w-4 h-4"></i> <span id="previewDownloadLabel">Download CSV</span>
        </button>
      </div>
    </div>

  </div>
</div>

{{-- Chatbot Widget - DISABLED FOR NOW - re-enable when ready

<div id="chatbotWidget" class="fixed bottom-6 right-6 z-50 flex flex-col items-end">

  <!-- Chat Panel -->
  <div id="chatPanel" class="hidden mb-4 w-[350px] sm:w-[380px] max-w-[90vw] h-[520px] max-h-[75vh] bg-white rounded-3xl shadow-2xl flex flex-col overflow-hidden border border-slate-100">

    <!-- Header -->
    <div class="bg-[#1E3A8A] px-5 py-4 flex items-center gap-3">
      <div class="relative w-10 h-10 rounded-full bg-white flex items-center justify-center shrink-0 overflow-hidden">
        <img src="/assets/chatbot-avatar.png" alt="Navi Avatar" class="w-full h-full object-cover" onerror="botAvatarFallback(this)">
      </div>
      <div class="flex-1">
        <p class="text-white text-sm font-semibold">Navi — Support Assistant</p>
        <p class="text-blue-200 text-xs flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> Online
        </p>
      </div>
      <button id="chatCloseBtn" class="text-blue-200 hover:text-white">
        <i data-lucide="x" class="w-5 h-5"></i>
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
      <button type="submit" class="w-10 h-10 rounded-full bg-[#10B981] flex items-center justify-center text-white hover:bg-emerald-600 transition-colors shrink-0">
        <i data-lucide="send" class="w-4 h-4"></i>
      </button>
    </form>
  </div>

  <!-- Floating Bubble Button -->
  <button id="chatToggleBtn" class="w-16 h-16 rounded-full bg-white shadow-xl flex items-center justify-center hover:scale-105 transition-transform relative overflow-hidden">
    <span class="absolute top-1 right-1 w-3.5 h-3.5 bg-[#10B981] rounded-full border-2 border-white z-10"></span>
    <img src="/assets/chatbot-avatar.png" alt="Chatbot" class="w-full h-full object-cover" onerror="botAvatarFallback(this)">
  </button>
</div>

--}}

<script>
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
        return [
            'id'       => $c->id,
            'initials' => getPhpInitials($c->customer_name ?? null),
            'color'    => $color,
            'name'     => $c->customer_name ?? 'Unknown',
            'email'    => $c->customer_email ?? '',
            'date'     => $c->date ?? '',
            'type'     => $c->type ?? 'mail',
            'subject'  => $c->subject ?? '',
            'staff'    => $c->staff ?? '',
            'status'   => $c->status ?? 'pending',
            'priority' => $c->priority ?? 'medium',
            'resp'     => $c->resp_time ?? '',
        ];
    }, isset($communications) ? $communications->all() : []));
  ?>;


  // Fallback: if /assets/chatbot-avatar.png is missing, show a built-in robot icon instead of a broken image
  function botAvatarFallback(imgEl) {
    imgEl.onerror = null; // prevent infinite loop if fallback itself fails
    const wrapper = imgEl.parentElement;
    wrapper.innerHTML = `
      <svg viewBox="0 0 64 64" class="w-full h-full p-1.5">
        <defs>
          <linearGradient id="botRingGradFallback-${Math.random().toString(36).slice(2)}" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#EC4899"/>
            <stop offset="50%" stop-color="#3B82F6"/>
            <stop offset="100%" stop-color="#06B6D4"/>
          </linearGradient>
        </defs>
        <rect x="6" y="16" width="52" height="32" rx="16" fill="#3B82F6"/>
        <rect x="9" y="19" width="46" height="26" rx="13" fill="#0B1220"/>
        <circle cx="24" cy="32" r="5.5" fill="#ffffff"/>
        <circle cx="40" cy="32" r="5.5" fill="#ffffff"/>
      </svg>`;
  }

 
  const typeIcon = { mail: 'mail', phone: 'phone', chat: 'message-circle' };

  const statusStyles = {
    completed: 'bg-green-100 text-green-600',
    pending: 'bg-yellow-100 text-yellow-600',
    resolved: 'bg-blue-100 text-blue-600',
    cancelled: 'bg-red-100 text-red-600',
  };

  const priorityStyles = {
    high: 'bg-red-100 text-red-500',
    medium: 'bg-yellow-100 text-yellow-500',
    low: 'bg-green-100 text-green-500',
  };

  function label(s) { if (!s) return 'N/A'; return s.charAt(0).toUpperCase() + s.slice(1); }

  // Sample chat history per customer (keyed by name) — replace/extend with real data later
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

  // Sample fallback chat for any customer not explicitly listed above
  const defaultChat = [
    { from: 'customer', text: 'Hello, I have a question about my recent request.', time: '9:00 AM' },
    { from: 'staff', text: 'Hi! Thanks for reaching out, let me look into that.', time: '9:05 AM' },
  ];

  function renderRows(filter) {
    const tbody = document.getElementById('tableBody');
    let filtered;
    if (filter === 'all') filtered = rows;
    else if (filter === 'today') filtered = rows.filter(r => r.date === getTodayPHPStyle());
    else filtered = rows.filter(r => r.status === filter);

    document.getElementById('showingCount').textContent = filtered.length;
    document.getElementById('totalCount').textContent = rows.length;

    tbody.innerHTML = filtered.map(r => `
      <tr class="hover:bg-slate-50 transition-colors">
        <td class="px-8 py-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-semibold shrink-0" style="background:${r.color}">${r.initials}</div>
            <div>
              <p class="font-semibold text-slate-700">${r.name}</p>
              <p class="text-xs text-slate-400">${r.email}</p>
            </div>
          </div>
        </td>
        <td class="px-4 py-4 text-slate-500 whitespace-nowrap">${r.date}</td>
        <td class="px-4 py-4 text-slate-400"><i data-lucide="${typeIcon[r.type]}" class="w-4 h-4"></i></td>
        <td class="px-4 py-4 text-slate-600 whitespace-nowrap">${r.subject}</td>
        <td class="px-4 py-4 text-slate-500 whitespace-nowrap">${r.staff}</td>
        <td class="px-4 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap ${statusStyles[r.status]}">${label(r.status)}</span></td>
        <td class="px-4 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap ${priorityStyles[r.priority]}">${label(r.priority)}</span></td>
        <td class="px-4 py-4 text-slate-500 whitespace-nowrap">${r.resp}</td>
        <td class="px-8 py-4">
          <div class="flex items-center gap-2">
            <button class="view-btn px-3 py-1.5 rounded-lg border border-[#10B981] text-[#10B981] text-xs font-semibold hover:bg-emerald-50 transition-colors whitespace-nowrap" data-name="${r.name}">View</button>
            <button class="edit-btn px-3 py-1.5 rounded-lg border border-[#CBD5E1] text-slate-600 text-xs font-semibold hover:bg-slate-50 transition-colors whitespace-nowrap" data-id="${r.id}" data-name="${r.name}">Edit</button>
          </div>
        </td>
      </tr>
    `).join('');
    lucide.createIcons();

    // Wire up View buttons for the currently rendered rows
    document.querySelectorAll('.view-btn').forEach(btn => {
      btn.addEventListener('click', () => openViewModal(btn.dataset.name));
    });

    // Wire up three-dot Edit buttons for the currently rendered rows
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', () => openEditModal(btn.dataset.id));
    });
  }

  // ================= Task 1: Accurate Stats =================
  // Matches PHP's now()->format('M d') used when saving new communications,
  // so "Today" comparisons line up with what's actually stored in the date column.
  function getTodayPHPStyle() {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const d = new Date();
    const month = months[d.getMonth()];
    const day = String(d.getDate()).padStart(2, '0');
    return `${month} ${day}`;
  }

  function updateStats() {
    const total = rows.length;
    const pendingCount = rows.filter(r => r.status === 'pending').length;
    const todayCount = rows.filter(r => r.date === getTodayPHPStyle()).length;
    const resolvedCount = rows.filter(r => r.status === 'completed' || r.status === 'resolved').length;
    const completedRate = total > 0 ? Math.round((resolvedCount / total) * 100) : 0;

    document.getElementById('statTotalLogs').textContent = total.toLocaleString();
    document.getElementById('statPending').textContent = pendingCount;
    document.getElementById('statToday').textContent = todayCount;
    document.getElementById('statCompletedRate').textContent = completedRate + '%';
  }

  updateStats();

  // Clicking the Pending stat card filters the table to pending communications
  document.getElementById('statPendingCard').addEventListener('click', () => {
    setActiveFilterTab('pending');
  });

  // Clicking the Today stat card filters the table to only today's logs
  document.getElementById('statTodayCard').addEventListener('click', () => {
    setActiveFilterTab('today');
  });

  // Shared helper: activates the matching filter tab visually (if it exists) and re-renders
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
    renderRows(filterValue);
  }

  // ================= Task 2: Live Date & Time =================
  function updateLiveDateTime() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    const timeStr = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true });
    document.getElementById('liveDateTime').textContent = `${dateStr} — ${timeStr}`;
  }

  updateLiveDateTime();
  setInterval(updateLiveDateTime, 1000);

  renderRows('all');

  // ---- View Modal logic ----
  const viewModal = document.getElementById('viewModal');

  function openViewModal(name) {
    const r = rows.find(row => row.name === name);
    if (!r) return;

    document.getElementById('modalAvatar').textContent = r.initials;
    document.getElementById('modalAvatar').style.background = r.color;
    document.getElementById('modalName').textContent = r.name;
    document.getElementById('modalEmail').textContent = r.email;
    document.getElementById('modalSubject').textContent = r.subject;
    document.getElementById('modalStaff').textContent = r.staff;
    document.getElementById('modalResp').textContent = r.resp;

    const statusEl = document.getElementById('modalStatus');
    statusEl.textContent = label(r.status);
    statusEl.className = 'px-2 py-0.5 rounded-full text-xs font-semibold ' + statusStyles[r.status];

    const chat = chatHistory[r.name] || defaultChat;
    document.getElementById('modalChat').innerHTML = chat.map(m => `
      <div class="flex ${m.from === 'staff' ? 'justify-end' : 'justify-start'}">
        <div class="max-w-[75%] ${m.from === 'staff' ? 'bg-[#1E3A8A] text-white' : 'bg-slate-100 text-slate-700'} rounded-2xl px-4 py-2.5 text-sm">
          <p>${m.text}</p>
          <p class="text-[10px] mt-1 ${m.from === 'staff' ? 'text-blue-200' : 'text-slate-400'}">${m.time}</p>
        </div>
      </div>
    `).join('');

    viewModal.classList.remove('hidden');
    viewModal.classList.add('flex');
  }

  function closeViewModal() {
    viewModal.classList.add('hidden');
    viewModal.classList.remove('flex');
  }

  document.getElementById('modalCloseBtn').addEventListener('click', closeViewModal);
  document.getElementById('modalOverlay').addEventListener('click', closeViewModal);

  // ---- Edit Modal logic ----
  const editModal = document.getElementById('editModal');
  let currentFilter = 'all';
  let editingName = null;

  function openEditModal(id) {
    console.log('Edit clicked:', id);

    const r = rows.find(row => row.id == id);
    if (!r) {
        console.log('Record not found');
        return;
    }

    editingName = id;

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
    editingName = null;
  }

  document.getElementById('editCloseBtn').addEventListener('click', closeEditModal);
  document.getElementById('editCancelBtn').addEventListener('click', closeEditModal);
  document.getElementById('editOverlay').addEventListener('click', closeEditModal);

  document.getElementById('editForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const r = rows.find(row => row.id == editingName);
    if (!r) return;

    // Save the edited values back into the row's data
    r.date = document.getElementById('editDate').value;
    r.type = document.getElementById('editType').value;
    r.subject = document.getElementById('editSubject').value;
    r.staff = document.getElementById('editStaff').value;
    r.status = document.getElementById('editStatus').value;
    r.priority = document.getElementById('editPriority').value;
    r.resp = document.getElementById('editResp').value;

    closeEditModal();
    renderRows(currentFilter); // refresh table with updated data
    updateStats(); // recalculate stat cards since status/date may have changed
  });

  // ---- New Communication Modal logic ----
  const newModal = document.getElementById('newModal');
  const avatarColors = ['#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444'];

  function getInitials(name) {
    return name.trim().split(/\s+/).map(w => w[0]).join('').slice(0, 2).toUpperCase();
  }

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
    resp_time: document.getElementById('newResp').value.trim() || 'Pending',
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
      window.location.reload(); // reload so the table pulls fresh data from the database
    } catch (err) {
      alert('Something went wrong saving this communication. Please try again.');
      console.error(err);
    }
  });

  document.querySelectorAll('.filter-tab').forEach(btn => {
    btn.addEventListener('click', () => {
      setActiveFilterTab(btn.dataset.filter);
    });
  });

  // Mobile sidebar toggle
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const menuBtn = document.getElementById('menuBtn');

  function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
  }
  function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  }
  menuBtn.addEventListener('click', openSidebar);
  overlay.addEventListener('click', closeSidebar);

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

  // Format card selection
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

  // Build the export dataset based on checked "Include" options
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
      row.Type = label(r.type === 'mail' ? 'Email' : r.type);
      row.Subject = r.subject;
      if (includeStaff) row.Staff = r.staff;
      if (includeStatus) row.Status = label(r.status);
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
    const totalRecords = 248; // matches "Showing 6 of 248 total records"
    const previewRows = rows.slice(0, 9);

    // Populate preview table (always shows the friendly columns regardless of format)
    const tbody = document.getElementById('previewTableBody');
    tbody.innerHTML = previewRows.map((r, i) => `
      <tr>
        <td class="py-2 pr-3 text-slate-400">${i + 1}</td>
        <td class="py-2 pr-3 font-medium text-slate-700 whitespace-nowrap">${r.name}</td>
        <td class="py-2 pr-3 text-slate-500 whitespace-nowrap">${r.email}</td>
        <td class="py-2 pr-3 text-slate-500 whitespace-nowrap">${r.date}</td>
        <td class="py-2 pr-3 text-slate-500 whitespace-nowrap">${label(r.type === 'mail' ? 'Email' : r.type)}</td>
        <td class="py-2 pr-3 text-slate-600 whitespace-nowrap">${r.subject}</td>
        <td class="py-2 pr-3"><span class="px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold whitespace-nowrap ${statusStyles[r.status]}">${label(r.status)}</span></td>
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

  // ================= Chatbot Widget =================
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

  // Shows a realistic "typing..." delay before the bot message appears
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
    botSay('Is there anything else I can help you with?', 600).then(() => {
      setOptions([
        { label: 'Yes, I have another question', action: showMainOptions },
        { label: 'No, that\'s all, thank you', action: endChat },
      ]);
    });
  }

  function endChat() {
    botSay(
      "I'm glad I could help! If you have any further concerns, feel free to reach out to our support team at " +
      "<span class='font-semibold text-[#1E3A8A]'>support@customerservice.com</span>. Have a great day! 😊",
      600
    );
  }

  function showMainOptions() {
    botSay('Sure! What would you like help with?', 500).then(() => {
      setOptions([
        { label: '📋 Communication History', action: handleCommHistory },
        { label: '🎫 Ticket Status', action: handleTicketStatus },
        { label: '❓ General Support', action: handleGeneralSupport },
      ]);
    });
  }

  function handleCommHistory() {
    botSay(
      "Our <span class='font-semibold'>Customer Communication History</span> keeps a complete record of every interaction " +
      "we've had with a customer — emails, calls, and live chats all in one place. It shows the subject, assigned staff, " +
      "current status, priority level, and response time for each communication. This helps our team follow up quickly " +
      "and avoid repeating questions you've already answered."
    ).then(askAnythingElse);
  }

  function handleTicketStatus() {
    botSay(
      "You can track your ticket status directly from the Communication History table — look for the " +
      "<span class='font-semibold'>Status</span> and <span class='font-semibold'>Priority</span> columns. " +
      "Tickets move through Pending → Completed/Resolved, and high priority issues are handled first."
    ).then(askAnythingElse);
  }

  function handleGeneralSupport() {
    botSay(
      "No problem! You can type your question below and I'll do my best to help, or one of our support staff " +
      "can follow up with you directly."
    ).then(askAnythingElse);
  }

  function handleBenjuQuestion() {
    botSay(
      "Benju Guzman is our <span class='font-semibold'>Tech IT Support</span> here — Benju handles the technical side of things and " +
      "is also the one who put together the <span class='font-semibold'>Customer Communication History</span> feature, the system " +
      "that organizes and tracks all customer interactions in this dashboard so nothing falls through the cracks."
    ).then(askAnythingElse);
  }

  function handleContactQuestion() {
    botSay(
      "You can reach our support team anytime at <span class='font-semibold text-[#1E3A8A]'>support@customerservice.com</span>, " +
      "or use the 'New Communication' button on the dashboard to log a new request."
    ).then(askAnythingElse);
  }

  function handleSiteCreatorQuestion() {
    botSay(
      "This site was created by <span class='font-semibold'>Benju Guzman</span>, our <span class='font-semibold'>Tech IT Support</span>. " +
      "Benju built this Customer Service dashboard and also put together me — this chat assistant — to help customers and staff " +
      "get quick answers anytime."
    ).then(askAnythingElse);
  }

  function handleCreatorQuestion() {
    botSay(
      "Great question! I'm <span class='font-semibold'>Navi</span>, the virtual assistant built into this Customer Service " +
      "dashboard. I was created by <span class='font-semibold'>Benju Guzman</span>, our <span class='font-semibold'>Tech IT Support</span>, " +
      "as part of the Customer Communication History system, so customers and staff have a quick way to get answers without " +
      "always waiting on a live agent."
    ).then(askAnythingElse);
  }

  function handleSystemQuestion() {
    botSay(
      "I work by matching what you type to a set of support topics I know about — like communication history, ticket status, " +
      "or contacting our team — and I reply with the most relevant answer. If you tap one of the quick-reply buttons, I'll " +
      "guide you straight there. And if I don't recognize something you typed, I'll offer you the main topics again so " +
      "you're never stuck. For anything more complex, I'll always point you to a real support staff member. 🙂"
    ).then(askAnythingElse);
  }

  function handleFreeText(text) {
    const t = text.toLowerCase();

    if (t.includes('who made this site') || t.includes('who created this site') || t.includes('who built this site') ||
        t.includes('who made this dashboard') || t.includes('who created this dashboard') || t.includes('who made this website') ||
        t.includes('who created this website')) {
      handleSiteCreatorQuestion();
    } else if (t.includes('who made you') || t.includes('who created you') || t.includes('who built you') || t.includes('created you')) {
      handleCreatorQuestion();
    } else if (t.includes('how do you work') || t.includes('how does this work') || t.includes('how you work') ||
               t.includes('how does your system') || t.includes('how do this system') || t.includes('how it work') ||
               (t.includes('system') && t.includes('work'))) {
      handleSystemQuestion();
    } else if (t.includes('staff') || t.includes('benju') || t.includes('it support') || t.includes('tech support')) {
      handleBenjuQuestion();
    } else if (t.includes('communication history') || t.includes('customer communication') || (t.includes('customer') && t.includes('work'))) {
      handleCommHistory();
    } else if (t.includes('ticket') || t.includes('status')) {
      handleTicketStatus();
    } else if (t.includes('email') || t.includes('contact') || t.includes('reach')) {
      handleContactQuestion();
    } else if (t.includes('thank') || t.includes('bye')) {
      endChat();
    } else {
      botSay(
        "Hmm, I'm just a local assistant here on this dashboard, so I might not have the answer to that one. 🙂 " +
        "But here's how I can help — pick an option below, or try asking in a different way!"
      ).then(() => {
        setOptions([
          { label: '📋 Communication History', action: handleCommHistory },
          { label: '🎫 Ticket Status', action: handleTicketStatus },
          { label: '❓ General Support', action: handleGeneralSupport },
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
        "Hello! 👋 I'm <span class='font-semibold'>Navi</span>, your customer service chat assistant. How can I help you today?",
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
</script>

</body>
</html>