export default () => ({ 
    isModalOpen: false,
    modalType: null, 
    modalLoading: false,
    
    users: [],  
     
    selectedUser: null, 
    editForm: {
        id: null,
        name: '',
        email: '',
    },
    errors: {},  
    init() {
        this.fetchUsers();
    },
 
    async fetchUsers() { 
        const response = await fetch('/api/users'); 
        this.users = await response.json();
    },
 
    openModal(type, user) {
        this.modalType = type;
        this.isModalOpen = true;
        this.errors = {};  
        
        if (user) {
            this.selectedUser = user;
        }

        if (type === 'edit') { 
            this.editForm.id = user.id;
            this.editForm.name = user.name;
            this.editForm.email = user.email;
        }
    },
    
    closeModal() {
        this.isModalOpen = false;
        this.modalType = null;
        this.selectedUser = null; 
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                },
                body: JSON.stringify(this.editForm),
            });
            
            if (response.ok) { 
                const updatedUser = await response.json();
                const index = this.users.findIndex(u => u.id === this.editForm.id);
                if (index !== -1) {
                    this.users[index] = updatedUser;
                }
                this.closeModal();
            } else {
                const errorData = await response.json();
                if (response.status === 422 && errorData.errors) { 
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