<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Vendedor;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\VendedorResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VendedorResource\RelationManagers;

class VendedorResource extends Resource
{
    protected static ?string $model = Vendedor::class;

    protected static ?string $navigationIcon = 'heroicon-c-user-plus';

    protected static ?string $navigationLabel = 'Vendedores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('VENDEDORES')
                ->description('Formulario de registro para vendedores. Campos Requeridos(*)')
                ->icon('heroicon-c-user-plus')
                ->schema([
                    Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('codigo')
                                ->label('Código')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->required()
                                ->default('TADMASS-P-' . rand(111111, 999999))
                                ->disabled()
                                ->dehydrated()
                                ->unique()
                                ->dehydrated()
                                ->maxLength(255),

                        ])->columns(4),
                    Forms\Components\TextInput::make('nombre')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('ci_rif')
                        ->label('CI/RIF')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('telefono')
                        ->label('Teléfono')
                        ->tel()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('direccion')
                        ->label('Dirección')
                        ->required()
                        ->maxLength(255),
                    Select::make('tipo')
                        ->required()
                        ->options([
                            'externo' => 'Externo Online',
                            'oficina' => 'Oficina',
                        ]),
                    Forms\Components\TextInput::make('registrado_por')
                        ->label('Registrado Por')
                        ->prefixIcon('heroicon-s-shield-check')
                        ->default(Auth::user()->name)
                        ->disabled()
                        ->dehydrated(),

                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ci_rif')
                    ->label('CI/RIF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->label('Dirección')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
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
            'index' => Pages\ListVendedors::route('/'),
            'create' => Pages\CreateVendedor::route('/create'),
            'edit' => Pages\EditVendedor::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Comercial y Clientes';
    }
}