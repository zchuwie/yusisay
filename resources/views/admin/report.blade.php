<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reports
        </h2>
    </x-slot>

    <div x-data="reportsTable()" x-init="init()" class="space-y-8 pb-6">
        <h1 class="text-3xl font-bold text-gray-800">Reported Content List</h1>

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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="report.title"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-center">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" x-text="report.total_reports"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a :href="`/posts/${report.post_id}`" target="_blank" title="View Post"
                                        class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-150">
                                        <i data-lucide="external-link" class="w-5 h-5"></i>
                                    </a>
                                    <button @click="resolveReport(report.post_id)" title="Mark as Resolved"
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

    <script>
        function reportsTable() {
            return {
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

                get paginationSummary() {
                    if (this.totalReports === 0) return 'Showing 0 results';
                    const start = (this.currentPage - 1) * this.perPage + 1;
                    const end = Math.min(this.currentPage * this.perPage, this.totalReports);
                    return `Showing ${start} to ${end} of ${this.totalReports} posts with reports`;
                },

                init() {
                    this.fetchReports();
                    this.$watch('searchQuery', () => { this.currentPage = 1; this.fetchReports(); });
                    this.$watch('currentPage', () => this.fetchReports());
                    
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
                        
                        if (!response.ok) {
                            const errorData = await response.json();
                            const message = errorData.error || `HTTP Status ${response.status}`;
                            throw new Error(message);
                        }

                        const data = await response.json();
                        
                        this.reports = data.data;
                        this.totalReports = data.total;
                        this.currentPage = data.current_page;
                        this.totalPages = data.last_page;

                    } catch (e) {
                        console.error("Fetch error:", e);
                        this.error = `Failed to load reports. ${e.message}`; 
                        this.reports = [];
                        this.totalReports = 0;
                    } finally {
                        this.loading = false;
                        setTimeout(() => lucide.createIcons(), 0);
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

                async resolveReport(postId) {
                    if (!confirm(`Are you sure you want to mark all reports for Post ID ${postId} as resolved?`)) {
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/admin/reports/${postId}/resolve`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to resolve report on the server.');
                        }

                        this.reports = this.reports.filter(r => r.post_id !== postId);
                        this.totalReports--;

                        alert(`Reports for Post ID ${postId} resolved successfully.`);

                    } catch (e) {
                        console.error("Resolve error:", e);
                        alert(`Error resolving reports: ${e.message}.`);
                    }
                }
            }
        }
    </script>
</x-admin-layout>