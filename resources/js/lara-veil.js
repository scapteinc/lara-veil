/**
 * Lara-Veil Admin JavaScript
 * Handles interactive functionality for the admin panel
 */

document.addEventListener('DOMContentLoaded', () => {
    initializeAdminPanel();
});

/**
 * Initialize admin panel functionality
 */
function initializeAdminPanel() {
    setupTableActions();
    setupFormValidation();
    setupModalHandlers();
    setupNotifications();
    setupConfirmations();
}

/**
 * Setup table action handlers
 */
function setupTableActions() {
    const tables = document.querySelectorAll('[data-table-actions]');

    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const actions = row.querySelectorAll('[data-action]');

            actions.forEach(action => {
                action.addEventListener('click', (e) => {
                    const actionType = action.dataset.action;
                    const rowId = action.dataset.id;

                    switch(actionType) {
                        case 'edit':
                            editRow(rowId);
                            break;
                        case 'delete':
                            deleteRow(rowId);
                            break;
                        case 'activate':
                            activateItem(rowId);
                            break;
                        case 'deactivate':
                            deactivateItem(rowId);
                            break;
                    }
                });
            });
        });
    });
}

/**
 * Setup form validation
 */
function setupFormValidation() {
    const forms = document.querySelectorAll('[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const isValid = validateForm(form);
            if (!isValid) {
                e.preventDefault();
                showNotification('Please correct the errors in the form', 'error');
            }
        });
    });
}

/**
 * Validate form inputs
 */
function validateForm(form) {
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('border-red-500');
            isValid = false;
        } else {
            input.classList.remove('border-red-500');
        }
    });

    return isValid;
}

/**
 * Setup modal handlers
 */
function setupModalHandlers() {
    const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
    const modals = document.querySelectorAll('[data-modal]');

    // Open modals
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = trigger.dataset.modalTrigger;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        });
    });

    // Close modals
    modals.forEach(modal => {
        const closeBtn = modal.querySelector('[data-modal-close]');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        }

        // Close on background click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    });
}

/**
 * Setup notification system
 */
function setupNotifications() {
    const notifications = document.querySelectorAll('[data-notification]');

    notifications.forEach(notif => {
        const timeout = notif.dataset.timeout || 5000;
        const closeBtn = notif.querySelector('[data-close-notification]');

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                notif.remove();
            });
        }

        if (timeout > 0) {
            setTimeout(() => {
                notif.remove();
            }, parseInt(timeout));
        }
    });
}

/**
 * Setup confirmation dialogs
 */
function setupConfirmations() {
    const confirmBtns = document.querySelectorAll('[data-confirm]');

    confirmBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const message = btn.dataset.confirm;
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Edit row handler
 */
function editRow(id) {
    window.location.href = `/admin/items/${id}/edit`;
}

/**
 * Delete row handler
 */
function deleteRow(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        fetch(`/admin/items/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Item deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Failed to delete item', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred', 'error');
        });
    }
}

/**
 * Activate item handler
 */
function activateItem(id) {
    fetch(`/admin/items/${id}/activate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Item activated successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Failed to activate item', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

/**
 * Deactivate item handler
 */
function deactivateItem(id) {
    fetch(`/admin/items/${id}/deactivate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Item deactivated successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Failed to deactivate item', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg text-white notification-${type}`;

    const bgColor = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500',
    }[type] || 'bg-blue-500';

    notification.classList.add(bgColor);
    notification.innerHTML = `
        <div class="flex justify-between items-center">
            <span>${message}</span>
            <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                &times;
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

/**
 * Export utility functions
 */
window.LaraVeil = {
    showNotification,
    editRow,
    deleteRow,
    activateItem,
    deactivateItem,
    validateForm,
};
