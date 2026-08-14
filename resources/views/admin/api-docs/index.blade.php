@extends('admin.layouts.master')

@section('title', 'مستندات API')

@section('content')
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مستندات API</h1>
            <p class="text-gray-400 text-sm lg:text-base">راهنمای کامل اندپوینت‌های نسخه V1 برای توسعه‌دهندگان اپلیکیشن</p>
        </div>
        <a href="{{ $scrambleDocsUrl }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-2 bg-yellow-primary text-dark-primary px-5 py-2.5 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200 shrink-0">
            <i class="fas fa-external-link-alt"></i>
            مستندات تعاملی (docs/api)
        </a>
    </div>

    <!-- Scramble notice -->
    <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 mb-6 flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-400 mt-1"></i>
        <div class="text-sm text-gray-300 leading-relaxed">
            علاوه بر این صفحه، می‌توانید مستندات تعاملی OpenAPI را در آدرس
            <a href="{{ $scrambleDocsUrl }}" target="_blank" rel="noopener noreferrer" class="text-yellow-primary hover:underline font-mono" dir="ltr">{{ $scrambleDocsUrl }}</a>
            مشاهده و تست کنید.
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Sticky TOC -->
        <aside class="xl:col-span-1">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-4 xl:sticky xl:top-6">
                <h2 class="text-yellow-primary font-bold mb-3 text-sm">فهرست مطالب</h2>
                <nav class="space-y-1 text-sm">
                    <a href="#intro" class="block text-gray-400 hover:text-yellow-primary py-1.5 transition-colors">مقدمه</a>
                    <a href="#auth" class="block text-gray-400 hover:text-yellow-primary py-1.5 transition-colors">احراز هویت</a>
                    <a href="#cities" class="block text-gray-400 hover:text-yellow-primary py-1.5 transition-colors">شهرها</a>
                    <a href="#categories" class="block text-gray-400 hover:text-yellow-primary py-1.5 transition-colors">دسته‌بندی‌ها</a>
                    <a href="#ads-public" class="block text-gray-400 hover:text-yellow-primary py-1.5 transition-colors">آگهی‌ها (عمومی)</a>
                    <a href="#ads-auth" class="block text-gray-400 hover:text-yellow-primary py-1.5 transition-colors">آگهی‌ها (نیازمند توکن)</a>
                    <a href="#payments" class="block text-gray-400 hover:text-yellow-primary py-1.5 transition-colors">پرداخت و ارتقا</a>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <div class="xl:col-span-3 space-y-8">

            {{-- ========== INTRO ========== --}}
            <section id="intro" class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 scroll-mt-6">
                <h2 class="text-yellow-primary font-bold text-lg mb-4 flex items-center gap-2">
                    <i class="fas fa-rocket"></i>
                    مقدمه
                </h2>

                <div class="space-y-4 text-gray-300 text-sm leading-relaxed">
                    <div>
                        <h3 class="text-gray-200 font-medium mb-2">آدرس پایه</h3>
                        <code class="block bg-dark-tertiary rounded-lg px-4 py-3 text-yellow-primary font-mono text-xs dir-ltr text-left" dir="ltr">{{ $baseUrl }}</code>
                    </div>

                    <div>
                        <h3 class="text-gray-200 font-medium mb-2">هدرهای پیشنهادی</h3>
                        <pre class="bg-dark-tertiary rounded-lg px-4 py-3 text-xs text-gray-300 overflow-x-auto font-mono dir-ltr text-left" dir="ltr">Accept: application/json
