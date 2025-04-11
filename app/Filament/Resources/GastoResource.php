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
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\GastoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GastoResource\RelationManagers;
use App\Filament\Resources\GastoResource\RelationManagers\GastoDetallesRelationManager;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;

    protected static ?string $navigationIcon = 'heroicon-m-credit-card';

    protected static ?string $navigationLabel = 'Egresos - Compras';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('INFORMACION DEL GASTOS')
                    ->description('Formulario de gastos')
                    ->icon('heroicon-m-credit-card')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('codigo')
                                    ->label('Código')
                                    ->prefixIcon('heroicon-c-clipboard-document-list')
                                    ->required()
                                    ->default('TADMASS-G-' . rand(111111, 999999))
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique()
                                    ->dehydrated()
                                    ->maxLength(255),

                            ])->columns(4),

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
                                Forms\Components\TextInput::make('ci_rif')
                                    ->label('CI o RIF')
                                    ->required(),
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre/Razon Social')
                                    ->required(),
                                Forms\Components\TextInput::make('registrado_por')
                                    ->label('Registrado por:')
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
                            ->label('Método de Pago')
                            ->required()
                            ->options(function (Get $get) {
                                if ($get('forma_pago') == 'dolares') {
                                    return MetodoPago::where('tipo_moneda', 'usd')->pluck('descripcion', 'descripcion');
                                }

                                if ($get('forma_pago') == 'bolivares') {
                                    return MetodoPago::where('tipo_moneda', 'bsd')->pluck('descripcion', 'descripcion');
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

                Forms\Components\Section::make('COSTOS:')
                    ->description('Formulario de gastos')
                    ->icon('heroicon-m-credit-card')
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

                        Forms\Components\TextInput::make('exento')
                            ->label('Exento(Bs.)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->hidden(function (Get $get) {
                                if ($get('forma_pago') == 'bolivares') {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->numeric()
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
                            ->label('Total Gasto en Bolivares(VESBs.)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->live()
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->default(0.00)
                            ->placeholder('0.00'),

                        Forms\Components\TextInput::make('conversion_a_usd')
                            ->label('Total Gasto en Dolares(US$)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->live()
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->placeholder('0.00'),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->badge()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('numero_factura')
                    ->label('Número de Factura')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('nro_control')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('fecha_factura')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('proveedor.nombre')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Metodo de pago')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('tasa_bcv')
                    ->label('Tasa BCV')
                    ->color('azul')
                    ->money('VES')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('monto_usd')
                    ->label('Monto US$')
                    ->color('verdeOscuro')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('monto_bsd')
                    ->label('Monto VES(Bs.)')
                    ->color('azul')
                    ->money('VES')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('iva')
                    ->label('IVA')
                    ->color('azul')
                    ->money('VES')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('total_gasto_bsd')
                    ->label('Total gastos Bs.')
                    ->color('azul')
                    ->money('VES')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('conversion_usd')
                    ->label('Conversión USD')
                    ->color('verdeOscuro')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('registrado_por')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
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
                    ->color('azul')
                    ->modalWidth(MaxWidth::FiveExtraLarge),
                Tables\Actions\EditAction::make()
                    ->color('verdeOscuro'),
                Tables\Actions\DeleteAction::make()
                    ->color('danger')
            ])
                ->icon('heroicon-c-bars-3-bottom-right')
                ->button()
                ->label('Acciones')
                ->color('azul')
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
            GastoDetallesRelationManager::class
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
        $parametro = Configuracion::first();

        if ($get('feedback') == true && $get('exento') == null) {
            $iva = $get('monto_bsd') * $parametro->iva;
            $set('iva', round($iva, 2));
            $set('total_gasto_bsd',  round(($get('monto_bsd') + $iva), 2));
            $set('conversion_a_usd', round($get('total_gasto_bsd') / $get('tasa_bcv'), 2));
        }

        if ($get('feedback') == true && $get('exento') != null) {
            $iva = $get('monto_bsd') * $parametro->iva;
            $set('iva', round($iva, 2));
            $set('total_gasto_bsd',  round(($get('monto_bsd') + $iva + $get('exento')), 2));
            $set('conversion_a_usd', round($get('total_gasto_bsd') / $get('tasa_bcv'), 2));
        }

        if ($get('feedback') == false && $get('forma_pago') == 'dolares') {
            $set('total_gasto_bsd', round($parametro->tasa_bcv * $get('monto_usd'), 2));
            $set('conversion_a_usd', $get('monto_usd'));
        }

        if ($get('feedback') == false && $get('forma_pago') == 'bolivares') {
            $set('total_gasto_bsd',  round($get('monto_bsd'), 2));
            $set('conversion_a_usd', round($get('monto_bsd') / $get('tasa_bcv'), 2));
        }
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Contable';
    }
}