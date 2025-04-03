<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\ProductoResource\Pages;
use App\Filament\Employee\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-c-square-3-stack-3d';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('descripcion')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('categoria_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('imagen')
                    ->maxLength(255),
                Forms\Components\TextInput::make('exitencia_real')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('marca')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('modelo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('fecha_vencimiento')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('unidad_medida')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('precio_detal')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('precio_compra_detal')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('precio_venta_detal')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('precio_mayor')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('precio_compra_mayor')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('precio_venta_mayor')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('registrado_por')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categoria_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('imagen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('exitencia_real')
                    ->searchable(),
                Tables\Columns\TextColumn::make('marca')
                    ->searchable(),
                Tables\Columns\TextColumn::make('modelo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unidad_medida')
                    ->searchable(),
                Tables\Columns\TextColumn::make('precio_detal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_compra_detal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_venta_detal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_mayor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_compra_mayor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_venta_mayor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}