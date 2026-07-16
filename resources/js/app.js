
lucide.createIcons();

// Fallback: if /assets/chatbot-avatar.png is missing, show a built-in robot icon instead of a broken image
function botAvatarFallback(imgEl) {
  imgEl.onerror = null;
  const wrapper = imgEl.parentElement;
  wrapper.innerHTML = `
    <svg viewBox="0 0 64 64" class="w-full h-full p-1.5">
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

function label(s) {
  if (!s) return 'N/A';
  return s.charAt(0).toUpperCase() + s.slice(1);
}

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

function renderRows(filter = 'all') {
    const tbody = document.getElementById('tableBody');
    const viewModal = document.getElementById('viewModal');

    if (!tbody) {
        console.error('tableBody not found!');
        return;
    }

    const filtered = filter === 'all'
        ? rows
        : rows.filter(r => r.status.toLowerCase() === filter.toLowerCase());

    tbody.innerHTML = '';

    filtered.forEach(r => {
        tbody.innerHTML += `
        <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-8 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-semibold shrink-0"
                        style="background:${r.color}">
                        ${r.initials}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-700">${r.name}</p>
                        <p class="text-xs text-slate-400">${r.email}</p>
                    </div>
                </div>
            </td>

            <td class="px-4 py-4 text-slate-500">${r.date}</td>

            <td class="px-4 py-4 text-slate-400">
                <i data-lucide="${typeIcon[r.type] || 'mail'}" class="w-4 h-4"></i>
            </td>

            <td class="px-4 py-4 text-slate-600">${r.subject}</td>

            <td class="px-4 py-4 text-slate-500">${r.staff}</td>

            <td class="px-4 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusStyles[r.status] || statusStyles.pending}">
                    ${label(r.status)}
                </span>
            </td>

            <td class="px-4 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-semibold ${priorityStyles[r.priority] || priorityStyles.medium}">
                    ${label(r.priority)}
                </span>
            </td>

            <td class="px-4 py-4 text-slate-500">${r.resp}</td>

            <td class="px-8 py-4">
                <div class="flex items-center gap-2">
                    <button class="view-btn px-3 py-1.5 rounded-lg border border-[#10B981] text-[#10B981] text-xs font-semibold"
                        data-name="${r.name}">
                        View
                    </button>

                    <button class="edit-btn px-3 py-1.5 rounded-lg border border-[#CBD5E1] text-slate-600 text-xs font-semibold"
                        data-name="${r.name}">
                        Edit
                    </button>
                </div>
            </td>
        </tr>`;
    });

    lucide.createIcons();
}
  `).join('');
  lucide.createIcons();

  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => openViewModal(btn.dataset.name));
  });

  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => openEditModal(btn.dataset.name));
  });
}

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
  statusEl.className = 'px-2 py-0.5 rounded-full text-xs font-semibold ' + (statusStyles[r.status] || statusStyles.pending);

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

function openEditModal(name) {
  const r = rows.find(row => row.name === name);
  if (!r) return;
  editingName = name;

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
  const r = rows.find(row => row.name === editingName);
  if (!r) return;

  r.date = document.getElementById('editDate').value;
  r.type = document.getElementById('editType').value;
  r.subject = document.getElementById('editSubject').value;
  r.staff = document.getElementById('editStaff').value;
  r.status = document.getElementById('editStatus').value;
  r.priority = document.getElementById('editPriority').value;
  r.resp = document.getElementById('editResp').value;

  closeEditModal();
  renderRows(currentFilter);
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

// Saves the new communication to the DATABASE (via Laravel route), then reloads
// so the table reflects real data from the server.
document.getElementById('newForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const payload = {
    customer_name: document.getElementById('newCustomerName').value.trim(),
    customer_email: document.getElementById('newCustomerEmail').value.trim(),
    date: document.getElementById('newDate').value.trim() || 'Jul 06',
    type: document.getElementById('newType').value,
    subject: document.getElementById('newSubject').value.trim(),
    staff: document.getElementById('newStaff').value.trim(),
    status: document.getElementById('newStatus').value,
    priority: document.getElementById('newPriority').value,
    resp_time: document.getElementById('newResp').value.trim() || 'Pending',
  };

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  try {
    const res = await fetch('/communication-history/store', {
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

document.querySelectorAll('.filter-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.filter-tab').forEach(b => {
      b.classList.remove('bg-[#10B981]', 'text-white', 'font-semibold');
      b.classList.add('bg-slate-100', 'text-slate-500', 'font-medium');
    });
    btn.classList.remove('bg-slate-100', 'text-slate-500', 'font-medium');
    btn.classList.add('bg-[#10B981]', 'text-white', 'font-semibold');
    currentFilter = btn.dataset.filter;
    renderRows(currentFilter);
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
  const totalRecords = rows.length;
  const previewRows = rows.slice(0, 9);

  const tbody = document.getElementById('previewTableBody');
  tbody.innerHTML = previewRows.map((r, i) => `
    <tr>
      <td class="py-2 pr-3 text-slate-400">${i + 1}</td>
      <td class="py-2 pr-3 font-medium text-slate-700 whitespace-nowrap">${r.name}</td>
      <td class="py-2 pr-3 text-slate-500 whitespace-nowrap">${r.email}</td>
      <td class="py-2 pr-3 text-slate-500 whitespace-nowrap">${r.date}</td>
      <td class="py-2 pr-3 text-slate-500 whitespace-nowrap">${label(r.type === 'mail' ? 'Email' : r.type)}</td>
      <td class="py-2 pr-3 text-slate-600 whitespace-nowrap">${r.subject}</td>
      <td class="py-2 pr-3"><span class="px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold whitespace-nowrap ${statusStyles[r.status] || statusStyles.pending}">${label(r.status)}</span></td>
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

// Locate this section inside your app.js and alter it to allow server dispatching
const newFormElement = document.getElementById('newForm');
if (newFormElement) {
  newFormElement.addEventListener('submit', (e) => {
    // REMOVE OR COMMENT OUT e.preventDefault() to allow data synchronization back to Laragon MySQL
    // e.preventDefault(); 
    
    // Close the interface modal visually before submission reload triggers
    if (typeof closeNewModal === 'function') {
        closeNewModal();
    }
  });
}