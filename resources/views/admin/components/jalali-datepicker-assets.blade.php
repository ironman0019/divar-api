<link rel="stylesheet" href="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css">
<style>
    jdp-container {
        --jdp-color-0: #1a1a2e;
        --jdp-color-1: #16213e;
        --jdp-color-2: #2a2a4a;
        --jdp-color-3: #4b5563;
        --jdp-color-4: #9ca3af;
        --jdp-color-5: #e5e7eb;
        --jdp-primary: #ffd700;
        --jdp-red: #f87171;
        font-family: 'Vazirmatn', sans-serif;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 215, 0, 0.25);
        color-scheme: dark;
    }

    jdp-container .jdp-day.selected,
    jdp-container .jdp-btn-today,
    jdp-container .jdp-btn-empty,
    jdp-container .jdp-btn-close {
        color: #0f0f23 !important;
        font-weight: 600;
    }

    jdp-container .jdp-year select,
    jdp-container .jdp-month select {
        background: #16213e;
        color: #e5e7eb;
    }
</style>
<script src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof jalaliDatepicker === 'undefined') {
            return;
        }

        jalaliDatepicker.startWatch({
            date: true,
            time: false,
            hasSecond: false,
            hideAfterChange: true,
            useDropdownYears: true,
            showTodayBtn: true,
            showEmptyBtn: true,
            persianDigits: false,
            targetValueInput: 'attr',
            targetValueType: 'attr'
        });

        document.querySelectorAll('input[data-jdp]').forEach(function (input) {
            input.addEventListener('jdp:change', function () {
                var selector = input.getAttribute('data-jdp-target-value-input');
                if (!selector) {
                    return;
                }

                var target = document.querySelector(selector);
                if (target && !input.value) {
                    target.value = '';
                }
            });
        });
    });
</script>
