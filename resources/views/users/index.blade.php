{{-- resources/views/users/index.blade.php --}}

@extends('layouts.app') 

@section('content')

<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    
    {{-- ✅ Main Alpine Data Component (must wrap all elements that need its data) --}}
    <div x-data="usersTable()" x-init="init()" class="space-y-8 pb-6">
        <h2 class="text-2xl font-semibold leading-tight">User Management</h2>

        {{-- Users Table --}}
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                {{-- ... (Table header remains the same) ... --}}
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="user in users" :key="user.id">
                        <tr>
                            <td x-text="user.name" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"></td>
                            <td x-text="user.email" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                {{-- Actions --}}
                                <button @click="openModal('edit', user)" ...>Edit</button>
                                <button @click="openModal('delete-confirm', user)" ...>Delete</button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="users.length === 0">
                        <tr><td colspan="3" class="text-center py-4 text-gray-500">No users found.</td></tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        {{---
            🛑 ERROR FIX: The Modal must be inside this x-data scope 🛑
            All variables like isModalOpen, modalType, selectedUser, errors, and modalLoading 
            are now accessible.
        ---}}
        
        {{-- Modal Component --}}
        <div x-show="isModalOpen" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50" 
            style="display: none;">
            
            <div x-show="isModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                
                {{-- Modal Header --}}
                <div x-bind:class="{ 
                        'bg-indigo-100 text-indigo-600': modalType === 'edit',
                        'bg-red-100 text-red-600': modalType === 'delete-confirm' 
                    }"
                    class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 x-text="modalType === 'edit' ? 'Edit User Details' : 'Confirm Deletion'" 
                        class="text-xl font-semibold"></h3>
                </div>
                
                {{-- Modal Body --}}
                <div class="p-6">
                    
                    {{-- Edit Form Content --}}
                    <div x-show="modalType === 'edit'">
                        <form @submit.prevent="saveUserChanges">
                            <input type="hidden" x-model="editForm.id">
                            
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" x-model.debounce.500ms="editForm.name" id="name" required
                                    x-bind:class="{'border-red-500': errors.name}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p x-show="errors.name" x-text="errors.name?.[0]" class="mt-1 text-sm text-red-600"></p>
                            </div>
                            
                            <div class="mb-6">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" x-model.debounce.500ms="editForm.email" id="email" required
                                    x-bind:class="{'border-red-500': errors.email}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p x-show="errors.email" x-text="errors.email?.[0]" class="mt-1 text-sm text-red-600"></p>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                    Cancel
                                </button>
                                <button type="submit" :disabled="modalLoading"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">
                                    <span x-show="!modalLoading">Save Changes</span>
                                    <span x-show="modalLoading">Saving...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    {{-- Delete Confirmation Content --}}
                    <div x-show="modalType === 'delete-confirm'">
                        <p class="text-gray-700 mb-6">Are you sure you want to delete user **<span x-text="selectedUser?.name"></span>** (ID: <span x-text="selectedUser?.id"></span>)? This action cannot be undone.</p>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Cancel
                            </button>
                            <button type="button" @click="confirmDelete" :disabled="modalLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50">
                                <span x-show="!modalLoading">Yes, Delete</span>
                                <span x-show="modalLoading">Deleting...</span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        {{-- End of Modal --}}

    </div>
    {{-- End of Main Alpine Component --}}

</div>
@endsection