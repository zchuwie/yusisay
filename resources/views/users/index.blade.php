{{-- resources/views/users/index.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

    {{-- ✅ Alpine Root --}}
    <div x-data="usersTable()" x-init="init()" class="space-y-8 pb-6">
        <h2 class="text-2xl font-semibold leading-tight">User Management</h2>

        {{-- Users Table --}}
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="user in users" :key="user.id">
                        <tr>
                            <td x-text="user.name" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"></td>
                            <td x-text="user.email" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button type="button" @click="openModal('edit', user)"
                                    class="px-3 py-1 bg-indigo-500 text-white text-xs rounded-md hover:bg-indigo-600">Edit</button>
                                <button type="button" @click="openModal('delete-confirm', user)"
                                    class="px-3 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">Delete</button>
                            </td>
                        </tr>
                    </template>

                    <template x-if="users.length === 0">
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">No users found.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- ✅ Modal --}}
        <div x-show="isModalOpen"
            x-transition
            class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50"
            style="display: none;">
            
            <div x-show="isModalOpen"
                x-transition
                class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">

                {{-- Header --}}
                <div :class="modalType === 'edit' ? 'bg-indigo-100 text-indigo-600' : 'bg-red-100 text-red-600'"
                    class="flex items-center justify-between p-3 border-b rounded-t">
                    <h3 x-text="modalType === 'edit' ? 'Edit User Details' : 'Confirm Deletion'" class="text-lg font-semibold"></h3>
                </div>

                {{-- Body --}}
                <div class="mt-4 space-y-6">

                    {{-- Edit Modal --}}
                    <div x-show="modalType === 'edit'">
                        <form @submit.prevent="saveUserChanges">
                            <input type="hidden" x-model="editForm.id">

                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" id="name" x-model="editForm.name" required
                                    :class="{'border-red-500': errors.name}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p x-show="errors.name" x-text="errors.name?.[0]" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <div class="mb-6">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" x-model="editForm.email" required
                                    :class="{'border-red-500': errors.email}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p x-show="errors.email" x-text="errors.email?.[0]" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" @click="closeModal"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Cancel</button>
                                <button type="submit" :disabled="modalLoading"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50">
                                    <span x-show="!modalLoading">Save Changes</span>
                                    <span x-show="modalLoading">Saving...</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Delete Modal --}}
                    <div x-show="modalType === 'delete-confirm'">
                        <p class="text-gray-700 mb-6">
                            Are you sure you want to delete 
                            <strong x-text="selectedUser?.name"></strong>
                            (ID: <span x-text="selectedUser?.id"></span>)?
                            This action cannot be undone.
                        </p>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeModal"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Cancel</button>
                            <button type="button" @click="confirmDelete" :disabled="modalLoading"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50">
                                <span x-show="!modalLoading">Yes, Delete</span>
                                <span x-show="modalLoading">Deleting...</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        {{-- End Modal --}}
    </div>
</div>
@endsection
