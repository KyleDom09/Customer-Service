<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard History</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
  body { font-family: 'Poppins', sans-serif; }
  ::-webkit-scrollbar { height: 6px; width: 6px; }
  ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
  .sidebar-transition { transition: transform 0.3s ease-in-out; }
</style>
</head>
<body class="bg-[#F8FAFC] text-slate-800">

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

      <div>
        <h2 class="text-2xl font-bold text-[#1E3A8A]">Dashboard History</h2>
        <p class="text-slate-400 text-sm hidden sm:block">A historical view of your communication and campaign data.</p>
      </div>

      <div class="hidden md:flex flex-1 justify-center px-8">
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

      <div class="grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-6">

        <!-- Left column -->
        <div class="space-y-6 min-w-0">

          <!-- Top stat cards -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

            <div class="bg-white rounded-2xl shadow-sm p-6">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                  <i data-lucide="mail" class="w-5 h-5 text-blue-500"></i>
                </div>
                <p class="text-sm text-slate-500 font-medium">Emails History</p>
              </div>
              <p class="text-3xl font-bold text-slate-800 mb-2" id="statEmails">0</p>
              <div class="flex items-center gap-2 text-xs">
                <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded-full font-semibold">12.4%</span>
                <span class="text-slate-400">this month</span>
              </div>
              <div class="mt-4 h-16 relative">
                <canvas id="emailsChart"></canvas>
              </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                  <i data-lucide="eye" class="w-5 h-5 text-[#10B981]"></i>
                </div>
                <p class="text-sm text-slate-500 font-medium">Open Rate</p>
              </div>
              <p class="text-3xl font-bold text-slate-800 mb-2" id="statOpenRate">0%</p>
              <div class="flex items-center gap-2 text-xs">
                <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded-full font-semibold">3.2%</span>
                <span class="text-slate-400">vs last period</span>
              </div>
              <div class="mt-4 h-16 relative">
                <canvas id="openRateChart"></canvas>
              </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                  <i data-lucide="user-plus" class="w-5 h-5 text-purple-500"></i>
                </div>
                <p class="text-sm text-slate-500 font-medium">New Customer</p>
              </div>
              <p class="text-3xl font-bold text-slate-800 mb-2" id="statNewCustomer">0</p>
              <div class="flex items-center gap-2 text-xs">
                <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded-full font-semibold">8.9%</span>
                <span class="text-slate-400">this weekend</span>
              </div>
              <div class="mt-4 h-16 relative">
                <canvas id="newCustomerChart"></canvas>
              </div>
            </div>

          </div>

          <!-- Performance Analytics -->
          <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
              <h3 class="text-lg font-bold text-slate-800">Performance Analytics</h3>
              <div class="flex items-center gap-4 flex-wrap">
                <div class="flex items-center gap-4 text-xs text-slate-500">
                  <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> History</span>
                  <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#10B981]"></span> Open Rate</span>
                  <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span> Canceled</span>
                </div>
                <select class="border border-[#E5E7EB] rounded-xl px-3 py-1.5 text-xs text-slate-600 focus:outline-none">
                  <option>Last 7 Days</option>
                  <option>Last 30 Days</option>
                  <option>Last 90 Days</option>
                </select>
              </div>
            </div>
            <div class="h-[280px] sm:h-[320px] relative">
              <canvas id="performanceChart"></canvas>
            </div>
          </div>

          <!-- Audience Growth + Top Campaigns -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            <div class="bg-white rounded-3xl shadow-sm p-6">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold text-slate-800">Audience Growth</h3>
                <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded-full text-xs font-semibold">+4.2%</span>
              </div>
              <p class="text-3xl font-bold text-slate-800 mb-4" id="statAudience">0</p>
              <div class="h-24 relative">
                <canvas id="audienceChart"></canvas>
              </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm p-6">
              <h3 class="text-lg font-bold text-slate-800 mb-4">Top Campaigns</h3>
              <div class="space-y-4" id="topCampaigns"></div>
            </div>

          </div>

        </div>

        <!-- Right column -->
        <div class="space-y-6 min-w-0">

          <!-- Sender Reputation -->
          <div class="bg-white rounded-3xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Sender Reputation</h3>
            <div class="flex flex-col items-center">
              <svg viewBox="0 0 200 120" class="w-full max-w-[220px]">
                <defs>
                  <linearGradient id="gaugeGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#F87171"/>
                    <stop offset="50%" stop-color="#FBBF24"/>
                    <stop offset="100%" stop-color="#34D399"/>
                  </linearGradient>
                </defs>
                <path d="M 15 105 A 85 85 0 0 1 185 105" fill="none" stroke="#F1F5F9" stroke-width="16" stroke-linecap="round"/>
                <path d="M 15 105 A 85 85 0 0 1 185 105" fill="none" stroke="url(#gaugeGrad)" stroke-width="16" stroke-linecap="round" id="gaugeArc"/>
                <line id="gaugeNeedle" x1="100" y1="105" x2="150" y2="45" stroke="#1E3A8A" stroke-width="3" stroke-linecap="round"/>
                <circle cx="100" cy="105" r="5" fill="#1E3A8A"/>
              </svg>
              <p class="text-4xl font-bold text-slate-800 -mt-2" id="statReputation">0</p>
              <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-semibold mt-1">GOOD!</span>
            </div>

            <div class="mt-6 pt-5 border-t border-slate-100 space-y-3">
              <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">Deliverability Score</span>
                <span class="font-semibold text-slate-800">94/100</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">Inbox Placement</span>
                <span class="font-semibold text-[#10B981]">Excellent</span>
              </div>
              <div class="pt-2 space-y-2 text-xs text-slate-500">
                <p class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> Spam Complaints: 0.02%</p>
                <p class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span> Bounce Rate: 1.4%</p>
                <p class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> Verified SPF/DKIM</p>
              </div>
            </div>
          </div>

          <!-- Campaign Schedule -->
          <div class="bg-white rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
              <h3 class="text-lg font-bold text-slate-800">Campaign Schedule</h3>
              <button class="flex items-center gap-1 px-3 py-1.5 rounded-xl bg-[#1E3A8A] text-white text-xs font-semibold hover:bg-[#1E3A8A]/90 transition-colors">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Schedule
              </button>
            </div>

            <div class="relative pl-6" id="campaignSchedule">
              <div class="absolute left-[7px] top-2 bottom-2 w-px bg-slate-200"></div>
            </div>
          </div>

        </div>
      </div>

    </main>
  </div>
