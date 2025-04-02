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
            ->icon('heroicon-c-user-plus')
            ->modalHeading(false)
            ->color('success')
            ->form([
                Section::make('Asignar Tecnico')
                    ->description('Debe llenar los campos de forma correcta. Campos Requeridos(*)')
                    ->icon('heroicon-c-user-plus')
                    ->schema([
                        //Imputs
                        TextInput::make('tasa')->label('Tasa')->required()
                        
                    ])
            ])
            ->action(function (array $arguments, array $data) {

                dd(1);
            });
    }

    
    public function render()
    {
        return view('livewire.bcv');
    }
}