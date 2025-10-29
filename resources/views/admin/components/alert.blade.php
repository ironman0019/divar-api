<!-- Custom Alert Modal -->
<div id="customAlert" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Background overlay with true blur (keeps page visible) -->
    <div class="fixed inset-0" id="alertOverlay"></div>
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Alert panel (centered, above overlay) -->
        <div class="relative z-10 inline-block w-full max-w-md p-0 my-8 overflow-hidden text-right align-middle transition-all transform bg-dark-secondary shadow-xl rounded-2xl border border-yellow-primary/20">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div id="alertIcon" class="w-10 h-10 rounded-full flex items-center justify-center">
                            <!-- Icon will be inserted here -->
                        </div>
                        <h3 id="alertTitle" class="text-lg font-bold text-gray-300">
                            <!-- Title will be inserted here -->
                        </h3>
                    </div>
                    <button id="alertClose" class="text-gray-400 hover:text-gray-300 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-4">
                <p id="alertMessage" class="text-gray-300 text-sm leading-relaxed">
                    <!-- Message will be inserted here -->
                </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-dark-tertiary">
                <button id="alertCancel" 
                        class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-600 rounded-lg hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <i class="fas fa-times ml-2"></i>
                    انصراف
                </button>
                <button id="alertConfirm" 
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <!-- Button content will be inserted here -->
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alert Styles -->
<style>
/* Blurred overlay without turning background black */
#alertOverlay {
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    background-color: rgba(0, 0, 0, 0.12);
}

.alert-icon-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.alert-icon-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.alert-icon-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.alert-icon-info {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.alert-btn-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.alert-btn-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.alert-btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.alert-btn-info {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.alert-btn-primary {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #0f0f23;
}

/* Animation classes */
.alert-enter {
    opacity: 0;
    transform: scale(0.9);
}

.alert-enter-active {
    opacity: 1;
    transform: scale(1);
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.alert-exit {
    opacity: 1;
    transform: scale(1);
}

.alert-exit-active {
    opacity: 0;
    transform: scale(0.9);
    transition: opacity 0.2s ease, transform 0.2s ease;
}
</style>