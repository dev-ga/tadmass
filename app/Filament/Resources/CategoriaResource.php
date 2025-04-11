<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Categoria;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoriaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoriaResource\RelationManagers;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;

    protected static ?string $navigationIcon = 'heroicon-s-swatch';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('CATEGORIAS')
                    ->description('Formulario de registro para categorias de productos. Campos Requeridos(*)')
                    ->icon('heroicon-s-swatch')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('codigo')
                                    ->label('Código')
                                    ->prefixIcon('heroicon-c-clipboard-document-list')
                                    ->required()
                                    ->default('TADMASS-C-' . rand(111111, 999999))
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique()
                                    ->dehydrated()
                                    ->maxLength(255),

                            ])->columns(4),

                        Forms\Components\TextInput::make('nombre')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->label('Nombre de categoría')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->label('Slug de categoría')
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('comision')
                            ->prefixIcon('heroicon-c-clipboard-document-list')
                            ->label('Comision de categoría(%)')
                            ->hint('Separador decimal con punto(.)')
                            ->helperText('Este porcentaje sera aplicado sobre el precio de venta del producto y sera asignado al vendedor')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('registrado_por')
                            ->prefixIcon('heroicon-s-shield-check')
                            ->default(Auth::user()->name)
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(255),
                    ])->columns(2),
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
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('comision')
                    ->label('Comisión')
                    ->badge()
                    ->color('verdeOscuro')
                    ->icon('heroicon-c-receipt-percent')
                    ->searchable(),
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
            ActionGroup::make([
                Tables\Actions\ViewAction::make()
                    ->color('azul')
                    ->modalWidth(MaxWidth::FiveExtraLarge),
                Tables\Actions\EditAction::make()
                    ->color('verdeOscuro'),
                Tables\Actions\DeleteAction::make()
                    ->color('danger')
            ])
                ->icon('heroicon-c-bars-3-bottom-right')
                ->button()
                ->label('Acciones')
                ->color('azul')
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
            'index' => Pages\ListCategorias::route('/'),
            'create' => Pages\CreateCategoria::route('/create'),
            'edit' => Pages\EditCategoria::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Productos y Abastecimiento';
    }
}