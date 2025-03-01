<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\VentaResource\Pages;
use App\Filament\Employee\Resources\VentaResource\RelationManagers;
use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('cliente_id')
                    ->relationship('cliente', 'id')
                    ->required(),
                Forms\Components\Select::make('vendedor_id')
                    ->relationship('vendedor', 'id')
                    ->required(),
                Forms\Components\TextInput::make('fecha')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('metodo_pago')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('monto_usd')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('monto_bsd')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('totaL_venta')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('tasa_bcv')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('comision_usd')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('comision_bsd')
                    ->required()
                    ->numeric()
                    ->default(0.00),
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
}