</div>

<script>
  lucide.createIcons();

  // ---- Mobile sidebar toggle ----
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const menuBtn = document.getElementById('menuBtn');
  function openSidebar() { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); }
  function closeSidebar() { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); }
  menuBtn.addEventListener('click', openSidebar);
  overlay.addEventListener('click', closeSidebar);

  // ---- Sample data (replace with real values from your database later) ----
  const emailsHistoryTotal = 248391;
  const openRatePercent = 34.7;
  const newCustomerTotal = 12847;
  const audienceTotal = 84200;
  const reputationScore = 87; // out of 100

  document.getElementById('statEmails').textContent = emailsHistoryTotal.toLocaleString();
  document.getElementById('statOpenRate').textContent = openRatePercent + '%';
  document.getElementById('statNewCustomer').textContent = newCustomerTotal.toLocaleString();
  document.getElementById('statAudience').textContent = (audienceTotal / 1000).toFixed(1) + 'k';
  document.getElementById('statReputation').textContent = reputationScore;

  // ---- Gauge needle rotation (0-100 mapped across the arc) ----
  const gaugeNeedle = document.getElementById('gaugeNeedle');
  const angle = -90 + (reputationScore / 100) * 180; // -90deg (left) to +90deg (right)
  gaugeNeedle.setAttribute('transform', `rotate(${angle} 100 105)`);

  const chartFont = { family: 'Poppins', size: 11 };
  Chart.defaults.font.family = 'Poppins';
  Chart.defaults.color = '#94A3B8';

  const commonBarOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { enabled: false } },
    scales: {
      x: { display: false },
      y: { display: false },
    },
  };

  // Emails History mini bar chart
  new Chart(document.getElementById('emailsChart'), {
    type: 'bar',
    data: {
      labels: ['M','T','W','T','F','S','S'],
      datasets: [{
        data: [22, 28, 15, 32, 24, 30, 40],
        backgroundColor: '#60A5FA',
        borderRadius: 4,
      }],
    },
    options: commonBarOptions,
  });

  // Open Rate mini line chart
  new Chart(document.getElementById('openRateChart'), {
    type: 'line',
    data: {
      labels: ['M','T','W','T','F','S','S'],
      datasets: [{
        data: [20, 24, 22, 26, 25, 28, 30],
        borderColor: '#10B981',
        borderWidth: 2,
        pointRadius: 0,
        tension: 0.4,
        fill: false,
      }],
    },
    options: commonBarOptions,
  });

  // New Customer mini area chart
  new Chart(document.getElementById('newCustomerChart'), {
    type: 'line',
    data: {
      labels: ['M','T','W','T','F','S','S'],
      datasets: [{
        data: [10, 14, 12, 18, 20, 22, 28],
        borderColor: '#A78BFA',
        backgroundColor: 'rgba(167,139,250,0.15)',
        borderWidth: 2,
        pointRadius: 0,
        tension: 0.4,
        fill: true,
      }],
    },
    options: commonBarOptions,
  });

  // Main Performance Analytics chart
  new Chart(document.getElementById('performanceChart'), {
    type: 'line',
    data: {
      labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
      datasets: [
        {
          label: 'History',
          data: [12000, 15500, 14000, 15000, 22000, 19000, 25000],
          borderColor: '#3B82F6',
          backgroundColor: 'rgba(59,130,246,0.08)',
          borderWidth: 2.5,
          pointRadius: 0,
          tension: 0.4,
          fill: true,
        },
        {
          label: 'Open Rate',
          data: [8000, 9500, 11000, 10500, 12500, 14500, 14000],
          borderColor: '#10B981',
          borderWidth: 2,
          borderDash: [4, 4],
          pointRadius: 0,
          tension: 0.4,
          fill: false,
        },
        {
          label: 'Canceled',
          data: [500, 700, 600, 800, 900, 700, 750],
          borderColor: '#EF4444',
          borderWidth: 2,
          pointRadius: 0,
          tension: 0.4,
          fill: false,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          grid: { color: '#F1F5F9' },
          ticks: { callback: (v) => '$' + (v / 1000) + 'k' },
        },
        x: { grid: { display: false } },
      },
    },
  });

  // Audience Growth mini chart
  new Chart(document.getElementById('audienceChart'), {
    type: 'line',
    data: {
      labels: ['1','2','3','4','5','6','7'],
      datasets: [{
        data: [70, 74, 76, 78, 80, 82, 84],
        borderColor: '#10B981',
        backgroundColor: 'rgba(16,185,129,0.12)',
        borderWidth: 2,
        pointRadius: 0,
        tension: 0.4,
        fill: true,
      }],
    },
    options: commonBarOptions,
  });

  // ---- Top Campaigns (progress bars) ----
  const campaigns = [
    { name: 'Spring Sale 2024', rate: 68 },
    { name: 'Product Update', rate: 52 },
    { name: 'Weekly Digest', rate: 41 },
  ];

  document.getElementById('topCampaigns').innerHTML = campaigns.map(c => `
    <div>
      <div class="flex items-center justify-between text-sm mb-1.5">
        <span class="font-medium text-slate-700">${c.name}</span>
        <span class="text-slate-400">${c.rate}% OR</span>
      </div>
      <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full bg-blue-400 rounded-full" style="width:${c.rate}%"></div>
      </div>
    </div>
  `).join('');

  // ---- Campaign Schedule (timeline) ----
  const schedule = [
    { day: 'Today', title: 'Product Launch Email', detail: 'Segment: All Active Users (45k)', time: '10:00 AM', active: true },
    { day: 'Wed', title: 'Newsletter #47', detail: '', time: '2:00 PM', active: false },
    { day: 'Fri', title: 'Re-engagement Blast', detail: '', time: '11:00 AM', active: false, highlight: true },
  ];

  document.getElementById('campaignSchedule').innerHTML += schedule.map(s => `
    <div class="relative mb-5 last:mb-0">
      <span class="absolute -left-6 top-1 w-3 h-3 rounded-full ${s.active ? 'bg-blue-500' : (s.highlight ? 'bg-purple-500' : 'bg-slate-300')} ring-4 ring-white"></span>
      <p class="text-xs text-slate-400 mb-1">${s.day}</p>
      <div class="flex items-center justify-between gap-3 ${s.active ? 'bg-blue-50 rounded-xl px-3 py-2.5' : ''}">
        <div>
          <p class="text-sm font-semibold ${s.highlight ? 'text-purple-600' : 'text-slate-800'}">${s.title}</p>
          ${s.detail ? `<p class="text-xs text-slate-400 mt-0.5">${s.detail}</p>` : ''}
        </div>
        <span class="text-xs font-medium ${s.active ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-500'} px-2 py-1 rounded-lg whitespace-nowrap">${s.time}</span>
      </div>
    </div>
  `).join('');
</script>

</body>
</html>