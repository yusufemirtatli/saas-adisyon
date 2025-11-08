<?php

namespace App\Filament\Resources\ProductCategories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
class ProductCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('team_id')
                    ->default(fn () => Filament::getTenant()?->id),
                TextInput::make('name')
                    ->label('Kategori Adı')
                    ->placeholder('Örn: İçecekler, Ana Yemekler, Tatlılar')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Açıklama')
                    ->placeholder('Kategori hakkında detaylı bilgi')
                    ->columnSpanFull()
                    ->rows(3),
                FileUpload::make('image')
                    ->label('Kategori Görseli')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->columnSpanFull()
                    ->directory('categories')
                    ->visibility('public'),
                Toggle::make('status')
                ->label('Aktif')
                ->default('active')
                ->required(),
            ]);
    }
}
