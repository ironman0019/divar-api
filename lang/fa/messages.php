<?php

return [
    // Advertisement messages
    'advertisements' => [
        'retrieved' => 'آگهی‌ها با موفقیت دریافت شد',
        'created' => 'آگهی با موفقیت ایجاد شد',
        'not_found' => 'آگهی مورد نظر یافت نشد',
        'details_retrieved' => 'جزئیات آگهی با موفقیت دریافت شد',
        'view_incremented' => 'تعداد بازدید آگهی افزایش یافت',
        'related_retrieved' => 'آگهی‌های مرتبط دریافت شد',
        'category_ads_retrieved' => 'آگهی‌های دسته‌بندی دریافت شد',
        'no_ads_found' => 'آگهی‌ای یافت نشد',
        'search_results' => 'نتایج جستجو دریافت شد',
    ],

    // Category messages
    'categories' => [
        'retrieved' => 'دسته‌بندی‌ها با موفقیت دریافت شد',
        'not_found' => 'دسته‌بندی مورد نظر یافت نشد',
        'attributes_retrieved' => 'ویژگی‌های دسته‌بندی دریافت شد',
        'hierarchy_retrieved' => 'ساختار سلسله‌مراتبی دسته‌بندی‌ها دریافت شد',
    ],

    // City messages
    'cities' => [
        'retrieved' => 'شهرها با موفقیت دریافت شد',
        'not_found' => 'شهر مورد نظر یافت نشد',
    ],

    // Filter and sort labels
    'filters' => [
        'sort_by' => [
            'newest' => 'جدیدترین',
            'oldest' => 'قدیمی‌ترین',
            'price_asc' => 'ارزان‌ترین',
            'price_desc' => 'گران‌ترین',
            'views' => 'پربازدیدترین',
            'most_relevant' => 'مرتبط‌ترین',
        ],
        'ads_type' => [
            'sell' => 'فروش',
            'buy' => 'خرید',
            'rent' => 'اجاره',
            'exchange' => 'معاوضه',
        ],
        'ads_status' => [
            'as_good_as_new' => 'در حد نو',
            'excellent' => 'عالی',
            'good' => 'خوب',
            'fair' => 'متوسط',
            'poor' => 'ضعیف',
        ],
        'price_range' => [
            'min_price' => 'حداقل قیمت',
            'max_price' => 'حداکثر قیمت',
        ],
    ],

    // Error messages
    'errors' => [
        'unauthorized' => 'شما باید وارد سیستم شوید',
        'invalid_city' => 'شهر انتخاب شده نامعتبر است',
        'invalid_category' => 'دسته‌بندی انتخاب شده نامعتبر است',
        'invalid_price_range' => 'محدوده قیمت نامعتبر است',
        'invalid_sort_option' => 'گزینه مرتب‌سازی نامعتبر است',
        'invalid_filter' => 'فیلتر اعمال شده نامعتبر است',
        'search_too_short' => 'عبارت جستجو باید حداقل 2 کاراکتر باشد',
        'server_error' => 'خطای سرور رخ داده است',
        'not_found' => 'مورد مورد نظر یافت نشد',
    ],

    // Success messages
    'success' => [
        'operation_completed' => 'عملیات با موفقیت انجام شد',
        'data_retrieved' => 'اطلاعات با موفقیت دریافت شد',
        'filter_applied' => 'فیلتر اعمال شد',
        'search_completed' => 'جستجو تکمیل شد',
    ],

    // Pagination messages
    'pagination' => [
        'page_info' => 'صفحه :current از :last',
        'total_items' => 'تعداد کل آیتم‌ها: :total',
        'items_per_page' => 'آیتم در هر صفحه: :per_page',
        'showing' => 'نمایش :from تا :to از :total آیتم',
    ],

    // Advertisement status labels
    'advertisement_status' => [
        0 => 'غیرفعال',
        1 => 'فعال',
        2 => 'تایید شده',
        3 => 'در انتظار تایید',
        4 => 'منقضی شده',
    ],

    // Category status labels
    'category_status' => [
        0 => 'غیرفعال',
        1 => 'فعال',
    ],

    // Attribute type labels
    'attribute_types' => [
        0 => 'متن',
        1 => 'عدد',
        2 => 'انتخاب',
        3 => 'چند انتخابی',
    ],

    // Payment messages
    'payments' => [
        'prices_retrieved' => 'قیمت‌های تبلیغات با موفقیت دریافت شد',
        'promotions_retrieved' => 'گزینه‌های تبلیغات آگهی دریافت شد',
        'initiated' => 'پرداخت با موفقیت آغاز شد',
        'verified_success' => 'پرداخت با موفقیت تایید شد',
        'verified_failed' => 'پرداخت ناموفق بود',
        'status_retrieved' => 'وضعیت پرداخت دریافت شد',
        'history_retrieved' => 'تاریخچه پرداخت‌ها دریافت شد',
        'not_found' => 'پرداخت مورد نظر یافت نشد',
        'advertisement_not_active' => 'آگهی باید فعال باشد تا بتوان آن را تبلیغ کرد',
        'price_not_found' => 'قیمت تبلیغات یافت نشد',
        'invalid_promotion_type' => 'نوع تبلیغات نامعتبر است',
        'invalid_duration' => 'مدت زمان نامعتبر است',
        'payment_already_processed' => 'پرداخت قبلاً پردازش شده است',
        'payment_failed' => 'پرداخت ناموفق بود',
        'gateway_error' => 'خطا در درگاه پرداخت',
    ],

    // Promotion type labels
    'promotion_types' => [
        'ladder' => 'نردبان',
        'special' => 'ویژه',
    ],

    // Payment status labels
    'payment_status' => [
        'pending' => 'در انتظار',
        'paid' => 'پرداخت شده',
        'failed' => 'ناموفق',
    ],
];
