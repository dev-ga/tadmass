<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Cliente;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClienteResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Filament\Resources\ClienteResource\RelationManagers\VentasRelationManager;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('CLIENTES')
                ->description('Formulario de registro para clientes. Campos Requeridos(*)')
                ->icon('heroicon-s-user-group')
                ->schema([

                    Forms\Components\TextInput::make('nombre')
                    ->prefixIcon('heroicon-c-clipboard-document-list')
                    ->label('Nombre y Apellidos o Razón Social')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('ci_rif')
                    ->prefixIcon('heroicon-c-clipboard-document-list')
                        ->label('CI o RIF')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->prefixIcon('heroicon-o-at-symbol')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('telefono')
                    ->prefixIcon('heroicon-m-phone')
                        ->label('Nro. de Telefono')
                        ->tel()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('direccion')
                    ->prefixIcon('heroicon-c-clipboard-document-list')
                        ->label('Dirección')    
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('registrado_por')
                    ->prefixIcon('heroicon-s-shield-check')
                        ->required()
                        ->maxLength(255),

            ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ci_rif')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
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
            VentasRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Administración';
    }
}