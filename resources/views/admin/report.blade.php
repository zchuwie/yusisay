<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Content Moderation</h2>
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

        <!-- Toast Messages -->
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

        <!-- REPORTS TAB -->
        <div x-show="activeTab === 'reports'" class="space-y-6">

            <!-- Search and Toggle -->
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700">Show:</span>
                    <button @click="toggleResolved" 
                        :class="showResolved ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 hover:shadow-md flex items-center gap-2">
                        <i :data-lucide="showResolved ? 'check-circle' : 'clock'" class="w-4 h-4"></i>
                        <span x-text="showResolved ? 'Resolved Reports' : 'Pending Reports'"></span>
                    </button>
                </div>
                <div class="relative w-full max-w-xs">
                    <input type="text" x-model.debounce.300ms="searchQuery" placeholder="Search reported post title..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                </div>
            </div>

            <!-- Loading / Error / Empty -->
            <div x-show="loading" class="text-center py-12 text-red-600 font-semibold">
                <i data-lucide="loader-2" class="w-6 h-6 mr-2 inline-block animate-spin"></i> Loading reports...
            </div>
            <div x-show="error" class="text-center py-12 text-red-600 font-semibold bg-red-50 rounded-lg border border-red-200">
                <i data-lucide="alert-triangle" class="w-6 h-6 mr-2 inline-block"></i>
                <span x-text="error"></span>
            </div>
            <div x-show="!loading && !error && reports.length === 0" class="text-center py-12 text-gray-500 bg-white rounded-xl shadow-md">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                <p x-text="showResolved ? 'No resolved reports found.' : 'No reported content found.'"></p>
            </div>

            <!-- Reports Table -->
            <div x-show="!loading && !error && reports.length > 0" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th @click="sortBy('post_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-900">
                                Post ID <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                            </th>
                            <th @click="sortBy('title')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-900">
                                Title <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Censored Content</th>
                            <th @click="sortBy('total_reports')" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-900">
                                Reports <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                            </th>
                            <th x-show="showResolved" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                                <td x-show="showResolved" class="px-6 py-4 whitespace-nowrap text-center">
                                    <template x-if="report.status === 'approved'">
                                        <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i> Approved
                                        </span>
                                    </template>
                                    <template x-if="report.status === 'dismissed'">
                                        <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i> Dismissed
                                        </span>
                                    </template>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <template x-if="!showResolved">
                                            <div class="flex gap-2">
                                                <button @click="approvePost(report.post_id)" class="flex items-center gap-1 px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition">
                                                    <i data-lucide="check-circle" class="w-4 h-4"></i> Approve
                                                </button>
                                                <button @click="declinePost(report.post_id)" class="flex items-center gap-1 px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                                    <i data-lucide="x-circle" class="w-4 h-4"></i> Decline
                                                </button>
                                            </div>
                                        </template>
                                        <button @click="openReportModal(report)" class="flex items-center gap-1 px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                                            <i data-lucide="eye" class="w-4 h-4"></i> View
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="flex items-center justify-between p-4 border-t border-gray-200 bg-white">
                    <span class="text-sm text-gray-600" x-text="paginationSummary"></span>
                    <div class="flex items-center space-x-2">
                        <button @click="prevPage" :disabled="currentPage === 1"
                            class="p-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition disabled:opacity-50 disabled:bg-white disabled:text-gray-400 disabled:cursor-not-allowed">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <button @click="nextPage" :disabled="currentPage >= totalPages"
                            class="p-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition disabled:opacity-50 disabled:bg-white disabled:text-gray-400 disabled:cursor-not-allowed">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CENSORED WORDS TAB -->
        <div x-show="activeTab === 'censoredWords'" class="space-y-6">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i data-lucide="plus-circle" class="w-5 h-5 mr-2 text-indigo-600"></i>
                    Add New Censored Word
                </h3>
                <div class="flex gap-3">
                    <input type="text" x-model="newWord" @keydown.enter="addCensoredWord" placeholder="Enter word to censor..."
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                    <button @click="addCensoredWord" :disabled="!newWord.trim() || addingWord"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:bg-indigo-400 disabled:cursor-not-allowed font-medium">
                        <span x-show="!addingWord">Add Word</span>
                        <span x-show="addingWord"><i data-lucide="loader-2" class="w-4 h-4 inline-block animate-spin"></i></span>
                    </button>
                </div>
            </div>

            <!-- Words List -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i data-lucide="list" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Censored Words List
                        <span class="ml-3 text-sm font-normal text-gray-500">(<span x-text="censoredWords.length"></span> words)</span>
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
                                <div class="text-xs text-gray-500">Added: <span x-text="formatDate(word.created_at)"></span></div>
                            </div>
                            <button @click="deleteCensoredWordModal(word.id, word.word)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Report Details Modal -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalOpen" 
                     @click="closeModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="modalOpen"
                     @click.away="closeModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Report Details</h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <div x-show="modalReportData" class="space-y-4">
                        <div class="border-b pb-3">
                            <span class="text-sm font-medium text-gray-500">Post ID:</span>
                            <span class="ml-2 text-sm text-gray-900" x-text="modalReportData?.post_id"></span>
                        </div>
                        <div class="border-b pb-3">
                            <span class="text-sm font-medium text-gray-500">Title:</span>
                            <span class="ml-2 text-sm text-gray-900" x-text="modalReportData?.title"></span>
                        </div>
                        <div class="border-b pb-3">
                            <span class="text-sm font-medium text-gray-500">Censored Content:</span>
                            <p class="mt-1 text-sm text-gray-900" x-text="modalReportData?.censored_content"></p>
                        </div>
                        <div class="border-b pb-3">
                            <span class="text-sm font-medium text-gray-500">Total Reports:</span>
                            <span class="ml-2 text-sm font-bold text-red-600" x-text="modalReportData?.total_reports"></span>
                        </div>
                        <div class="border-b pb-3">
                            <span class="text-sm font-medium text-gray-500">Report Details:</span>
                            <div x-show="modalReportData?.reporters && modalReportData.reporters.length > 0" class="mt-2">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reporter</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reported At</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <template x-for="reporter in modalReportData.reporters" :key="reporter.id">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="reporter.username"></td>
                                                    <td class="px-4 py-2 text-sm text-gray-900" x-text="reporter.reason"></td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(reporter.created_at)"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div x-show="!modalReportData?.reporters || modalReportData.reporters.length === 0" class="mt-2">
                                <p class="text-sm text-gray-500">No report details available</p>
                            </div>
                        </div>
                        <div x-show="modalReportData?.status && (modalReportData?.status === 'approved' || modalReportData?.status === 'dismissed')" class="border-b pb-3">
                            <span class="text-sm font-medium text-gray-500">Status:</span>
                            <template x-if="modalReportData?.status === 'approved'">
                                <span class="ml-2 px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i> Approved
                                </span>
                            </template>
                            <template x-if="modalReportData?.status === 'dismissed'">
                                <span class="ml-2 px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i> Dismissed
                                </span>
                            </template>
                        </div>
                        <div x-show="modalReportData?.reviewed_at && (modalReportData?.status === 'approved' || modalReportData?.status === 'dismissed')" class="border-b pb-3">
                            <span class="text-sm font-medium text-gray-500">Reviewed At:</span>
                            <span class="ml-2 text-sm text-gray-900" x-text="formatDate(modalReportData?.reviewed_at)"></span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Word Confirmation Modal -->
        <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="deleteModalOpen"
                     @click="deleteModalOpen = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="deleteModalOpen"
                     @click.away="deleteModalOpen = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-red-600">Delete Censored Word</h3>
                        <button @click="deleteModalOpen = false" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm text-gray-600">Are you sure you want to delete the censored word:</p>
                        <p class="mt-2 text-lg font-bold text-gray-900" x-text="deleteWordText"></p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button @click="deleteModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button @click="confirmDeleteWord" :disabled="deletingWord" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition disabled:bg-red-400">
                            <span x-show="!deletingWord">Delete</span>
                            <span x-show="deletingWord">Deleting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        const lucide = window.lucide;

        function moderationPanel() {
            return {
                activeTab: 'reports',
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
                showResolved: false,

                censoredWords: [],
                wordsLoading: true,
                newWord: '',
                addingWord: false,

                modalOpen: false,
                modalPostId: null,
                modalReportData: null,
                deleteModalOpen: false,
                deleteWordId: null,
                deleteWordText: '',
                deletingWord: false,
                removePostModalOpen: false,
                removePostId: null,
                removingPost: false,

                message: null,
                messageType: null,

                get paginationSummary() {
                    if (this.totalReports === 0) return 'Showing 0 results';
                    const start = (this.currentPage - 1) * this.perPage + 1;
                    const end = Math.min(this.currentPage * this.perPage, this.totalReports);
                    const type = this.showResolved ? 'resolved reports' : 'posts with reports';
                    return `Showing ${start} to ${end} of ${this.totalReports} ${type}`;
                },

                init() {
                    this.fetchReports();
                    this.fetchCensoredWords();
                    
                    this.$watch('searchQuery', () => { 
                        this.currentPage = 1; 
                        this.fetchReports(); 
                    });
                    
                    this.$watch('currentPage', () => {
                        this.fetchReports();
                    });
                    
                    this.$watch('activeTab', () => {
                        setTimeout(() => lucide.createIcons(), 50);
                    });
                    
                    this.$watch('showResolved', () => {
                        setTimeout(() => lucide.createIcons(), 100);
                    });
                    
                    this.$watch('reports', () => {
                        setTimeout(() => lucide.createIcons(), 100);
                    });
                    
                    this.$watch('message', () => { 
                        if (this.message) {
                            setTimeout(() => {
                                this.message = null;
                            }, 5000);
                        }
                    });
                },

                async fetchReports() {
                    this.loading = true;
                    this.error = null;
                    
                    try {
                        const params = new URLSearchParams({
                            page: this.currentPage,
                            per_page: this.perPage,
                            sort_by: this.sortColumn,
                            sort_dir: this.sortDirection,
                            resolved: this.showResolved ? '1' : '0'
                        });
                        
                        // Only add search parameter if searchQuery is not empty
                        if (this.searchQuery.trim()) {
                            params.append('search', this.searchQuery.trim());
                        }
                        
                        const res = await fetch(`/admin/api/reports?${params.toString()}`);
                        
                        if (!res.ok) {
                            throw new Error(`HTTP ${res.status}`);
                        }
                        
                        const data = await res.json();
                        
                        // ... rest of the function remains the same
                        
                        console.log('API Response:', data); // Debug log
                        console.log('Show Resolved:', this.showResolved); // Debug log
                        
                        // Handle different response structures
                        let reportsData = [];
                        if (Array.isArray(data)) {
                            reportsData = data;
                        } else if (data.data && Array.isArray(data.data)) {
                            reportsData = data.data;
                        } else if (data.reports && Array.isArray(data.reports)) {
                            reportsData = data.reports;
                        }
                        
                        this.reports = reportsData.map(r => ({
                            ...r,
                            status: r.status || 'pending',
                            total_reports: r.total_reports || r.report_count || 1,
                            censored_content: r.censored_content || r.content || 'N/A',
                            reviewed_at: r.reviewed_at || null
                        }));
                        
                        console.log('Processed Reports:', this.reports); // Debug log
                        console.log('Total Reports:', this.reports.length); // Debug log
                        
                        this.totalReports = data.total || this.reports.length;
                        this.currentPage = data.current_page || 1;
                        this.totalPages = data.last_page || Math.ceil(this.totalReports / this.perPage);
                        
                    } catch (e) {
                        console.error('Fetch Reports Error:', e); // Debug log
                        this.error = `Failed to load reports. ${e.message}`;
                        this.reports = [];
                        this.totalReports = 0;
                    } finally {
                        this.loading = false;
                        setTimeout(() => lucide.createIcons(), 100);
                    }
                },

                toggleResolved() {
                    this.showResolved = !this.showResolved;
                    this.currentPage = 1;
                    this.searchQuery = ''; // Reset search when toggling
                    this.fetchReports();
                    setTimeout(() => lucide.createIcons(), 100);
                },

                async fetchCensoredWords() {
                    this.wordsLoading = true;
                    try {
                        const res = await fetch('/admin/api/censored-words');
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const data = await res.json();
                        this.censoredWords = Array.isArray(data) ? data : (data.data || data);
                    } catch (e) {
                        this.showMessage(`Failed to load censored words. ${e.message}`, 'error');
                    } finally {
                        this.wordsLoading = false;
                        setTimeout(() => lucide.createIcons(), 50);
                    }
                },

                async addCensoredWord() {
                    if (!this.newWord.trim()) return;
                    this.addingWord = true;
                    try {
                        const res = await fetch('/admin/api/censored-words', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ word: this.newWord.trim() })
                        });
                        const result = await res.json();
                        if (!res.ok) throw new Error(result.message || 'Failed to add word');
                        this.censoredWords.unshift(result.word);
                        this.showMessage(result.message || `Added "${result.word.word}" successfully.`, 'success');
                        this.newWord = '';
                    } catch (e) {
                        this.showMessage(`Error adding word: ${e.message}`, 'error');
                    } finally {
                        this.addingWord = false;
                        setTimeout(() => lucide.createIcons(), 50);
                    }
                },

                deleteCensoredWordModal(id, word) {
                    this.deleteWordId = id;
                    this.deleteWordText = word;
                    this.deleteModalOpen = true;
                    setTimeout(() => lucide.createIcons(), 50);
                },

                async confirmDeleteWord() {
                    const id = this.deleteWordId;
                    this.deleteModalOpen = false;
                    this.deletingWord = true;
                    
                    try {
                        const res = await fetch(`/admin/api/censored-words/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        
                        this.censoredWords = this.censoredWords.filter(w => w.id !== id);
                        this.showMessage('Censored word removed successfully.', 'success');
                        
                    } catch (e) {
                        this.showMessage(`Error deleting word: ${e.message}`, 'error');
                    } finally {
                        this.deletingWord = false;
                        setTimeout(() => lucide.createIcons(), 50);
                    }
                },

                openReportModal(report) {
                    this.modalReportData = report;
                    this.modalPostId = report.post_id;
                    this.modalOpen = true;
                    setTimeout(() => lucide.createIcons(), 50);
                },

                closeModal() {
                    this.modalOpen = false;
                    this.modalReportData = null;
                    this.modalPostId = null;
                },

                async approvePost(postId) {
                    if (!confirm(`Approve and hide Post ID ${postId}? This will mark the post as hidden.`)) {
                        return;
                    }
                    
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch(`/admin/api/reports/${postId}/approve`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            }
                        });
                        
                        if (!res.ok) {
                            throw new Error(`HTTP ${res.status}`);
                        }
                        
                        const result = await res.json();
                        
                        // Update the post status in the local array
                        const post = this.reports.find(r => r.post_id === postId);
                        if (post) {
                            post.status = 'approved';
                        }
                        
                        this.showMessage(result.message || `Post ID ${postId} approved and hidden.`, 'success');
                        
                        // Optionally refresh the reports list
                        await this.fetchReports();
                        
                    } catch (e) {
                        this.showMessage(`Error approving post: ${e.message}`, 'error');
                    } finally {
                        setTimeout(() => lucide.createIcons(), 50);
                    }
                },

                async declinePost(postId) {
                    if (!confirm(`Decline reports for Post ID ${postId}? This will dismiss all reports for this post.`)) {
                        return;
                    }
                    
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch(`/admin/api/reports/${postId}/decline`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            }
                        });
                        
                        if (!res.ok) {
                            throw new Error(`HTTP ${res.status}`);
                        }
                        
                        const result = await res.json();
                        
                        // Update the post status in the local array
                        const post = this.reports.find(r => r.post_id === postId);
                        if (post) {
                            post.status = 'declined';
                        }
                        
                        this.showMessage(result.message || `Reports for Post ID ${postId} declined.`, 'success');
                        
                        // Optionally refresh the reports list
                        await this.fetchReports();
                        
                    } catch (e) {
                        this.showMessage(`Error declining post: ${e.message}`, 'error');
                    } finally {
                        setTimeout(() => lucide.createIcons(), 50);
                    }
                },

                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                    }
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) {
                        this.currentPage++;
                    }
                },

                sortBy(column) {
                    if (this.sortColumn === column) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortColumn = column;
                        this.sortDirection = 'asc';
                    }
                    this.currentPage = 1;
                    this.fetchReports();
                },

                showMessage(text, type) {
                    this.messageType = type;
                    this.message = text;
                },

                maskName(name) {
                    if (!name) return 'User';
                    if (name.length <= 5) return name[0] + '***';
                    const first = name.slice(0, 2);
                    const last = name.slice(-3);
                    return `${first}***${last}`;
                },

                formatDate(dateString) {
                    if (!dateString) return 'N/A';
                    const options = {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    };
                    return new Date(dateString).toLocaleDateString(undefined, options);
                }
            };
        }

        // Initialize lucide icons when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => lucide.createIcons(), 100);
        });
    </script>
</x-admin-layout>