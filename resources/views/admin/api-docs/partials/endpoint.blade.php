@php
    $methodColors = [
        'GET' => 'bg-green-500/20 text-green-400 border-green-500/40',
        'POST' => 'bg-blue-500/20 text-blue-400 border-blue-500/40',
        'PUT' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/40',
        'PATCH' => 'bg-orange-500/20 text-orange-400 border-orange-500/40',
        'DELETE' => 'bg-red-500/20 text-red-400 border-red-500/40',
    ];
    $methodClass = $methodColors[$method] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/40';
    $query = $query ?? [];
    $body = $body ?? [];
@endphp

<article class="bg-dark-secondary rounded-xl border border-yellow-primary/20 overflow-hidden">
    <div class="p-4 lg:p-5 border-b border-gray-700/60">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="px-2.5 py-1 rounded-md text-xs font-bold border {{ $methodClass }}" dir="ltr">{{ $method }}</span>
            <code class="text-yellow-primary font-mono text-sm dir-ltr" dir="ltr">/api/V1{{ $path }}</code>
            @if($auth)
                <span class="bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-full text-xs">نیاز به توکن</span>
            @else
                <span class="bg-gray-600/40 text-gray-300 border border-gray-500/30 px-2 py-0.5 rounded-full text-xs">عمومی</span>
            @endif
        </div>
        <h3 class="text-gray-100 font-semibold mb-1">{{ $title }}</h3>
        <p class="text-gray-400 text-sm leading-relaxed">{{ $description }}</p>
    </div>

    @if(count($query) > 0)
        <div class="px-4 lg:px-5 py-4 border-b border-gray-700/60">
            <h4 class="text-gray-300 font-medium text-sm mb-3">پارامترهای Query</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-gray-500 text-right">
                            <th class="pb-2 font-medium">نام</th>
                            <th class="pb-2 font-medium">نوع</th>
                            <th class="pb-2 font-medium">الزامی</th>
                            <th class="pb-2 font-medium">توضیح</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-300">
                        @foreach($query as $param)
                            <tr class="border-t border-gray-800">
                                <td class="py-2 font-mono text-yellow-primary text-xs" dir="ltr">{{ $param['name'] }}</td>
                                <td class="py-2 text-xs">{{ $param['type'] }}</td>
                                <td class="py-2 text-xs">{{ $param['required'] ? 'بله' : 'خیر' }}</td>
                                <td class="py-2 text-xs text-gray-400">{{ $param['desc'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(count($body) > 0)
        <div class="px-4 lg:px-5 py-4 border-b border-gray-700/60">
            <h4 class="text-gray-300 font-medium text-sm mb-3">بدنه درخواست (Body)</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-gray-500 text-right">
                            <th class="pb-2 font-medium">نام</th>
                            <th class="pb-2 font-medium">نوع</th>
                            <th class="pb-2 font-medium">الزامی</th>
                            <th class="pb-2 font-medium">توضیح</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-300">
                        @foreach($body as $param)
                            <tr class="border-t border-gray-800">
                                <td class="py-2 font-mono text-yellow-primary text-xs" dir="ltr">{{ $param['name'] }}</td>
                                <td class="py-2 text-xs">{{ $param['type'] }}</td>
                                <td class="py-2 text-xs">{{ $param['required'] ? 'بله' : 'خیر' }}</td>
                                <td class="py-2 text-xs text-gray-400">{{ $param['desc'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
        @if(!empty($requestSample))
            <div class="px-4 lg:px-5 py-4 {{ !empty($responseSample) ? 'lg:border-l border-gray-700/60' : '' }} border-b lg:border-b-0 border-gray-700/60">
                <h4 class="text-gray-300 font-medium text-sm mb-2">نمونه درخواست</h4>
                <pre class="bg-dark-tertiary rounded-lg px-3 py-3 text-xs text-gray-300 overflow-x-auto font-mono leading-relaxed dir-ltr text-left" dir="ltr">{{ $requestSample }}</pre>
            </div>
        @endif
        @if(!empty($responseSample))
            <div class="px-4 lg:px-5 py-4 {{ empty($requestSample) ? 'lg:col-span-2' : '' }}">
                <h4 class="text-gray-300 font-medium text-sm mb-2">نمونه پاسخ</h4>
                <pre class="bg-dark-tertiary rounded-lg px-3 py-3 text-xs text-gray-300 overflow-x-auto font-mono leading-relaxed dir-ltr text-left" dir="ltr">{{ $responseSample }}</pre>
            </div>
        @endif
    </div>
</article>
