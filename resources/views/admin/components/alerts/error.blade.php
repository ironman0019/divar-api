@php $alertId = 'alert_' . uniqid(); @endphp

<div id="{{ $alertId }}" class="fixed top-4 left-1/2 -translate-x-1/2 z-[60] max-w-xl w-[90%] sm:w-auto">
    <div class="flex items-start gap-3 bg-red-500/15 border border-red-400/40 text-red-300 px-4 py-3 rounded-xl shadow-2xl ring-1 ring-red-400/10 transition-all duration-500 ease-out opacity-0 translate-y-[-8px] alert-enter">
        <div class="shrink-0 mt-0.5">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-500/25 text-red-300">
                <i class="fas fa-exclamation-circle"></i>
            </span>
        </div>
        <div class="min-w-0">
            <p class="font-medium text-sm">{{ $title ?? 'خطا' }}</p>
            <p class="text-xs sm:text-sm text-red-200/90 mt-0.5">{{ $message ?? ($slot ?? '') }}</p>
        </div>
        <button type="button" class="ml-auto text-red-200/70 hover:text-red-100 transition-colors" onclick="(function(){const el=document.getElementById('{{ $alertId }}'); if(el){ el.classList.add('alert-exit'); setTimeout(()=> el.remove(), 250); }})();">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<style>
.alert-enter { opacity: 0; transform: translate(-50%, -8px); }
.alert-enter.alert-show { opacity: 1; transform: translate(-50%, 0); }
.alert-exit { opacity: 0 !important; transform: translate(-50%, -6px) !important; transition: opacity .25s ease, transform .25s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const el = document.getElementById('{{ $alertId }}');
    if(!el) return;
    const box = el.firstElementChild;
    requestAnimationFrame(()=> box.classList.add('alert-show'));
    setTimeout(()=> {
        if (!el.isConnected) return;
        box.classList.add('alert-exit');
        setTimeout(()=> el.remove(), 250);
    }, {{ $timeout ?? 4000 }});
});
</script>


