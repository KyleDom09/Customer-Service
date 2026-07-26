<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Service - Self-Service Portal</title>
    <script src="{{ asset('vendor/tailwind.js') }}"></script>
    <script defer src="{{ asset('vendor/alpine.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}">
</head>
<body class="bg-[#f0f4f8] text-slate-800 font-sans antialiased" 
      x-init="init()"
      x-data="{ 
        currentView: 'main', 
        searchQuery: '',
        isModalOpen: false,
        newTitle: '',
        newProblem: '',
        newSolution: '',
        isProfileOpen: false,
        isNotifOpen: false,
        isSearchOpen: false,
        isRefundFormOpen: false,
        isSubmittingRefund: false,
        refundSubmitted: false,
        refundTitle: '',
        refundDescription: '',
        refundImageName: '',
        refundImagePreview: '',
        refundImageFile: null,
        refundRequests: [],
        notifications: [
            { text: 'Welcome to the Self-Service Portal!', time: 'Just now', read: true }
        ],
        addNotification(text) {
            this.notifications.unshift({ text: text, time: 'Just now', read: false });
        },
        unreadCount() {
            return this.notifications.filter(n => !n.read).length;
        },
        markNotificationsRead() {
            this.notifications.forEach(n => n.read = true);
        },
        csrfToken() {
            return document.querySelector('meta[name=csrf-token]').getAttribute('content');
        },
        handleRefundImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.refundImageFile = file;
            this.refundImageName = file.name;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.refundImagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        resetRefundForm() {
            this.refundTitle = '';
            this.refundDescription = '';
            this.refundImageName = '';
            this.refundImagePreview = '';
            this.refundImageFile = null;
            if (this.$refs && this.$refs.refundImageInput) {
                this.$refs.refundImageInput.value = '';
            }
        },
        async submitRefundRequest() {
            if (this.refundTitle.trim() === '') {
                alert('Please enter a title.');
                return;
            }

            this.isSubmittingRefund = true;

            let formData = new FormData();
            formData.append('title', this.refundTitle);
            formData.append('description', this.refundDescription);
            if (this.refundImageFile) {
                formData.append('image', this.refundImageFile);
            }

            try {
                const response = await fetch('/customer-service/self-service/refund-requests', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken()
                    },
                    body: formData
                });

                if (!response.ok) throw new Error('Failed to submit refund request');

                const saved = await response.json();

                // Merge server data with the local image preview so it displays
                // immediately without needing to re-fetch the stored file.
                this.refundRequests.unshift({
                    id: saved.id,
                    title: saved.title,
                    description: saved.description,
                    status: saved.status,
                    imagePreview: this.refundImagePreview,
                    submittedAt: new Date(saved.created_at).toLocaleString()
                });

                this.addNotification('Return request sent: ' + saved.title);
                this.refundSubmitted = true;
            } catch (e) {
                console.error('Failed to submit refund request', e);
                alert('Something went wrong. Please try again.');
            } finally {
                this.isSubmittingRefund = false;
            }
        },
        closeRefundModal() {
            this.isRefundFormOpen = false;
            this.refundSubmitted = false;
            this.resetRefundForm();
        },
        async deleteRefundRequest(id) {
            this.openDeleteModal('refund-cancel', id, 'this return request');
        },
        refundStatusFilter: 'all',
        get filteredRefundRequests() {
            if (this.refundStatusFilter === 'all') return this.refundRequests;
            return this.refundRequests.filter(r => r.status === this.refundStatusFilter);
        },
        async approveRefundRequest(id) {
            try {
                const res = await fetch('/customer-service/self-service/refund-requests/' + id + '/approve', {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken() }
                });
                if (!res.ok) throw new Error('Failed to approve');
                const updated = await res.json();
                const target = this.refundRequests.find(r => r.id === id);
                if (target) target.status = updated.status;
            } catch (e) {
                console.error(e);
                alert('Something went wrong while approving.');
            }
        },
        async rejectRefundRequest(id) {
            try {
                const res = await fetch('/customer-service/self-service/refund-requests/' + id + '/reject', {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken() }
                });
                if (!res.ok) throw new Error('Failed to reject');
                const updated = await res.json();
                const target = this.refundRequests.find(r => r.id === id);
                if (target) target.status = updated.status;
            } catch (e) {
                console.error(e);
                alert('Something went wrong while rejecting.');
            }
        },
        async adminDeleteRefundRequest(id, title) {
            this.openDeleteModal('refund-admin', id, title ? ('"' + title + '"') : 'this refund request');
        },

        // ===== Shared delete confirmation modal =====
        deleteModal: {
            open: false,
            type: null,   // 'refund-cancel' | 'refund-admin' | 'billing' | 'article'
            id: null,
            label: ''
        },
        openDeleteModal(type, id, label) {
            this.deleteModal = { open: true, type, id, label };
        },
        closeDeleteModal() {
            this.deleteModal = { open: false, type: null, id: null, label: '' };
        },
        async confirmDeleteModal() {
            const { type, id } = this.deleteModal;
            try {
                if (type === 'refund-cancel') {
                    const res = await fetch('/customer-service/self-service/refund-requests/' + id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken() }
                    });
                    if (!res.ok) throw new Error('Failed to delete refund request');
                    this.refundRequests = this.refundRequests.filter(r => r.id !== id);
                    this.addNotification('Return request cancelled.');
                } else if (type === 'refund-admin') {
                    const res = await fetch('/customer-service/self-service/refund-requests/' + id + '/admin', {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken() }
                    });
                    if (!res.ok) throw new Error('Failed to delete refund request');
                    this.refundRequests = this.refundRequests.filter(r => r.id !== id);
                } else if (type === 'billing') {
                    const res = await fetch('/customer-service/self-service/billing-items/' + id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken() }
                    });
                    if (!res.ok) throw new Error('Failed to delete billing item');
                    this.billingItems = this.billingItems.filter(i => i.id !== id);
                } else if (type === 'article') {
                    const res = await fetch('/customer-service/self-service/articles/' + id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken() }
                    });
                    if (!res.ok) throw new Error('Failed to delete article');
                    this.articleItems = this.articleItems.filter(a => a.id !== id);
                }
            } catch (e) {
                console.error(e);
                alert('Something went wrong while deleting.');
            } finally {
                this.closeDeleteModal();
            }
        },

        // ===== Billing items: inline edit =====
        editingBillingId: null,
        editBillingTitle: '',
        editBillingProblem: '',
        startEditBilling(item) {
            this.editingBillingId = item.id;
            this.editBillingTitle = item.title;
            this.editBillingProblem = item.problem;
        },
        cancelEditBilling() {
            this.editingBillingId = null;
        },
        async saveEditBilling(item) {
            try {
                const res = await fetch('/customer-service/self-service/billing-items/' + item.id, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken(),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ title: this.editBillingTitle, problem: this.editBillingProblem })
                });
                if (!res.ok) throw new Error('Failed to update');
                const updated = await res.json();
                item.title = updated.title;
                item.problem = updated.problem;
                this.editingBillingId = null;
            } catch (e) {
                console.error(e);
                alert('Something went wrong while updating.');
            }
        },
        deleteBillingItem(item) {
            this.openDeleteModal('billing', item.id, '"' + item.title + '"');
        },

        // ===== Articles: inline edit =====
        editingArticleId: null,
        editArticleTitle: '',
        editArticleDescription: '',
        startEditArticle(article) {
            this.editingArticleId = article.id;
            this.editArticleTitle = article.title;
            this.editArticleDescription = article.description;
        },
        cancelEditArticle() {
            this.editingArticleId = null;
        },
        async saveEditArticle(article) {
            try {
                const res = await fetch('/customer-service/self-service/articles/' + article.id, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken(),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ title: this.editArticleTitle, description: this.editArticleDescription })
                });
                if (!res.ok) throw new Error('Failed to update');
                const updated = await res.json();
                article.title = updated.title;
                article.description = updated.description;
                this.editingArticleId = null;
            } catch (e) {
                console.error(e);
                alert('Something went wrong while updating.');
            }
        },
        deleteArticle(article) {
            this.openDeleteModal('article', article.id, '"' + article.title + '"');
        },
        async rateItem(item, stars) {
            item.rating = stars;
            let endpoint = item.hasOwnProperty('problem')
                ? '/customer-service/self-service/billing-items/' + item.id + '/rate'
                : '/customer-service/self-service/articles/' + item.id + '/rate';
            try {
                await fetch(endpoint, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken()
                    },
                    body: JSON.stringify({ rating: stars })
                });
            } catch (e) {
                console.error('Failed to save rating', e);
            }
        },
        overallRating() {
            let allItems = [...this.billingItems, ...this.articleItems];
            let rated = allItems.filter(i => i.rating);
            if(rated.length === 0) return 5;
            let sum = rated.reduce((acc, i) => acc + i.rating, 0);
            return (sum / rated.length).toFixed(1);
        },
        searchResults() {
            if(this.searchQuery.trim() === '') return [];
            let q = this.searchQuery.toLowerCase();
            let results = [];
            this.billingItems.forEach(item => {
                let text = (item.title + ' ' + item.problem + ' ' + item.steps.join(' ')).toLowerCase();
                if(text.includes(q)) {
                    results.push({ category: 'Billing', title: item.title, snippet: item.problem, view: 'billing' });
                }
            });
            this.articleItems.forEach(item => {
                let text = (item.title + ' ' + item.description).toLowerCase();
                if(text.includes(q)) {
                    results.push({ category: 'Article', title: item.title, snippet: item.description.substring(0, 80) + '...', view: 'articles' });
                }
            });
            return results;
        },
        goToResult(result) {
            this.currentView = result.view;
            this.searchQuery = '';
            this.isSearchOpen = false;
        },
        isArticleModalOpen: false,
        newArticleTitle: '',
        newArticleContent: '',
        // Default/fallback articles - shown immediately on load, replaced by database data once init() finishes fetching
        articleItems: [
            {
                title: 'Upcoming System Maintenance',
                description: 'Stay informed about scheduled platform downtime.  Common Issue: Unsure when the system might be unavailable for quarterly updates.  Try this to fix: Check the \'News\' banner on the dashboard. Maintenance windows are posted 48 hours in advance for all users. Pro-Tip: If you have active sync processes running, save your work locally 1 hour before the scheduled time.',
                isNew: false,
                rating: null
            },
            {
                title: 'Understanding SLA Response Times',
                description: 'Know when to expect a reply from support. Critical: 2 hrs, High: 12 hrs, Standard: 24 hrs. Common Issue: Unsure about how long it takes to get a response on a support ticket. Try this to fix: Visit \'SLA Tracking\' to see our current response targets based on priority levels (Critical: 2 hrs, High: 12 hrs, Standard: 24 hrs). Pro-Tip: Always attach a ticket ID when referencing open cases to speed up the lookup.',
                isNew: false,
                rating: null
            },
            {
                title: 'Resetting a Locked Account',
                description: 'Self-service steps for account security lockouts. Common Issue: Account locked due to multiple incorrect login attempts. Try this to fix: Wait 15 minutes for the automated reset. Use the \'Forgot Password\' link on the login page to verify your identity via your registered corporate email. Pro-Tip: Check your spam folder if the verification code does not arrive within 3 minutes.',
                isNew: false,
                rating: null
            }
        ],
        // Default/fallback billing items - shown immediately on load, replaced by database data once init() finishes fetching
        billingItems: [
            {
                title: 'Double Charging',
                icon: 'fa-file-invoice-dollar',
                problem: 'Why am I seeing two identical charges on my bank statement?',
                steps: [
                    'Check if one of the charges is marked as Pending (this is a temporary authorization hold).',
                    'Wait 24 to 48 hours for your bank to clear and remove the duplicate temporary hold.',
                    'If both charges remain active after 2 days, contact support with your receipt.'
                ],
                isNew: false,
                rating: null
            },
            {
                title: 'Card Declining',
                icon: 'fa-exclamation-circle',
                problem: 'Why am I seeing two identical charges on my bank statement?',
                steps: [
                    'Check if one of the charges is marked as Pending (this is a temporary authorization hold).',
                    'Wait 24 to 48 hours for your bank to clear and remove the duplicate temporary hold.',
                    'If both charges remain active after 2 days, contact support with your receipt.'
                ],
                isNew: false,
                rating: null
            },
            {
                title: 'Update Tax Information',
                icon: 'fa-id-card',
                problem: 'How can I check if my requested refund has been processed?',
                steps: [
                    'Check your transaction history for a green line item showing a negative balance credit.',
                    'Allow 5 to 10 standard business days for your bank to post the credit to your card.',
                    'If the credit does not show up after 10 days, contact support with the refund ID.'
                ],
                isNew: false,
                rating: null
            }
        ],
        async init() {
            try {
                const [billingRes, articlesRes] = await Promise.all([
                    fetch('/customer-service/self-service/billing-items'),
                    fetch('/customer-service/self-service/articles')
                ]);
                const dbBilling = await billingRes.json();
                const dbArticles = await articlesRes.json();
                // Only replace the default items if the database actually returned data
                if (Array.isArray(dbBilling) && dbBilling.length > 0) {
                    this.billingItems = dbBilling;
                }
                if (Array.isArray(dbArticles) && dbArticles.length > 0) {
                    this.articleItems = dbArticles;
                }
            } catch (e) {
                console.error('Failed to load data from database, keeping defaults', e);
            }

            try {
                const refundRes = await fetch('/customer-service/self-service/refund-requests');
                const dbRefunds = await refundRes.json();
                if (Array.isArray(dbRefunds)) {
                    // Map DB fields (image_path / created_at) to the same shape
                    // submitRefundRequest() uses (imagePreview / submittedAt).
                    this.refundRequests = dbRefunds.map(r => ({
                        id: r.id,
                        title: r.title,
                        description: r.description,
                        status: r.status,
                        imagePreview: r.image_path ? '/storage/' + r.image_path : '',
                        submittedAt: new Date(r.created_at).toLocaleString()
                    }));
                }
            } catch (e) {
                console.error('Failed to load refund requests', e);
            }
        },
        async addNewArticle() {
            if(this.newArticleTitle.trim() === '') return;
            try {
                const res = await fetch('/customer-service/self-service/articles', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken()
                    },
                    body: JSON.stringify({
                        title: this.newArticleTitle,
                        description: this.newArticleContent
                    })
                });
                const newArticle = await res.json();
                this.articleItems.unshift(newArticle);
                this.addNotification('New article added: ' + this.newArticleTitle);
            } catch (e) {
                console.error('Failed to save article', e);
            }
            this.newArticleTitle = '';
            this.newArticleContent = '';
            this.isArticleModalOpen = false;
        },
        async addNewBillingItem() {
            if(this.newTitle.trim() === '') return;
            try {
                const res = await fetch('/customer-service/self-service/billing-items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken()
                    },
                    body: JSON.stringify({
                        title: this.newTitle,
                        problem: this.newProblem,
                        steps: this.newSolution.split('\n').filter(step => step.trim() !== '')
                    })
                });
                const newItem = await res.json();
                this.billingItems.unshift(newItem);
                this.addNotification('Billing item added: ' + this.newTitle);
            } catch (e) {
                console.error('Failed to save billing item', e);
            }
            // Reset fields & close modal
            this.newTitle = '';
            this.newProblem = '';
            this.newSolution = '';
            this.isModalOpen = false;
        }
      }">

    <div class="flex h-screen overflow-hidden">
        
        @include('partials.sidebar')

        <div class="flex-1 flex flex-col overflow-y-auto">
            
            <header class="bg-white px-8 py-4 flex items-center justify-between border-b border-slate-200 sticky top-0 z-10">
                <div class="flex items-center space-x-4">
                    <h2 class="text-xl font-bold text-[#1e3a8a] cursor-pointer" @click="currentView = 'main'">Self-Service Portal</h2>
                </div>
                
                <div class="w-1/2 relative" @click.outside="isSearchOpen = false">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" x-model="searchQuery" @focus="isSearchOpen = true" placeholder="Search for order tracking, billing guides, or inventory issues..." 
                           class="w-full pl-10 pr-4 py-2 bg-slate-100 border border-transparent rounded-full focus:outline-none focus:bg-white focus:border-blue-500 transition text-sm">

                    <div x-show="isSearchOpen && searchQuery.trim() !== ''" x-transition
                         class="absolute left-0 right-0 mt-2 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden z-20 max-h-80 overflow-y-auto"
                         style="display: none;">
                        <template x-for="(result, index) in searchResults()" :key="index">
                            <div @click="goToResult(result)" class="px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0">
                                <div class="flex items-center space-x-2">
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded"
                                          :class="result.category === 'Billing' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'"
                                          x-text="result.category"></span>
                                    <span class="text-sm font-semibold text-slate-700" x-text="result.title"></span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-1" x-text="result.snippet"></p>
                            </div>
                        </template>
                        <template x-if="searchResults().length === 0">
                            <div class="px-4 py-6 text-center text-xs text-slate-400">
                                No results found for "<span x-text="searchQuery"></span>"
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="relative" @click.outside="isNotifOpen = false">
                        <button @click="isNotifOpen = !isNotifOpen; if(isNotifOpen) markNotificationsRead()" class="text-slate-400 hover:text-slate-600 relative cursor-pointer">
                            <i class="fas fa-bell text-lg"></i>
                            <span x-show="unreadCount() > 0" class="absolute top-0 right-0 w-2 h-2 bg-emerald-400 rounded-full"></span>
                        </button>

                        <div x-show="isNotifOpen" x-transition
                             class="absolute right-0 mt-3 w-80 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden z-20"
                             style="display: none;">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-sm font-bold text-blue-900">Notifications</p>
                            </div>
                            <div class="max-h-72 overflow-y-auto">
                                <template x-for="(notif, index) in notifications" :key="index">
                                    <div class="px-4 py-3 border-b border-slate-100 last:border-0 flex items-start space-x-2">
                                        <i class="fas fa-circle text-[6px] mt-1.5" :class="notif.read ? 'text-slate-300' : 'text-blue-600'"></i>
                                        <div>
                                            <p class="text-xs text-slate-700" x-text="notif.text"></p>
                                            <p class="text-[10px] text-slate-400 mt-0.5" x-text="notif.time"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-6 text-center text-xs text-slate-400">No notifications yet</div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="relative border-l pl-4 border-slate-200" @click.outside="isProfileOpen = false">
                        <button @click="isProfileOpen = !isProfileOpen" class="flex items-center space-x-2 cursor-pointer">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=dbeafe&color=1e3a8a" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs text-slate-400 transition" :class="isProfileOpen ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="isProfileOpen" x-transition
                             class="absolute right-0 mt-3 w-64 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden z-20"
                             style="display: none;">
                            <div class="p-4 flex items-center space-x-3 border-b border-slate-100">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=dbeafe&color=1e3a8a" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="text-sm font-bold text-blue-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-400">{{ auth()->user()->email }}</p>
                                </div>
                                <span class="ml-auto flex items-center space-x-1 text-[11px] text-emerald-600 font-semibold">
                                    <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                                    <span>Online</span>
                                </span>
                            </div>
                            <div class="p-4 grid grid-cols-2 gap-3 text-center">
                                <div class="bg-slate-50 rounded-lg py-2">
                                    <p class="text-sm font-bold text-blue-900">30</p>
                                    <p class="text-[10px] text-slate-400">Total Assigned</p>
                                </div>
                                <div class="bg-slate-50 rounded-lg py-2">
                                    <p class="text-sm font-bold text-blue-900">17</p>
                                    <p class="text-[10px] text-slate-400">Total Resolved</p>
                                </div>
                                <div class="bg-slate-50 rounded-lg py-2">
                                    <p class="text-sm font-bold text-blue-900">32m</p>
                                    <p class="text-[10px] text-slate-400">Avg. Response</p>
                                </div>
                                <div class="bg-slate-50 rounded-lg py-2">
                                    <p class="text-sm font-bold text-blue-900">3.9/5.0</p>
                                    <p class="text-[10px] text-slate-400">CSAT Score</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-500 hover:bg-red-50 flex items-center gap-2">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-8 flex-1">

                <template x-if="currentView === 'main'">
                    <div class="space-y-8">
                        <div>
                            <span class="px-4 py-2 bg-white text-emerald-600 font-semibold rounded-lg shadow-xs border border-slate-100 text-sm">Categories</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div @click="currentView = 'billing'" class="bg-white p-6 rounded-xl border border-slate-200 hover:shadow-md transition cursor-pointer group">
                                <h3 class="text-lg font-bold text-blue-900 group-hover:text-blue-600 transition">Billing & Invoicing Solutions</h3>
                                <p class="text-slate-500 text-sm mt-2 leading-relaxed">Clear, self-service goal: finding answers instantly without needing to wait for a support ticket.</p>
                                <div class="flex items-center space-x-4 mt-6 text-xs text-slate-400">
                                    <span><i class="fas fa-file-invoice mr-1"></i> Billing</span>
                                    <span><i class="fas fa-receipt mr-1"></i> Invoices</span>
                                </div>
                            </div>

                            <div @click="currentView = 'sla'" class="bg-white p-6 rounded-xl border border-slate-200 hover:shadow-md transition cursor-pointer group">
                                <h3 class="text-lg font-bold text-blue-900 group-hover:text-blue-600 transition">Suppport Tickets & SLA Timelines</h3>
                                <p class="text-slate-500 text-sm mt-2 leading-relaxed">Track your ticket priority, understand our resolution targets, and view escalation protocols.</p>
                                <div class="flex items-center space-x-4 mt-6 text-xs text-slate-400">
                                    <span><i class="fas fa-box mr-1"></i> Orders</span>
                                    <span><i class="fas fa-tracking mr-1"></i> Tracking</span>
                                </div>
                            </div>

                            <div @click="currentView = 'shipping'" class="bg-white p-6 rounded-xl border border-slate-200 hover:shadow-md transition cursor-pointer group">
                                <h3 class="text-lg font-bold text-blue-900 group-hover:text-blue-600 transition">Shipping & Transit Schedules</h3>
                                <p class="text-slate-500 text-sm mt-2 leading-relaxed">Track standard processing rules, regional delivery zones, and carrier delay procedures.</p>
                                <div class="flex items-center space-x-4 mt-6 text-xs text-slate-400">
                                    <span><i class="fas fa-undo mr-1"></i> Returns</span>
                                    <span><i class="fas fa-hand-holding-usd mr-1"></i> Refunds</span>
                                </div>
                            </div>

                            <div @click="currentView = 'returns'" class="bg-white p-6 rounded-xl border border-slate-200 hover:shadow-md transition cursor-pointer group">
                                <h3 class="text-lg font-bold text-blue-900 group-hover:text-blue-600 transition">Returns, Refunds & Warranty</h3>
                                <p class="text-slate-500 text-sm mt-2 leading-relaxed">Review product return eligibility, warranty coverage terms, and standard refund timelines.</p>
                                <div class="flex items-center space-x-4 mt-6 text-xs text-slate-400">
                                    <span><i class="fas fa-store mr-1"></i> Vendors</span>
                                    <span><i class="fas fa-shopping-cart mr-1"></i> Orders</span>
                                </div>
                            </div>
                        </div>

                        <div @click="currentView = 'articles'" class="bg-white p-6 rounded-xl border border-slate-200 cursor-pointer hover:shadow-sm">
                            <h3 class="text-md font-bold text-blue-900 mb-4">Popular Articles</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <template x-for="(article, index) in articleItems.slice(0, 2)" :key="index">
                                    <div class="flex justify-between items-start border-l-2 border-emerald-500 pl-3">
                                        <div>
                                            <span class="font-semibold text-emerald-600 block" x-text="article.title"></span>
                                            <span class="text-slate-500 text-xs line-clamp-1" x-text="article.description"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex justify-center pt-4">
                            <span class="bg-emerald-500 text-white font-medium px-4 py-1.5 rounded-full text-xs flex items-center space-x-1 shadow-xs">
                                <span x-text="'Rated ' + overallRating() + '/5'"></span>
                                <div class="flex space-x-0.5 text-[10px]">
                                    <template x-for="star in [1,2,3,4,5]" :key="star">
                                        <i class="fas fa-star" :class="Math.round(overallRating()) >= star ? '' : 'opacity-40'"></i>
                                    </template>
                                </div>
                            </span>
                        </div>
                    </div>
                </template>

                <template x-if="currentView === 'billing'">
                    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-xl font-bold text-blue-900">Billing & Invoicing Solutions</h3>
                                    <button @click="isModalOpen = true" class="w-6 h-6 bg-blue-600 text-white rounded-md flex items-center justify-center hover:bg-blue-700 transition shadow-xs text-sm cursor-pointer">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Manage your financial records and resolve discrepancy issues.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(item, index) in billingItems" :key="index">
                                <div class="bg-slate-50 p-6 rounded-xl border border-slate-100 relative">
                                    <div class="absolute top-4 right-4 flex items-center space-x-2 text-xs font-semibold">
                                        <template x-if="editingBillingId !== item.id">
                                            <div class="flex items-center space-x-2">
                                                <button @click="startEditBilling(item)" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition cursor-pointer">Edit</button>
                                                <button @click="deleteBillingItem(item)" class="px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-lg transition cursor-pointer">Delete</button>
                                            </div>
                                        </template>
                                        <template x-if="editingBillingId === item.id">
                                            <div class="flex items-center space-x-2">
                                                <button @click="saveEditBilling(item)" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg transition cursor-pointer">Save</button>
                                                <button @click="cancelEditBilling()" class="px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg transition cursor-pointer">Cancel</button>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex items-start space-x-4">
                                        <div class="bg-blue-50 text-blue-600 p-3 rounded-xl flex items-center justify-center">
                                            <i :class="'fas ' + item.icon"></i>
                                        </div>
                                        <div class="flex-1 space-y-2">
                                            <template x-if="editingBillingId !== item.id">
                                                <div class="space-y-2">
                                                    <div class="flex items-center space-x-2">
                                                        <h4 class="font-bold text-blue-900 text-md" x-text="item.title"></h4>
                                                        <template x-if="item.isNew">
                                                            <span class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider animate-pulse">Newly Added</span>
                                                        </template>
                                                    </div>
                                                    <p class="text-xs text-slate-500 font-medium">Problem: <span class="italic text-slate-600" x-text="'&ldquo;' + item.problem + '&rdquo;'"></span></p>
                                                </div>
                                            </template>
                                            <template x-if="editingBillingId === item.id">
                                                <div class="space-y-2 pr-24">
                                                    <label class="block text-[10px] font-semibold text-slate-500 uppercase">Title</label>
                                                    <input type="text" x-model="editBillingTitle" class="w-full px-3 py-1.5 text-sm border border-blue-300 rounded-lg focus:outline-none focus:border-blue-500">
                                                    <label class="block text-[10px] font-semibold text-slate-500 uppercase pt-1">Problem</label>
                                                    <textarea x-model="editBillingProblem" rows="2" class="w-full px-3 py-1.5 text-sm border border-blue-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                                                </div>
                                            </template>
                                            <div class="text-xs text-slate-500 space-y-1 pt-1 pl-4">
                                                <p class="font-semibold text-slate-700 mb-1">Step-by-Step Solution:</p>
                                                <template x-for="(step, sIdx) in item.steps" :key="sIdx">
                                                    <div class="flex space-x-1.5 py-0.5">
                                                        <span class="text-slate-400" x-text="(sIdx + 1) + '.'"></span>
                                                        <span x-text="step" class="text-slate-600"></span>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="flex items-center space-x-1.5 pt-2 text-xs">
                                                <span class="text-slate-400 mr-1">Was this helpful?</span>
                                                <template x-for="star in [1,2,3,4,5]" :key="star">
                                                    <i class="fas fa-star cursor-pointer"
                                                       :class="(item.rating || 0) >= star ? 'text-amber-400' : 'text-slate-200'"
                                                       @click="rateItem(item, star)"></i>
                                                </template>
                                                <template x-if="item.rating">
                                                    <span class="text-slate-400 ml-1">Thanks for your feedback!</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="currentView === 'sla'">
                    <div class="max-w-5xl mx-auto space-y-6">
                        <div class="bg-white p-6 rounded-xl border border-slate-200">
                            <h3 class="text-xl font-bold text-blue-900">Support Tickets & SLA Timelines</h3>
                            <p class="text-xs text-slate-400 mt-1">Track priority handling, system response windows, and escalation path protocols.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-xl border border-slate-200 flex flex-col justify-between space-y-4">
                                <div>
                                    <div class="flex justify-center mb-3">
                                        <div class="w-10 h-10 bg-blue-50 text-blue-900 rounded-xl flex items-center justify-center border border-blue-100">
                                            <i class="fas fa-shield-alt text-lg"></i>
                                        </div>
                                    </div>
                                    <h4 class="font-bold text-blue-900 text-center text-sm mb-4">Support Ticket Priority Levels</h4>
                                    <div class="grid grid-cols-3 gap-2 text-center">
                                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                            <span class="text-[10px] font-bold text-red-500 uppercase block">Critical</span>
                                            <span class="text-sm font-bold text-blue-950">&lt; 2 Hours</span>
                                            <span class="text-[9px] text-slate-400 block mt-1">System wide failure</span>
                                        </div>
                                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                            <span class="text-[10px] font-bold text-amber-500 uppercase block">High</span>
                                            <span class="text-sm font-bold text-blue-950">&lt; 12 Hours</span>
                                            <span class="text-[9px] text-slate-400 block mt-1">Major loss of function</span>
                                        </div>
                                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                            <span class="text-[10px] font-bold text-emerald-500 uppercase block">Standard</span>
                                            <span class="text-sm font-bold text-blue-950">&lt; 24 Hours</span>
                                            <span class="text-[9px] text-slate-400 block mt-1">General queries & tasks</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 bg-blue-50/50 rounded-xl border-l-4 border-blue-900 text-xs">
                                    <p class="font-bold text-blue-900">PROBLEM FIX & HELPFUL ADVICE</p>
                                    <p class="text-slate-600 mt-1 leading-relaxed"><span class="font-semibold text-blue-900">Pro-Tip:</span> Clearly document steps to reproduce issues to help our engineering team meet the Critical resolution window faster.</p>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-xl border border-slate-200 flex flex-col justify-between space-y-4">
                                <div>
                                    <div class="flex justify-center mb-3">
                                        <div class="w-10 h-10 bg-blue-50 text-blue-900 rounded-xl flex items-center justify-center border border-blue-100">
                                            <i class="fas fa-chart-line text-lg"></i>
                                        </div>
                                    </div>
                                    <h4 class="font-bold text-blue-900 text-center text-sm mb-4">Escalation Path Procedures</h4>
                                    
                                    <div class="space-y-3 max-w-sm mx-auto py-2">
                                        <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                                            <div class="flex items-center space-x-2 bg-slate-50 px-3 py-2 rounded-lg border w-[46%] shadow-xs">
                                                <span class="w-4 h-4 bg-blue-900 text-white rounded-full flex items-center justify-center text-[9px]">1</span>
                                                <span class="text-[11px]">Level 2 Support Tech</span>
                                            </div>
                                            <i class="fas fa-chevron-right text-slate-300"></i>
                                            <div class="flex items-center space-x-2 bg-slate-50 px-3 py-2 rounded-lg border w-[46%] shadow-xs">
                                                <span class="w-4 h-4 bg-blue-900 text-white rounded-full flex items-center justify-center text-[9px]">2</span>
                                                <span class="text-[11px]">Account Manager CC</span>
                                            </div>
                                        </div>
                                        <div class="flex justify-center">
                                            <div class="flex items-center space-x-2 bg-slate-50 px-3 py-1.5 rounded-lg border text-xs shadow-xs">
                                                <span class="w-4 h-4 bg-blue-900 text-white rounded-full flex items-center justify-center text-[9px]">3</span>
                                                <span class="font-semibold text-slate-700 text-[11px]">Full Audit Logging</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 bg-blue-50/50 rounded-xl border-l-4 border-blue-900 text-xs">
                                    <p class="font-bold text-blue-900">PROBLEM FIX & HELPFUL ADVICE</p>
                                    <p class="text-slate-600 mt-1 leading-relaxed"><span class="font-semibold text-blue-900">Pro-Tip:</span> If a ticket exceeds 50% of its SLA time an Account Manager is automatically notified to oversee the resolution path.</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl border border-slate-200 space-y-5">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-center text-blue-900">
                                    <i class="fas fa-hourglass-half text-lg"></i>
                                </div>
                                <h4 class="font-bold text-blue-900 text-md">SLA Pause Conditions & Clock Management</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex items-start space-x-3">
                                    <i class="fas fa-user-clock text-slate-400 text-sm mt-0.5"></i>
                                    <div>
                                        <p class="text-xs font-bold text-blue-950">Pending Customer</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Clock stops during info requests</p>
                                    </div>
                                </div>
                                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex items-start space-x-3">
                                    <i class="fas fa-tools text-slate-400 text-sm mt-0.5"></i>
                                    <div>
                                        <p class="text-xs font-bold text-blue-950">Scheduled Maintenance</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Exempt from uptime SLA counts</p>
                                    </div>
                                </div>
                                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex items-start space-x-3">
                                    <i class="fas fa-handshake text-slate-400 text-sm mt-0.5"></i>
                                    <div>
                                        <p class="text-xs font-bold text-blue-950">Third-Party Holds</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">External vendor dependencies</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-blue-50/50 rounded-xl border-l-4 border-blue-900 text-xs">
                                <p class="font-bold text-blue-900">PROBLEM FIX & HELPFUL ADVICE</p>
                                <p class="text-slate-600 mt-1 leading-relaxed"><span class="font-semibold text-blue-900">Pro-Tip:</span> Response time metrics are adjusted for global holidays and pre-announced infrastructure maintenance windows.</p>
                            </div>
                        </div>

                    </div>
                </template>

                <template x-if="currentView === 'shipping'">
                    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 space-y-6">
                        <div>
                            <h3 class="text-xl font-bold text-blue-900">Shipping & Transit Schedules</h3>
                            <p class="text-xs text-slate-400 mt-1">Track standard processing rules, regional delivery zones, and carrier procedures.</p>
                        </div>
                        <div class="space-y-4 text-sm text-slate-600">
                            <div class="p-4 bg-slate-50 rounded-xl border flex items-start space-x-3">
                                <div class="p-2 bg-blue-50 text-blue-900 rounded-lg"><i class="fas fa-box-open"></i></div>
                                <div>
                                    <h4 class="font-bold text-blue-950 mb-1 text-xs">Standard Order Processing & Handling</h4>
                                    <ul class="list-disc pl-5 space-y-1 text-[11px]">
                                        <li>Warehouse Validation: Every order undergoes a 4-point inventory check prior to release.</li>
                                        <li>24h Packaging: Standard retail units are boxed and ready for carrier pickup within 24 business hours.</li>
                                        <li>Bulk Freight: Larger palletized shipments require 48 hours for secure strapping and load balancing.</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-xl border flex items-start space-x-3">
                                <div class="p-2 bg-blue-50 text-blue-900 rounded-lg"><i class="fas fa-map-marked-alt"></i></div>
                                <div>
                                    <h4 class="font-bold text-blue-950 mb-1 text-xs">Transit Timelines & Shipping Zones</h4>
                                    <ul class="list-disc pl-5 space-y-1 text-[11px]">
                                        <li>Zone A (Metropolitan): 1-2 business days. Direct routing from regional hubs ensures rapid last-mile delivery.</li>
                                        <li>Zone B (Suburban/Regional): 3-5 business days. Transit involves one intermediate distribution center stop.</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-xl border flex items-start space-x-3">
    <div class="p-2 bg-blue-50 text-blue-900 rounded-lg"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div>
        <h4 class="font-bold text-blue-950 mb-1 text-xs">Carrier Delay Protocols</h4>
        <ul class="list-disc pl-5 space-y-1 text-[11px]">
            <li>Weather/Act of God: SLA suspended. Rerouting algorithms activate automatically to find alternative transit paths.</li>
            <li>Capacity Constraints: Peak season volume may add 24-48 hours. Priority given to expedited shipping tiers.</li>
        </ul>
    </div>
