@extends('admin.layouts.master')

@section('title', 'پنل ادمین')

@section('content')
<!-- Dashboard Content -->
<main class="p-4 lg:p-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs lg:text-sm">کل کاربران</p>
                    <p class="text-xl lg:text-2xl font-bold text-yellow-primary">12,458</p>
                    <p class="text-green-400 text-xs lg:text-sm">↑ 12% از ماه قبل</p>
                </div>
                <div class="bg-yellow-primary/20 p-3 lg:p-4 rounded-full">
                    <i class="fas fa-users text-yellow-primary text-lg lg:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs lg:text-sm">فروش امروز</p>
                    <p class="text-xl lg:text-2xl font-bold text-yellow-primary">$24,500</p>
                    <p class="text-green-400 text-xs lg:text-sm">↑ 8% از دیروز</p>
                </div>
                <div class="bg-green-500/20 p-3 lg:p-4 rounded-full">
                    <i class="fas fa-dollar-sign text-green-400 text-lg lg:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs lg:text-sm">سفارشات جدید</p>
                    <p class="text-xl lg:text-2xl font-bold text-yellow-primary">156</p>
                    <p class="text-red-400 text-xs lg:text-sm">↓ 3% از دیروز</p>
                </div>
                <div class="bg-blue-500/20 p-3 lg:p-4 rounded-full">
                    <i class="fas fa-shopping-cart text-blue-400 text-lg lg:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs lg:text-sm">بازدید سایت</p>
                    <p class="text-xl lg:text-2xl font-bold text-yellow-primary">89,420</p>
                    <p class="text-green-400 text-xs lg:text-sm">↑ 15% از هفته قبل</p>
                </div>
                <div class="bg-purple-500/20 p-3 lg:p-4 rounded-full">
                    <i class="fas fa-eye text-purple-400 text-lg lg:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Activity Row -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <!-- Chart -->
        <div class="xl:col-span-2 bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20">
            <h3 class="text-yellow-primary font-bold text-base lg:text-lg mb-4">نمودار فروش</h3>
            <div class="h-48 lg:h-64 bg-dark-tertiary rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-yellow-primary text-3xl lg:text-4xl mb-2"></i>
                    <p class="text-gray-400 text-sm lg:text-base">نمودار فروش ماهانه</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20">
            <h3 class="text-yellow-primary font-bold text-base lg:text-lg mb-4">فعالیت‌های اخیر</h3>
            <div class="gap-y-3 lg:gap-y-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-6 h-6 lg:w-8 lg:h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-plus text-white text-xs"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-gray-300 text-xs lg:text-sm">کاربر جدید عضو شد</p>
                        <p class="text-gray-500 text-xs">5 دقیقه پیش</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div
                        class="w-6 h-6 lg:w-8 lg:h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shopping-bag text-white text-xs"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-gray-300 text-xs lg:text-sm">سفارش جدید ثبت شد</p>
                        <p class="text-gray-500 text-xs">12 دقیقه پیش</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div
                        class="w-6 h-6 lg:w-8 lg:h-8 bg-yellow-primary rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-star text-dark-primary text-xs"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-gray-300 text-xs lg:text-sm">نظر جدید دریافت شد</p>
                        <p class="text-gray-500 text-xs">25 دقیقه پیش</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div
                        class="w-6 h-6 lg:w-8 lg:h-8 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation text-white text-xs"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-gray-300 text-xs lg:text-sm">خطا در سیستم</p>
                        <p class="text-gray-500 text-xs">1 ساعت پیش</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <!-- Recent Orders Table -->
        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                <h3 class="text-yellow-primary font-bold text-base lg:text-lg">آخرین سفارشات</h3>
                <button
                    class="text-yellow-primary hover:text-yellow-secondary text-xs lg:text-sm self-start sm:self-auto">مشاهده
                    همه</button>
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full min-w-[500px]">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                شماره</th>
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                مشتری</th>
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                مبلغ</th>
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-800">
                            <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm">#1234</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">احمد محمدی</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$899</td>
                            <td class="py-2 lg:py-3">
                                <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded-full text-xs">تکمیل</span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-800">
                            <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm">#1235</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">مریم احمدی</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$699</td>
                            <td class="py-2 lg:py-3">
                                <span
                                    class="bg-yellow-primary/20 text-yellow-primary px-2 py-1 rounded-full text-xs">انتظار</span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-800">
                            <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm">#1236</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">علی رضایی</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$199</td>
                            <td class="py-2 lg:py-3">
                                <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded-full text-xs">ارسال</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Products Table -->
        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                <h3 class="text-yellow-primary font-bold text-base lg:text-lg">محصولات پرفروش</h3>
                <button
                    class="text-yellow-primary hover:text-yellow-secondary text-xs lg:text-sm self-start sm:self-auto">مشاهده
                    همه</button>
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full min-w-[400px]">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                محصول</th>
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                فروش</th>
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                درآمد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-800">
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">لپ تاپ ایسوس</td>
                            <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm">45</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$40,455</td>
                        </tr>
                        <tr class="border-b border-gray-800">
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">گوشی سامسونگ</td>
                            <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm">38</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$26,562</td>
                        </tr>
                        <tr class="border-b border-gray-800">
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">هدفون بی‌سیم</td>
                            <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm">29</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$5,771</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Full Width Table -->
    <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
            <h3 class="text-yellow-primary font-bold text-base lg:text-lg">گزارش مالی ماهانه</h3>
            <div class="flex gap-2">
                <button class="bg-yellow-primary text-dark-primary px-3 py-1 rounded text-xs font-medium">صادرات
                    Excel</button>
                <button class="text-yellow-primary hover:text-yellow-secondary text-xs">فیلتر</button>
            </div>
        </div>
        <div class="table-container overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">ماه
                        </th>
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">کل فروش
                        </th>
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                            هزینه‌ها</th>
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">سود
                            خالص</th>
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">تعداد
                            سفارش</th>
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">رشد
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-800">
                        <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm font-medium">آبان 1403
                        </td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$125,400</td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$45,200</td>
                        <td class="py-2 lg:py-3 text-green-400 text-xs lg:text-sm">$80,200</td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">1,240</td>
                        <td class="py-2 lg:py-3 text-green-400 text-xs lg:text-sm">↑ 12%</td>
                    </tr>
                    <tr class="border-b border-gray-800">
                        <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm font-medium">مهر 1403
                        </td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$112,800</td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$42,500</td>
                        <td class="py-2 lg:py-3 text-green-400 text-xs lg:text-sm">$70,300</td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">1,128</td>
                        <td class="py-2 lg:py-3 text-green-400 text-xs lg:text-sm">↑ 8%</td>
                    </tr>
                    <tr class="border-b border-gray-800">
                        <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm font-medium">شهریور 1403
                        </td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$98,600</td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$38,900</td>
                        <td class="py-2 lg:py-3 text-green-400 text-xs lg:text-sm">$59,700</td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">986</td>
                        <td class="py-2 lg:py-3 text-red-400 text-xs lg:text-sm">↓ 3%</td>
                    </tr>
                    <tr class="border-b border-gray-800">
                        <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm font-medium">مرداد 1403
                        </td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$104,200</td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">$41,200</td>
                        <td class="py-2 lg:py-3 text-green-400 text-xs lg:text-sm">$63,000</td>
                        <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">1,042</td>
                        <td class="py-2 lg:py-3 text-green-400 text-xs lg:text-sm">↑ 5%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>



@endsection