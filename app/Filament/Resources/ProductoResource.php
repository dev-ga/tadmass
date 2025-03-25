<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Almacen;
use Filament\Forms\Get;
use App\Models\Producto;
use Filament\Forms\Form;
use App\Models\Categoria;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\ProductoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductoResource\RelationManagers;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-c-square-3-stack-3d';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('PRODUCTOS')
                ->description('Formulario para el registro de los Articulos/Productos. Campos Requeridos(*)')
                ->icon('heroicon-c-square-3-stack-3d')
                ->schema([
                    //Imagen del producto
                    Section::make()
                        ->schema([
                            FileUpload::make('image')
                                ->label('Imagen del Producto')
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ]),
                        ])->columns(2),
                    
                    Section::make()
                        ->schema([
                            ToggleButtons::make('feedback')
                            ->label('Tipo de venta?')
                            ->boolean()
                            ->inline()
                            ->inlineLabel(false)
                            ->options([
                                'mayor' => 'Mayor',
                                'detal' => 'Detal',
                            ]),
                            
                        ])
                        ->afterStateUpdated(function (Get $get, $set) {
                            if ($get('feedback') == 'mayor') {
                                $set('codigo', 'TADMASS-M-' . rand(111111, 999999));
                                $set('unidad_medida', 'bulto');
                            }
                            if ($get('feedback') == 'detal') {
                                $set('codigo', 'TADMASS-D-' . rand(111111, 999999));
                                $set('unidad_medida', 'unidad');
                            }
                        })
                        ->live(),

                    Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('codigo')
                                ->label('Codigo de Registro')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->required()
                                ->live()
                                ->disabled()
                                ->dehydrated()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('nombre')
                                ->label('Nombre')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('descripcion')
                                ->label('Descripcion')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->required()
                                ->maxLength(255),
                            //select categorias
                            Forms\Components\Select::make('categoria_id')
                                ->label('Categoria')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->required()
                                ->options(Categoria::all()->pluck('nombre', 'id'))
                                ->searchable(),

                            Forms\Components\TextInput::make('marca')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->label('Marca')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('modelo')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->label('Modelo')
                                ->required()
                                ->maxLength(255),
                            //fecha de vencimiento
                            Forms\Components\Datepicker::make('fecha_vencimiento')
                                ->label('Fecha de Vencimiento')
                                ->prefixIcon('heroicon-c-calendar-days')
                                ->minDate(now()->addDay(1)),

                            Forms\Components\TextInput::make('unidad_medida')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->label('Unidad de Medida')
                                ->disabled()
                                ->dehydrated()
                                ->maxLength(255),
                                
                            //Vental Detal
                            //-------------------------------------------------------
                            Forms\Components\TextInput::make('precio_venta_detal')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->label('Precio Venta(Detal)')
                                ->hint('Separador decimal(.)')
                                ->hidden(function (Get $get) {
                                    return $get('feedback') == 'mayor';
                                })
                                ->numeric(),
                            Forms\Components\TextInput::make('precio_compra_detal')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->label('Precio Compra(Detal)')
                                ->hint('Separador decimal(.)')
                                ->hidden(function (Get $get) {
                                    return $get('feedback') == 'mayor';
                                })
                                ->numeric(),
                            //-------------------------------------------------------


                            //Vental Mayor
                            //-------------------------------------------------------
                            Forms\Components\TextInput::make('precio_venta_mayor')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->label('Precio Venta(Mayor)')
                                ->hint('Separador decimal(.)')
                                ->hidden(function (Get $get) {
                                    return $get('feedback') == 'detal';
                                })
                                ->numeric(),
                            Forms\Components\TextInput::make('precio_compra_mayor')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->label('Precio Compra(Mayor)')
                                ->hint('Separador decimal(.)')
                                ->hidden(function (Get $get) {
                                    return $get('feedback') == 'detal';
                                })
                                ->numeric(),
                            //-------------------------------------------------------

                            Forms\Components\TextInput::make('registrado_por')
                                ->label('Registrado Por')
                                ->prefixIcon('heroicon-s-shield-check')
                                ->default(Auth::user()->name)
                                ->disabled()
                                ->dehydrated()
                                ->maxLength(255),
                                    
                        ])
                        ->hidden(function (Get $get) {
                            return $get('feedback') == false;
                        })
                        ->columns(3),

                ])->columns(2),
                
            Section::make('ENTRADA DE INVENTARIO')
                ->description('Informacion para el registro de la Entrada de Inventario. Campos Requeridos(*)')
                ->icon('heroicon-m-list-bullet')
                ->schema([

                    Forms\Components\Select::make('almacen_id')
                        ->label('Almacen')
                        ->prefixIcon('heroicon-c-clipboard-document-list')
                        ->required()
                        ->options(Almacen::all()->pluck('nombre', 'id'))
                        ->searchable(),
                    Forms\Components\TextInput::make('existencia')
                        ->label('Existencia')
                        ->prefixIcon('heroicon-c-clipboard-document-list')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('registrado_por')
                        ->label('Registrado Por')
                        ->prefixIcon('heroicon-s-shield-check')
                        ->default(Auth::user()->name)
                        ->disabled()
                        ->dehydrated()
                ])
                ->hidden(function (Get $get) {
                    return $get('feedback') == false;
                })
                ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->badge()   
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categoria.nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('imagen')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('marca')
                    ->searchable(),
                Tables\Columns\TextColumn::make('modelo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unidad_medida')
                    ->searchable(),
                Tables\Columns\TextColumn::make('precio_compra_detal')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('precio_venta')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_compra_mayor')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('registrado_por')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
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

    public static function getNavigationGroup(): ?string
    {
        return 'Administración';
    }
}