Content-Type: application/json
Authorization: Bearer {token}   // فقط برای مسیرهای محافظت‌شده</pre>
                    </div>

                    <div>
                        <h3 class="text-gray-200 font-medium mb-2">فرمت پاسخ استاندارد</h3>
                        <p class="mb-2 text-gray-400">تمام پاسخ‌های موفق و ناموفق معمولاً با ساختار زیر برمی‌گردند:</p>
                        <pre class="bg-dark-tertiary rounded-lg px-4 py-3 text-xs text-gray-300 overflow-x-auto font-mono dir-ltr text-left" dir="ltr">{
  "status": "ok",
  "message": "پیام توضیحی",
  "data": { }
}</pre>
                        <pre class="bg-dark-tertiary rounded-lg px-4 py-3 text-xs text-gray-300 overflow-x-auto font-mono dir-ltr text-left mt-2" dir="ltr">{
  "status": "failed",
  "message": "شرح خطا",
  "data": null
}</pre>
                    </div>

                    <div>
                        <h3 class="text-gray-200 font-medium mb-2">احراز هویت</h3>
                        <p class="text-gray-400">مسیرهای محافظت‌شده با <span class="text-yellow-primary">Laravel Sanctum</span> محافظت می‌شوند. پس از ورود یا تأیید OTP، توکن را در هدر <code class="text-yellow-primary font-mono" dir="ltr">Authorization: Bearer {token}</code> ارسال کنید.</p>
                    </div>

                    <div>
                        <h3 class="text-gray-200 font-medium mb-2">کدهای رایج HTTP</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-400">
                            <li><span class="text-green-400 font-mono">200</span> — موفق</li>
                            <li><span class="text-yellow-400 font-mono">400 / 422</span> — خطای اعتبارسنجی یا درخواست نامعتبر</li>
                            <li><span class="text-red-400 font-mono">401</span> — نیاز به ورود / توکن نامعتبر</li>
                            <li><span class="text-red-400 font-mono">404</span> — یافت نشد</li>
                            <li><span class="text-orange-400 font-mono">429</span> — محدودیت نرخ (مثلاً OTP)</li>
                            <li><span class="text-red-400 font-mono">500</span> — خطای سرور</li>
                        </ul>
                    </div>
                </div>
            </section>

            {{-- ========== AUTH ========== --}}
            <section id="auth" class="scroll-mt-6 space-y-4">
                <h2 class="text-yellow-primary font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-user-shield"></i>
                    احراز هویت
                </h2>

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/auth/register/send-otp',
                    'auth' => false,
                    'title' => 'ارسال OTP ثبت‌نام',
                    'description' => 'اطلاعات ثبت‌نام را دریافت کرده و کد تأیید ۴ رقمی را پیامک می‌کند. محدودیت نرخ: throttle:otp.',
                    'body' => [
                        ['name' => 'name', 'type' => 'string', 'required' => true, 'desc' => 'نام کاربر (حداقل ۲ کاراکتر)'],
                        ['name' => 'email', 'type' => 'string', 'required' => false, 'desc' => 'ایمیل (یکتا)'],
                        ['name' => 'mobile', 'type' => 'string', 'required' => true, 'desc' => 'موبایل با فرمت 09xxxxxxxxx'],
                        ['name' => 'password', 'type' => 'string', 'required' => true, 'desc' => 'رمز عبور (حداقل ۶ کاراکتر)'],
                        ['name' => 'password_confirmation', 'type' => 'string', 'required' => true, 'desc' => 'تکرار رمز عبور'],
                    ],
                    'requestSample' => '{
  "name": "علی رضایی",
  "mobile": "09123456789",
  "password": "secret1",
  "password_confirmation": "secret1"
}',
                    'responseSample' => '{
  "status": "ok",
  "message": "OTP sent via SMS",
  "data": {
    "mobile": "09123456789",
    "expires_in": 120
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/auth/register/verify-otp',
                    'auth' => false,
                    'title' => 'تأیید OTP و تکمیل ثبت‌نام',
                    'description' => 'کد OTP را بررسی کرده، کاربر را ایجاد می‌کند و توکن Sanctum برمی‌گرداند.',
                    'body' => [
                        ['name' => 'mobile', 'type' => 'string', 'required' => true, 'desc' => 'موبایل 09xxxxxxxxx'],
                        ['name' => 'otp', 'type' => 'string', 'required' => true, 'desc' => 'کد ۴ رقمی'],
                    ],
                    'requestSample' => '{
  "mobile": "09123456789",
  "otp": "1234"
}',
                    'responseSample' => '{
  "status": "ok",
  "message": null,
  "data": {
    "user": { "id": 1, "name": "علی رضایی", "mobile": "09123456789" },
    "token": "1|xxxxxxxxxxxxxxxx"
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/auth/login',
                    'auth' => false,
                    'title' => 'ورود با موبایل و رمز عبور',
                    'description' => 'ورود کاربر موجود و دریافت توکن.',
                    'body' => [
                        ['name' => 'mobile', 'type' => 'string', 'required' => true, 'desc' => 'موبایل 09xxxxxxxxx'],
                        ['name' => 'password', 'type' => 'string', 'required' => true, 'desc' => 'رمز عبور'],
                    ],
                    'requestSample' => '{
  "mobile": "09123456789",
  "password": "secret1"
}',
                    'responseSample' => '{
  "status": "ok",
  "message": null,
  "data": {
    "user": { "id": 1, "name": "علی رضایی", "mobile": "09123456789" },
    "token": "1|xxxxxxxxxxxxxxxx"
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/auth/logout',
                    'auth' => true,
                    'title' => 'خروج',
                    'description' => 'توکن فعلی کاربر را باطل می‌کند.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "You have been logged out successfully!",
  "data": ""
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/auth/reset-password/send-otp',
                    'auth' => false,
                    'title' => 'ارسال OTP بازیابی رمز',
                    'description' => 'برای موبایل ثبت‌شده کد OTP ارسال می‌کند. محدودیت نرخ: throttle:otp.',
                    'body' => [
                        ['name' => 'mobile', 'type' => 'string', 'required' => true, 'desc' => 'موبایل موجود در سیستم'],
                    ],
                    'requestSample' => '{
  "mobile": "09123456789"
}',
                    'responseSample' => '{
  "status": "ok",
  "message": "OTP sent via SMS",
  "data": {
    "mobile": "09123456789",
    "token": "otp_token_here",
    "expires_in": 120
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/auth/reset-password/verify-otp',
                    'auth' => false,
                    'title' => 'تأیید OTP و تغییر رمز',
                    'description' => 'با توکن OTP و کد تأیید، رمز جدید تنظیم می‌شود.',
                    'body' => [
                        ['name' => 'token', 'type' => 'string', 'required' => true, 'desc' => 'توکن دریافتی از send-otp'],
                        ['name' => 'otp', 'type' => 'string', 'required' => true, 'desc' => 'کد ۴ رقمی'],
                        ['name' => 'password', 'type' => 'string', 'required' => true, 'desc' => 'رمز جدید (حداقل ۶ کاراکتر)'],
                        ['name' => 'password_confirmation', 'type' => 'string', 'required' => true, 'desc' => 'تکرار رمز'],
                    ],
                    'requestSample' => '{
  "token": "otp_token_here",
  "otp": "1234",
  "password": "newpass1",
  "password_confirmation": "newpass1"
}',
                    'responseSample' => '{
  "status": "ok",
  "message": "password changed successfully",
  "data": ""
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/auth/send-otp',
                    'auth' => false,
                    'title' => 'ارسال OTP ورود سریع',
                    'description' => 'ورود/ثبت‌نام با OTP. اگر کاربر وجود نداشته باشد، پس از تأیید ساخته می‌شود. محدودیت نرخ: throttle:otp.',
                    'body' => [
                        ['name' => 'mobile', 'type' => 'string', 'required' => true, 'desc' => 'موبایل 09xxxxxxxxx'],
                    ],
                    'requestSample' => '{
  "mobile": "09123456789"
}',
                    'responseSample' => '{
  "status": "ok",
  "message": "OTP sent via SMS",
  "data": {
    "mobile": "09123456789",
    "token": "otp_token_here",
    "expires_in": 120
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/auth/verify-otp',
                    'auth' => false,
                    'title' => 'تأیید OTP ورود سریع',
                    'description' => 'کد OTP را تأیید کرده و توکن احراز هویت برمی‌گرداند.',
                    'body' => [
                        ['name' => 'token', 'type' => 'string', 'required' => true, 'desc' => 'توکن دریافتی از send-otp'],
                        ['name' => 'otp', 'type' => 'string', 'required' => true, 'desc' => 'کد ۴ رقمی'],
                    ],
                    'requestSample' => '{
  "token": "otp_token_here",
  "otp": "1234"
}',
                    'responseSample' => '{
  "status": "ok",
  "message": "OTP verified successfully",
  "data": {
    "token": "1|xxxxxxxxxxxxxxxx",
    "user": { "id": 1, "mobile": "09123456789" }
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/auth/check-cooldown',
                    'auth' => false,
                    'title' => 'بررسی کول‌داون OTP',
                    'description' => 'مشخص می‌کند آیا امکان ارسال مجدد OTP وجود دارد یا خیر.',
                    'body' => [
                        ['name' => 'mobile', 'type' => 'string', 'required' => true, 'desc' => 'موبایل 09xxxxxxxxx'],
                    ],
                    'requestSample' => '{
  "mobile": "09123456789"
}',
                    'responseSample' => '{
  "status": "ok",
  "message": "Can send OTP",
  "data": {
    "cooldown": 0,
    "can_send": true
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/auth/profile',
                    'auth' => true,
                    'title' => 'دریافت پروفایل',
                    'description' => 'اطلاعات کاربر لاگین‌شده را برمی‌گرداند.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "Profile retrieved successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "علی رضایی",
      "mobile": "09123456789",
      "email": "ali@example.com",
      "city_id": 1
    }
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'PUT',
                    'path' => '/auth/profile',
                    'auth' => true,
                    'title' => 'به‌روزرسانی پروفایل',
                    'description' => 'نام، ایمیل و شهر کاربر را به‌روز می‌کند.',
                    'body' => [
                        ['name' => 'name', 'type' => 'string', 'required' => true, 'desc' => 'نام'],
                        ['name' => 'email', 'type' => 'string', 'required' => false, 'desc' => 'ایمیل'],
                        ['name' => 'city_id', 'type' => 'integer', 'required' => false, 'desc' => 'شناسه شهر (وجود در جدول cities)'],
                    ],
                    'requestSample' => '{
  "name": "علی رضایی",
  "email": "ali@example.com",
  "city_id": 1
}',
                    'responseSample' => '{
  "status": "ok",
  "message": "Profile updated successfully",
  "data": {
    "user": { "id": 1, "name": "علی رضایی", "email": "ali@example.com", "city_id": 1 }
  }
}',
                ])
            </section>

            {{-- ========== CITIES ========== --}}
            <section id="cities" class="scroll-mt-6 space-y-4">
                <h2 class="text-yellow-primary font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-city"></i>
                    شهرها
                </h2>

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/cities',
                    'auth' => false,
                    'title' => 'لیست شهرها',
                    'description' => 'تمام شهرهای سیستم را برمی‌گرداند.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": [
    {
      "id": 1,
      "name": "تهران",
      "status": { "value": 1, "label": "فعال" },
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}',
                ])
            </section>

            {{-- ========== CATEGORIES ========== --}}
            <section id="categories" class="scroll-mt-6 space-y-4">
                <h2 class="text-yellow-primary font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-folder"></i>
                    دسته‌بندی‌ها
                </h2>

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/categories',
                    'auth' => false,
                    'title' => 'لیست دسته‌بندی‌ها',
                    'description' => 'دسته‌بندی‌های فعال را برمی‌گرداند. با پارامترهای کوئری قابل فیلتر است.',
                    'query' => [
                        ['name' => 'parents_only', 'type' => 'boolean', 'required' => false, 'desc' => 'فقط دسته‌های والد'],
                        ['name' => 'children_only', 'type' => 'boolean', 'required' => false, 'desc' => 'فقط دسته‌های فرزند'],
                        ['name' => 'hierarchical', 'type' => 'boolean', 'required' => false, 'desc' => 'ساختار سلسله‌مراتبی با children'],
                    ],
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": [
    { "id": 1, "name": "کالای دیجیتال", "parent_id": null }
  ]
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/categories/hierarchy',
                    'auth' => false,
                    'title' => 'سلسله‌مراتب دسته‌بندی‌ها',
                    'description' => 'دسته‌های والد فعال همراه با فرزندان فعال.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": [
    {
      "id": 1,
      "name": "کالای دیجیتال",
      "children": [{ "id": 2, "name": "موبایل" }]
    }
  ]
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/categories/{id}',
                    'auth' => false,
                    'title' => 'جزئیات یک دسته‌بندی',
                    'description' => 'اطلاعات یک دسته فعال به‌همراه والد و فرزندان.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "id": 2,
    "name": "موبایل",
    "parent": { "id": 1, "name": "کالای دیجیتال" },
    "children": []
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/categories/{id}/attributes',
                    'auth' => false,
                    'title' => 'ویژگی‌های دسته‌بندی',
                    'description' => 'ویژگی‌ها و مقادیر فعال مربوط به یک دسته.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "category": { "id": 2, "name": "موبایل" },
    "attributes": [
      {
        "id": 1,
        "name": "برند",
        "values": [{ "id": 10, "value": "سامسونگ" }]
      }
    ]
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/categories/{parentId}/children',
                    'auth' => false,
                    'title' => 'زیرمجموعه‌های یک دسته',
                    'description' => 'فرزندان فعال یک دسته والد را برمی‌گرداند.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "parent": { "id": 1, "name": "کالای دیجیتال" },
    "children": [{ "id": 2, "name": "موبایل" }]
  }
}',
                ])
            </section>

            {{-- ========== ADS PUBLIC ========== --}}
            <section id="ads-public" class="scroll-mt-6 space-y-4">
                <h2 class="text-yellow-primary font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-ad"></i>
                    آگهی‌ها (عمومی)
                </h2>

                <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-4 text-sm text-gray-400">
                    <p class="mb-2 text-gray-300 font-medium">پارامترهای فیلتر مشترک (لیست و جستجو)</p>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-1 list-disc list-inside">
                        <li><code class="text-yellow-primary font-mono" dir="ltr">search</code> — جستجوی متنی (حداقل ۲ کاراکتر)</li>
                        <li><code class="text-yellow-primary font-mono" dir="ltr">city_id</code> — شناسه شهر</li>
                        <li><code class="text-yellow-primary font-mono" dir="ltr">category_id</code> — شناسه دسته</li>
                        <li><code class="text-yellow-primary font-mono" dir="ltr">min_price</code> / <code class="text-yellow-primary font-mono" dir="ltr">max_price</code> — بازه قیمت</li>
                        <li><code class="text-yellow-primary font-mono" dir="ltr">ads_type</code> — sell, buy, rent, exchange</li>
                        <li><code class="text-yellow-primary font-mono" dir="ltr">ads_status</code> — as_good_as_new, excellent, good, fair, poor</li>
                        <li><code class="text-yellow-primary font-mono" dir="ltr">category_values[]</code> — آرایه شناسه مقادیر ویژگی</li>
                        <li><code class="text-yellow-primary font-mono" dir="ltr">is_special</code> / <code class="text-yellow-primary font-mono" dir="ltr">is_ladder</code> / <code class="text-yellow-primary font-mono" dir="ltr">willing_to_trade</code></li>
                        <li><code class="text-yellow-primary font-mono" dir="ltr">sort_by</code> — newest, oldest, price_asc, price_desc, views, most_relevant</li>
                    </ul>
                </div>

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/advertisements',
                    'auth' => false,
                    'title' => 'لیست آگهی‌ها',
                    'description' => 'آگهی‌های فعال، منتشرشده و منقضی‌نشده با صفحه‌بندی ۲۰ تایی. فیلترها و گزینه‌های مرتب‌سازی در پاسخ موجودند.',
                    'query' => [
                        ['name' => 'city_id', 'type' => 'integer', 'required' => false, 'desc' => 'فیلتر شهر'],
                        ['name' => 'category_id', 'type' => 'integer', 'required' => false, 'desc' => 'فیلتر دسته'],
                        ['name' => 'min_price / max_price', 'type' => 'integer', 'required' => false, 'desc' => 'بازه قیمت'],
                        ['name' => 'ads_type', 'type' => 'string', 'required' => false, 'desc' => 'نوع آگهی'],
                        ['name' => 'sort_by', 'type' => 'string', 'required' => false, 'desc' => 'مرتب‌سازی (پیش‌فرض newest)'],
                        ['name' => 'page', 'type' => 'integer', 'required' => false, 'desc' => 'شماره صفحه'],
                    ],
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "data": [ { "id": 1, "title": "آیفون ۱۳" } ],
    "pagination": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 20,
      "total": 100
    },
    "filters": { "sort_options": [], "ads_types": [], "ads_statuses": [] }
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/advertisements/search',
                    'auth' => false,
                    'title' => 'جستجوی آگهی‌ها',
                    'description' => 'جستجو با پارامتر q (حداقل ۲ کاراکتر). سایر فیلترهای لیست نیز قابل اعمال هستند.',
                    'query' => [
                        ['name' => 'q', 'type' => 'string', 'required' => true, 'desc' => 'عبارت جستجو (حداقل ۲ کاراکتر)'],
                    ],
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "search_term": "آیفون",
    "data": [],
    "pagination": {},
    "filters": {}
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/advertisements/filters',
                    'auth' => false,
                    'title' => 'گزینه‌های فیلتر',
                    'description' => 'لیست گزینه‌های مرتب‌سازی، نوع آگهی و وضعیت کالا.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "sort_options": [{ "value": "newest", "label": "جدیدترین" }],
    "ads_types": [{ "value": "sell", "label": "فروش" }],
    "ads_statuses": [{ "value": "good", "label": "خوب" }]
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/advertisements/category/{categoryId}',
                    'auth' => false,
                    'title' => 'آگهی‌های یک دسته',
                    'description' => 'آگهی‌های یک دسته‌بندی به‌همراه ویژگی‌های آن دسته برای فیلتر.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "category": { "id": 2, "name": "موبایل" },
    "data": [],
    "pagination": {},
    "attributes": {},
    "filters": {}
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/advertisements/{advertisement}',
                    'auth' => false,
                    'title' => 'جزئیات آگهی',
                    'description' => 'جزئیات کامل آگهی فعال و منتشرشده. تعداد بازدید افزایش می‌یابد و آگهی‌های مرتبط نیز برمی‌گردند.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "advertisement": {
      "id": 1,
      "title": "آیفون ۱۳",
      "description": "...",
      "price": 25000000,
      "images": { "primary": "...", "gallery": [] },
      "category": {},
      "city": {},
      "user": { "id": 1, "name": "علی" }
    },
    "related_advertisements": []
  }
}',
                ])
            </section>

            {{-- ========== ADS AUTH ========== --}}
            <section id="ads-auth" class="scroll-mt-6 space-y-4">
                <h2 class="text-yellow-primary font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    آگهی‌ها (نیازمند توکن)
                </h2>

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/advertisements',
                    'auth' => true,
                    'title' => 'ایجاد آگهی جدید',
                    'description' => 'ایجاد آگهی با وضعیت «در انتظار تأیید» (status=3). برای آپلود تصویر از multipart/form-data استفاده کنید.',
                    'body' => [
                        ['name' => 'title', 'type' => 'string', 'required' => true, 'desc' => 'عنوان (حداکثر ۲۵۵)'],
                        ['name' => 'description', 'type' => 'string', 'required' => true, 'desc' => 'توضیحات (حداکثر ۵۰۰۰)'],
                        ['name' => 'category_id', 'type' => 'integer', 'required' => true, 'desc' => 'شناسه دسته'],
                        ['name' => 'city_id', 'type' => 'integer', 'required' => true, 'desc' => 'شناسه شهر'],
                        ['name' => 'ads_type', 'type' => 'string', 'required' => true, 'desc' => 'sell | buy | rent | exchange'],
                        ['name' => 'ads_status', 'type' => 'string', 'required' => true, 'desc' => 'as_good_as_new | excellent | good | fair | poor'],
                        ['name' => 'price', 'type' => 'integer', 'required' => false, 'desc' => 'قیمت (تومان)'],
                        ['name' => 'contact', 'type' => 'string', 'required' => false, 'desc' => 'اطلاعات تماس'],
                        ['name' => 'tags', 'type' => 'string', 'required' => false, 'desc' => 'برچسب‌ها'],
                        ['name' => 'lat / lng', 'type' => 'number', 'required' => false, 'desc' => 'مختصات جغرافیایی'],
                        ['name' => 'willing_to_trade', 'type' => 'boolean', 'required' => false, 'desc' => 'آمادگی معاوضه'],
                        ['name' => 'image', 'type' => 'file', 'required' => false, 'desc' => 'تصویر اصلی (jpeg/png/jpg/gif، حداکثر ۵MB)'],
                        ['name' => 'images[]', 'type' => 'file[]', 'required' => false, 'desc' => 'گالری (حداکثر ۱۰ تصویر)'],
                        ['name' => 'category_values[]', 'type' => 'integer[]', 'required' => false, 'desc' => 'شناسه مقادیر ویژگی دسته'],
                    ],
                    'requestSample' => 'Content-Type: multipart/form-data

