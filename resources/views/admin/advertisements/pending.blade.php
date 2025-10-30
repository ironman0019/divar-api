@extends('admin.layouts.master')

@section('title', 'آگهی‌های در انتظار')

@section('content')
<!-- Pending Advertisements Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">آگهی‌های در انتظار</h1>
            <p class="text-gray-400 text-sm lg:text-base">تایید یا رد آگهی‌های ارسالی کاربران</p>
        </div>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <a href="{{ route('admin.advertisements.index') }}" 
               class="bg-gray-600 text-gray-300 px-4 py-2 rounded-lg font-medium hover:bg-gray-500 transition-colors duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                همه آگهی‌ها
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        @include('admin.components.alerts.success', ['message' => session('success')])
    @endif

    @if(session('error'))
        @include('admin.components.alerts.error', ['message' => session('error')])
    @endif

    <!-- Search and Filters -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 mb-6">
        <form method="GET" class="space-y-4">
            <!-- Search Bar -->
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-gray-300 font-medium mb-2">جستجو</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="جستجو در عنوان و توضیحات..."
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                </div>
                
                <!-- Sort -->
                <div class="lg:w-48">
                    <label for="sort" class="block text-gray-300 font-medium mb-2">مرتب‌سازی</label>
                    <select name="sort" id="sort" class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>جدیدترین</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>عنوان</option>
                        <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>قیمت</option>
                    </select>
                </div>

                <!-- Sort Direction -->
                <div class="lg:w-32">
                    <label for="direction" class="block text-gray-300 font-medium mb-2">ترتیب</label>
                    <select name="direction" id="direction" class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>نزولی</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>صعودی</option>
                    </select>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex gap-3">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-search ml-2"></i>
                    اعمال فیلتر
                </button>
                <a href="{{ route('admin.advertisements.pending') }}" 
                   class="bg-gray-600 text-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-500 transition-colors duration-200">
                    <i class="fas fa-times ml-2"></i>
                    پاک کردن
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    @if($advertisements->count() > 0)
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-4 mb-6">
            <form id="bulkForm" method="POST" class="flex items-center gap-4">
                @csrf
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 text-yellow-primary bg-dark-tertiary border-gray-600 rounded focus:ring-yellow-primary focus:ring-2">
                    <label for="selectAll" class="text-gray-300 font-medium">انتخاب همه</label>
                </div>
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="bulkAction('approve')"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-600 transition-colors duration-200">
                        <i class="fas fa-check ml-2"></i>
                        تایید انتخاب شده‌ها
                    </button>
                    <button type="button" 
                            onclick="bulkAction('reject')"
                            class="bg-red-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-600 transition-colors duration-200">
                        <i class="fas fa-times ml-2"></i>
                        رد انتخاب شده‌ها
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Pending Advertisements Table -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">
                            <input type="checkbox" id="selectAllTable" class="w-4 h-4 text-yellow-primary bg-dark-tertiary border-gray-600 rounded focus:ring-yellow-primary focus:ring-2">
                        </th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">تصویر</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">عنوان</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">کاربر</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">دسته‌بندی</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">قیمت</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">تاریخ ارسال</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($advertisements as $advertisement)
                        <tr class="hover:bg-dark-tertiary/50 transition-colors duration-200">
                            <!-- Checkbox -->
                            <td class="px-6 py-4">
                                <input type="checkbox" 
                                       name="advertisement_ids[]" 
                                       value="{{ $advertisement->id }}"
                                       class="advertisement-checkbox w-4 h-4 text-yellow-primary bg-dark-tertiary border-gray-600 rounded focus:ring-yellow-primary focus:ring-2">
                            </td>

                            <!-- Image -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($advertisement->image)
                                        <img src="{{ asset($advertisement->image) }}" 
                                             alt="{{ $advertisement->title }}" 
                                             class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <div class="w-12 h-12 bg-gray-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    @if($advertisement->galleries_count > 0)
                                        <span class="bg-yellow-primary text-dark-primary text-xs px-2 py-1 rounded-full">
                                            +{{ $advertisement->galleries_count }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Title -->
                            <td class="px-6 py-4">
                                <div class="max-w-xs">
                                    <h3 class="text-gray-300 font-medium truncate">{{ $advertisement->title }}</h3>
                                    <p class="text-gray-400 text-sm mt-1 line-clamp-2">{{ Str::limit($advertisement->description, 60) }}</p>
                                </div>
                            </td>

                            <!-- User -->
                            <td class="px-6 py-4">
                                <div class="text-gray-300">
                                    <p class="font-medium">{{ $advertisement->user->name ?? 'نامشخص' }}</p>
                                    <p class="text-sm text-gray-400">{{ $advertisement->user->email ?? '' }}</p>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-4">
                                <span class="text-gray-300">{{ $advertisement->category->name ?? 'نامشخص' }}</span>
                            </td>

                            <!-- Price -->
                            <td class="px-6 py-4">
                                @if($advertisement->price)
                                    <span class="text-yellow-primary font-medium">{{ number_format($advertisement->price) }} تومان</span>
                                @else
                                    <span class="text-gray-400">توافقی</span>
                                @endif
                            </td>

                            <!-- Created At -->
                            <td class="px-6 py-4">
                                <span class="text-gray-300">{{ $advertisement->created_at->format('Y/m/d H:i') }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <!-- View -->
                                    <a href="{{ route('admin.advertisements.show', $advertisement) }}" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors duration-200"
                                       title="مشاهده">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Approve -->
                                    <form method="POST" 
                                          action="{{ route('admin.advertisements.approve', $advertisement) }}" 
                                          class="inline toggle-form"
                                          data-title="تایید آگهی"
                                          data-message="آیا از تایید این آگهی اطمینان دارید؟">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-green-400 hover:text-green-300 transition-colors duration-200"
                                                title="تایید">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>

                                    <!-- Reject -->
                                    <form method="POST" 
                                          action="{{ route('admin.advertisements.reject', $advertisement) }}" 
                                          class="inline toggle-form"
                                          data-title="رد آگهی"
                                          data-message="آیا از رد این آگهی اطمینان دارید؟">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-red-400 hover:text-red-300 transition-colors duration-200"
                                                title="رد">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-clock text-4xl mb-4"></i>
                                <p>هیچ آگهی در انتظاری یافت نشد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($advertisements->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $advertisements->links() }}
            </div>
        @endif
    </div>
</main>

<script>
// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.advertisement-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    document.getElementById('selectAllTable').checked = this.checked;
});

document.getElementById('selectAllTable').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.advertisement-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    document.getElementById('selectAll').checked = this.checked;
});

// Individual checkbox change
document.querySelectorAll('.advertisement-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.advertisement-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.advertisement-checkbox:checked');
        
        document.getElementById('selectAll').checked = allCheckboxes.length === checkedCheckboxes.length;
        document.getElementById('selectAllTable').checked = allCheckboxes.length === checkedCheckboxes.length;
    });
});

// Bulk actions
function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.advertisement-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('لطفاً حداقل یک آگهی را انتخاب کنید.');
        return;
    }

    const actionText = action === 'approve' ? 'تایید' : 'رد';
    if (!confirm(`آیا از ${actionText} ${checkedBoxes.length} آگهی انتخاب شده اطمینان دارید؟`)) {
        return;
    }

    // Create form for bulk action
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action === 'approve' ? '{{ route("admin.advertisements.bulk-approve") }}' : '{{ route("admin.advertisements.bulk-reject") }}';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);

    // Add selected advertisement IDs
    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'advertisement_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
