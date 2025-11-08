<x-admin-layout>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Management
        </h2>
    </x-slot>

    <div x-data="usersTable()" x-init="init()" class="space-y-8 pb-6">
 
        <div x-show="toast.show"
            x-cloak
            x-transition
            :class="{
                'bg-green-500': toast.type === 'success',
                'bg-red-500': toast.type === 'error',
                'bg-blue-500': toast.type === 'info'
            }"
            class="fixed top-4 right-4 z-[100] px-6 py-4 rounded-lg shadow-lg text-white max-w-md">
            <div class="flex items-center gap-3">
                <i :data-lucide="toast.icon" class="w-5 h-5"></i>
                <span x-text="toast.message"></span>
            </div>
        </div>
 
        <div class="flex items-center justify-end">
            <div class="relative w-full max-w-xs">
                <input type="text" x-model.debounce.300ms="searchQuery"
                    placeholder="Search users..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <i data-lucide="search"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"></i>
            </div>
        </div>
 
        <div x-show="loading" class="text-center py-12 text-indigo-600 font-semibold">
            <i data-lucide="loader-2" class="w-6 h-6 mr-2 inline-block animate-spin"></i> Loading users...
        </div>

        <div x-show="error" class="text-center py-12 text-red-600 font-semibold">
            <i data-lucide="alert-triangle" class="w-6 h-6 mr-2 inline-block"></i>
            <span x-text="error"></span>
        </div>

        <div x-show="!loading && !error && users.length === 0"
            class="text-center py-12 text-gray-500 bg-white rounded-xl shadow-md">
            No user accounts found.
        </div>
 
        <div x-show="!loading && users.length > 0"
            class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th @click="toggleSort('users.id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:text-gray-800">
                            ID <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                        </th>
                        <th @click="toggleSort('users.name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:text-gray-800">
                            Name <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                        </th>
                        <th @click="toggleSort('users.email')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:text-gray-800">
                            Email <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Profile Picture
                        </th>
                        <th @click="toggleSort('user_infos.created_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:text-gray-800">
                            Joined <i data-lucide="arrow-up-down" class="w-3 h-3 inline ml-1 align-middle"></i>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="user in users" :key="user.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-800" x-text="user.id"></td>
                            <td class="px-6 py-4 text-sm text-gray-800" x-text="user.name"></td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="user.email"></td>
                            <td class="px-6 py-4 text-sm">
                                <template x-if="user.profile_picture">
                                    <img :src="user.profile_picture" class="w-10 h-10 rounded-full object-cover border">
                                </template>
                                <template x-if="!user.profile_picture">
                                    <span class="text-gray-400 italic">No picture</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="formatDate(user.joined_at)"></td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openModal('edit', user)" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg">
                                    <i data-lucide="user"></i>
                                </button>
                                <button @click="openModal('delete', user)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
 
        <div x-show="isModalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500 bg-opacity-70">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4" x-text="modalType === 'edit' ? 'Edit User' : 'Delete User'"></h3>

                <div x-show="modalType === 'edit'" class="space-y-4">
                    <div>
                        <label class="text-sm font-medium">Name</label>
                        <input type="text" x-model="editForm.name" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Email</label>
                        <input type="email" x-model="editForm.email" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>

                <div x-show="modalType === 'delete'">
                    <p>Are you sure you want to delete <strong x-text="selectedUser?.name"></strong>?</p>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button @click="isModalOpen = false" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button x-show="modalType === 'edit'" @click="saveEdit()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Save</button>
                    <button x-show="modalType === 'delete'" @click="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('usersTable', () => ({
            users: [],
            loading: true,
            error: null,
            isModalOpen: false,
            modalType: '',
            selectedUser: null,
            editForm: { name: '', email: '' },
            toast: { show: false, message: '', type: '', icon: '' },
            searchQuery: '',
            sortBy: 'user_infos.created_at',
            sortDir: 'desc',

            async init() {
                this.fetchUsers();
                this.$watch('searchQuery', () => this.fetchUsers());
                this.$nextTick(() => lucide.createIcons());
            },

            async fetchUsers() {
                this.loading = true;
                try {
                    const res = await axios.get(`/admin/api/users`, {
                        params: {
                            search: this.searchQuery,
                            sort_by: this.sortBy,
                            sort_dir: this.sortDir
                        }
                    });
                    this.users = res.data.data || res.data;
                    this.error = null;
                    this.$nextTick(() => lucide.createIcons());
                } catch (err) {
                    console.error(err);
                    this.error = 'Failed to load users.';
                } finally {
                    this.loading = false;
                }
            },

            toggleSort(column) {
                if (this.sortBy === column) {
                    this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortBy = column;
                    this.sortDir = 'asc';
                }
                this.fetchUsers();
            },

            openModal(type, user) {
                this.modalType = type;
                this.selectedUser = user;
                this.isModalOpen = true;
                this.editForm = { name: user.name, email: user.email };
            },

            async saveEdit() {
                try {
                    await axios.put(`/admin/api/users/${this.selectedUser.id}`, this.editForm);
                    this.showToast('User updated successfully', 'success');
                    this.isModalOpen = false;
                    this.fetchUsers();
                } catch {
                    this.showToast('Failed to update user', 'error');
                }
            },

            async confirmDelete() {
                try {
                    await axios.delete(`/admin/api/users/${this.selectedUser.id}`);
                    this.showToast('User deleted successfully', 'success');
                    this.isModalOpen = false;
                    this.fetchUsers();
                } catch {
                    this.showToast('Failed to delete user', 'error');
                }
            },

            showToast(message, type) {
                const icons = { success: 'check-circle', error: 'alert-triangle', info: 'info' };
                this.toast = { show: true, message, type, icon: icons[type] };
                this.$nextTick(() => lucide.createIcons());
                setTimeout(() => (this.toast.show = false), 3000);
            },

            formatDate(dateString) {
                return new Date(dateString).toLocaleDateString();
            }
        }));
    });
    </script>
</x-admin-layout>
