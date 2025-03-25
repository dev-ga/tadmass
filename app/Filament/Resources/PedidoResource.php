<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
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
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\PedidoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PedidoResource\RelationManagers;

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
                                    ->label('Codigo')
                                    ->prefixIcon('heroicon-c-clipboard-document-list')
                                    ->required()
                                    ->default('TADMASS-PED-' . rand(111111, 999999))
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique()
                                    ->dehydrated()
                                    ->maxLength(255),

                            ])->columns(4),
                        Select::make('cliente_id')
                            ->options(Cliente::all()->pluck('nombre', 'id'))
                            ->searchable(),
                        Select::make('vendedor_id')
                            ->options(Vendedor::all()->pluck('nombre', 'id'))
                            ->default(function (Get $get) {
                                return Auth::user()->id;
                            })
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
                                    ->label('Cantidad')
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
                            ->afterStateUpdated(function (Get $get, Set $set,) {
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
                        Forms\Components\TextInput::make('monto_usd')
                            ->label('Monto USD($)')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('monto_bsd')
                            ->label('Monto BSD(Bs.)')
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
                    ->badge()
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
                Tables\Columns\TextColumn::make('monto_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_bsd')
                    ->numeric()
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
        return 'Administración';
    }
}