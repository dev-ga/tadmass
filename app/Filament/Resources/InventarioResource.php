<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Almacen;
use Filament\Forms\Form;
use App\Models\Inventario;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InventarioResource\Pages;
use App\Http\Controllers\MovimientoInventarioController;
use App\Filament\Resources\InventarioResource\RelationManagers;
use App\Filament\Resources\InventarioResource\RelationManagers\MovimientoInventariosRelationManager;

class InventarioResource extends Resource
{
    protected static ?string $model = Inventario::class;

    protected static ?string $navigationIcon = 'heroicon-s-table-cells';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\Section::make('INVENTARIO')
    //                 ->description('Formulario de registro/actualizacion del inventario. Campos Requeridos(*)')
    //                 ->icon('heroicon-c-building-library')
    //                 ->schema([

    //                         Forms\Components\Select::make('almacen_id')
    //                             ->prefixIcon('heroicon-c-clipboard-document-list')
    //                             ->relationship('almacen', 'nombre')
    //                             ->required(),
    //                         //producto
    //                         Forms\Components\Select::make('producto_id')
    //                             ->prefixIcon('heroicon-c-clipboard-document-list')
    //                             ->relationship('producto', 'nombre')
    //                             ->required(),

    //                         Forms\Components\TextInput::make('categoria_id')
    //                             ->label('Categoria')
    //                             ->prefixIcon('heroicon-c-clipboard-document-list')
    //                             ->required()
    //                             ->maxLength(255),
    //                         Forms\Components\TextInput::make('existencia')
    //                             ->label('Existencia')
    //                             ->prefixIcon('heroicon-c-clipboard-document-list')
    //                             ->required()
    //                             ->numeric(),
    //                         Forms\Components\TextInput::make('precio_venta_mayor')
    //                             ->label('Precio Venta Mayor')
    //                             ->prefixIcon('heroicon-c-clipboard-document-list')
    //                             ->hint('Separador decimal con punto(.)' . ' Ejemplo: 1235.67')
    //                             ->required()
    //                             ->numeric(),
    //                         Forms\Components\TextInput::make('precio_venta')
    //                             ->label('Precio Venta Detal')
    //                             ->prefixIcon('heroicon-c-clipboard-document-list')
    //                             ->hint('Separador decimal con punto(.)' . ' Ejemplo: 1235.67')
    //                             ->required()
    //                             ->numeric(),
    //                         Forms\Components\TextInput::make('registrado_por')
    //                             ->label('Registrado Por')
    //                             ->prefixIcon('heroicon-s-shield-check')
    //                             ->default(Auth::user()->name)
    //                             ->disabled()
    //                             ->dehydrated()


