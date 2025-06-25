<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TableResource\Pages;
use App\Filament\Resources\TableResource\RelationManagers;
use App\Models\Table as TableModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TableResource extends Resource
{
    protected static ?string $model = TableModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Restaurant';
    protected static ?string $label = 'Table';
    protected static ?string $pluralLabel = 'Tables';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('seats')
                    ->numeric()
                    ->required(),

                Forms\Components\Toggle::make('is_reserved')
                    ->label('Reserved?'),

                Forms\Components\DateTimePicker::make('reservation_start')
                    ->label('Reservation Start')
                    ->default(null),

                Forms\Components\DateTimePicker::make('reservation_end')
                    ->label('Reservation End')
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('seats'),
                Tables\Columns\IconColumn::make('is_reserved')->boolean(),
                Tables\Columns\TextColumn::make('reservation_start')->dateTime()->label('Start'),
                Tables\Columns\TextColumn::make('reservation_end')->dateTime()->label('End'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_reserved')->label('Only reserved?'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTables::route('/'),
            'create' => Pages\CreateTable::route('/create'),
            'edit' => Pages\EditTable::route('/{record}/edit'),
        ];
    }
}
