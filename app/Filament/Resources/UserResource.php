<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            Forms\Components\Section::make('USUARIOS TADMASS')
                ->description('Formulario de registro de usuarios. Campos Requeridos(*)')
                ->icon('heroicon-s-user-circle')
                ->schema([

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DateTimePicker::make('email_verified_at')
                        ->disabled()
                        ->dehydrated()
                        ->default(now()),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),

                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Configuración';
    }
}