<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('team_id')
                    ->default(fn () => Filament::getTenant()?->id),
                TextInput::make('name')
                    ->label('Ürün Adı')
                    ->placeholder('Örn: Çay, Kahve, Hamburger')
                    ->required()
                    ->maxLength(255),
                Select::make('product_category_id')
                    ->label('Kategori')
                    ->relationship('productCategory', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('cost')
                    ->label('Maliyet')
                    ->required()
                    ->numeric()
                    ->prefix('₺')
                    ->step(0.01)
                    ->minValue(0),
                TextInput::make('price')
                    ->label('Fiyat')
                    ->required()
                    ->numeric()
                    ->prefix('₺')
                    ->step(0.01)
                    ->minValue(0),
                Textarea::make('description')
                    ->label('Açıklama')
                    ->placeholder('Ürün hakkında detaylı bilgi')
                    ->columnSpanFull()
                    ->rows(3),
                FileUpload::make('image')
                    ->label('Ürün Görseli')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '1:1',
                        '4:3',
                        '16:9',
                    ])
                    ->maxSize(2048)
                    ->columnSpanFull(),
                Toggle::make('status')
                ->label('Aktif')
                ->default('active')
                ->required(),
            ]);
    }
}
