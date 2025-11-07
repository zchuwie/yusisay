    <x-admin-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Content Moderation
            </h2>
        </x-slot>

        <div x-data="moderationPanel()" x-init="init()" class="space-y-8 pb-6">
            <h1 class="text-3xl font-bold text-gray-800">Content Moderation Panel</h1>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'reports'" 
                            :class="activeTab === 'reports' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <i data-lucide="flag" class="w-4 h-4 inline mr-2"></i>
                        Reported Content
                        <span x-show="totalReports > 0" x-text="totalReports" class="ml-2 bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded-full"></span>
                    </button>
                    <button @click="activeTab = 'censoredWords'" 
                            :class="activeTab === 'censoredWords' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <i data-lucide="shield-ban" class="w-4 h-4 inline mr-2"></i>
                        Censored Words
                        <span x-show="censoredWords.length > 0" x-text="censoredWords.length" class="ml-2 bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded-full"></span>
                    </button>
                </nav>
            </div>

            <!-- Custom Message/Toast Box -->
            <div x-show="message" :class="{ 
                'bg-green-100 border-green-400 text-green-700': messageType === 'success',
                'bg-red-100 border-red-400 text-red-700': messageType === 'error'
            }" class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-md border"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-4"
            >
                <span x-text="message"></span>
            </div>

            <!-- REPORTS TAB CONTENT -->
            <div x-show="activeTab === 'reports'" class="space-y-6">
                <div class="flex items-center justify-end">
                    <div class="relative w-full max-w-xs">
                        <input type="text" x-model.debounce.300ms="searchQuery" placeholder="Search reported post title..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150">
                        <i data-lucide="search"
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                    </div>
                </div>

                <div x-show="loading" class="text-center py-12 text-red-600 font-semibold">
                    <i data-lucide="loader-2" class="w-6 h-6 mr-2 inline-block animate-spin"></i> Loading reports...
                </div>
                <div x-show="error" class="text-center py-12 text-red-600 font-semibold bg-red-50 rounded-lg border border-red-200">
                    <i data-lucide="alert-triangle" class="w-6 h-6 mr-2 inline-block"></i> <span x-text="error"></span>
                </div>
                <div x-show="!loading && !error && reports.length === 0" class="text-center py-12 text-gray-500 bg-white rounded-xl shadow-md">
                    No reported content found.
                </div>
                
                <div x-show="!loading && !error && reports.length > 0" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-900" @click="sortBy('post_id')">
                                    Post ID <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-900" @click="sortBy('posts.title')">
                                    Post Title <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Censored Content Preview
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-900" @click="sortBy('total_reports')">
                                    Total Reports <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="report in reports" :key="report.post_id">
                                <tr class="hover:bg-gray-50 transition duration-100 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="report.post_id"></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate" x-text="report.title"></td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-sm truncate" x-text="report.censored_content"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" x-text="report.total_reports"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a :href="`/posts/${report.post_id}`" target="_blank" title="View Post"
                                                class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-150">
                                                <i data-lucide="external-link" class="w-5 h-5"></i>
                                            </a>
                                            <button @click="resolveReportModal(report.post_id)" title="Mark as Resolved"
                                                class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-150">
                                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div class="flex items-center justify-between p-4 border-t border-gray-200 bg-white">
                        <span class="text-sm text-gray-600" x-text="paginationSummary"></span>
                        <div class="flex items-center space-x-2">
                            <button @click="prevPage" :disabled="currentPage === 1"
                                class="p-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition disabled:opacity-50 disabled:bg-white disabled:text-gray-400">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                            </button>
                            <button @click="nextPage" :disabled="currentPage >= totalPages"
                                class="p-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition disabled:opacity-50 disabled:bg-white disabled:text-gray-400">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CENSORED WORDS TAB CONTENT -->
            <div x-show="activeTab === 'censoredWords'" class="space-y-6">
                <!-- Add New Word Section -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i data-lucide="plus-circle" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Add New Censored Word
                    </h3>
                    <div class="flex gap-3">
                        <input type="text" x-model="newWord" @keydown.enter="addCensoredWord" 
                            placeholder="Enter word to censor..."
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                        <button @click="addCensoredWord" :disabled="!newWord.trim() || addingWord"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:bg-indigo-400 disabled:cursor-not-allowed font-medium">
                            <span x-show="!addingWord">Add Word</span>
                            <span x-show="addingWord">
                                <i data-lucide="loader-2" class="w-4 h-4 inline-block animate-spin"></i>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Censored Words List -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i data-lucide="list" class="w-5 h-5 mr-2 text-indigo-600"></i>
                            Censored Words List
                            <span class="ml-3 text-sm font-normal text-gray-500">
                                (<span x-text="censoredWords.length"></span> words)
                            </span>
                        </h3>
                    </div>

                    <div x-show="wordsLoading" class="text-center py-12 text-indigo-600 font-semibold">
                        <i data-lucide="loader-2" class="w-6 h-6 mr-2 inline-block animate-spin"></i> Loading censored words...
                    </div>

                    <div x-show="!wordsLoading && censoredWords.length === 0" class="text-center py-12 text-gray-500">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                        <p>No censored words configured yet.</p>
                        <p class="text-sm text-gray-400 mt-1">Add words above to start filtering content.</p>
                    </div>

                    <div x-show="!wordsLoading && censoredWords.length > 0" class="divide-y divide-gray-200">
                        <template x-for="word in censoredWords" :key="word.id">
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-gray-100 px-4 py-2 rounded-lg font-mono text-sm font-semibold text-gray-800" x-text="word.word"></div>
                                    <div class="text-xs text-gray-500">
                                        Added: <span x-text="formatDate(word.created_at)"></span>
                                    </div>
                                </div>
                                <button @click="deleteCensoredWordModal(word.id, word.word)" 
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Resolve Report Modal -->
            <div x-show="modalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click.away="modalOpen = false">
                <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm transform transition-all" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 mr-2 text-red-500"></i> Confirm Resolution
                    </h3>
                    <p class="mt-4 text-sm text-gray-600">
                        Are you sure you want to mark all pending reports for Post ID <span x-text="modalPostId" class="font-mono bg-gray-100 px-1 py-0.5 rounded"></span> as resolved/dismissed? This action cannot be undone.
                    </p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="modalOpen = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button @click="confirmResolve()"
                                :disabled="resolveLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition disabled:bg-green-400">
                            <span x-show="!resolveLoading">Resolve Reports</span>
                            <span x-show="resolveLoading">
                                <i data-lucide="loader-2" class="w-4 h-4 mr-1 inline-block animate-spin"></i> Resolving...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Delete Censored Word Modal -->
            <div x-show="deleteModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click.away="deleteModalOpen = false">
                <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm transform transition-all" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-lucide="trash-2" class="w-6 h-6 mr-2 text-red-500"></i> Delete Censored Word
                    </h3>
                    <p class="mt-4 text-sm text-gray-600">
                        Are you sure you want to remove "<span x-text="deleteWordText" class="font-mono bg-gray-100 px-1 py-0.5 rounded font-semibold"></span>" from the censored words list?
                    </p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="deleteModalOpen = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button @click="confirmDeleteWord()"
                                :disabled="deletingWord"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition disabled:bg-red-400">
                            <span x-show="!deletingWord">Delete Word</span>
                            <span x-show="deletingWord">
                                <i data-lucide="loader-2" class="w-4 h-4 mr-1 inline-block animate-spin"></i> Deleting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Remove Post Modal -->
            <div x-show="removePostModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click.away="removePostModalOpen = false">
                <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm transform transition-all" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-lucide="alert-octagon" class="w-6 h-6 mr-2 text-red-500"></i> Remove Post
                    </h3>
                    <p class="mt-4 text-sm text-gray-600">
                        Are you sure you want to <span class="font-semibold text-red-600">permanently delete</span> Post ID <span x-text="removePostId" class="font-mono bg-gray-100 px-1 py-0.5 rounded"></span>? This will remove the post and all associated reports. This action cannot be undone.
                    </p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="removePostModalOpen = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button @click="confirmRemovePost()"
                                :disabled="removingPost"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition disabled:bg-red-400">
                            <span x-show="!removingPost">Remove Post</span>
                            <span x-show="removingPost">
                                <i data-lucide="loader-2" class="w-4 h-4 mr-1 inline-block animate-spin"></i> Removing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const lucide = window.lucide;

            function moderationPanel() {
                return {
                    activeTab: 'reports',

                    // Reports data
                    reports: [],
                    loading: true,
                    error: null,
                    searchQuery: '',
                    currentPage: 1,
                    perPage: 10,
                    totalReports: 0,
                    totalPages: 1,
                    sortColumn: 'total_reports',
                    sortDirection: 'desc',

                    // Censored words data
                    censoredWords: [],
                    wordsLoading: true,
                    newWord: '',
                    addingWord: false,

                    // Modal state
                    modalOpen: false,
                    modalPostId: null,
                    deleteModalOpen: false,
                    deleteWordId: null,
                    deleteWordText: '',
                    deletingWord: false,
                    removePostModalOpen: false,
                    removePostId: null,
                    removingPost: false,

                    // Message state
                    message: null,
                    messageType: null,
                    resolveLoading: false,

                    get paginationSummary() {
                        if (this.totalReports === 0) return 'Showing 0 results';
                        const start = (this.currentPage - 1) * this.perPage + 1;
                        const end = Math.min(this.currentPage * this.perPage, this.totalReports);
                        return `Showing ${start} to ${end} of ${this.totalReports} posts with reports`;
                    },

                    init() {
                        this.fetchReports();
                        this.fetchCensoredWords();
                        this.$watch('searchQuery', () => { this.currentPage = 1; this.fetchReports(); });
                        this.$watch('currentPage', () => this.fetchReports());
                        this.$watch('activeTab', () => setTimeout(() => lucide.createIcons(), 0));
                        this.$watch('message', () => {
                            if (this.message) setTimeout(() => { this.message = null; }, 5000);
                        });
                        setTimeout(() => lucide.createIcons(), 200);
                    },

                    async fetchReports() {
                        this.loading = true;
                        this.error = null;
                        try {
                            const params = new URLSearchParams({
                                page: this.currentPage,
                                per_page: this.perPage,
                                search: this.searchQuery,
                                sort_by: this.sortColumn,
                                sort_dir: this.sortDirection,
                            });
                            const response = await fetch(`/admin/api/reports?${params.toString()}`);
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            const data = await response.json();
                            this.reports = data.data;
                            this.totalReports = data.total;
                            this.currentPage = data.current_page;
                            this.totalPages = data.last_page;
                        } catch (e) {
                            this.error = `Failed to load reports. ${e.message}`;
                            this.reports = [];
                            this.totalReports = 0;
                        } finally {
                            this.loading = false;
                            setTimeout(() => lucide.createIcons(), 0);
                        }
                    },

                    async fetchCensoredWords() {
                        this.wordsLoading = true;
                        try {
                            const response = await fetch('/admin/api/censored-words');
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            const data = await response.json();

                            // Laravel returns either an array or an object — handle both
                            this.censoredWords = Array.isArray(data)
                                ? data
                                : (data.data || data);

                        } catch (e) {
                            console.error("Fetch censored words error:", e);
                            this.showMessage(`Failed to load censored words. ${e.message}`, 'error');
                        } finally {
                            this.wordsLoading = false;
                            setTimeout(() => lucide.createIcons(), 0);
                        }
                    },

                    async addCensoredWord() {
                        if (!this.newWord.trim()) return;
                        this.addingWord = true;

                        try {
                            const response = await fetch('/admin/api/censored-words', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ word: this.newWord.trim() })
                            });

                            const result = await response.json();
                            if (!response.ok) throw new Error(result.message || 'Failed to add word');

                            // ✅ Laravel returns result.word, not result.data
                            const added = result.word;
                            this.censoredWords.unshift(added);

                            this.showMessage(result.message || `Added "${added.word}" successfully.`, 'success');
                            this.newWord = '';

                        } catch (e) {
                            console.error("Add word error:", e);
                            this.showMessage(`Error adding word: ${e.message}`, 'error');
                        } finally {
                            this.addingWord = false;
                            setTimeout(() => lucide.createIcons(), 0);
                        }
                    },

                    deleteCensoredWordModal(id, word) {
                        this.deleteWordId = id;
                        this.deleteWordText = word;
                        this.deleteModalOpen = true;
                        setTimeout(() => lucide.createIcons(), 0);
                    },

                    async confirmDeleteWord() {
                        const id = this.deleteWordId;
                        this.deleteModalOpen = false;
                        this.deletingWord = true;
                        try {
                            const response = await fetch(`/admin/api/censored-words/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            this.censoredWords = this.censoredWords.filter(w => w.id !== id);
                            this.showMessage('Censored word removed successfully.', 'success');
                        } catch (e) {
                            console.error("Delete word error:", e);
                            this.showMessage(`Error deleting word: ${e.message}`, 'error');
                        } finally {
                            this.deletingWord = false;
                            setTimeout(() => lucide.createIcons(), 0);
                        }
                    },

                    sortBy(column) {
                        if (this.sortColumn === column)
                            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                        else {
                            this.sortColumn = column;
                            this.sortDirection = 'asc';
                        }
                        this.currentPage = 1;
                        this.fetchReports();
                    },

                    prevPage() { if (this.currentPage > 1) this.currentPage--; },
                    nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },

                    resolveReportModal(postId) {
                        this.modalPostId = postId;
                        this.modalOpen = true;
                        setTimeout(() => lucide.createIcons(), 0);
                    },

                    removePostModal(postId) {
                        this.removePostId = postId;
                        this.removePostModalOpen = true;
                        setTimeout(() => lucide.createIcons(), 0);
                    },

                    async confirmRemovePost() {
                        const postId = this.removePostId;
                        this.removePostModalOpen = false;
                        this.removingPost = true;
                        try {
                            const response = await fetch(`/admin/api/posts/${postId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            this.reports = this.reports.filter(r => r.post_id !== postId);
                            this.totalReports = Math.max(0, this.totalReports - 1);
                            this.showMessage(`Post ID ${postId} removed successfully.`, 'success');
                            if (this.reports.length === 0 && this.currentPage > 1) this.currentPage--;
                        } catch (e) {
                            console.error("Remove post error:", e);
                            this.showMessage(`Error removing post: ${e.message}`, 'error');
                        } finally {
                            this.removingPost = false;
                            setTimeout(() => lucide.createIcons(), 0);
                        }
                    },

                    async confirmResolve() {
                        const postId = this.modalPostId;
                        this.modalOpen = false;
                        this.resolveLoading = true;
                        try {
                            const response = await fetch(`/admin/api/reports/${postId}/resolve`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            this.reports = this.reports.filter(r => r.post_id !== postId);
                            this.totalReports = Math.max(0, this.totalReports - 1);
                            this.showMessage(`Reports for Post ID ${postId} marked as resolved.`, 'success');
                            if (this.reports.length === 0 && this.currentPage > 1) this.currentPage--;
                        } catch (e) {
                            console.error("Resolve report error:", e);
                            this.showMessage(`Error resolving reports: ${e.message}`, 'error');
                        } finally {
                            this.resolveLoading = false;
                            setTimeout(() => lucide.createIcons(), 0);
                        }
                    },

                    showMessage(text, type) {
                        this.messageType = type;
                        this.message = text;
                    },

                    formatDate(dateString) {
                        if (!dateString) return 'N/A';
                        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                        return new Date(dateString).toLocaleDateString(undefined, options);
                    }
                }
            }
        </script>
    </x-admin-layout>