<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Configuracion;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Http\Controllers\VentaController;
use App\Filament\Resources\PedidoResource;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Actions\Action;

class EditPedido extends EditRecord
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            Actions\Action::make("Ejecutar venta")
            ->form([
                Section::make('Totales del Pedido')
                    ->schema([

                        TextInput::make('total_usd')
                            ->label('Total USD($)')
                            ->prefix('US$')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->default(function () {
                                return $this->record->monto_usd ?? 0.00;
                            }),

                        TextInput::make('total_bsd')
                            ->label('Total VES(Bs.)')
                            ->prefix('Bs.')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->default(function () {
                                return $this->record->monto_bsd ?? 0.00;
                            })
                    ])->columns(2),

                Section::make()
                    ->schema([
                        ToggleButtons::make('metodo_pago')
                            ->label('Método de Pago')
                            ->options([
                                'usd' => 'USD($)',
                                'bsd' => 'VES(Bs.)',
                                'multiple' => 'Multiple'
                            ])
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateMontos($get, $set);
                            })
                            ->icons([
                                'usd' => 'heroicon-o-currency-dollar',
                                'bsd' => 'heroicon-m-banknotes',
                                'multiple' => 'heroicon-o-currency-dollar',
                            ])
                            ->live()
                            ->inline(),

                            //Input para pago en usd
                            //-------------------------------------------------------------
                                Grid::make(3)
                                ->schema([
                                    TextInput::make('cash')
                                        ->label('Efectivo($)')
                                        ->prefix('$')
                                        ->default(fn(Get $get) => $get('total_usd'))
                                        ->afterStateUpdated(function (Get $get, Set $set) {
                                            $total = $get('total_usd') - $get('cash');
                                            $set('zelle', $total);
                                        })
                                        ->live(true)
                                        ->numeric()
                                        ->hintAction(
                                            Action::make('reset')
                                                ->icon('heroicon-o-arrow-path')
                                                ->action(function (Set $set) {
                                                    $set('zelle', null);
                                                    $set('cash', null);
                                                })
                                            ),
                                    TextInput::make('zelle')
                                        ->label('Transferencia(Zelle)')
                                        ->prefix('Zelle')
                                        ->placeholder('0.00')
                                        ->live()
                                        ->disabled()
                                        ->dehydrated()
                                        ->numeric(),
                                    TextInput::make('ref_zelle')
                                        ->label('Referencia(Zelle)')
                                        ->prefix('#')
                                        ->hidden(fn(Get $get) => $get('zelle') == null),
                                ])->hidden(fn(Get $get) => $get('metodo_pago') != 'usd'),
                    //-----------------------------------------------------------------

                        ToggleButtons::make('tipo_usd')
                            ->label('Tipo US$')
                            ->hidden(fn(Get $get) => $get('metodo_pago') != 'multiple')
                            ->options([
                                'cash'  => 'Efectivo USD($)',
                                'zelle' => 'Zelle USD($)',
                            ])
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateMontos($get, $set);
                            })
                            ->icons([
                                'cash' => 'heroicon-o-currency-dollar',
                                'zelle' => 'heroicon-o-currency-dollar',
                            ])
                            ->live()
                            ->inline()
                             ->default('cash'),
                        ToggleButtons::make('tipo_bsd')
                            ->label('Tipo VES(Bs.)')
                            ->inline()
                            ->live()
                            ->hidden(fn(Get $get) => $get('metodo_pago') != 'bsd' && $get('metodo_pago') != 'multiple')
                            ->options([
                                'punto'         => 'Punto de Venta',
                                'pago-movil'    => 'Pago Movil',
                                'transferencia' => 'Transferencia',
                            ])
                            ->icons([
                                'punto'         => 'heroicon-m-banknotes',
                                'pago-movil'    => 'heroicon-m-banknotes',
                                'transferencia' => 'heroicon-m-banknotes',
                            ])
                            ->default('pago-movil'),

                            //Input para pago en bsd
                            //-------------------------------------------------------------
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('ref_bsd')
                                        ->label('Referencia')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'La referencia es requerida',
                                        ])
                                        ->prefix('#'),
                                ])->hidden(fn(Get $get) => $get('metodo_pago') != 'bsd'),
                            //-----------------------------------------------------------------

                            Grid::make()
                                ->schema([

                                    TextInput::make('pago_usd')
                                        ->label('Monto($)')
                                        ->numeric()
                                        ->afterStateUpdated(function (Get $get, Set $set) {
                                            $total = $get('total_usd') - $get('pago_usd');
                                            $set('pago_bsd', round($total * Configuracion::first()->tasa_bcv, 2));
                                        })
                                        ->hintAction(
                                            Action::make('reset')
                                                ->icon('heroicon-o-arrow-path')
                                                ->action(function (Set $set) {
                                                    $set('pago_usd', null);
                                                    $set('pago_bsd', null);
                                                })
                                        )
                                        ->live(true)
                                        ->placeholder('0.00')
                                        ->prefix('US$'),

                                    TextInput::make('pago_bsd')
                                        ->label('Monto(Bs.)')
                                        ->numeric()
                                        // Read-only, because it's calculated
                                        // ->readOnly()
                                        ->disabled()
                                        ->dehydrated()
                                        ->placeholder('0.00')
                                        ->prefix('Bs.'),

                                    TextInput::make('multiple_ref_bsd')
                                        ->label('Referencia Bs.')
                                        ->required()
                                        ->hidden(fn(Get $get) => $get('metodo_pago') != 'multiple')
                                        ->validationMessages([
                                            'required' => 'La referencia es requerida',
                                        ])
                                        ->prefix('#'),

                                    TextInput::make('multiple_ref_usd')
                                        ->label('Referencia US$')
                                        ->required()
                                        ->hidden(fn(Get $get) => $get('tipo_usd') != 'zelle')
                                        ->validationMessages([
                                            'required' => 'La referencia es requerida',
                                        ])
                                        ->prefix('#'),

                                ])
                                ->hidden(fn(Get $get) => $get('metodo_pago') != 'multiple')
                                ->columns(2),

                    ])->columnSpan(1)->columns(1),
            ])
            ->action(function ($data) {

                if($data['metodo_pago'] == 'usd'){

                    $registro_venta_usd = VentaController::registrar_venta_usd($this->record, $data, $this->record->detalles->toArray());
                    if ($registro_venta_usd['success'] == true) {
                        Notification::make()
                            ->success()
                            ->title('Venta registrada')
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('ERROR')
                            ->body($registro_venta_usd['message'])
                            ->send();
                    }
                }

                if($data['metodo_pago'] == 'bsd'){

                    $registro_venta_bsd = VentaController::registrar_venta_bsd($this->record, $data, $this->record->detalles->toArray());
                    if ($registro_venta_bsd['success'] == true) {
                        Notification::make()
                            ->success()
                            ->title('Venta registrada')
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('ERROR')
                            ->body($registro_venta_bsd['message'])
                            ->send();
                    }
                }

                if($data['metodo_pago'] == 'multiple'){

                    $registro_venta_multiple = VentaController::registrar_venta_multiple($this->record, $data, $this->record->detalles->toArray());
                    if ($registro_venta_multiple['success'] == true) {
                        Notification::make()
                            ->success()
                            ->title('Venta registrada')
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('ERROR')
                            ->body($registro_venta_multiple['message'])
                            ->send();
                    }
                }




            })
            ->color('success')
            // ->modalContent(fn(StaticAction $action) => $action->label('Close'))
            ->modalSubmitAction(fn(StaticAction $action, Get $get) => $action->label('Procesar venta'))
            ->icon('heroicon-o-check-circle'),
        ];
    }

    public static function updateMontos(Get $get, Set $set): void
    {
        if ($get('metodo_pago') == 'usd') {
            $set('monto_usd', $get('total_usd'));
        }
        if ($get('metodo_pago') == 'bsd') {
            $set('monto_bsd', $get('total_bsd'));
        }
    }
}
