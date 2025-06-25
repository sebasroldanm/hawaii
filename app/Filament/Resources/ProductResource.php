<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Ingredient;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Products';
    protected static ?string $label = 'Product';
    protected static ?string $pluralLabel = 'Products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('$'),

                Forms\Components\Toggle::make('is_in_stock')
                    ->label('Is in stock?')
                    ->default(true),

                Forms\Components\TextInput::make('stock')
                    ->label('Stock quantity')
                    ->numeric()
                    ->visible(fn($get) => $get('is_in_stock'))
                    ->nullable(),

                Forms\Components\Toggle::make('is_composite')
                    ->label('Is composite?')
                    ->default(false)
                    ->reactive(),

                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', fn($query) => $query->orderBy('name'))
                    ->preload()
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('preparation_time')
                    ->numeric()
                    ->label('Preparation Time (minutes)')
                    ->nullable(),

                Forms\Components\TextInput::make('preparation_area')
                    ->label('Preparation Area')
                    ->nullable(),

                Forms\Components\Section::make('Ingredients')
                    ->visible(fn(Forms\Get $get) => $get('is_composite') === true)
                    ->schema([
                        Forms\Components\HasManyRepeater::make('recipes')
                            ->relationship()
                            ->label('Recipe Ingredients')
                            ->schema([
                                Forms\Components\Select::make('ingredient_id')
                                    ->relationship('ingredient', 'name')
                                    ->label('Ingredient')
                                    ->preload()
                                    ->searchable()
                                    ->required(),

                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->defaultItems(0)
                            ->collapsible()
                            ->createItemButtonLabel('Add Ingredient'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('price')->money('usd')->sortable(),
                Tables\Columns\IconColumn::make('is_in_stock')->boolean(),
                Tables\Columns\IconColumn::make('is_composite')->boolean(),
                Tables\Columns\TextColumn::make('stock')->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable(),
                Tables\Columns\TextColumn::make('preparation_time')->label('Prep. Time (min)'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
