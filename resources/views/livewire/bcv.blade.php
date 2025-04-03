<div>
    <div>
        <div class="flex content-center">
            <x-filament-actions::modals />
            <div class="flex items-center p-2">
                <div>
                        <img id="theme-mode"
                            x-data="{ darkMode: document.documentElement.classList.contains('dark') }"
                            x-init="$watch('darkMode', value => $el.src = value ? '{{ asset('images/BCV.png') }}' : '{{ asset('images/BCV2.png') }}')"
                            :src="darkMode ? '{{ asset('images/BCV.png') }}' : '{{ asset('images/BCV2.png') }}'"
                            class="w-16 h-auto p-0.5 rounded-full"
                            alt="Logo" style="margin-right: 20px">
                            {{-- negro --}}
                        {{-- <img class="w-16 h-auto rounded-full block dark:hidden" src="{{ asset('images/BCV2.png') }}" alt="" style="margin-right: 20px"> --}}
                            {{-- blanco --}}
                        {{-- <img class="w-16 h-auto rounded-full hidden dark:block" src="{{ asset('images/BCV.png') }}" alt="" style="margin-right: 20px;"> --}}
                    </div>
                    {{-- <span class="line-clamp-1" style="padding-left: 5px">Actualizar Tasa BCV</span> --}}
                <div class="">
                    {{-- <x-filament-actions::group :actions="[$this->actualizarAction]" icon="heroicon-m-ellipsis-vertical" color="#0489dc" /> --}}
                        {{ $this->actualizarAction }}
                </div>
            </div>
        </div>
    </div>
</div>
