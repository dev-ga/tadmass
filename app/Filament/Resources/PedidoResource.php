<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Gasto;
use App\Models\Pedido;
use App\Models\Cliente;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Producto;
use App\Models\Vendedor;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Configuracion;
use Filament\Resources\Resource;
use Awcodes\TableRepeater\Header;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use App\Filament\Exports\PedidoExporter;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use App\Http\Controllers\VentaController;

use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\PedidoResource\Pages;
use Awcodes\TableRepeater\Components\TableRepeater;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PedidoResource\RelationManagers;

use Filament\Actions\Exports\Enums\ExportFormat;


class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-m-clipboard-document-check';

    public static function form(Form $form): Form
    {
        $products = Producto::get();
        return $form
            ->schema([

                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('codigo')
                                    ->label('Código')
                                    ->prefixIcon('heroicon-c-clipboard-document-list')
                                    ->required()
                                    ->default('TADMASS-PED-' . rand(111111, 999999))
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique()
                                    ->dehydrated()
                                    ->maxLength(255)
                                    ->hiddenOn(Pages\EditPedido::class),

                            ])->columns(4),
                        Select::make('cliente_id')
                            ->prefixIcon('heroicon-c-list-bullet')
                            ->label('Cliente')
                            ->options(Cliente::all()->pluck('nombre', 'id'))
                            ->required()
                            ->validationMessages([
                                'required' => 'Debe seleccionar un cliente',
                            ])
                            ->searchable(),

                        Select::make('vendedor_id')
                            ->prefixIcon('heroicon-c-list-bullet')
                            ->label('Vendedor')
                            ->options(Vendedor::all()->pluck('nombre', 'id'))
                            ->default(function (Get $get) {
                                return Auth::user()->id;
                            })
                            ->disabled()
                            ->dehydrated()
                            ->searchable(),

                        Forms\Components\TextInput::make('registrado_por')
                            ->prefixIcon('heroicon-s-shield-check')
                            ->default(Auth::user()->name)
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(255),

                    ])->columnSpan('full')->columns(3),

                Section::make()
                    ->schema([
                        TableRepeater::make('productos')
                            ->headers([
                                Header::make('Producto'),
                                Header::make('Precio Unitario($)')->width('155px'),
                                Header::make('Cantidad')->width('80px'),
                                Header::make('Precio de venta($)')->width('155px'),
                            ])
                            // Repeatable field is live so that it will trigger the state update on each change
                            ->live()
                            // After adding a new row, we need to update the totals
                            ->afterStateUpdated(function (Get $get, Set $set,) {
                                self::updateTotals($get, $set);
                            })
                            // After deleting a row, we need to update the totals
                            ->deleteAction(
                                fn(Action $action) => $action->after(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                            )
                            ->schema([
                                Forms\Components\Select::make('producto_id')
                                    ->relationship('productos', 'nombre')
                                    ->options(
                                        $products->mapWithKeys(function (Producto $product) {
                                            return [
                                                $product->id => '
                                                <div>
                                                    <div style="font-weight: bold;">' . e($product->nombre) . '</div>
                                                    <div style="font-size: 12px; color: #6b7280; text-transform: capitalize;">Tipo de venta: ' . e($product->tipo_venta) . '</div>
                                                </div>'
                                            ];
                                        })
                                    )
                                    ->native(false) // usa el select estilizado de Filament
                                    ->allowHtml()
                                    // Disable options that are already selected in other rows
                                    ->disableOptionWhen(function ($value, $state, Get $get) {
                                        return collect($get('../*.producto_id'))
                                            ->reject(fn($id) => $id == $state)
                                            ->filter()
                                            ->contains($value);
                                    })
                                    ->afterStateUpdated(function (Get $get, Set $set,) {
                                        //actualizamos el precio de venta
                                        $set('precio_venta', Producto::find($get('producto_id'))->precio_venta);
                                        // $set('subtotal_venta', Producto::find($get('producto_id'))->precio_venta * $get('cantidad'));
                                    })
                                    ->live()
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Debe seleccionar un producto',
                                    ]),

                                Forms\Components\TextInput::make('precio_venta')
                                    ->label('Precio de venta($)')
                                    ->prefix('US$')
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->integer()
                                    ->default(0)
                                    ->afterStateUpdated(function (Get $get, Set $set,) {
                                        //actualizamos el precio de venta
                                        $set('subtotal_venta', Producto::find($get('producto_id'))->precio_venta * $get('cantidad'));
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('subtotal_venta')
                                    ->label('Precio de venta($)')
                                    ->prefix('US$')
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),

                            ])
                            // Disable reordering
                            ->reorderable(false)
                            ->relationship('detalles')
                            ->stackAt(MaxWidth::Small)
                            ->addable(function ($record) {
                                if (isset($record->status) && $record->status == 'procesado') {
                                    return false;
                                }
                                return true;
                            })
                            ->deletable(function ($record) {
                                if (isset($record->status) && $record->status == 'procesado') {
                                    return false;
                                }
                                return true;
                            })
                            ->columns(3)
                    ])
                    ->columnSpan(2)
                    ->columns(1),

                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('monto_usd')
                            ->prefix('US$')
                            ->label('Monto USD($)')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('monto_bsd')
                            ->prefix('Bs.')
                            ->label('Monto VES(Bs.)')
                            ->required()
                            ->numeric()
                            ->default(0.00),

                    ])->columnSpan(1)->columns(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->badge()
                    ->color('colorAzul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vendedor.nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->laBel('Registrado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->color(function (mixed $state): string {
                        return match ($state) {
                            'por-procesar' => 'warning',
                            'procesado' => 'success',
                            default => 'info',
                        };
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('monto_usd')
                    ->label('Monto USD($)')
                    ->numeric()
                    ->money('USD')
                    ->badge()
                    ->color('verdeOscuro')
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_bsd')
                    ->label('Monto VES(Bs.)')
                    ->numeric()
                    ->badge()
                    ->color('verdeOscuro')
                    ->icon('heroicon-o-credit-card')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Ver Detalle')
                        ->modalHeading('Detalle del Pedido')
                        ->color('colorAzul')
                        ->modalWidth(MaxWidth::SixExtraLarge),

                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->label('Editar')
                        ->hidden(fn($record) => $record->status == 'procesado'),

                    Tables\Actions\Action::make("Ejecutar venta")
                        ->form([
                            //Totales
                            //--------------------------------------------------
                            Section::make('Totales del Pedido')
                                ->schema([

                                    TextInput::make('total_usd')
                                        ->label('Total USD($)')
                                        ->prefix('US$')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated()
                                        ->default(function ($record) {
                                            return $record->monto_usd ?? 0.00;
                                        }),

                                    TextInput::make('total_bsd')
                                        ->label('Total VES(Bs.)')
                                        ->prefix('Bs.')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated()
                                        ->default(function ($record) {
                                            return $record->monto_bsd ?? 0.00;
                                        })
                                ])->columns(2),
                            //--------------------------------------------------

                            //Metodo de Pago
                            //--------------------------------------------------
                            Section::make()
                                ->schema([

                                    //Toggle para metodo de pago
                                    //-------------------------------------------------------------
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

                                    //Input para formulario de pago en US$
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

                                    //Toggle para tipo de pago en US$ y Bs.
                                    //-------------------------------------------------------------
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
                                    Grid::make(3)
                                        ->hidden(fn(Get $get) => $get('metodo_pago') != 'multiple')
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
                                            TextInput::make('ref_zelle')
                                                ->label('Referencia(Zelle)')
                                                ->prefix('#')
                                                ->required(fn(Get $get) => $get('tipo_usd') == 'zelle')
                                                ->disabled(function (Get $get) {
                                                    if ($get('tipo_usd') == 'zelle') {
                                                        return false;
                                                    }
                                                    return true;
                                                })
                                                ->hidden(fn(Get $get) => $get('metodo_pago') != 'bsd' && $get('metodo_pago') != 'multiple'),

                                        ]),

                                    ToggleButtons::make('tipo_bsd')
                                        ->multiple()
                                        ->label('Tipo VES(Bs.)')
                                        ->inline()
                                        ->live()
                                        ->options([
                                            'pago-movil'    => 'Pago Movil',
                                            'punto'         => 'Punto de Venta',
                                            'transferencia' => 'Transferencia',
                                        ])
                                        ->icons([
                                            'punto'         => 'heroicon-m-banknotes',
                                            'pago-movil'    => 'heroicon-m-banknotes',
                                            'transferencia' => 'heroicon-m-banknotes',
                                        ])
                                        ->hidden(fn(Get $get) => $get('metodo_pago') != 'bsd' && $get('metodo_pago') != 'multiple'),


                                    //Input para pago en bsd
                                    //-------------------------------------------------------------
                                    Grid::make()
                                        ->schema([
                                            TextInput::make('pago_bsd')
                                                ->label('Monto(Bs.)')
                                                ->numeric()
                                                // Read-only, because it's calculated
                                                // ->readOnly()
                                                ->disabled()
                                                ->dehydrated()
                                                ->placeholder('0.00')
                                                ->prefix('Bs.'),
                                        ])
                                        ->hidden(fn(Get $get) => $get('metodo_pago') != 'multiple')
                                        ->columns(3),
                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('referencia_pagoMovil_bsd')
                                                ->label('Ref: Pago Movil')
                                                ->required(fn(Get $get) => in_array('pago-movil', $get('tipo_bsd')))
                                                ->validationMessages([
                                                    'required' => 'La referencia del pago movil es requerida',
                                                ])
                                                ->live()
                                                ->prefix('#')
                                                ->disabled(function (Get $get) {
                                                    if (in_array('pago-movil', $get('tipo_bsd'))) {
                                                        return false;
                                                    }
                                                    return true;
                                                }),

                                            TextInput::make('referencia_puntoVenta_bsd')
                                                ->label('Ref: Punto de Venta')
                                                ->required(fn(Get $get) => in_array('punto', $get('tipo_bsd')))
                                                ->validationMessages([
                                                    'required' => 'La referencia del punto de venta es requerida',
                                                ])
                                                ->prefix('#')
                                                ->live()
                                                ->disabled(function (Get $get) {
                                                    if (in_array('punto', $get('tipo_bsd'))) {
                                                        return false;
                                                    }
                                                    return true;
                                                }),


                                            TextInput::make('referencia_transferencia_bsd')
                                                ->label('Ref: Transferencia')
                                                ->required(fn(Get $get) => in_array('transferencia', $get('tipo_bsd')))
                                                ->validationMessages([
                                                    'required' => 'La referencia de la transferencia es requerida',
                                                ])
                                                ->prefix('#')
                                                ->disabled(function (Get $get) {
                                                    if (in_array('transferencia', $get('tipo_bsd'))) {
                                                        return false;
                                                    }
                                                    return true;
                                                }),

                                        ])->hidden(fn(Get $get) => $get('metodo_pago') != 'bsd' && $get('metodo_pago') != 'multiple'),

                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('pagoMovil_bsd')
                                                ->label('Monto Pago Movil VES(Bs.)')
                                                ->numeric()
                                                // Read-only, because it's calculated
                                                // ->readOnly()
                                                ->placeholder('0.00')
                                                ->prefix('Bs.')
                                                ->disabled(function (Get $get) {
                                                    for ($i = 0; $i < count($get('tipo_bsd')); $i++) {
                                                        if ($get('tipo_bsd')[$i] == 'pago-movil') {
                                                            return false;
                                                        }
                                                    }
                                                    return true;
                                                }),


                                            TextInput::make('puntoVenta_bsd')
                                                ->label('Monto Punto de Venta VES(Bs.)')
                                                ->numeric()
                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                    $total = $get('total_usd') - $get('pago_usd');
                                                    $set('pago_bsd', round($total * Configuracion::first()->tasa_bcv, 2));
                                                })
                                                ->live(true)
                                                ->placeholder('0.00')
                                                ->prefix('Bs.')
                                                ->disabled(function (Get $get) {
                                                    for ($i = 0; $i < count($get('tipo_bsd')); $i++) {
                                                        if ($get('tipo_bsd')[$i] == 'punto') {
                                                            return false;
                                                        }
                                                    }
                                                    return true;
                                                }),



                                            TextInput::make('transferencia_bsd')
                                                ->label('Monto Transferencia VES(Bs.)')
                                                ->numeric()
                                                // Read-only, because it's calculated
                                                // ->readOnly()
                                                ->placeholder('0.00')
                                                ->prefix('Bs.')
                                                ->disabled(function (Get $get) {
                                                    for ($i = 0; $i < count($get('tipo_bsd')); $i++) {
                                                        if ($get('tipo_bsd')[$i] == 'transferencia') {
                                                            return false;
                                                        }
                                                    }
                                                    return true;
                                                }),


                                        ])->hidden(fn(Get $get) => $get('metodo_pago') != 'bsd'),
                                    //----------------------------------------------------------------

                                ])
                                ->columnSpan(1)
                                ->columns(1),
                            //--------------------------------------------------
                        ])
                        ->action(function ($data, $record) {

                            if ($data['metodo_pago'] == 'usd') {

                                $registro_venta_usd = VentaController::registrar_venta_usd($record, $data, $record->detalles->toArray());
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

                            if ($data['metodo_pago'] == 'bsd') {

                                $registro_venta_bsd = VentaController::registrar_venta_bsd($record, $data, $record->detalles->toArray());
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

                            if ($data['metodo_pago'] == 'multiple') {

                                $registro_venta_multiple = VentaController::registrar_venta_multiple($record, $data, $record->detalles->toArray());
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
                        ->color('verdeOscuro')
                        // ->buttonLabel('Registrar venta')
                        ->hidden(fn($record) => $record->status != 'por-procesar')
                        // ->modalSubmitAction(fn(StaticAction $action, Get $get) => $action->label('Procesar venta'))
                        ->icon('heroicon-o-check-circle'),

                    Tables\Actions\DeleteAction::make()
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->size('sm')
                        ->action(function ($record) {
                            dd('aqui', $record);
                        })
                        ->after(function ($record) {
                            dd($record);
                        })
                        ->hidden(fn($record) => $record->status == 'procesado'),
                    Tables\Actions\ExportAction::make()
                        ->exporter(PedidoExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                            ExportFormat::Csv,
                        ])
                ])
                ->icon('heroicon-c-bars-3-bottom-right')
                ->button()
                ->label('Acciones')
                ->color('azul')
                // ->disabled(fn($record) => $record->status == 'procesado')

            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('verdeOscuro')
                    ->exporter(PedidoExporter::class)
                    ->formats([
                        ExportFormat::Xlsx,
                        ExportFormat::Csv,
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(PedidoExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                            ExportFormat::Csv,
                        ])
                ]),
            ])
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }

    public static function updateTotals(Get $get, Set $set): void
    {

        $parametro = Configuracion::first();

        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('productos'))->filter(fn($item) => !empty($item['producto_id']) && !empty($item['cantidad']));

        // Retrieve prices for all selected products
        $prices = Producto::find($selectedProducts->pluck('producto_id'))->pluck('precio_venta', 'id');

        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            $product['precio_venta'] = $prices[$product['producto_id']] ?? 0;
            return $subtotal + ($prices[$product['producto_id']] * $product['cantidad']);
        }, 0);

        // Update the state with the new values
        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('monto_usd', number_format($subtotal, 2, '.', ''));
        $set('monto_bsd', number_format($subtotal * $parametro->tasa_bcv, 2, '.', ''));
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Ventas';
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