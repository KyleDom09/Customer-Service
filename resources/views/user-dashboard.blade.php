<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>My Support Dashboard</title>
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
<style>
  body { font-family: 'Segoe UI', 'Poppins', sans-serif; }
  ::-webkit-scrollbar { height: 6px; width: 6px; }
  ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
</style>
</head>
<body class="bg-[#F8FAFC] text-slate-800">

<div class="flex min-h-screen">

  <!-- Simple top-only layout for customer view (no admin sidebar) -->
  <div class="flex-1 min-w-0">

    <!-- Top Navbar -->
    <header class="h-20 bg-white flex items-center justify-between px-4 sm:px-8 sticky top-0 z-20 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-[#1E3A8A] flex items-center justify-center">
          <i data-lucide="life-buoy" class="w-5 h-5 text-white"></i>
        </div>
        <div>
          <p class="font-bold text-slate-800 leading-tight">My Support Dashboard</p>
          <p class="text-xs text-slate-400">Welcome back, {{ auth()->user()->name }}</p>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
          <div class="w-9 h-9 rounded-full bg-[#1E3A8A] text-white flex items-center justify-center font-semibold text-sm">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </div>
          <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ auth()->user()->name }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm text-red-500 hover:bg-red-50 transition-colors">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Log out</span>
          </button>
        </form>
      </div>
    </header>

    <main class="p-4 sm:p-8 max-w-6xl mx-auto">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
          <h2 class="text-2xl sm:text-3xl font-bold text-[#1E3A8A]">Hi, {{ explode(' ', auth()->user()->name)[0] }} 👋</h2>
          <p class="text-slate-500 text-sm mt-1">Track your support requests and conversation history here.</p>
        </div>
        <button id="newTicketBtn" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors shadow-sm shadow-emerald-200 self-start">
          <i data-lucide="plus" class="w-4 h-4"></i>
          File a New Concern
        </button>
      </div>

      <!-- Quick Links -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
        <a href="{{ url('/customer-service/self-service') }}" class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-4 hover:shadow-md hover:-translate-y-0.5 transition-all">
          <div class="w-12 h-12 rounded-xl bg-[#1E3A8A]/10 flex items-center justify-center shrink-0">
            <i data-lucide="life-buoy" class="w-6 h-6 text-[#1E3A8A]"></i>
          </div>
          <div>
            <p class="font-bold text-slate-800">Self-Service Portal</p>
            <p class="text-sm text-slate-400">Browse help articles, view billing items, or request a refund.</p>
          </div>
          <i data-lucide="chevron-right" class="w-5 h-5 text-slate-300 ml-auto"></i>
        </a>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <div class="w-11 h-11 rounded-xl bg-[#1E3A8A]/10 flex items-center justify-center mb-3">
            <i data-lucide="ticket" class="w-5 h-5 text-[#1E3A8A]"></i>
          </div>
          <p class="text-3xl font-bold text-[#1E3A8A]">{{ $tickets->count() }}</p>
          <p class="text-sm text-slate-500 font-medium mt-1">Total Tickets Filed</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center mb-3">
            <i data-lucide="clock" class="w-5 h-5 text-amber-600"></i>
          </div>
          <p class="text-3xl font-bold text-[#1E3A8A]">{{ $tickets->whereIn('status', ['OPEN', 'PENDING', 'IN PROGRESS'])->count() }}</p>
          <p class="text-sm text-slate-500 font-medium mt-1">Currently Open</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center mb-3">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-[#10B981]"></i>
          </div>
          <p class="text-3xl font-bold text-[#1E3A8A]">{{ $tickets->whereIn('status', ['RESOLVED', 'CLOSED'])->count() }}</p>
          <p class="text-sm text-slate-500 font-medium mt-1">Resolved / Closed</p>
        </div>
      </div>

      <!-- My Tickets -->
      <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-8">
        <div class="p-6 sm:p-8 pb-4 flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold text-slate-800">My Tickets</h3>
            <p class="text-sm text-slate-400 mt-1">All the concerns you've filed with our support team.</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[700px] text-sm">
            <thead>
              <tr class="bg-[#F8FAFC] text-slate-400 text-xs uppercase tracking-wide">
                <th class="text-left font-semibold px-8 py-3">Ticket #</th>
                <th class="text-left font-semibold px-4 py-3">Subject</th>
                <th class="text-left font-semibold px-4 py-3">Category</th>
                <th class="text-left font-semibold px-4 py-3">Priority</th>
                <th class="text-left font-semibold px-4 py-3">Status</th>
                <th class="text-left font-semibold px-8 py-3">Filed</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse($tickets as $ticket)
              <tr onclick="openChatDrawer({{ $ticket->id }}, '{{ $ticket->ticket_number }}', '{{ $ticket->status }}')" class="hover:bg-slate-50/50 transition-colors cursor-pointer">
                <td class="px-8 py-4 font-medium text-slate-500 whitespace-nowrap">{{ $ticket->ticket_number }}</td>
                <td class="px-4 py-4 text-slate-700 font-medium">{{ $ticket->subject }}</td>
                <td class="px-4 py-4 text-slate-500">{{ $ticket->category }}</td>
                <td class="px-4 py-4">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold border tracking-wider
                    {{ $ticket->priority == 'CRITICAL' ? 'bg-red-50 text-red-600 border-red-200' : '' }}
                    {{ $ticket->priority == 'HIGH' ? 'bg-orange-50 text-orange-600 border-orange-200' : '' }}
                    {{ $ticket->priority == 'MEDIUM' ? 'bg-amber-50 text-amber-600 border-amber-200' : '' }}
                    {{ $ticket->priority == 'LOW' ? 'bg-slate-100 text-slate-500 border-slate-200' : '' }}">
                    {{ $ticket->priority }}
                  </span>
                </td>
                <td class="px-4 py-4">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider
                    {{ $ticket->status == 'OPEN' ? 'bg-blue-50 text-blue-600 border border-blue-100' : '' }}
                    {{ $ticket->status == 'IN PROGRESS' ? 'bg-purple-50 text-purple-600 border border-purple-100' : '' }}
                    {{ $ticket->status == 'PENDING' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                    {{ $ticket->status == 'RESOLVED' ? 'bg-green-50 text-green-600 border border-green-100' : '' }}
                    {{ $ticket->status == 'CLOSED' ? 'bg-slate-100 text-slate-400 border border-slate-200' : '' }}
                    {{ $ticket->status == 'CANCELLED' ? 'bg-red-50 text-red-500 border border-red-100' : '' }}">
                    {{ $ticket->status }}
                  </span>
                </td>
                <td class="px-8 py-4 text-slate-400 whitespace-nowrap">{{ $ticket->created_at->diffForHumans() }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="px-8 py-10 text-center text-slate-400">
                  You haven't filed any tickets yet. Click "File a New Concern" to get started.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- My Communication History -->
      <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 pb-4">
          <h3 class="text-xl font-bold text-slate-800">My Communication History</h3>
          <p class="text-sm text-slate-400 mt-1">Every interaction you've had with our support staff.</p>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[700px] text-sm">
            <thead>
              <tr class="bg-[#F8FAFC] text-slate-400 text-xs uppercase tracking-wide">
                <th class="text-left font-semibold px-8 py-3">Date</th>
                <th class="text-left font-semibold px-4 py-3">Channel</th>
                <th class="text-left font-semibold px-4 py-3">Subject</th>
                <th class="text-left font-semibold px-4 py-3">Staff</th>
                <th class="text-left font-semibold px-8 py-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse($communications as $comm)
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-8 py-4 text-slate-500 whitespace-nowrap">{{ $comm->date }}</td>
                <td class="px-4 py-4 text-slate-600 capitalize">{{ $comm->type }}</td>
                <td class="px-4 py-4 text-slate-700">{{ $comm->subject }}</td>
                <td class="px-4 py-4 text-slate-500">{{ $comm->staff ?: 'Unassigned' }}</td>
                <td class="px-8 py-4">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider
                    {{ in_array($comm->status, ['completed','resolved']) ? 'bg-green-100 text-green-600' : '' }}
                    {{ $comm->status == 'pending' ? 'bg-yellow-100 text-yellow-600' : '' }}
                    {{ $comm->status == 'cancelled' ? 'bg-red-100 text-red-600' : '' }}">
                    {{ ucfirst($comm->status) }}
                  </span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="px-8 py-10 text-center text-slate-400">
                  No communication history yet.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- File New Concern Modal -->
<div id="newTicketModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div id="newTicketOverlay" class="absolute inset-0 bg-black/50"></div>
  <div class="relative bg-white w-full max-w-lg rounded-3xl shadow-xl flex flex-col overflow-hidden max-h-[90vh]">

    <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100">
      <p class="font-semibold text-slate-800">File a New Concern</p>
      <button id="newTicketCloseBtn" class="text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <form action="{{ route('tickets.storeUser') }}" method="POST" class="px-6 sm:px-8 py-6 space-y-4 overflow-y-auto">
      @csrf

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Your Name</label>
        <input type="text" name="customer_name" required value="{{ auth()->user()->name }}" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Your Email</label>
        <input type="email" name="customer_email" required value="{{ auth()->user()->email }}" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Category</label>
        <select name="category" required class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
          <option value="Billing">Billing</option>
          <option value="Auth">Account / Login</option>
          <option value="Technical">Technical</option>
          <option value="Feature">Feature Request</option>
        </select>
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Priority</label>
        <select name="priority" required class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
          <option value="LOW">Low</option>
          <option value="MEDIUM" selected>Medium</option>
          <option value="HIGH">High</option>
          <option value="CRITICAL">Critical</option>
        </select>
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Subject</label>
        <input type="text" name="subject" required placeholder="e.g. Unable to access my account" class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20">
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 mb-1 block">Description</label>
        <textarea name="description" rows="4" placeholder="Describe your concern in detail..." class="w-full border border-[#E5E7EB] rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20 resize-none"></textarea>
      </div>

      <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" id="newTicketCancelBtn" class="px-4 py-2.5 rounded-xl border border-[#CBD5E1] text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">Cancel</button>
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#10B981] text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">Submit Ticket</button>
      </div>
    </form>
  </div>
</div>

<!-- TICKET CHAT DRAWER (User side) -->
<div id="chat-drawer" class="fixed inset-y-0 right-0 w-[380px] bg-white shadow-2xl border-l border-slate-200 z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col p-6">
    <div class="flex justify-between items-start border-b border-slate-100 pb-4">
        <div>
            <h2 id="chat-drawer-id" class="text-sm font-bold text-slate-900">Ticket</h2>
            <p id="chat-drawer-status" class="text-[10px] text-slate-400 mt-0.5"></p>
        </div>
        <button type="button" onclick="closeChatDrawer()" class="text-slate-400 hover:text-slate-600 text-sm font-bold bg-slate-100 w-6 h-6 rounded-full flex items-center justify-center">✕</button>
    </div>

    <div id="chat-messages" class="space-y-3 flex-1 overflow-y-auto my-4 bg-slate-50/60 border border-slate-100 rounded-xl p-3"></div>

    <form id="chat-send-form" class="flex items-end gap-2">
        <textarea id="chat-input" rows="2" placeholder="I-type ang mensahe mo..." class="flex-1 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-blue-500 transition resize-none"></textarea>
        <button type="submit" class="bg-[#00CB92] text-white w-9 h-9 shrink-0 rounded-xl text-xs font-semibold hover:bg-[#00B582] transition">↩</button>
    </form>

    <form id="cancel-ticket-form" method="POST" action="" class="mt-2">
        @csrf
        @method('PATCH')
        <button type="submit" id="cancel-ticket-btn" onclick="return confirm('Sigurado ka bang gusto mong i-cancel ang ticket na ito?')" class="hidden w-full border border-red-200 text-red-500 py-2 rounded-xl text-xs font-semibold hover:bg-red-50 transition">
            Cancel Ticket
        </button>
    </form>
</div>

<script>
  lucide.createIcons();

  const newTicketModal = document.getElementById('newTicketModal');
  function openNewTicketModal() {
    newTicketModal.classList.remove('hidden');
    newTicketModal.classList.add('flex');
  }
  function closeNewTicketModal() {
    newTicketModal.classList.add('hidden');
    newTicketModal.classList.remove('flex');
  }

  document.getElementById('newTicketBtn').addEventListener('click', openNewTicketModal);
  document.getElementById('newTicketCloseBtn').addEventListener('click', closeNewTicketModal);
  document.getElementById('newTicketCancelBtn').addEventListener('click', closeNewTicketModal);
  document.getElementById('newTicketOverlay').addEventListener('click', closeNewTicketModal);

  @if(session('success'))
    alert("{{ session('success') }}");
  @endif
  let currentChatTicketId = null;

  function openChatDrawer(ticketId, ticketLabel, status) {
      currentChatTicketId = ticketId;
      document.getElementById('chat-drawer-id').innerText = ticketLabel;
      document.getElementById('chat-drawer-status').innerText = status;
      loadMessages(ticketId);

      // Show "Cancel Ticket" button lang kung OPEN pa
      const cancelBtn = document.getElementById('cancel-ticket-btn');
      const cancelForm = document.getElementById('cancel-ticket-form');
      cancelForm.action = `/tickets/${ticketId}/cancel`;
      if (status === 'OPEN') {
          cancelBtn.classList.remove('hidden');
      } else {
          cancelBtn.classList.add('hidden');
      }

      document.getElementById('chat-drawer').classList.remove('translate-x-full');
  }

  function closeChatDrawer() {
      document.getElementById('chat-drawer').classList.add('translate-x-full');
  }

  async function loadMessages(ticketId) {
      const res = await fetch(`/tickets/${ticketId}/messages`);
      const messages = await res.json();
      renderMessages(messages);
  }

  function renderMessages(messages) {
      const container = document.getElementById('chat-messages');
      container.innerHTML = '';
      messages.forEach(msg => {
          const isMine = msg.sender_role === 'customer';
          const bubble = document.createElement('div');
          bubble.className = 'flex ' + (isMine ? 'justify-end' : 'justify-start');
          bubble.innerHTML = `
              <div class="max-w-[80%]">
                  <div class="text-[9px] font-semibold ${isMine ? 'text-right text-[#00875F]' : 'text-slate-600'}">${msg.sender_name}</div>
                  <div class="text-[11px] px-3 py-2 rounded-2xl ${isMine ? 'bg-[#00CB92] text-white' : 'bg-white border border-slate-200 text-slate-700'}">${msg.body}</div>
              </div>`;
          container.appendChild(bubble);
      });
      container.scrollTop = container.scrollHeight;
  }

  document.getElementById('chat-send-form').addEventListener('submit', async function(e) {
      e.preventDefault();
      const input = document.getElementById('chat-input');
      const body = input.value.trim();
      if (!body || !currentChatTicketId) return;

      await fetch(`/tickets/${currentChatTicketId}/messages`, {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ body })
      });

      input.value = '';
      loadMessages(currentChatTicketId);
  });
</script>

</body>
</html>