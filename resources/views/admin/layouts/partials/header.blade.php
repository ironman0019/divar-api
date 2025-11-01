        <!-- Header -->
        <header class="bg-dark-secondary/50 backdrop-blur-sm border-b border-yellow-primary/20 p-4 lg:p-6 pt-16 lg:pt-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold text-yellow-primary">داشبورد مدیریت</h1>
                    <p class="text-gray-400 text-sm lg:text-base">خوش آمدید به پنل کنترل</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <button
                            class="bg-dark-tertiary p-2 lg:p-3 rounded-full text-yellow-primary hover:bg-yellow-primary hover:text-dark-primary transition-all glow-effect">
                            <i class="fas fa-bell text-sm lg:text-base"></i>
                        </button>
                        <span
                            class="absolute -top-1 -left-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 lg:w-5 lg:h-5 flex items-center justify-center text-xs">3</span>
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
