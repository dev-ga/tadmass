<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Almacen;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AlmacenResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AlmacenResource\RelationManagers;

class AlmacenResource extends Resource
{
    protected static ?string $model = Almacen::class;

    protected static ?string $navigationIcon = 'heroicon-c-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('ALMACENES')
                ->description('Formulario de registro de Almacenes. Campos Requeridos(*)')
                ->icon('heroicon-c-building-library')
                ->schema([

                    Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('codigo')
                                ->label('Codigo')
                                ->prefixIcon('heroicon-c-clipboard-document-list')
                                ->required()
                                ->default('TADMASS-A-' . rand(111111, 999999))
                                ->disabled()
                                ->dehydrated()
                                ->unique()
                                ->dehydrated()
                                ->maxLength(255),

                        ])->columns(4),

                    Forms\Components\TextInput::make('nombre')
                        ->prefixIcon('heroicon-c-clipboard-document-list')
                        ->label('Nombre de Almacen')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('direccion')
                        ->prefixIcon('heroicon-c-clipboard-document-list')
                        ->label('Direccion del Almacen')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('telefono')
                        ->prefixIcon('heroicon-m-phone')
                        ->label('Telefono')
                        ->tel()
                        ->required()
                        ->maxLength(255),
                    Select::make('tipo_almacen')
                        ->prefixIcon('heroicon-m-numbered-list')
                        ->label('Tipo de almacen')
                        ->options([
                            'mayor' => 'Mayorista',
                            'detal' => 'Detal',
                        ])
                        ->searchable(),
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
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_almacen')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
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
            'index' => Pages\ListAlmacens::route('/'),
            'create' => Pages\CreateAlmacen::route('/create'),
            'edit' => Pages\EditAlmacen::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Administración';
    }
}