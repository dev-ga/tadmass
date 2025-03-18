<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Gasto;
use App\Models\Almacen;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\MetodoPago;
use Filament\Tables\Table;
use App\Models\Configuracion;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\GastoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GastoResource\RelationManagers;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;

    protected static ?string $navigationIcon = 'heroicon-m-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('INFORMACION DEL GASTOS')
                    ->description('Formulario de gastos')
                    ->icon('heroicon-m-arrow-trending-down')
                    ->schema([

                        Forms\Components\TextInput::make('codigo')
                            ->label('Codigo de gasto')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->default('G-'.random_int(11111, 999999)),
                            
                        Forms\Components\TextInput::make('nro_control')
                            ->label('Nro. de Control')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->required()
                            ->rules(['required', 'string', 'max:255'])
                            ->validationMessages([
                                'required'  => 'Campo requerido',
                            ]),

                        Forms\Components\DatePicker::make('fecha_factura')
                            ->label('Fecha de Factura')
                            ->prefixIcon('heroicon-m-calendar-days')
                            ->format('d-m-Y'),

                        Forms\Components\TextInput::make('numero_factura')
                            ->label('Nro. de Factura')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->rules(['required', 'string', 'max:255'])
                            ->validationMessages([
                                'required'  => 'Campo requerido',
                            ]),

                        Forms\Components\TextInput::make('descripcion')
                            ->label('Descripción')
                            ->prefixIcon('heroicon-s-pencil')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('proveedor_id')
                            ->prefixIcon('heroicon-m-numbered-list')
                            ->relationship('proveedor', 'nombre')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('rif')
                                    ->label('Rif')
                                    ->required(),
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre/Razon Social')
                                    ->required(),
                            ])
                            ->required(),

                        Forms\Components\Select::make('forma_pago')
                            ->label('Forma de Pago')
                            ->prefixIcon('heroicon-m-numbered-list')
                            ->required()
                            ->live()
                            ->options([
                                'dolares' => 'Dolares',
                                'bolivares' => 'Bolivares',
                            ]),

                        Forms\Components\Select::make('metodo_pago')
                            ->prefixIcon('heroicon-m-numbered-list')
                            ->label('Metodo de Pago')
                            ->required()
                            ->options(function (Get $get) {
                                if ($get('forma_pago') == 'dolares') {
                                    return MetodoPago::where('tipo_moneda', 'usd')->pluck('descripcion', 'id');
                                }

                                if ($get('forma_pago') == 'bolivares') {
                                    return MetodoPago::where('tipo_moneda', 'bsd')->pluck('descripcion', 'id');
                                }
                            })
                            ->live(),

                        Forms\Components\TextInput::make('registrado_por')
                            ->prefixIcon('heroicon-s-shield-check')
                            ->label('Cargado por:')
                            ->disabled()
                            ->dehydrated()
                            ->default(Auth::user()->name),

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Textarea::make('observacion')
                                    ->label('Observaciones Relevante'),
                            ])
                    ])->columns(2),

                Forms\Components\Section::make('ASOCIADO A:')
                    ->description('Formulario de gastos')
                    ->icon('heroicon-m-arrow-trending-down')
                    ->schema([
                        Forms\Components\Select::make('almacen_id')
                            ->prefixIcon('heroicon-m-numbered-list')
                            ->live()
                            ->label('Almacenes')
                            ->options(Almacen::all()->pluck('nombre', 'id')),
                    ])->columns(1),

                Forms\Components\Section::make('COSTOS:')
                    ->description('Formulario de gastos')
                    ->icon('heroicon-m-arrow-trending-down')
                    ->schema([

                        ToggleButtons::make('feedback')
                            ->label('Maneja IVA?')
                            ->boolean()
                            ->inline()
                            ->live()
                            ->hidden(function (Get $get) {
                                if ($get('forma_pago') == 'bolivares') {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->default(false)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('tasa_bcv')
                            ->hidden(function (Get $get) {
                                if ($get('forma_pago') == 'bolivares') {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->live()
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('monto_usd')
                            ->label('Monto en USD($)')
                            ->prefixIcon('heroicon-s-currency-dollar')
                            ->numeric()
                            ->live(onBlur: true)
                            ->hidden(function (Get $get) {
                                if ($get('forma_pago') == 'dolares') {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotales($get, $set);
                            })
                            ->placeholder('0.00'),

                        Forms\Components\TextInput::make('monto_bsd')
                            ->label('Monto en BSD(Bs.)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->hidden(function (Get $get) {
                                if ($get('forma_pago') == 'bolivares') {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->numeric()
                            ->placeholder('0.00')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotales($get, $set);
                            }),

                        Forms\Components\TextInput::make('iva')
                            ->label('IVA(%)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->hidden(function (Get $get) {
                                if ($get('feedback') == true) {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->live()
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->default(0.00),

                        Forms\Components\TextInput::make('total_gasto_bsd')
                            ->label('Total Gasto en Bolivares(Bs.)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->live()
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->default(0.00)
                            ->placeholder('0.00'),

                        Forms\Components\TextInput::make('conversion_a_usd')
                            ->label('Total Gasto en Dolares($)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->live()
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->placeholder('0.00'),

                    ])
                    ->hidden(function (Get $get) {
                        if ($get('sucursal_id')  || $get('almacen_id') != null) {
                            return false;
                        } else {
                            return true;
                        }
                    })
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nro_gasto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_gasto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero_factura')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nro_control')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_factura')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proveedor_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tasa_bcv')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_bsd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('iva')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_gasto_bsd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('conversion_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registrado_por')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListGastos::route('/'),
            'create' => Pages\CreateGasto::route('/create'),
            'edit' => Pages\EditGasto::route('/{record}/edit'),
        ];
    }

    public static function updateTotales(Get $get, Set $set): void
    {
        $parametro_iva = Configuracion::first()->iva;

        if ($get('feedback') == true) {
            $iva = $get('monto_bsd') * $parametro_iva;
            $set('iva', round($iva, 2));
            $set('total_gasto_bsd',  round(($get('monto_bsd') + $iva), 2));
            $set('conversion_a_usd', round($get('total_gasto_bsd') / $get('tasa_bcv'), 2));
        }

        if ($get('feedback') == false && $get('forma_pago') == 'dolares') {
            $set('conversion_a_usd', round($get('monto_usd'), 2));
        }

        if ($get('feedback') == false && $get('forma_pago') == 'bolivares') {
            $set('total_gasto_bsd',  round($get('monto_bsd'), 2));
            $set('conversion_a_usd', round($get('monto_bsd') / $get('tasa_bcv'), 2));
        }
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Contabilidad';
    }
}