@php
    $inputId = $id ?? $name;
    $rawValue = $value ?? '';
    $gregorianValue = $rawValue instanceof \DateTimeInterface
        ? $rawValue->format('Y-m-d')
        : (\App\Support\JalaliDate::toGregorian((string) $rawValue) ?? '');
    $jalaliValue = $gregorianValue ? \App\Support\JalaliDate::toJalali($gregorianValue) : '';
@endphp

<input type="text"
       data-jdp
       data-jdp-only-date
       data-jdp-target-value-input="#{{ $inputId }}"
       data-jdp-target-value-type="gregorian"
       value="{{ $jalaliValue }}"
       placeholder="{{ $placeholder ?? 'انتخاب تاریخ' }}"
       autocomplete="off"
       class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none cursor-pointer">
<input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $gregorianValue }}">
