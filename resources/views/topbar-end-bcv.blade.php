{{-- @php
use App\Models\TasaBcv;
$tasa = TasaBcv::first();

@endphp --}}
<div class="flex items-center">
    <img id="theme-mode" x-data="{ darkMode: document.documentElement.classList.contains('dark') }" x-init="$watch('darkMode', value => $el.src = value ? '{{ asset('images/BCV.png') }}' : '{{ asset('images/BCV2.png') }}')" :src="darkMode ? '{{ asset('images/BCV.png') }}' : '{{ asset('images/BCV2.png') }}'" class="w-10 h-auto p-0.5 rounded-full" alt="Logo">
    <div class="ml-2 text-left">
        <div class="ml-2 text-xs text-black leading-7 font-semibold">
            0.00Bs.
        </div>
    </div>
</div>