</div>
                        </div>
                    </div>
                </template>

                <template x-if="currentView === 'returns'">
                    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-blue-900">Returns, Refunds & Warranty</h3>
                                <p class="text-xs text-slate-400 mt-1">Review return eligibility, warranty protection guidelines, and credit processing timelines.</p>
                            </div>
                            <button @click="isRefundFormOpen = true" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#1e3a8a] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">
                                <i class="fas fa-paper-plane"></i>
                                New Return Request
                            </button>
                        </div>

                        <div class="rounded-xl border border-blue-100 bg-gradient-to-r from-blue-50 to-slate-50 p-5">
                            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h4 class="text-sm font-bold text-blue-900">Need help with a return or refund?</h4>
                                    <p class="text-xs text-slate-500">Upload a photo, add a short title, and describe the issue. Your request will be submitted instantly.</p>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Fast support workflow
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-slate-600">
                            <div class="p-5 bg-slate-50 rounded-xl border space-y-2">
                                <h4 class="font-bold text-blue-950 text-sm">Return Policy & Rules</h4>
                                <p><span class="font-bold text-emerald-600">✓</span> Items must be in original packaging</p>
                                <p><span class="font-bold text-emerald-600">✓</span> Free return shipping labels provided</p>
                            </div>
                            <div class="p-5 bg-slate-50 rounded-xl border space-y-2">
                                <h4 class="font-bold text-blue-950 text-sm">Enterprise Warranty Coverage</h4>
                                <p><span class="font-bold text-emerald-600">✓</span> 12-month full unit replacement</p>
                                <p><span class="font-bold text-emerald-600">✓</span> 48-hour expedited shipping on parts</p>
                            </div>
                            <div class="p-5 bg-slate-50 rounded-xl border space-y-2">
                                <h4 class="font-bold text-blue-950 text-sm">Order & Shipping Support</h4>
                                <p><span class="font-bold text-emerald-600">✓</span> Real-time package tracking</p>
                                <p><span class="font-bold text-emerald-600">✓</span> Address modification requests</p>
                            </div>
                            <div class="p-5 bg-slate-50 rounded-xl border space-y-2">
                                <h4 class="font-bold text-blue-950 text-sm">Technical Assistance</h4>
                                <p><span class="font-bold text-emerald-600">✓</span> 24/7 self-service knowledge base</p>
                                <p><span class="font-bold text-emerald-600">✓</span> Live chat with a technician</p>
                            </div>
                        </div>

                        <template x-if="refundRequests.length > 0">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-bold text-blue-900">Recent return requests</h4>
                                    <div class="flex items-center space-x-2">
                                        <template x-for="tab in ['all', 'pending', 'approved', 'rejected']" :key="tab">
                                            <button @click="refundStatusFilter = tab"
                                                    class="px-2.5 py-1 rounded-lg text-[10px] font-semibold capitalize transition"
                                                    :class="refundStatusFilter === tab
                                                        ? 'bg-[#1e3a8a] text-white'
                                                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                                    x-text="tab"></button>
                                        </template>
                                    </div>
                                </div>
                                <template x-for="request in filteredRefundRequests" :key="request.id">
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800" x-text="request.title"></p>
                                                <p class="mt-1 text-xs text-slate-500" x-text="request.description"></p>
                                                <p class="mt-2 text-[11px] text-slate-400" x-text="'Submitted ' + request.submittedAt"></p>
                                            </div>
                                            <div class="flex flex-col items-end space-y-1.5">
                                                <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide"
                                                      :class="{
                                                          'bg-amber-100 text-amber-700': request.status === 'pending',
                                                          'bg-emerald-100 text-emerald-700': request.status === 'approved',
                                                          'bg-rose-100 text-rose-700': request.status === 'rejected'
                                                      }"
                                                      x-text="request.status"></span>
                                                <div class="flex items-center space-x-2 text-[10px] font-semibold">
                                                    <button x-show="request.status === 'pending'"
                                                            @click="approveRefundRequest(request.id)"
                                                            class="text-emerald-600 hover:text-emerald-800 hover:underline cursor-pointer">
                                                        Approve
                                                    </button>
                                                    <button x-show="request.status === 'pending'"
                                                            @click="rejectRefundRequest(request.id)"
                                                            class="text-amber-600 hover:text-amber-800 hover:underline cursor-pointer">
                                                        Reject
                                                    </button>
                                                    <button @click="adminDeleteRefundRequest(request.id, request.title)"
                                                            class="text-rose-500 hover:text-rose-700 hover:underline cursor-pointer">
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <template x-if="request.imagePreview">
                                            <img :src="request.imagePreview" alt="Refund attachment" class="mt-3 h-24 w-full rounded-lg border border-slate-200 object-cover">
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="currentView === 'articles'">
                    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 space-y-6">
                        <div class="flex items-center justify-between border-b pb-2">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-xl font-bold text-blue-900">Popular Articles Knowledgebase</h3>
                                <button @click="isArticleModalOpen = true" class="w-6 h-6 bg-blue-600 text-white rounded-md flex items-center justify-center hover:bg-blue-700 transition shadow-xs text-sm cursor-pointer">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <template x-for="(article, index) in articleItems" :key="index">
                                <div>
                                    <div class="flex items-start justify-between gap-3">
                                        <h4 x-show="editingArticleId !== article.id" class="text-md font-bold text-emerald-600" x-text="article.title"></h4>
                                        <input x-show="editingArticleId === article.id" type="text" x-model="editArticleTitle" class="flex-1 px-3 py-1.5 text-sm font-bold text-emerald-700 border border-blue-300 rounded-lg focus:outline-none focus:border-blue-500">
                                        <div class="flex items-center space-x-2 text-xs font-semibold shrink-0">
                                            <template x-if="editingArticleId !== article.id">
                                                <div class="flex items-center space-x-2">
                                                    <button @click="startEditArticle(article)" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition cursor-pointer">Edit</button>
                                                    <button @click="deleteArticle(article)" class="px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-lg transition cursor-pointer">Delete</button>
                                                </div>
                                            </template>
                                            <template x-if="editingArticleId === article.id">
                                                <div class="flex items-center space-x-2">
                                                    <button @click="saveEditArticle(article)" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg transition cursor-pointer">Save</button>
                                                    <button @click="cancelEditArticle()" class="px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg transition cursor-pointer">Cancel</button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <p x-show="editingArticleId !== article.id" class="text-xs text-slate-500 mt-1 leading-relaxed" x-text="article.description"></p>
                                    <textarea x-show="editingArticleId === article.id" x-model="editArticleDescription" rows="3" class="w-full mt-2 px-3 py-1.5 text-xs border border-blue-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                                    <div class="flex items-center space-x-1.5 pt-2 text-xs">
                                        <span class="text-slate-400 mr-1">Was this helpful?</span>
                                        <template x-for="star in [1,2,3,4,5]" :key="star">
                                            <i class="fas fa-star cursor-pointer"
                                               :class="(article.rating || 0) >= star ? 'text-amber-400' : 'text-slate-200'"
                                               @click="rateItem(article, star)"></i>
                                        </template>
                                        <template x-if="article.rating">
                                            <span class="text-slate-400 ml-1">Thanks for your feedback!</span>
                                        </template>
                                    </div>
                                    <hr class="border-slate-100 mt-6" x-show="index !== articleItems.length - 1">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

            </main>
        </div>
    </div>

    <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4" 
         x-show="isModalOpen" 
         x-transition
         style="display: none;">
        
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden border border-slate-100 p-6 space-y-4">
            <div>
                <h3 class="text-lg font-bold text-blue-900">Add New Billing Information</h3>
                <p class="text-xs text-slate-400">Create a new self-service entry for the billing portal.</p>
            </div>

            <div class="space-y-3 text-xs">
                <div class="space-y-1">
                    <label class="block font-semibold text-slate-600">Title</label>
                    <input type="text" x-model="newTitle" placeholder="e.g., Late Fees Policy" 
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                
                <div class="space-y-1">
                    <label class="block font-semibold text-slate-600">Problem Statement</label>
                    <textarea x-model="newProblem" rows="2" placeholder="e.g., Why was I charged a late fee?" 
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="block font-semibold text-slate-600">Solution Steps (Press Enter for new line)</label>
                    <textarea x-model="newSolution" rows="4" placeholder="Step 1...&#10;Step 2...&#10;Step 3..." 
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2 text-xs font-semibold">
                <button @click="isModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition cursor-pointer">
                    Cancel
                </button>
                <button @click="addNewBillingItem()" class="px-4 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white rounded-lg transition shadow-xs cursor-pointer">
                    Save Item
                </button>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4" 
         x-show="isRefundFormOpen" 
         x-transition
         style="display: none;">
        
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden border border-slate-100 p-6 space-y-4">

            <template x-if="!isSubmittingRefund && !refundSubmitted">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-bold text-blue-900">Submit a Return or Refund Request</h3>
                        <p class="text-xs text-slate-400">Upload an image of the item, add a short title, and describe the issue.</p>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-600">Title</label>
                            <input type="text" x-model="refundTitle" placeholder="e.g., Damaged product received" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-semibold text-slate-600">Description</label>
                            <textarea x-model="refundDescription" rows="4" placeholder="Tell us what happened and what support is needed..." class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block font-semibold text-slate-600">Upload image</label>
                            <input type="file" accept="image/*" x-ref="refundImageInput" @change="handleRefundImageUpload($event)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 file:mr-3 file:rounded file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                            <template x-if="refundImagePreview">
                                <img :src="refundImagePreview" alt="Preview" class="h-32 w-full rounded-lg border border-slate-200 object-cover">
                            </template>
                            <p x-show="refundImageName" class="text-[11px] text-slate-500" x-text="'Selected file: ' + refundImageName"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-2 text-xs font-semibold">
                        <button @click="isRefundFormOpen = false; resetRefundForm()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition cursor-pointer">
                            Cancel
                        </button>
                        <button @click="submitRefundRequest()" class="px-4 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white rounded-lg transition shadow-xs cursor-pointer">
                            Send
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="isSubmittingRefund">
                <div class="flex flex-col items-center justify-center py-10">
                    <svg class="animate-spin h-8 w-8 text-blue-600" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <p class="mt-3 text-sm text-slate-500">Submitting your request...</p>
                </div>
            </template>

            <template x-if="refundSubmitted">
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="h-12 w-12 rounded-full bg-emerald-100 flex items-center justify-center">
                        <i class="fas fa-check text-emerald-600 text-lg"></i>
                    </div>
                    <p class="mt-3 text-sm font-bold text-blue-900">Successful!</p>
                    <p class="mt-1 text-xs text-slate-500">Your return/refund request has been submitted.</p>
                    <button @click="closeRefundModal()" class="mt-5 px-4 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white text-xs font-semibold rounded-lg transition shadow-xs cursor-pointer">
                        Close
                    </button>
                </div>
            </template>

        </div>
    </div>

    <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4" 
         x-show="isArticleModalOpen" 
         x-transition
         style="display: none;">
        
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden border border-slate-100 p-6 space-y-4">
            <div>
                <h3 class="text-lg font-bold text-blue-900">Add New Article</h3>
                <p class="text-xs text-slate-400">Create a new self-service entry for the Popular Articles Knowledgebase.</p>
            </div>

            <div class="space-y-3 text-xs">
                <div class="space-y-1">
                    <label class="block font-semibold text-slate-600">Title</label>
                    <input type="text" x-model="newArticleTitle" placeholder="e.g., Updating Payment Methods" 
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div class="space-y-1">
                    <label class="block font-semibold text-slate-600">Content</label>
                    <textarea x-model="newArticleContent" rows="5" placeholder="Common Issue: ... Try this to fix: ... Pro-Tip: ..." 
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2 text-xs font-semibold">
                <button @click="isArticleModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition cursor-pointer">
                    Cancel
                </button>
                <button @click="addNewArticle()" class="px-4 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white rounded-lg transition shadow-xs cursor-pointer">
                    Save Article
                </button>
            </div>
        </div>
    </div>

    <!-- Shared delete confirmation modal -->
    <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
         x-show="deleteModal.open"
         x-transition
         style="display: none;">
        <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden border border-slate-100 p-6 space-y-4 text-center">
            <div class="mx-auto h-12 w-12 rounded-full bg-rose-100 flex items-center justify-center">
                <i class="fas fa-trash text-rose-600"></i>
            </div>
            <div>
                <h3 class="text-md font-bold text-blue-950">Delete this item?</h3>
                <p class="mt-1 text-xs text-slate-500">
                    You're about to delete <span class="font-semibold" x-text="deleteModal.label"></span>. This cannot be undone.
                </p>
            </div>
            <div class="flex items-center justify-center space-x-2 pt-2 text-xs font-semibold">
                <button @click="closeDeleteModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition cursor-pointer">
                    Cancel
                </button>
                <button @click="confirmDeleteModal()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition shadow-xs cursor-pointer">
                    Delete
                </button>
            </div>
        </div>
    </div>

</body>
</html>