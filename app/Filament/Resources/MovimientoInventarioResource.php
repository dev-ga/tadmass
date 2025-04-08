<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\MovimientoInventario;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MovimientoInventarioResource\Pages;
use App\Filament\Resources\MovimientoInventarioResource\RelationManagers;

class MovimientoInventarioResource extends Resource
{
    protected static ?string $model = MovimientoInventario::class;

    protected static ?string $navigationIcon = 'heroicon-c-shopping-cart';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             //
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('codigo_producto')
                    ->label('Código producto')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('producto.nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo de Movimiento')
                    ->badge()
                    ->color(function (mixed $state): string {
                        return match ($state) {
                            'entrada' => 'verdeOscuro',
                            'salida' => 'danger',
                        };
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->badge()
                    ->color('verdeOscuro')
                    ->icon('heroicon-c-shopping-cart')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unidad_medida')
                    ->label('Unidad')
                    ->color('verdeOscuro')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('registrado_por')
                    ->label('Registrado por')
                    ->icon('heroicon-c-user')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                // ActionGroup::make([
                //     // Tables\Actions\ViewAction::make(),
                //     // Tables\Actions\EditAction::make(),
                //     Tables\Actions\DeleteAction::make(), 
                // ])
                // // ->link()
                // ->icon('heroicon-c-bars-3-bottom-right')
                // ->button()
                // ->label('Acciones')
                // ->color('azul')
            ])
            ->headerActions([
                // Tables\Actions\ExportAction::make()
                //     ->icon('heroicon-m-arrow-down-tray')
                //     ->color('verdeOscuro')
                //     ->exporter(PedidoExporter::class)
                //     ->formats([
                //         ExportFormat::Xlsx,
                //         ExportFormat::Csv,
                //     ])
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListMovimientoInventarios::route('/'),
            'create' => Pages\CreateMovimientoInventario::route('/create'),
            'edit' => Pages\EditMovimientoInventario::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Inventario';
    }
}