@extends('admin.layouts.master')

@section('title', 'تعرفه تبلیغات')

@section('content')
<main class="p-4 lg:p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">تعرفه تبلیغات</h1>
            <p class="text-gray-400 text-sm lg:text-base">مدیریت قیمت نردبان و ویژه</p>
        </div>
        <a href="{{ route('admin.payment.promotion-prices.create') }}"
           class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary mt-4 sm:mt-0">
            <i class="fas fa-plus ml-2"></i> افزودن تعرفه
        </a>
    </div>

    @if(session('success'))
        @include('admin.components.alerts.success', ['message' => session('success')])
    @endif

    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <select name="type" class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300">
                <option value="">همه انواع</option>
                <option value="ladder" @selected(request('type') === 'ladder')>نردبان</option>
                <option value="special" @selected(request('type') === 'special')>ویژه</option>
            </select>
            <select name="is_active" class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300">
                <option value="">همه وضعیت‌ها</option>
                <option value="1" @selected(request('is_active') === '1')>فعال</option>
                <option value="0" @selected(request('is_active') === '0')>غیرفعال</option>
            </select>
            <button type="submit" class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg">فیلتر</button>
        </form>
    </div>

    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="text-right text-gray-400 py-4 px-6 text-sm">نوع</th>
                        <th class="text-right text-gray-400 py-4 px-6 text-sm">مدت (روز)</th>
                        <th class="text-right text-gray-400 py-4 px-6 text-sm">قیمت</th>
                        <th class="text-right text-gray-400 py-4 px-6 text-sm">وضعیت</th>
                        <th class="text-right text-gray-400 py-4 px-6 text-sm">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotionPrices as $price)
                        <tr class="border-b border-gray-800 hover:bg-dark-tertiary/50">
                            <td class="py-4 px-6 text-gray-300">{{ $price->type_label }}</td>
                            <td class="py-4 px-6 text-gray-300">{{ $price->duration_days }}</td>
                            <td class="py-4 px-6 text-gray-300">{{ $price->formatted_price }}</td>
                            <td class="py-4 px-6">
                                <form action="{{ route('admin.payment.promotion-prices.toggle-status', $price) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1 rounded-full text-xs {{ $price->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $price->is_active ? 'فعال' : 'غیرفعال' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.payment.promotion-prices.edit', $price) }}" class="text-yellow-primary p-2"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.payment.promotion-prices.destroy', $price) }}" method="POST" class="inline delete-form"
                                          data-message="آیا از حذف این تعرفه اطمینان دارید؟" data-title="حذف تعرفه">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 p-2"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-12 text-center text-gray-400">تعرفه‌ای یافت نشد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($promotionPrices->hasPages())
        <div class="mt-6">{{ $promotionPrices->links() }}</div>
    @endif
</main>
@endsection
