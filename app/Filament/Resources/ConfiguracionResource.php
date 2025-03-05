<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfiguracionResource\Pages;
use App\Filament\Resources\ConfiguracionResource\RelationManagers;
use App\Models\Configuracion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConfiguracionResource extends Resource
{
    protected static ?string $model = Configuracion::class;

    protected static ?string $navigationIcon = 'heroicon-c-cog-8-tooth';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('iva')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('isrl')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('porcen_venta_detal')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('porcen_venta_mayor')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('porcen_venta_general')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('sueldo_base_vendedores')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('graficos_filament_heading')
                    ->required()
                    ->maxLength(255)
                    ->default('Grafico'),
                Forms\Components\TextInput::make('graficos_filament_getType')
                    ->required()
                    ->maxLength(255)
                    ->default('bar'),
                Forms\Components\TextInput::make('graficos_dataset_label')
                    ->maxLength(255),
                Forms\Components\TextInput::make('graficos_dataset_borderColor')
                    ->maxLength(255),
                Forms\Components\TextInput::make('graficos_dataset_backgroundColor')
                    ->maxLength(255),
                Forms\Components\TextInput::make('graficos_dataset_backgroundColor_array')
                    ->maxLength(255),
                Forms\Components\TextInput::make('graficos_dataset_fill')
                    ->maxLength(255),
                Forms\Components\TextInput::make('graficos_filament_geDescriptiont')
                    ->maxLength(255),
                Forms\Components\TextInput::make('graficos_option_scale_x_display')
                    ->maxLength(255),
                Forms\Components\TextInput::make('graficos_option_scale_y_display')
                    ->maxLength(255),
                Forms\Components\TextInput::make('graficos_option_scale_x_ticks_stepSize')
                    ->numeric(),
                Forms\Components\TextInput::make('graficos_option_scale_y_ticks_stepSize')
                    ->numeric(),
                Forms\Components\TextInput::make('graficos_option_scale_indexAxis_x_y')
                    ->numeric(),
                Forms\Components\TextInput::make('graficos_option_plugins_legend_display')
                    ->maxLength(255),
                Forms\Components\TextInput::make('graficos_option_plugins_legend_position')
                    ->maxLength(255),
                Forms\Components\TextInput::make('graficos_option_plugins_legend_align')
                    ->maxLength(255),
                Forms\Components\TextInput::make('dash_panel_footer')
                    ->maxLength(255),
                Forms\Components\TextInput::make('dash_panel_topBar_end')
                    ->maxLength(255),
                Forms\Components\TextInput::make('dash_panel_topBar_start')
                    ->maxLength(255),
                Forms\Components\TextInput::make('dash_panel_sideBar_nav_start')
                    ->maxLength(255),
                Forms\Components\TextInput::make('dash_panel_page_header_action_before')
                    ->maxLength(255),
                Forms\Components\TextInput::make('dash_panel_page_header_action_after')
                    ->maxLength(255),
                Forms\Components\TextInput::make('dash_panel_titulo')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('iva')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('isrl')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('porcen_venta_detal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('porcen_venta_mayor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('porcen_venta_general')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sueldo_base_vendedores')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('graficos_filament_heading')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_filament_getType')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_dataset_label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_dataset_borderColor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_dataset_backgroundColor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_dataset_backgroundColor_array')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_dataset_fill')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_filament_geDescriptiont')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_option_scale_x_display')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_option_scale_y_display')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_option_scale_x_ticks_stepSize')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('graficos_option_scale_y_ticks_stepSize')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('graficos_option_scale_indexAxis_x_y')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('graficos_option_plugins_legend_display')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_option_plugins_legend_position')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graficos_option_plugins_legend_align')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dash_panel_footer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dash_panel_topBar_end')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dash_panel_topBar_start')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dash_panel_sideBar_nav_start')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dash_panel_page_header_action_before')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dash_panel_page_header_action_after')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dash_panel_titulo')
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
            'index' => Pages\ListConfiguracions::route('/'),
            'create' => Pages\CreateConfiguracion::route('/create'),
            'edit' => Pages\EditConfiguracion::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Seguridad';
    }
}