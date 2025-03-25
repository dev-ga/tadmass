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
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\VentaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VentaResource\RelationManagers;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-c-presentation-chart-line';

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
                                    ->label('Codigo')
                                    ->prefixIcon('heroicon-c-clipboard-document-list')
                                    ->required()
                                    ->default('TADMASS-C-' . rand(111111, 999999))
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique()
                                    ->dehydrated()
                                    ->maxLength(255),
                            ])->columns(4),
                        Forms\Components\Select::make('cliente_id')
                            ->relationship('cliente', 'nombre')
                            ->required(),
                        Forms\Components\Select::make('vendedor_id')
                            ->relationship('vendedor', 'nombre')
                            ->required(),
                        Forms\Components\TextInput::make('registrado_por')
                            ->prefixIcon('heroicon-s-shield-check')
                            ->default(Auth::user()->name)
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(255),
                    ])->columnSpan('full')->columns(3),
                
                Section::make()
                    ->schema([
                        // Repeatable field for invoice items
                        Forms\Components\Repeater::make('productos')
                            // Defined as a relationship to the InvoiceProduct model
                            ->relationship('detalles')
                            ->schema([
                                
                                Forms\Components\Select::make('producto_id')
                                    ->relationship('productos', 'nombre')
                                    // Options are all products, but we have modified the display to show the price as well
                                    ->options(
                                        // Producto::all()->pluck('nombre', 'id')
                                        $products->mapWithKeys(function (Producto $product) {
                                            return [$product->id => sprintf('%s ($%s)', $product->nombre, $product->precio_venta)];
                                        })
                                    )
                                    // Disable options that are already selected in other rows
                                    ->disableOptionWhen(function ($value, $state, Get $get) {
                                        return collect($get('../*.producto_id'))
                                            ->reject(fn($id) => $id == $state)
                                            ->filter()
                                            ->contains($value);
                                    })
                                    ->required()
                                    ->afterStateUpdated(function (Get $get, Set $set,) {
                                        //actualizamos el precio de venta
                                        $set('precio_venta', Producto::find($get('producto_id'))->precio_venta);
                                    })
                                    ->live(),
                                    
                                Forms\Components\TextInput::make('cantidad')
                                    ->integer()
                                    ->default(1)
                                    ->required(),

                                Forms\Components\TextInput::make('precio_venta')
                                    ->label('Precio')
                                    ->default(0.00)
                                    ->numeric()
                                    ->required(),
                            ])
                            // Repeatable field is live so that it will trigger the state update on each change
                            ->live()
                            // After adding a new row, we need to update the totals
                            ->afterStateUpdated(function (Get $get, Set $set, ) {
                                // Log::info($data['product_id']);
                                self::updateTotals($get, $set);
                            })
                            // After deleting a row, we need to update the totals
                            ->deleteAction(
                                fn(Action $action) => $action->after(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                            )
                            // Disable reordering
                            ->reorderable(false)
                            ->columns(3)
                            
                    ])->columnSpan(2)->columns(1),

                Section::make()
                    ->schema([
                        
                        Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->numeric()
                                    // Read-only, because it's calculated
                                    ->readOnly()
                                    ->prefix('$')
                                    // This enables us to display the subtotal on the edit page load
                                    ->afterStateHydrated(function (Get $get, Set $set) {
                                        self::updateTotals($get, $set);
                                    }),
                                Forms\Components\TextInput::make('iva')
                                    ->suffix('%')
                                    ->required()
                                    ->numeric()
                                    ->default(Configuracion::first()->iva)
                                    // Live field, as we need to re-calculate the total on each change
                                    ->live(true)
                                    // This enables us to display the subtotal on the edit page load
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateTotals($get, $set);
                                    })
                                
                            ])->columns(2),
                            
                        Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('total_usd')
                                    ->label('Total Dolares($)')
                                    ->numeric()
                                    // Read-only, because it's calculated
                                    ->readOnly()
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('total_bsd')
                                    ->label('Total Bolivares(Bs.)')
                                    ->numeric()
                                    // Read-only, because it's calculated
                                    ->readOnly()
                                    ->prefix('Bs.'),
                                
                            ])->columns(2),
                            
                        ToggleButtons::make('metodo_pago')
                            ->label('Metodo de Pago')
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

                        Grid::make()
                            ->schema([
                                
                                Forms\Components\TextInput::make('monto_usd')
                                    ->label('Monto($)')
                                    ->numeric()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $total = $get('total_usd') - $get('monto_usd');
                                        $set('monto_bsd', $total * Configuracion::first()->tasa_bcv);
                                    })
                                    ->live(true)
                                    ->prefix('$'),
                                    
                                Forms\Components\TextInput::make('monto_bsd')
                                    ->label('Monto(Bs.)')
                                    ->numeric()
                                    // Read-only, because it's calculated
                                    ->readOnly()
                                    ->prefix('$'),

                            ])
                            ->hidden(fn (Get $get) => $get('metodo_pago') != 'multiple')
                            ->columns(2),
                        
                    ])->columnSpan(1)->columns(1),
                    
            ])->columns(3);
            
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente.id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vendedor.id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->searchable(),
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monto_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_bsd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('totaL_venta')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasa_bcv')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comision_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comision_bsd')
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
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Contabilidad';
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