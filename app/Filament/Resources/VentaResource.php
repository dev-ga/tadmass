<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Venta;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Producto;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Configuracion;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\VentaResource\Pages;
use App\Filament\Resources\VentaResource\RelationManagers\DetallesRelationManager;
use App\Filament\Resources\VentaResource\RelationManagers\PagoDetallesRelationManager;
use App\Filament\Resources\VentaResource\RelationManagers\PedidoDetalleRelationManager;
use Filament\Forms\Components\DatePicker;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationLabel = 'Ingresos - Ventas';

    protected static ?string $navigationIcon = 'heroicon-c-presentation-chart-line';

    public static function form(Form $form): Form
    {
        $products = Producto::get();
        return $form
            ->schema([
                Section::make()
                    ->heading('Detalles de la Venta')
                    ->description('Venta, total de venta y detalles del pedido')
                    ->schema([
                        datePicker::make('created_at')
                            ->label('Fecha de venta')
                            ->prefixIcon('heroicon-o-calendar-date-range')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('codigo')
                            ->label('Codigo')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->required()
                            ->default('TADMASS-V-' . rand(111111, 999999))
                            ->disabled()
                            ->dehydrated()
                            ->unique()
                            ->dehydrated()
                            ->maxLength(255),
                        Forms\Components\Select::make('cliente_id')
                            ->prefixIcon('heroicon-c-list-bullet')
                            ->relationship('cliente', 'nombre')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\Select::make('vendedor_id')
                            ->prefixIcon('heroicon-c-list-bullet')
                            ->relationship('vendedor', 'nombre')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        
                        Forms\Components\TextInput::make('registrado_por')
                            ->prefixIcon('heroicon-s-shield-check')
                            ->default(Auth::user()->name)
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(255),
                    ])->columns(5),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vendedor.nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prod_asociados')
                    ->label('Productos Asociados')
                    ->alignCenter()
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Método de Pago')
                    ->badge()
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_venta_usd')
                    ->label('Venta US$')
                    ->color('verdeOscuro')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_venta_bsd')
                    ->label('Venta VES(Bs.)')
                    ->color('azul')
                    ->money('VES')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasa_bcv')
                    ->label('Tasa BCV')
                    ->money('VES')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('comision_usd')
                    ->label('Comisión US$')
                    ->color('verdeOscuro')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('comision_bsd')
                    ->label('Comisión VES(Bs.)')
                    ->color('azul')     
                    ->money('VES')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registrado_por')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                ])
                ->icon('heroicon-c-bars-3-bottom-right')
                ->button()
                ->label('Acciones')
                ->color('azul')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DetallesRelationManager::class,
            PagoDetallesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Contable';
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
            return $subtotal + ($prices[$product['producto_id']] * $product['cantidad']);
        }, 0);

        // Update the state with the new values
        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('total_usd', number_format($subtotal + ($subtotal * ($get('iva') / 100)), 2, '.', ''));
        $set('total_bsd', number_format($subtotal * $parametro->tasa_bcv, 2, '.', ''));
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