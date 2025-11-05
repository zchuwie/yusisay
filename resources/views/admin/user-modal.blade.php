{{-- MODAL STRUCTURE --}}
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
    >

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40" aria-hidden="true"></div>

    {{-- Modal Panel Wrapper (Fixes: click-outside added to inner panel) --}}
    <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0 relative z-50">
        <div 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200" 
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:w-full sm:max-w-lg pointer-events-auto z-[9999]"
            @click.outside="isModalOpen = false"
        >
            
            <div x-show="selectedUser" class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full"
                         :class="{
                            'bg-indigo-100 text-indigo-600': modalType === 'view' || modalType === 'edit',
                            'bg-red-100 text-red-600': modalType === 'delete-confirm'
                         }">
                        <i :data-lucide="modalType === 'delete-confirm' ? 'alert-triangle' : (modalType === 'edit' ? 'square-pen' : 'user')" class="w-6 h-6 action-icon"></i>
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
                    <span x-show="loading" class="mr-2"><i data-lucide="loader-2" class="w-4 h-4 inline-block animate-spin action-icon"></i></span>
                    Save Changes
                </button>
                
                <button x-show="modalType === 'delete-confirm'" type="button" @click="confirmDelete()"
                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                    <span x-show="loading" class="mr-2"><i data-lucide="loader-2" class="w-4 h-4 inline-block animate-spin action-icon"></i></span>
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