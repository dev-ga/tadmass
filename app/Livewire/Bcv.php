<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Actions\Action;
use App\Models\Configuracion;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Support\Enums\MaxWidth;

class Bcv extends Component implements HasForms, HasActions
{

    use InteractsWithActions;
    use InteractsWithForms;

    public $tasa;

    public function mount(Configuracion $config)
    {
        $this->tasa = $config->tasa_bcv;
    }

    public function ActualizarAction(): Action
    {
        return Action::make('actualizar')
            ->label('Actualizar BCV')
            ->modalHeading(false)
            ->modalWidth(MaxWidth::Large)
            ->color('azul')
            ->form([
                Section::make('Banco Central de Venezuela')
                ->label('Actualizar BCV')
                ->description('Formulario de actualizacion de la tasa. Campos Requeridos(*)')
                    ->icon('heroicon-s-currency-dollar')
                    ->schema([
                        //Imputs
                        TextInput::make('tasa')
                        ->label('VES(Bs.)')
                        ->hint('Separador decimal(.)')
                        ->required()

                    ])
            ])
            ->action(function (array $arguments, array $data) {

                Configuracion::select('id, tasa_bcv, fecha_update_tasa')->where('id', 1)->update([
                    'tasa_bcv' => $data['tasa'],
                    'fecha_update_tasa' => now()->format('d-m-Y')
                ]);

                $this->dispatch('bcv-update');
            })
            ->modalSubmitActionLabel('Actualiazar');
    }


    public function render()
    {
        return view('livewire.bcv');
    }
}