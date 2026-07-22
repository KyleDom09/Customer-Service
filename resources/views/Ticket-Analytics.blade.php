<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ticket Analytics</title>
<script src="{{ asset('vendor/tailwind.js') }}"></script>
<script src="{{ asset('vendor/chart.min.js') }}"></script>
<style>
  body { font-family: 'Segoe UI', 'Poppins', sans-serif; }
  ::-webkit-scrollbar { height: 6px; width: 6px; }
  ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
</style>
</head>
<body class="bg-[#F8FAFC] text-slate-800">

<div class="flex min-h-screen">

  <!-- Sidebar -->
  @include('partials.sidebar')

  <!-- Main -->
  <div class="flex-1 min-w-0">

    <!-- Top Navbar -->
    <header class="h-20 bg-white flex items-center justify-between px-4 sm:px-8 sticky top-0 z-20 border-b border-slate-100">
      <div>
        <h2 class="text-2xl font-bold text-[#1E3A8A]">Ticket Analytics</h2>
        <p class="text-slate-400 text-sm hidden sm:block">Real-time overview of ticket status, priority, and agent workload.</p>
      </div>
    </header>

    <main class="p-4 sm:p-8">

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Tickets by Status -->
        <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8">
          <h3 class="text-lg font-bold text-slate-800 mb-4">Tickets by Status</h3>
          <div class="h-[280px] relative flex items-center justify-center">
            <canvas id="statusChart"></canvas>
          </div>
        </div>

        <!-- Tickets by Priority -->
        <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8">
          <h3 class="text-lg font-bold text-slate-800 mb-4">Tickets by Priority</h3>
          <div class="h-[280px] relative flex items-center justify-center">
            <canvas id="priorityChart"></canvas>
          </div>
        </div>

      </div>

      <!-- Tickets by Agent -->
      <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Tickets by Agent (Workload)</h3>
        <div class="h-[320px] relative">
          <canvas id="agentChart"></canvas>
        </div>
      </div>

    </main>
  </div>
</div>

<script>
  // Real data passed from TicketController@analytics
  const statusData = {!! json_encode($statusCounts) !!};
  const priorityData = {!! json_encode($priorityCounts) !!};
  const agentData = {!! json_encode($agentCounts) !!};

  Chart.defaults.font.family = 'Poppins';
  Chart.defaults.color = '#94A3B8';

  // ---- Tickets by Status (donut) ----
  const statusLabels = Object.keys(statusData);
  const statusValues = Object.values(statusData);
  const statusColors = {
    'OPEN': '#3B82F6',
    'PENDING': '#F59E0B',
    'IN PROGRESS': '#8B5CF6',
    'RESOLVED': '#10B981',
    'CLOSED': '#94A3B8',
  };

  new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
      labels: statusLabels,
      datasets: [{
        data: statusValues,
        backgroundColor: statusLabels.map(s => statusColors[s] || '#CBD5E1'),
        borderWidth: 0,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } },
      },
      cutout: '65%',
    },
  });

  // ---- Tickets by Priority (donut) ----
  const priorityLabels = Object.keys(priorityData);
  const priorityValues = Object.values(priorityData);
  const priorityColors = {
    'CRITICAL': '#EF4444',
    'HIGH': '#F97316',
    'MEDIUM': '#F59E0B',
    'LOW': '#10B981',
  };

  new Chart(document.getElementById('priorityChart'), {
    type: 'doughnut',
    data: {
      labels: priorityLabels,
      datasets: [{
        data: priorityValues,
        backgroundColor: priorityLabels.map(p => priorityColors[p] || '#CBD5E1'),
        borderWidth: 0,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } },
      },
      cutout: '65%',
    },
  });

  // ---- Tickets by Agent (bar) ----
  const agentLabels = agentData.map(a => a.name);
  const agentValues = agentData.map(a => a.total);

  new Chart(document.getElementById('agentChart'), {
    type: 'bar',
    data: {
      labels: agentLabels,
      datasets: [{
        label: 'Tickets Assigned',
        data: agentValues,
        backgroundColor: '#3B82F6',
        borderRadius: 6,
        maxBarThickness: 42,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F1F5F9' } },
        x: { grid: { display: false } },
      },
    },
  });
</script>

</body>
</html>