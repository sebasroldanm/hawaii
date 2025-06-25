<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as Info;
use App\Models\Table as TableModel;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Order';
    protected static ?string $label = 'Order';
    protected static ?string $pluralLabel = 'Orders';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Wizard::make([
                Forms\Components\Wizard\Step::make('Select Table')
                    ->schema([
                        Forms\Components\Select::make('table_id')
                            ->label('Table')
                            ->relationship(
                                'table',
                                'title',
                                fn($query) =>
                                $query->whereDoesntHave('orders', function ($q) {
                                    $q->whereIn('status', ['pending', 'preparing']);
                                })
                            )
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('name')
                            ->label('Order Name (optional)')
                            ->maxLength(255),
                    ]),

                Forms\Components\Wizard\Step::make('Add Products')
                    ->schema([
                        Forms\Components\Repeater::make('orderDetails')
                            ->label('Products')
                            ->relationship()
                            ->schema([
                                Forms\Components\Hidden::make('id'),
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->label('Product')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('unit_price', $product->price);
                                        }
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->reactive(),

                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\Placeholder::make('subtotal')
                                    ->label('Subtotal')
                                    ->content(
                                        fn(callable $get) =>
                                        '$' . number_format(($get('quantity') ?? 0) * ($get('unit_price') ?? 0), 2)
                                    ),
                            ])
                            ->defaultItems(1)
                            ->createItemButtonLabel('Add another product')
                            ->columns(4),
                    ]),

                Forms\Components\Wizard\Step::make('Confirm')
                    ->schema([
                        Forms\Components\Section::make('Order Summary')
                            ->schema([
                                Forms\Components\Placeholder::make('table_summary')
                                    ->label('Table')
                                    ->content(
                                        fn(callable $get) =>
                                        TableModel::find($get('table_id'))?->title ?? 'N/A'
                                    ),

                                Forms\Components\Repeater::make('orderDetails')
                                    ->label(null)
                                    ->schema([
                                        Forms\Components\Placeholder::make('product_name')
                                            ->label('Product')
                                            ->content(
                                                fn(callable $get) =>
                                                Product::find($get('product_id'))?->name ?? 'N/A'
                                            ),

                                        Forms\Components\Placeholder::make('quantity')
                                            ->label('Qty')
                                            ->content(fn(callable $get) => $get('quantity')),

                                        Forms\Components\Placeholder::make('unit_price')
                                            ->label('Unit Price')
                                            ->content(
                                                fn(callable $get) =>
                                                '$' . number_format($get('unit_price'), 2)
                                            ),

                                        Forms\Components\Placeholder::make('subtotal')
                                            ->label('Subtotal')
                                            ->content(
                                                fn(callable $get) =>
                                                '$' . number_format(($get('unit_price') ?? 0) * ($get('quantity') ?? 0), 2)
                                            ),

                                        Forms\Components\Placeholder::make('preparation_time')
                                            ->label('Estimated Prep Time')
                                            ->content(
                                                fn(callable $get) =>
                                                Product::find($get('product_id'))?->preparation_time
                                                    ? Product::find($get('product_id'))->preparation_time . ' min'
                                                    : 'N/A'
                                            ),
                                    ])
                                    ->columns(5)
                                    ->defaultItems(0)
                                    ->disabled(),

                                Forms\Components\Placeholder::make('order_total')
                                    ->label('Total Order')
                                    ->content(fn(callable $get) => '$' . number_format(
                                        collect($get('orderDetails') ?? [])
                                            ->reduce(fn($carry, $item) =>
                                            $carry + (($item['unit_price'] ?? 0) * ($item['quantity'] ?? 0)), 0),
                                        2
                                    )),
                                Forms\Components\Placeholder::make('estimated_time_total')
                                    ->label('Estimated Total Prep Time')
                                    ->content(function (callable $get) {
                                        $orderDetails = collect($get('orderDetails') ?? []);
                                        $totalTime = $orderDetails->reduce(function ($carry, $item) {
                                            $product = \App\Models\Product::find($item['product_id'] ?? null);
                                            if ($product && $product->preparation_time) {
                                                return $carry + $product->preparation_time;
                                            }
                                            return $carry;
                                        }, 0);
                                        return $totalTime > 0 ? $totalTime . ' min' : 'N/A';
                                    }),
                            ]),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('table.title')->label('Table')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\IconColumn::make('is_paid')->label('Paid')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->since()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Info\Section::make('Order Information')
                    ->columns(3)
                    ->schema([
                        Info\TextEntry::make('id')->label('Order #'),
                        Info\TextEntry::make('name')->label('Name'),
                        Info\TextEntry::make('table.title')->label('Table'),
                        Info\TextEntry::make('status')->label('Status'),
                        Info\TextEntry::make('is_paid')
                            ->label('Paid')
                            ->formatStateUsing(fn(bool $state) => $state ? 'Yes' : 'No'),
                        Info\TextEntry::make('created_at')->label('Created At'),
                    ]),

                Info\Section::make('Order Timing')
                    ->columns(2)
                    ->schema([
                        Info\TextEntry::make('started_at')->label('Started At'),
                        Info\TextEntry::make('completed_at')->label('Completed At'),
                    ]),

                Info\Section::make('Ordered Products')
                    ->schema([
                        Info\RepeatableEntry::make('orderDetails')
                            ->label('Products')
                            ->columns(4)
                            ->schema([
                                Info\TextEntry::make('product.name')->label('Product'),
                                Info\TextEntry::make('quantity'),
                                Info\TextEntry::make('unit_price')->money('USD', true),
                                Info\TextEntry::make('state')->label('State'),
                                Info\TextEntry::make('started_at')->label('Started'),
                                Info\TextEntry::make('completed_at')->label('Completed'),
                                Info\TextEntry::make('note')->label('Note')->columnSpanFull(),
                            ]),
                    ]),

                Info\Section::make('Summary')
                    ->columns(2)
                    ->schema([
                        Info\TextEntry::make('order_total')
                            ->label('Total')
                            ->formatStateUsing(
                                fn($record) =>
                                '$' . number_format($record->orderDetails->sum(fn($d) => $d->unit_price * $d->quantity), 2)
                            ),
                        Info\TextEntry::make('estimated_time_total')
                            ->label('Est. Prep Time')
                            ->formatStateUsing(
                                fn($record) =>
                                $record->orderDetails->sum(fn($d) => $d->product->preparation_time) . ' min'
                            ),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
