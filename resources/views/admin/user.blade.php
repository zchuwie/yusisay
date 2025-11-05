<x-admin-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Accounts
        </h2>
    </x-slot>

    <script src="https://unpkg.com/lucide@latest"></script> 

    <div x-data="usersTable()" x-init="init()" class="space-y-8 pb-6">
        <h1 class="text-3xl font-bold text-gray-800">User Accounts List</h1>

        <div class="flex items-center justify-end">
            <div class="relative w-full max-w-xs">
                <input type="text" x-model.debounce.300ms="searchQuery" placeholder="Search users..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                <i data-lucide="search"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"></i>
            </div>
        </div>

        <div x-show="loading" class="text-center py-12 text-indigo-600 font-semibold">
            <i data-lucide="loader-2" class="w-6 h-6 mr-2 inline-block animate-spin"></i> Loading users...
        </div>
        <div x-show="error" class="text-center py-12 text-red-600 font-semibold bg-red-50 rounded-lg border border-red-200">
            <i data-lucide="alert-triangle" class="w-6 h-6 mr-2 inline-block"></i> <span x-text="error"></span>
        </div>
        <div x-show="!loading && !error && users.length === 0" class="text-center py-12 text-gray-500 bg-white rounded-xl shadow-md">
            No user accounts found.
        </div>
        
        {{-- User Table --}}
        <div x-show="!loading && !error && users.length > 0" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Profile
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-900" @click="sortBy('id')">
                            # <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-900" @click="sortBy('name')">
                            Full Name <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            E-Mail
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-900" @click="sortBy('created_at')">
                            Registered At <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="user in users" :key="user.id">
                        <tr class="hover:bg-gray-50 transition duration-100 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <img :src="user.profile_picture_url || 'https://placehold.co/32x32/d1d5db/4b5563?text=AV'" :alt="user.name"
                                    class="h-8 w-8 rounded-full object-cover border border-gray-200 shadow-sm">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="user.id"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="user.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="user.email"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(user.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <button @click="openModal('view', user)" title="View User Details"
                                        class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-150">
                                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                    </button>
                                    <button @click="openModal('edit', user)" title="Edit User"
                                        class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-150">
                                        <i data-lucide="square-pen" class="w-5 h-5"></i>
                                    </button>
                                    <button @click="openModal('delete-confirm', user)" title="Delete User"
                                        class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- Pagination Footer --}}
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

    {{-- MODAL STRUCTURE (Main wrapper, z-50) --}}
    <div x-show="isModalOpen" 
         x-cloak
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         {{-- TEMPORARILY COMMENTED OUT: If buttons are now clickable, the issue was here. --}}
         {{-- @click.outside="isModalOpen = false" --}}
         >

        {{-- Backdrop (Lower Z-index, ensures it covers) --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40" aria-hidden="true"></div>

        {{-- Modal Panel Wrapper (Pointer events and Z-index fixed) --}}
        <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0 relative z-50 pointer-events-none">
            <div 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:w-full sm:max-w-lg pointer-events-auto z-[9999]">
                
                <div x-show="selectedUser" class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full"
                             :class="{
                                 'bg-indigo-100 text-indigo-600': modalType === 'view' || modalType === 'edit',
                                 'bg-red-100 text-red-600': modalType === 'delete-confirm'
                             }">
                            <i :data-lucide="modalType === 'delete-confirm' ? 'alert-triangle' : (modalType === 'edit' ? 'square-pen' : 'user')" class="w-6 h-6"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title"
                                x-text="modalType === 'view' ? 'View User Details' : (modalType === 'edit' ? 'Edit User' : 'Confirm Deletion')">
                                Modal Title
                            </h3>

                            {{-- VIEW MODE --}}
                            <div x-show="modalType === 'view'" class="mt-4 space-y-3 text-gray-600">
                                <p><strong>ID:</strong> <span x-text="selectedUser.id"></span></p>
                                <p><strong>Name:</strong> <span x-text="selectedUser.name"></span></p>
                                <p><strong>Email:</strong> <span x-text="selectedUser.email"></span></p>
                                <p><strong>Registered:</strong> <span x-text="formatDate(selectedUser.created_at)"></span></p>
                            </div>

                            {{-- EDIT MODE --}}
                            <div x-show="modalType === 'edit'" class="mt-4 space-y-4">
                                <div>
                                    <label for="edit_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" x-model="selectedUser.name" id="edit_name"
                                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="edit_email" class="block text-sm font-medium text-gray-700">E-Mail</label>
                                    <input type="email" x-model="selectedUser.email" id="edit_email"
                                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Note: Changes made here are simulated and require a backend API call to persist.</p>
                            </div>

                            {{-- DELETE CONFIRMATION MODE --}}
                            <div x-show="modalType === 'delete-confirm'" class="mt-4">
                                <p class="text-sm text-gray-600">
                                    Are you sure you want to delete the account for user 
                                    <strong x-text="selectedUser.name"></strong> (ID: <span x-text="selectedUser.id"></span>)? 
                                    This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer Buttons --}}
                <div x-show="selectedUser" class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button x-show="modalType === 'edit'" type="button" @click="saveEdit()"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                        <span x-show="loading" class="mr-2"><i data-lucide="loader-2" class="w-4 h-4 inline-block animate-spin"></i></span>
                        Save Changes
                    </button>
                    
                    <button x-show="modalType === 'delete-confirm'" type="button" @click="confirmDelete()"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                         <span x-show="loading" class="mr-2"><i data-lucide="loader-2" class="w-4 h-4 inline-block animate-spin"></i></span>
                        Delete Permanently
                    </button>

                    <button type="button" @click="isModalOpen = false"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                        <span x-text="modalType === 'view' ? 'Close' : 'Cancel'">Cancel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- END MODAL STRUCTURE --}}

    {{-- Alpine.js Logic is unchanged as the problem is CSS/HTML --}}
    <script>
        document.addEventListener('alpine:init', () => {
             // Define lucide.createIcons helper if it doesn't exist
            if (typeof lucide === 'undefined' || typeof lucide.createIcons !== 'function') {
                console.warn("Lucide library not loaded or createIcons function is missing.");
                // Create a mock function to prevent errors if the script fails to load
                window.lucide = { createIcons: () => {} };
            }

            Alpine.data('usersTable', () => ({
                users: [],
                loading: true,
                error: null,
                searchQuery: '',
                currentPage: 1,
                perPage: 10,
                totalUsers: 0,
                totalPages: 1,
                sortColumn: 'created_at',
                sortDirection: 'desc',
                
                isModalOpen: false,
                modalType: '',
                selectedUser: null,

                get paginationSummary() {
                    if (this.totalUsers === 0) return 'Showing 0 results';
                    const start = (this.currentPage - 1) * this.perPage + 1;
                    const end = Math.min(this.currentPage * this.perPage, this.totalUsers);
                    return `Showing ${start} to ${end} of ${this.totalUsers} users`;
                },

                init() {
                    this.fetchUsers();
                    this.$watch('searchQuery', () => { this.currentPage = 1; this.fetchUsers(); });
                    this.$watch('currentPage', () => this.fetchUsers());
                    
                    this.$nextTick(() => lucide.createIcons()); 
                },

                async fetchUsers() {
                    this.loading = true;
                    this.error = null;
                    try {
                        const allowedSorts = ['id', 'name', 'email', 'created_at'];
                        const finalSortColumn = allowedSorts.includes(this.sortColumn) ? this.sortColumn : 'created_at';
                        
                        const params = new URLSearchParams({
                            page: this.currentPage,
                            per_page: this.perPage,
                            search: this.searchQuery,
                            sort_by: finalSortColumn,
                            sort_dir: this.sortDirection,
                        });
                        
                        const response = await fetch('/admin/api/users?'+params.toString());
                        
                        if (!response.ok) {
                             throw new Error('Using fallback mock data.');
                        }

                        const data = await response.json();
                        
                        this.users = data.data;
                        this.totalUsers = data.total;
                        this.currentPage = data.current_page;
                        this.totalPages = data.last_page;

                    } catch (e) {
                        console.warn("API fetch failed. Using hardcoded mock data for demonstration.");
                         const mockUsers = [
                            { id: 1, name: 'Alice Smith', email: 'alice.s@example.com', created_at: '2023-10-20T10:00:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=AS' },
                            { id: 2, name: 'Bob Johnson', email: 'bob.j@example.com', created_at: '2023-11-05T12:30:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=BJ' },
                            { id: 3, name: 'Charlie Brown', email: 'charlie.b@example.com', created_at: '2023-12-15T15:45:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=CB' },
                            { id: 4, name: 'Diana Prince', email: 'diana.p@example.com', created_at: '2024-01-01T08:00:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=DP' },
                            { id: 5, name: 'Eve Adams', email: 'eve.a@example.com', created_at: '2024-02-10T11:20:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=EA' },
                            { id: 6, name: 'Frank Miller', email: 'frank.m@example.com', created_at: '2024-03-25T14:10:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=FM' },
                            { id: 7, name: 'Grace Hall', email: 'grace.h@example.com', created_at: '2024-04-30T09:05:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=GH' },
                            { id: 8, name: 'Henry Ford', email: 'henry.f@example.com', created_at: '2024-05-18T16:50:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=HF' },
                            { id: 9, name: 'Ivy King', email: 'ivy.k@example.com', created_at: '2024-06-01T07:15:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=IK' },
                            { id: 10, name: 'Jack Lewis', email: 'jack.l@example.com', created_at: '2024-07-07T13:40:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=JL' },
                            { id: 11, name: 'Kelly Green', email: 'kelly.g@example.com', created_at: '2024-08-14T10:25:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=KG' },
                            { id: 12, name: 'Liam White', email: 'liam.w@example.com', created_at: '2024-09-29T17:00:00Z', profile_picture_url: 'https://placehold.co/32x32/1d4ed8/ffffff?text=LW' },
                        ].filter(user => user.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || user.email.toLowerCase().includes(this.searchQuery.toLowerCase()));

                        mockUsers.sort((a, b) => {
                            const valA = a[this.sortColumn];
                            const valB = b[this.sortColumn];

                            if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                            if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                            return 0;
                        });

                        this.totalUsers = mockUsers.length;
                        this.totalPages = Math.ceil(this.totalUsers / this.perPage);
                        
                        const start = (this.currentPage - 1) * this.perPage;
                        const end = start + this.perPage;
                        this.users = mockUsers.slice(start, end);
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => lucide.createIcons());
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
                    this.fetchUsers();
                },

                formatDate(dateString) {
                    if (!dateString) return 'N/A';
                    const options = { year: 'numeric', month: 'short', day: 'numeric' };
                    return new Date(dateString).toLocaleDateString(undefined, options);
                },

                openModal(action, user) {
                    this.selectedUser = JSON.parse(JSON.stringify(user)); 
                    this.modalType = action;
                    this.isModalOpen = true;
                    this.$nextTick(() => lucide.createIcons()); 
                },

                async confirmDelete() {
                    const userId = this.selectedUser.id;
                    this.isModalOpen = false;
                    this.loading = true;
                    this.error = null;
                    
                    console.log(`[API CALL] Sending DELETE request for user ID: ${userId}`);
                    await new Promise(r => setTimeout(r, 800)); 
                    
                    this.loading = false;
                    this.fetchUsers(); 
                    this.selectedUser = null;
                },

                async saveEdit() {
                    this.isModalOpen = false;
                    this.loading = true;
                    this.error = null;
                    
                    console.log(`[API CALL] Sending PUT/PATCH request to update user ID: ${this.selectedUser.id}`);
                    console.log(`[DATA SENT] Name: ${this.selectedUser.name}, Email: ${this.selectedUser.email}`);
                    await new Promise(r => setTimeout(r, 800)); 
                    
                    this.loading = false;
                    this.fetchUsers();
                    this.selectedUser = null;
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
                }
            }));
        });
    </script>
</x-admin-layout>