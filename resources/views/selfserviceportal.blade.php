<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Service - Self-Service Portal</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        <aside class="w-64 bg-[#1e3a8a] text-white flex flex-col justify-between shadow-lg">
            <div>
                <div class="p-6 border-b border-blue-900">
                    <h1 class="text-xl font-bold tracking-wide">Customer Service</h1>
                </div>
                <nav class="mt-6 px-4 space-y-2">
                    <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-blue-900 transition">
                        <i class="fas fa-th-large w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-blue-900 transition">
                        <i class="fas fa-ticket-alt w-5"></i>
                        <span>Ticket Management</span>
                    </a>
                    <button @click="currentView = 'main'" :class="currentView === 'main' ? 'bg-[#2563eb] text-white' : 'text-slate-300 hover:bg-blue-900'" class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition text-left cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-laptop-house w-5"></i>
                            <span>Self-Service Portal</span>
                        </div>
                        <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                    </button>
                    <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-blue-900 transition">
                        <i class="fas fa-comments w-5"></i>
                        <span>Communication History</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-blue-900 transition">
                        <i class="fas fa-history w-5"></i>
                        <span>SLA Tracking</span>
                    </a>
                </nav>
            </div>
            
            <div class="p-4 bg-blue-950 m-4 rounded-xl flex items-center space-x-3">
                <div class="bg-blue-900 p-2 rounded-lg text-slate-300">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Support Status</p>
                    <p class="text-sm font-semibold text-emerald-400">Online</p>
                </div>
            </div>
        </aside>

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
                            <img src="https://ui-avatars.com/api/?name=Henry+Taylee&background=dbeafe&color=1e3a8a" alt="User Avatar" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-medium text-slate-700">Henry Taylee</span>
                            <i class="fas fa-chevron-down text-xs text-slate-400 transition" :class="isProfileOpen ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="isProfileOpen" x-transition
                             class="absolute right-0 mt-3 w-64 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden z-20"
                             style="display: none;">
                            <div class="p-4 flex items-center space-x-3 border-b border-slate-100">
                                <img src="https://ui-avatars.com/api/?name=Henry+Taylee&background=dbeafe&color=1e3a8a" alt="User Avatar" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="text-sm font-bold text-blue-900">Henry Taylee</p>
                                    <p class="text-xs text-slate-400">Support Agent</p>
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
                                    <div class="flex items-start space-x-4">
                                        <div class="bg-blue-50 text-blue-600 p-3 rounded-xl flex items-center justify-center">
                                            <i :class="'fas ' + item.icon"></i>
                                        </div>
                                        <div class="flex-1 space-y-2">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="font-bold text-blue-900 text-md" x-text="item.title"></h4>
                                                <template x-if="item.isNew">
                                                    <span class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider animate-pulse">Newly Added</span>
                                                </template>
                                            </div>
                                            <p class="text-xs text-slate-500 font-medium">Problem: <span class="italic text-slate-600" x-text="'&ldquo;' + item.problem + '&rdquo;'"></span></p>
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
                        <div>
                            <h3 class="text-xl font-bold text-blue-900">Returns, Refunds & Warranty</h3>
                            <p class="text-xs text-slate-400 mt-1">Review return eligibility, warranty protection guidelines, and credit processing timelines.</p>
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
<div class="p-5 bg-slate-50 rounded-xl border space-y-2">
    <h4 class="font-bold text-blue-950 text-sm">Account & Subscription</h4>
    <p><span class="font-bold text-emerald-600">✓</span> Manage billing and payment methods</p>
    <p><span class="font-bold text-emerald-600">✓</span> View and download past invoices</p>
</div>

<div class="p-5 bg-slate-50 rounded-xl border space-y-2">
    <h4 class="font-bold text-blue-950 text-sm">Product Registration</h4>
    <p><span class="font-bold text-emerald-600">✓</span> Register new hardware purchases</p>
    <p><span class="font-bold text-emerald-600">✓</span> Access digital user manuals</p>
</div>


                            

                            
                        </div>
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
                                    <h4 class="text-md font-bold text-emerald-600" x-text="article.title"></h4>
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed" x-text="article.description"></p>
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

</body>
</html>