    <!-- Mobile Menu Button -->
    <button class="fixed top-4 right-4 z-50 lg:hidden bg-yellow-primary text-dark-primary p-3 rounded-full shadow-lg"
        onclick="toggleMobileSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar Overlay for Mobile -->
    <div class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" id="sidebar-overlay"
        onclick="toggleMobileSidebar()"></div>

    <!-- Sidebar -->
    <div class="fixed right-0 top-0 h-full w-64 bg-dark-secondary shadow-2xl z-50 transform translate-x-full lg:translate-x-0 transition-transform duration-300"
        id="sidebar">
        <div class="p-4 lg:p-6 border-b border-yellow-primary/20">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-yellow-primary to-yellow-dark rounded-full flex items-center justify-center glow-effect">
                    <i class="fas fa-crown text-dark-primary text-lg lg:text-xl"></i>
                </div>
                <div>
                    <h2 class="text-yellow-primary font-bold text-base lg:text-lg">پنل ادمین</h2>
                    <p class="text-gray-400 text-xs lg:text-sm">مدیریت سیستم</p>
                </div>
            </div>
        </div>

        <nav class="mt-6 overflow-y-auto h-[calc(100vh-200px)]">
            <!-- Dashboard -->
            <div
                class="sidebar-item px-4 lg:px-6 py-3 text-yellow-primary bg-yellow-primary/10 border-r-3 border-yellow-primary">
                <div class="flex items-center gap-3">
                    <i class="fas fa-tachometer-alt"></i>
                    <a class="font-medium text-sm lg:text-base" href="{{ route('admin.dashboard') }}">داشبورد</a>
                </div>
            </div>

            <!-- Users Dropdown -->
            <div class="sidebar-item">
                <div class="px-4 lg:px-6 py-3 text-gray-300 hover:text-yellow-primary cursor-pointer flex items-center justify-between"
                    onclick="toggleDropdown('users')">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-users"></i>
                        <span class="font-medium text-sm lg:text-base">کاربران</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300" id="users-arrow"></i>
                </div>
                <div class="hidden bg-dark-tertiary" id="users-dropdown">
                    <a href="{{ route('admin.users.index') }}"
                        class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">مدیریت
                        کاربران</a>
                    <a href="{{ route('admin.users.create') }}"
                        class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">افزودن
                        کاربر</a>
                </div>
            </div>



            <!-- Analytics -->
            <div class="sidebar-item px-4 lg:px-6 py-3 text-gray-300 hover:text-yellow-primary cursor-pointer">
                <a href="{{ route('admin.statistics.index') }}" class="flex items-center gap-3">
                    <i class="fas fa-chart-bar"></i>
                    <span class="font-medium text-sm lg:text-base">آمار و تحلیل</span>
                </a>
            </div>

            <!-- Finance Dropdown -->
            <div class="sidebar-item">
                <div class="px-4 lg:px-6 py-3 text-gray-300 hover:text-yellow-primary cursor-pointer flex items-center justify-between"
                    onclick="toggleDropdown('finance')">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-coins"></i>
                        <span class="font-medium text-sm lg:text-base">مالی</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300" id="finance-arrow"></i>
                </div>
                <div class="hidden bg-dark-tertiary" id="finance-dropdown">
                    <a href="{{ route('admin.payment.income-report.index') }}"
                        class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">گزارش
                        درآمد</a>
                    <a href="{{ route('admin.payment.transactions.index') }}"
                        class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">تراکنش‌ها</a>
                </div>
            </div>

            <!-- Menu Management -->
            <div class="sidebar-item">
                <div class="px-4 lg:px-6 py-3 text-gray-300 hover:text-yellow-primary cursor-pointer flex items-center justify-between"
                    onclick="toggleDropdown('menus')">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-bars"></i>
                        <span class="font-medium text-sm lg:text-base">مدیریت منوها</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300" id="menus-arrow"></i>
                </div>
                <div class="hidden bg-dark-tertiary" id="menus-dropdown">
                    <a href="{{ route('admin.menus.index') }}"
                        class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">لیست
                        منوها</a>
                    <a href="{{ route('admin.menus.create') }}"
                        class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">افزودن
                        منو جدید</a>
                </div>
            </div>

            <!-- Categories Dropdown -->
            <div class="sidebar-item">
                <div class="px-4 lg:px-6 py-3 text-gray-300 hover:text-yellow-primary cursor-pointer flex items-center justify-between"
                    onclick="toggleDropdown('categories')">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-folder"></i>
                        <span class="font-medium text-sm lg:text-base">دسته‌بندی‌ها</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300" id="categories-arrow"></i>
                </div>
                <div class="hidden bg-dark-tertiary" id="categories-dropdown">
                    <a href="{{ route('admin.categories.index') }}" class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">لیست دسته‌بندی‌ها</a>
                    <a href="{{ route('admin.categories.create') }}" class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">افزودن دسته‌بندی جدید</a>
                    <a href="{{ route('admin.categories.attributes.index') }}" class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">ویژگی‌های دسته‌بندی</a>
                    <a href="{{ route('admin.categories.values.index') }}" class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">مقادیر ویژگی‌ها</a>
                </div>
            </div>

            <!-- Advertisements Dropdown -->
            <div class="sidebar-item">
                <div class="px-4 lg:px-6 py-3 text-gray-300 hover:text-yellow-primary cursor-pointer flex items-center justify-between"
                    onclick="toggleDropdown('advertisements')">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-ad"></i>
                        <span class="font-medium text-sm lg:text-base">آگهی‌ها</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300" id="advertisements-arrow"></i>
                </div>
                <div class="hidden bg-dark-tertiary" id="advertisements-dropdown">
                    <a href="{{ route('admin.advertisements.index') }}" class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">همه آگهی‌ها</a>
                    <a href="{{ route('admin.advertisements.pending') }}" class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">آگهی‌های در انتظار</a>
                    <a href="{{ route('admin.advertisements.featured') }}" class="block px-8 lg:px-12 py-2 text-gray-400 hover:text-yellow-primary text-sm">آگهی‌های ویژه</a>
                </div>
            </div>

            <!-- Settings -->
            <div class="sidebar-item px-4 lg:px-6 py-3 text-gray-300 hover:text-yellow-primary cursor-pointer">
                <a href="{{ route('admin.settings.general') }}" class="flex items-center gap-3">
                    <i class="fas fa-cog"></i>
                    <span class="font-medium text-sm lg:text-base">تنظیمات</span>
                </a>
            </div>
        </nav>

        <div class="absolute bottom-4 lg:bottom-6 right-4 lg:right-6 left-4 lg:left-6">
            <div class="bg-dark-tertiary rounded-lg p-3 lg:p-4 border border-yellow-primary/20">
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=ffd700&color=0f0f23&bold=true"
                        class="w-8 h-8 lg:w-10 lg:h-10 rounded-full">
                    <div>
                        <p class="text-yellow-primary font-medium text-sm">ادمین سیستم: {{ $user->name }}</p>
                        <p class="text-gray-400 text-xs">آنلاین</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
