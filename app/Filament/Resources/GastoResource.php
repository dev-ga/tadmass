<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GastoResource\Pages;
use App\Filament\Resources\GastoResource\RelationManagers;
use App\Models\Gasto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;

    protected static ?string $navigationIcon = 'heroicon-m-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nro_gasto')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('descripcion')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tipo_gasto')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('numero_factura')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nro_control')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('fecha')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('fecha_factura')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('proveedor_id')
                    ->numeric(),
                Forms\Components\TextInput::make('metodo_pago')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tasa_bcv')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('monto_usd')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('monto_bsd')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('iva')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('total_gasto_bsd')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('conversion_usd')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('registrado_por')
                    ->required()
                    ->maxLength(255)
                    ->default(0),
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
}