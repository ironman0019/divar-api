        <!-- Header -->
        <header class="bg-dark-secondary/50 backdrop-blur-sm border-b border-yellow-primary/20 p-4 lg:p-6 pt-16 lg:pt-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold text-yellow-primary">داشبورد مدیریت</h1>
                    <p class="text-gray-400 text-sm lg:text-base">خوش آمدید به پنل کنترل</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative" id="admin-notifications">
                        <button type="button" id="notification-toggle"
                            class="bg-dark-tertiary p-2 lg:p-3 rounded-full text-yellow-primary hover:bg-yellow-primary hover:text-dark-primary transition-all glow-effect"
                            aria-label="اعلان‌ها" aria-expanded="false" aria-haspopup="true">
                            <i class="fas fa-bell text-sm lg:text-base"></i>
                        </button>
                        @if(($adminUnreadCount ?? 0) > 0)
                            <span id="notification-badge"
                                class="absolute -top-1 -left-1 bg-red-500 text-white text-xs rounded-full min-w-4 h-4 lg:min-w-5 lg:h-5 px-1 flex items-center justify-center">
                                {{ $adminUnreadCount > 9 ? '9+' : $adminUnreadCount }}
                            </span>
                        @else
                            <span id="notification-badge"
                                class="absolute -top-1 -left-1 bg-red-500 text-white text-xs rounded-full min-w-4 h-4 lg:min-w-5 lg:h-5 px-1 hidden items-center justify-center">
                                0
                            </span>
                        @endif
                    </div>

                    <div id="notification-backdrop" class="notification-backdrop hidden"></div>

                    <div id="notification-dropdown"
                        class="notification-dropdown hidden w-80 sm:w-96 border border-yellow-primary/30 rounded-xl overflow-hidden">
                        <div class="notification-dropdown-header flex items-center justify-between px-4 py-3 border-b border-yellow-primary/20">
                            <h3 class="text-yellow-primary font-bold text-sm">اعلان‌ها</h3>
                            @if(($adminUnreadCount ?? 0) > 0)
                                <button type="button" id="mark-all-read-btn"
                                    class="text-xs text-gray-400 hover:text-yellow-primary transition-colors">
                                    علامت‌گذاری همه به عنوان خوانده شده
                                </button>
                            @endif
                        </div>

                        <div class="notification-list-scroll max-h-80 overflow-y-auto" id="notification-list">
                            @forelse($adminNotifications ?? [] as $notification)
                                <a href="{{ $notification['url'] }}"
                                    data-notification-id="{{ $notification['id'] }}"
                                    class="notification-item flex items-start gap-3 px-4 py-3 transition-colors {{ $notification['read'] ? 'is-read' : '' }}">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $notification['icon_class'] }}">
                                        <i class="fas {{ $notification['icon'] }} text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-white text-sm font-medium">{{ $notification['title'] }}</p>
                                        <p class="text-gray-300 text-xs mt-0.5">{{ $notification['message'] }}</p>
                                        <p class="text-gray-400 text-xs mt-1">{{ $notification['created_at']->diffForHumans() }}</p>
                                    </div>
                                    @unless($notification['read'])
                                        <span class="w-2 h-2 bg-yellow-primary rounded-full shrink-0 mt-2 unread-dot"></span>
                                    @endunless
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center text-gray-400 text-sm" id="notification-empty">
                                    <i class="fas fa-bell-slash text-2xl mb-2 block opacity-50"></i>
                                    اعلان جدیدی وجود ندارد
                                </div>
                            @endforelse
                        </div>

                        @if(($adminPendingAdsCount ?? 0) > 0)
                            <div class="notification-dropdown-footer px-4 py-3 border-t border-yellow-primary/20">
                                <a href="{{ route('admin.advertisements.pending') }}"
                                    class="text-yellow-primary hover:text-yellow-secondary text-xs font-medium flex items-center justify-center gap-2">
                                    <i class="fas fa-list"></i>
                                    مشاهده {{ $adminPendingAdsCount }} آگهی در انتظار
                                </a>
                            </div>
                        @endif
                    </div>

                    <button
                        class="bg-gradient-to-r from-yellow-primary to-yellow-dark text-dark-primary px-4 py-2 lg:px-6 lg:py-2 rounded-full font-medium hover:shadow-lg transition-all text-sm lg:text-base">
                        پروفایل
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-red-500/20 text-red-400 border border-red-500/30 px-4 py-2 lg:px-6 lg:py-2 rounded-full font-medium hover:bg-red-500 hover:text-white transition-all text-sm lg:text-base flex items-center gap-2">
                            <i class="fas fa-sign-out-alt"></i>
                            خروج
                        </button>
                    </form>
                </div>
            </div>
        </header>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const container = document.getElementById('admin-notifications');
                    if (!container) return;

                    const backdrop = document.getElementById('notification-backdrop');
                    const dropdown = document.getElementById('notification-dropdown');
                    const toggleBtn = document.getElementById('notification-toggle');
                    const badge = document.getElementById('notification-badge');
                    const markAllBtn = document.getElementById('mark-all-read-btn');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    document.body.appendChild(backdrop);
                    document.body.appendChild(dropdown);

                    function positionDropdown() {
                        const rect = toggleBtn.getBoundingClientRect();
                        const dropdownWidth = dropdown.offsetWidth || 384;
                        const viewportPadding = 12;

                        let left = rect.left;
                        if (left + dropdownWidth > window.innerWidth - viewportPadding) {
                            left = window.innerWidth - dropdownWidth - viewportPadding;
                        }
                        if (left < viewportPadding) {
                            left = viewportPadding;
                        }

                        dropdown.style.top = (rect.bottom + 12) + 'px';
                        dropdown.style.left = left + 'px';
                    }

                    function setDropdownOpen(isOpen) {
                        dropdown.classList.toggle('hidden', !isOpen);
                        backdrop.classList.toggle('hidden', !isOpen);
                        document.body.classList.toggle('notification-panel-open', isOpen);
                        toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                        if (isOpen) {
                            positionDropdown();
                        }
                    }

                    function updateBadge(count) {
                        if (!badge) return;

                        if (count > 0) {
                            badge.textContent = count > 9 ? '9+' : count;
                            badge.classList.remove('hidden');
                            badge.classList.add('flex');
                        } else {
                            badge.classList.add('hidden');
                            badge.classList.remove('flex');
                        }
                    }

                    function markNotificationRead(notificationId) {
                        fetch('{{ route('admin.notifications.mark-read') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ notification_id: notificationId }),
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.unread_count !== undefined) {
                                    updateBadge(data.unread_count);
                                }
                            })
                            .catch(() => {});
                    }

                    toggleBtn.addEventListener('click', function (event) {
                        event.stopPropagation();
                        const isOpen = dropdown.classList.contains('hidden');
                        setDropdownOpen(isOpen);
                    });

                    backdrop.addEventListener('click', function () {
                        setDropdownOpen(false);
                    });

                    document.addEventListener('click', function (event) {
                        if (dropdown.classList.contains('hidden')) return;

                        if (!dropdown.contains(event.target) && !container.contains(event.target)) {
                            setDropdownOpen(false);
                        }
                    });

                    window.addEventListener('resize', function () {
                        if (!dropdown.classList.contains('hidden')) {
                            positionDropdown();
                        }
                    });

                    dropdown.querySelectorAll('.notification-item').forEach(function (item) {
                        item.addEventListener('click', function () {
                            const notificationId = item.getAttribute('data-notification-id');
                            if (!notificationId) return;

                            item.classList.add('is-read');
                            item.querySelector('.unread-dot')?.remove();
                            markNotificationRead(notificationId);
                        });
                    });

                    markAllBtn?.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        fetch('{{ route('admin.notifications.mark-all-read') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                        })
                            .then(response => response.json())
                            .then(() => {
                                updateBadge(0);
                                dropdown.querySelectorAll('.notification-item').forEach(function (item) {
                                    item.classList.add('is-read');
                                    item.querySelector('.unread-dot')?.remove();
                                });
                                markAllBtn.remove();
                            })
                            .catch(() => {});
                    });
                });
            </script>
        @endpush