    //                 ])->columns(2),
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('producto.nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('almacen.nombre')
                    ->label('Almacén')
                    ->searchable(),
                Tables\Columns\TextColumn::make('existencia')
                    ->label('Existencia')
                    ->icon('heroicon-c-adjustments-vertical')
                    ->color('verdeOscuro')
                    ->badge()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unidad_medida')
                    ->label('Unidad')
                    ->color('verdeOscuro')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_venta')
                    ->badge()
                    ->color('verdeOscuro')
                    ->icon('heroicon-s-currency-dollar')
                    ->label('Precio US$')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registrado_por')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                    // Tables\Actions\ViewAction::make(),
                    // Tables\Actions\EditAction::make()
                    // ->color('verdeOscuro'),
                    // Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('Mover Existencia')
                        ->label('Mover Existencia')
                        ->color('azul')
                        ->icon('heroicon-s-truck')
                        ->form([
                            Section::make('MOVER EXISTENCIA')
                                ->icon('heroicon-s-truck')
                                ->description('Fomrulario de Mover Existencia de los productos del almacén mayorista al almacén detal')
                                ->schema([
                                    Forms\Components\Select::make('almacen_id')
                                        ->prefixIcon('heroicon-c-clipboard-document-list')
                                        ->options(Almacen::select('id', 'nombre')->where('tipo_almacen', 'detal')->pluck('nombre', 'id'))
                                        ->preload()
                                        ->searchable()
                                        // ->relationship('almacen', 'nombre')
                                        ->required(),
                                    Forms\Components\TextInput::make('cantidad')
                                        ->label('Cantidad en Bultos')
                                        ->prefixIcon('heroicon-c-clipboard-document-list')
                                        ->required(),
                                    Forms\Components\TextInput::make('precio_venta')
                                        ->prefixIcon('heroicon-c-clipboard-document-list')
                                        ->label('Precio Venta en US$')
                                        ->hint('Separador decimal(.)')
                                        ->required(),

                                ])->columns(2),
                        ])
                        ->action(function ($record, array $data) {

                            $mover_existencia_individual = MovimientoInventarioController::mover_existencia_individual($record, $data);

                            if ($mover_existencia_individual['success'] == true) {
                                Notification::make()
                                    ->success()
                                    ->title('Movimiento Exitoso')
                                    ->body($mover_existencia_individual['message'])
                                    ->send();
                            } else {
                                Notification::make()
                                    ->danger()
                                    ->title('Error')
                                    ->body($mover_existencia_individual['message'])
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('Reposicion')
                        ->label('Mover Existencia')
                        ->color('azul')
                        ->icon('heroicon-s-truck')
                        ->form([
                            Section::make('MOVER EXISTENCIA')
                                ->icon('heroicon-s-truck')
                                ->description('Fomrulario de Mover Existencia de los productos del almacén mayorista al almacén detal')
                                ->schema([
                                    Forms\Components\Select::make('almacen_id')
                                        ->prefixIcon('heroicon-c-clipboard-document-list')
                                        ->options(Almacen::select('id', 'nombre')->where('tipo_almacen', 'detal')->pluck('nombre', 'id'))
                                        ->preload()
                                        ->searchable()
                                        // ->relationship('almacen', 'nombre')
                                        ->required(),
                                    Forms\Components\TextInput::make('cantidad')
                                        ->label('Cantidad en Bultos')
                                        ->prefixIcon('heroicon-c-clipboard-document-list')
                                        ->required(),
                                    Forms\Components\TextInput::make('precio_venta')
                                        ->prefixIcon('heroicon-c-clipboard-document-list')
                                        ->label('Precio Venta en US$')
                                        ->hint('Separador decimal(.)')
                                        ->required(),

                                ])->columns(2),
                        ])
                        ->action(function ($record, array $data) {

                            $mover_existencia_individual = MovimientoInventarioController::mover_existencia_individual($record, $data);

                            if ($mover_existencia_individual['success'] == true) {
                                Notification::make()
                                    ->success()
                                    ->title('Movimiento Exitoso')
                                    ->body($mover_existencia_individual['message'])
                                    ->send();
                            } else {
                                Notification::make()
                                    ->danger()
                                    ->title('Error')
                                    ->body($mover_existencia_individual['message'])
                                    ->send();
                            }
                        }),
                ])
                ->icon('heroicon-c-bars-3-bottom-right')
                ->button()
                ->label('Acciones')
                ->color('azulClaro')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('Mover Existencia')
                        ->label('Mover Existencia')
                        ->color('azul')
                        ->icon('heroicon-s-truck')
                        ->form([
                            Section::make('MOVER EXISTENCIA')
                                ->icon('heroicon-s-truck')
                                ->description('Fomrulario de Mover Existencia de los productos del almacén mayorista al almacén detal')
                                ->schema([
                                    Forms\Components\Select::make('almacen_id')
                                        ->prefixIcon('heroicon-c-clipboard-document-list')
                                        ->options(Almacen::select('id', 'nombre')->where('tipo_almacen', 'detal')->pluck('nombre', 'id'))
                                        ->preload()
                                        ->searchable()
                                        // ->relationship('almacen', 'nombre')
                                        ->required(),
                                    Forms\Components\TextInput::make('cantidad')
                                        ->label('Cantidad en Bultos')
                                        ->prefixIcon('heroicon-c-clipboard-document-list')
                                        ->required(),
                                    Forms\Components\TextInput::make('precio_venta')
                                        ->prefixIcon('heroicon-c-clipboard-document-list')
                                        ->label('Precio Venta en US$')
                                        ->hint('Separador decimal(.)')
                                        ->required(),
                                    
                                ])->columns(2),
                        ])
                        ->action(function (Collection $records, array $data) {
                            // dd(str_replace('-M-', '-D-',  $records[0]->codigo), $records[0]->codigo, $records);
                            for ($i = 0; $i < count($records); $i++) {
                                $mover_existencia = MovimientoInventarioController::mover_existencia_masiva($records[$i], $data);

                                if ($mover_existencia['success'] == true) {
                                    Notification::make()
                                        ->success()
                                        ->title('Movimiento Masivo Exitoso')
                                        ->body($mover_existencia['message'])
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->danger()
                                        ->title('Error')
                                        ->body($mover_existencia['message'])
                                        ->send();
                                }
                            }

                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MovimientoInventariosRelationManager::class
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
        return 'Inventario';
    }
}