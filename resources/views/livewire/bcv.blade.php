<div>
    <x-filament-actions::modals />

    <x-filament-actions::group :actions="[

        ($this->actualizarAction)(['cita' => $items->id])

    ]" icon="heroicon-m-ellipsis-vertical" color="colorOne" />

</div>
