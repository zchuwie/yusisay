// resources/js/usersTable.js

export default () => ({
    // 🛑 FIX: Initialize all variables referenced in the HTML 🛑
    isModalOpen: false,
    modalType: null, // 'edit' or 'delete-confirm'
    modalLoading: false,
    
    users: [], // The main array of user objects fetched from the API
    
    // Properties for the selected user and the edit form
    selectedUser: null, // Holds the original user data
    editForm: {
        id: null,
        name: '',
        email: '',
    },
    errors: {}, // Object to hold validation errors from the server

    // --- Component Lifecycle ---
    init() {
        this.fetchUsers();
    },

    // --- Data Fetching ---
    async fetchUsers() {
        // Assume you have a route to get user data
        const response = await fetch('/api/users'); 
        this.users = await response.json();
    },

    // --- Modal Control ---
    openModal(type, user) {
        this.modalType = type;
        this.isModalOpen = true;
        this.errors = {}; // Clear previous errors
        
        if (user) {
            this.selectedUser = user;
        }

        if (type === 'edit') {
            // Populate the edit form with the selected user's data
            this.editForm.id = user.id;
            this.editForm.name = user.name;
            this.editForm.email = user.email;
        }
    },
    
    closeModal() {
        this.isModalOpen = false;
        this.modalType = null;
        this.selectedUser = null;
        // Optionally reset the form fields here if needed
    },

    // --- Actions ---
    async saveUserChanges() {
        this.modalLoading = true;
        try {
            const url = `/api/users/${this.editForm.id}`;
            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    // Include CSRF token if required by Laravel
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                },
                body: JSON.stringify(this.editForm),
            });
            
            if (response.ok) {
                // Find the index of the user and update the array
                const updatedUser = await response.json();
                const index = this.users.findIndex(u => u.id === this.editForm.id);
                if (index !== -1) {
                    this.users[index] = updatedUser;
                }
                this.closeModal();
            } else {
                const errorData = await response.json();
                if (response.status === 422 && errorData.errors) {
                    // Validation errors
                    this.errors = errorData.errors;
                } else {
                    alert('Error saving changes: ' + (errorData.message || 'Unknown error'));
                }
            }
        } catch (e) {
            console.error('API Error:', e);
            alert('A network error occurred.');
        } finally {
            this.modalLoading = false;
        }
    },

    async confirmDelete() {
        this.modalLoading = true;
        try {
            const url = `/api/users/${this.selectedUser.id}`;
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                }
            });

            if (response.ok) {
                // Remove the user from the reactive list
                this.users = this.users.filter(u => u.id !== this.selectedUser.id);
                this.closeModal();
            } else {
                alert('Error deleting user.');
            }
        } catch (e) {
            console.error('API Error:', e);
            alert('A network error occurred.');
        } finally {
            this.modalLoading = false;
        }
    }
});