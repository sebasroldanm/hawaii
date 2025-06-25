<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Filament\Resources\RecipeResource\RelationManagers;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationIcon = 'heroicon-o-check';
    protected static ?string $navigationGroup = 'Products';
    protected static ?string $label = 'Recipe';
    protected static ?string $pluralLabel = 'Recipes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('composite_id')
                ->label('Composite Product')
                ->options(Product::where('is_composite', true)->pluck('name', 'id'))
                ->required()
                ->searchable(),

            Forms\Components\Select::make('ingredient_id')
                ->label('Ingredient')
                ->options(Ingredient::pluck('name', 'id'))
                ->required()
                ->searchable(),

            Forms\Components\TextInput::make('quantity')
                ->numeric()
                ->required()
                ->label('Quantity'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Composite Product'),
                Tables\Columns\TextColumn::make('ingredient.name')->label('Ingredient'),
                Tables\Columns\TextColumn::make('quantity')->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }
}
