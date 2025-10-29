/**
 * Custom Alert System for Admin Panel
 * Similar to SweetAlert but customized for the admin theme
 */

class AdminAlert {
    static instance = null;

    static getInstance() {
        if (!AdminAlert.instance) {
            AdminAlert.instance = new AdminAlert();
        }
        return AdminAlert.instance;
    }
    constructor() {
        this.alertElement = document.getElementById('customAlert');
        this.overlay = document.getElementById('alertOverlay');
        this.icon = document.getElementById('alertIcon');
        this.title = document.getElementById('alertTitle');
        this.message = document.getElementById('alertMessage');
        this.confirmBtn = document.getElementById('alertConfirm');
        this.cancelBtn = document.getElementById('alertCancel');
        this.closeBtn = document.getElementById('alertClose');
        
        this.ready = !!(this.alertElement && this.overlay && this.icon && this.title && this.message && this.confirmBtn && this.cancelBtn && this.closeBtn);
        
        this.init();
    }

    init() {
        if (!this.ready) return;
        // Event listeners
        this.confirmBtn.addEventListener('click', () => {
            if (typeof this.confirmCallback === 'function') {
                this.confirmCallback();
            } else {
                this.hide();
            }
        });

        this.cancelBtn.addEventListener('click', () => {
            if (typeof this.cancelCallback === 'function') {
                this.cancelCallback();
            }
            this.hide();
        });

        this.closeBtn.addEventListener('click', () => this.hide());

        this.overlay.addEventListener('click', () => {
            if (this.allowOutsideClick) {
                this.hide();
            }
        });
        
        // Prevent modal close on escape key for important alerts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.alertElement.classList.contains('block')) {
                this.hide();
            }
        });
    }

    show(options = {}) {
        const {
            type = 'info',
            title = 'اطلاع',
            message = '',
            confirmText = 'تأیید',
            cancelText = 'انصراف',
            showCancel = true,
            confirmCallback = null,
            cancelCallback = null,
            allowOutsideClick = true
        } = options;

        // Fallback when component DOM not found
        if (!this.ready) {
            const shouldProceed = (type === 'danger' || type === 'question')
                ? window.confirm(message || 'آیا مطمئن هستید؟')
                : true;
            if (shouldProceed && typeof confirmCallback === 'function') confirmCallback();
            return;
        }

        // Set content
        this.title.textContent = title;
        this.message.textContent = message;
        this.confirmBtn.innerHTML = `<i class="fas fa-check ml-2"></i>${confirmText}`;
        this.cancelBtn.innerHTML = `<i class="fas fa-times ml-2"></i>${cancelText}`;

        // Set icon and colors based on type
        this.setIcon(type);
        this.setButtonStyle(type);

        // Show/hide cancel button
        this.cancelBtn.style.display = showCancel ? 'block' : 'none';

        // Store callbacks
        this.confirmCallback = confirmCallback;
        this.cancelCallback = cancelCallback;
        this.allowOutsideClick = allowOutsideClick;

        // Show alert with animation
        this.alertElement.classList.remove('hidden');
        this.alertElement.classList.add('block');
        
        // Add animation class
        setTimeout(() => {
            this.alertElement.classList.add('alert-enter-active');
        }, 10);

        // Focus on confirm button
        setTimeout(() => {
            this.confirmBtn.focus();
        }, 300);
    }

    hide() {
        if (!this.ready) return;
        // Remove animation class
        this.alertElement.classList.remove('alert-enter-active');
        this.alertElement.classList.add('alert-exit-active');
        
        // Hide after animation
        setTimeout(() => {
            this.alertElement.classList.add('hidden');
            this.alertElement.classList.remove('block', 'alert-exit-active');
        }, 200);
    }

    setIcon(type) {
        const iconClasses = {
            success: 'fas fa-check',
            warning: 'fas fa-exclamation-triangle',
            danger: 'fas fa-times',
            info: 'fas fa-info-circle',
            question: 'fas fa-question-circle'
        };

        const iconClass = iconClasses[type] || iconClasses.info;
        this.icon.className = `w-10 h-10 rounded-full flex items-center justify-center alert-icon-${type}`;
        this.icon.innerHTML = `<i class="${iconClass}"></i>`;
    }

    setButtonStyle(type) {
        const buttonClasses = {
            success: 'alert-btn-success',
            warning: 'alert-btn-warning',
            danger: 'alert-btn-danger',
            info: 'alert-btn-info',
            question: 'alert-btn-primary'
        };

        // Remove all button classes
        this.confirmBtn.className = this.confirmBtn.className.replace(/alert-btn-\w+/g, '');
        
        // Add new class
        const buttonClass = buttonClasses[type] || buttonClasses.info;
        this.confirmBtn.classList.add(buttonClass, 'px-4', 'py-2', 'text-sm', 'font-medium', 'rounded-lg', 'transition-colors', 'duration-200', 'focus:outline-none', 'focus:ring-2');
        
        // Add focus ring color
        if (type === 'danger') {
            this.confirmBtn.classList.add('focus:ring-red-500');
        } else if (type === 'success') {
            this.confirmBtn.classList.add('focus:ring-green-500');
        } else if (type === 'warning') {
            this.confirmBtn.classList.add('focus:ring-yellow-500');
        } else {
            this.confirmBtn.classList.add('focus:ring-blue-500');
        }
    }

    // Static methods for easy use
    static success(message, title = 'موفقیت', confirmText = 'تأیید') {
        return new Promise((resolve) => {
            const alert = AdminAlert.getInstance();
            alert.show({
                type: 'success',
                title,
                message,
                confirmText,
                showCancel: false,
                confirmCallback: () => {
                    alert.hide();
                    resolve(true);
                }
            });
        });
    }

    static error(message, title = 'خطا', confirmText = 'تأیید') {
        return new Promise((resolve) => {
            const alert = AdminAlert.getInstance();
            alert.show({
                type: 'danger',
                title,
                message,
                confirmText,
                showCancel: false,
                confirmCallback: () => {
                    alert.hide();
                    resolve(true);
                }
            });
        });
    }

    static warning(message, title = 'هشدار', confirmText = 'تأیید') {
        return new Promise((resolve) => {
            const alert = AdminAlert.getInstance();
            alert.show({
                type: 'warning',
                title,
                message,
                confirmText,
                showCancel: false,
                confirmCallback: () => {
                    alert.hide();
                    resolve(true);
                }
            });
        });
    }

    static confirm(message, title = 'تأیید عملیات', confirmText = 'تأیید', cancelText = 'انصراف') {
        return new Promise((resolve) => {
            const alert = AdminAlert.getInstance();
            alert.show({
                type: 'question',
                title,
                message,
                confirmText,
                cancelText,
                showCancel: true,
                confirmCallback: () => {
                    alert.hide();
                    resolve(true);
                },
                cancelCallback: () => {
                    alert.hide();
                    resolve(false);
                }
            });
        });
    }

    static delete(message, title = 'حذف آیتم', confirmText = 'حذف', cancelText = 'انصراف') {
        return new Promise((resolve) => {
            const alert = AdminAlert.getInstance();
            alert.show({
                type: 'danger',
                title,
                message,
                confirmText: `<i class="fas fa-trash ml-2"></i>${confirmText}`,
                cancelText,
                showCancel: true,
                confirmCallback: () => {
                    alert.hide();
                    resolve(true);
                },
                cancelCallback: () => {
                    alert.hide();
                    resolve(false);
                }
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Make AdminAlert available globally
    window.AdminAlert = AdminAlert;
    
    // Handle delete forms
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('delete-form')) {
            e.preventDefault();
            
            const form = e.target;
            const message = form.dataset.message || 'آیا از حذف این آیتم اطمینان دارید؟';
            const title = form.dataset.title || 'حذف آیتم';
            
            AdminAlert.delete(message, title).then((confirmed) => {
                if (confirmed) {
                    form.submit();
                }
            });
        }
        
        // Handle toggle forms
        if (e.target.classList.contains('toggle-form')) {
            e.preventDefault();
            
            const form = e.target;
            const message = form.dataset.message || 'آیا می‌خواهید وضعیت این آیتم را تغییر دهید؟';
            const title = form.dataset.title || 'تغییر وضعیت';
            
            AdminAlert.confirm(message, title, 'تأیید', 'انصراف').then((confirmed) => {
                if (confirmed) {
                    form.submit();
                }
            });
        }
    });
});
