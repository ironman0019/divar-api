@extends('admin.layouts.master')

@section('title', 'مدیریت شهرها')

@section('content')
<main class="p-4 lg:p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مدیریت شهرها</h1>
            <p class="text-gray-400 text-sm lg:text-base">افزودن و ویرایش شهرهای فعال در API</p>
        </div>
        <a href="{{ route('admin.cities.create') }}"
           class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200 mt-4 sm:mt-0">
            <i class="fas fa-plus ml-2"></i>
            افزودن شهر
        </a>
    </div>

    @if(session('success'))
        @include('admin.components.alerts.success', ['message' => session('success')])
    @endif
    @if(session('error'))
        @include('admin.components.alerts.error', ['message' => session('error')])
    @endif

    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-4 mb-6">
        <form method="GET" action="{{ route('admin.cities.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="جستجو..."
                   class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:outline-none">
            <select name="status" class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:outline-none">
                <option value="">همه وضعیت‌ها</option>
                <option value="1" @selected(request('status') === '1')>فعال</option>
                <option value="0" @selected(request('status') === '0')>غیرفعال</option>
            </select>
            <button type="submit" class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary">
                <i class="fas fa-search ml-2"></i> جستجو
            </button>
        </form>
    </div>

    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">نام</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">وضعیت</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cities as $city)
                        <tr class="border-b border-gray-800 hover:bg-dark-tertiary/50">
                            <td class="py-4 px-6 text-gray-300">{{ $city->name }}</td>
                            <td class="py-4 px-6">
                                <form action="{{ route('admin.cities.toggle-status', $city) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 rounded-full text-xs font-medium {{ $city->status ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $city->status ? 'فعال' : 'غیرفعال' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.cities.edit', $city) }}" class="text-yellow-primary hover:text-yellow-secondary p-2"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" class="inline delete-form"
                                          data-message="آیا از حذف شهر '{{ $city->name }}' اطمینان دارید؟"
                                          data-title="حذف شهر">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 p-2"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-gray-400">شهری یافت نشد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($cities->hasPages())
        <div class="mt-6">{{ $cities->links() }}</div>
    @endif
</main>
@endsection