title=آیفون ۱۳
description=در حد نو
category_id=2
city_id=1
ads_type=sell
ads_status=as_good_as_new
price=25000000
image=@photo.jpg',
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "advertisement": { "id": 10, "title": "آیفون ۱۳", "status": 3 }
  }
}',
                ])
            </section>

            {{-- ========== PAYMENTS ========== --}}
            <section id="payments" class="scroll-mt-6 space-y-4">
                <h2 class="text-yellow-primary font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-credit-card"></i>
                    پرداخت و ارتقا
                </h2>

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/payments/promotion-prices',
                    'auth' => true,
                    'title' => 'لیست قیمت‌های ارتقا',
                    'description' => 'قیمت‌های فعال نردبان و ویژه را برمی‌گرداند.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "promotion_prices": [
      {
        "id": 1,
        "type": "ladder",
        "type_label": "نردبان",
        "duration_days": 7,
        "price": 50000,
        "formatted_price": "50,000 تومان"
      }
    ]
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/payments/advertisement-promotions',
                    'auth' => true,
                    'title' => 'گزینه‌های ارتقای یک آگهی',
                    'description' => 'گزینه‌های ارتقا برای آگهی متعلق به کاربر لاگین‌شده.',
                    'query' => [
                        ['name' => 'advertisement_id', 'type' => 'integer', 'required' => true, 'desc' => 'شناسه آگهی کاربر'],
                    ],
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": { }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'POST',
                    'path' => '/payments/promote-advertisement',
                    'auth' => true,
                    'title' => 'شروع پرداخت ارتقا',
                    'description' => 'پرداخت برای نردبان یا ویژه را آغاز کرده و لینک درگاه را برمی‌گرداند. آگهی باید متعلق به کاربر و قابل ارتقا باشد.',
                    'body' => [
                        ['name' => 'advertisement_id', 'type' => 'integer', 'required' => true, 'desc' => 'شناسه آگهی'],
                        ['name' => 'promotion_type', 'type' => 'string', 'required' => true, 'desc' => 'ladder یا special'],
                        ['name' => 'duration_days', 'type' => 'integer', 'required' => true, 'desc' => 'مدت به روز (حداقل ۱)'],
                    ],
                    'requestSample' => '{
  "advertisement_id": 10,
  "promotion_type": "ladder",
  "duration_days": 7
}',
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "payment": { "id": 1, "amount": 50000, "status": "pending" },
    "payment_url": "https://...",
    "authority": "A000000000000000000000000000000000000"
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/payments/status',
                    'auth' => true,
                    'title' => 'وضعیت پرداخت',
                    'description' => 'وضعیت یک پرداخت را با authority دریافت می‌کند.',
                    'query' => [
                        ['name' => 'authority', 'type' => 'string', 'required' => true, 'desc' => 'کد authority درگاه'],
                    ],
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "id": 1,
    "status": "paid",
    "status_label": "پرداخت شده",
    "amount": 50000,
    "payment_type": "ladder",
    "ref_id": "123456"
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/payments/history',
                    'auth' => true,
                    'title' => 'تاریخچه پرداخت‌های کاربر',
                    'description' => 'لیست پرداخت‌های کاربر لاگین‌شده با صفحه‌بندی ۱۰ تایی.',
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "data": [],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 0
    }
  }
}',
                ])

                @include('admin.api-docs.partials.endpoint', [
                    'method' => 'GET',
                    'path' => '/payment/callback',
                    'auth' => false,
                    'title' => 'کالبک درگاه پرداخت',
                    'description' => 'مسیر عمومی که درگاه پرداخت پس از تراکنش به آن هدایت می‌کند. معمولاً توسط کلاینت اپ فراخوانی مستقیم نمی‌شود.',
                    'query' => [
                        ['name' => '...', 'type' => 'mixed', 'required' => false, 'desc' => 'پارامترهای بازگشتی درگاه (مثل Authority و Status)'],
                    ],
                    'body' => [],
                    'requestSample' => null,
                    'responseSample' => '{
  "status": "ok",
  "message": "...",
  "data": {
    "payment": {},
    "message": "...",
    "gateway_transaction_id": "..."
  }
}',
                ])
            </section>

            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 text-center">
                <p class="text-gray-400 text-sm mb-3">برای تست تعاملی اندپوینت‌ها از Scramble استفاده کنید:</p>
                <a href="{{ $scrambleDocsUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 text-yellow-primary hover:underline font-medium" dir="ltr">
                    <i class="fas fa-book-open"></i>
                    {{ $scrambleDocsUrl }}
                </a>
            </div>
        </div>
    </div>
</main>
@endsection
