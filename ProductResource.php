<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Admin Panel';
    protected static ?string $slug = 'sv23810310277_products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', \Str::slug($state))),

                        TextInput::make('slug')
                            ->required()
                            ->unique(table: 'sv23810310277_products', column: 'slug'),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required(),

                        RichEditor::make('description')
                            ->label('Description'),

                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('VNĐ'),

                        TextInput::make('stock_quantity')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(0),

                        FileUpload::make('image_path')
                            ->label('Product Image')
                            ->image()
                            ->directory('products'),

                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'out_of_stock' => 'Out of Stock',
                            ])
                            ->default('draft'),

                        // Trường sáng tạo 1: warranty_period
                        TextInput::make('warranty_period')
                            ->label('Warranty (months)')
                            ->numeric()
                            ->minValue(1)
                            ->default(12),

                        // Trường sáng tạo 2: discount_percent
                        TextInput::make('discount_percent')
                            ->label('Discount (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, $set, $get) => 
                                $set('price_after_discount', $get('price') * (1 - $state / 100))
                            ),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('price')->money('VND', true),
                TextColumn::make('discount_percent')->label('Discount (%)'),
                TextColumn::make('price_after_discount')
                    ->label('Price After Discount')
                    ->money('VND', true),
                TextColumn::make('stock_quantity'),
                TextColumn::make('status_label')->label('Status'),
                TextColumn::make('warranty_period')->label('Warranty (months)'),
                TextColumn::make('warranty_message')
                    ->label('Warranty Info'),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
            ])
            ->defaultSort('id', 'desc');
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