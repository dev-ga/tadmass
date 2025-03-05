<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Inventario;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InventarioResource\Pages;
use App\Filament\Resources\InventarioResource\RelationManagers;

class InventarioResource extends Resource
{
    protected static ?string $model = Inventario::class;

    protected static ?string $navigationIcon = 'heroicon-s-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            // ->schema([
            //     Forms\Components\TextInput::make('almacen_id')
            //         ->required()
            //         ->maxLength(255),
            //     Forms\Components\Select::make('producto_id')
            //         ->relationship('producto', 'name')
            //         ->required(),
            //     Forms\Components\TextInput::make('categoria_id')
            //         ->required()
            //         ->maxLength(255),
            //     Forms\Components\TextInput::make('existencia')
            //         ->required()
            //         ->numeric(),
            //     Forms\Components\TextInput::make('precio_venta_mayor')
            //         ->required()
            //         ->numeric()
            //         ->default(0.00),
            //     Forms\Components\TextInput::make('precio_venta_detal')
            //         ->required()
            //         ->numeric()
            //         ->default(0.00),
            //     Forms\Components\TextInput::make('registrado_por')
            //         ->required()
            //         ->maxLength(255),
            // ]);
            ->schema([
                Forms\Components\Section::make('INVENTARIO')
                    ->description('Formulario de registro/actualizacion del inventario. Campos Requeridos(*)')
                    ->icon('heroicon-c-building-library')
                    ->schema([

                        Forms\Components\TextInput::make('almacen_id')
                            ->label('Almacen')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('producto_id')
                            ->label('Producto')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->required(),
                        Forms\Components\TextInput::make('categoria_id')
                            ->label('Categoria')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('existencia')
                            ->label('Existencia')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('precio_venta_mayor')
                            ->label('Precio Venta Mayor')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->hint('Separador decimal con punto(.)' . ' Ejemplo: 1235.67')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('precio_venta_detal')
                            ->label('Precio Venta Detal')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->hint('Separador decimal con punto(.)' . ' Ejemplo: 1235.67')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('registrado_por')
                            ->label('Registrado Por')
                            ->prefixIcon('heroicon-s-shield-check')
                            ->default(Auth::user()->name)
                            ->disabled()
                            ->dehydrated()


                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('almacen_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('producto.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categoria_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('existencia')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_venta_mayor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_venta_detal')
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
            'index' => Pages\ListInventarios::route('/'),
            'create' => Pages\CreateInventario::route('/create'),
            'edit' => Pages\EditInventario::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Administración';
    }
}