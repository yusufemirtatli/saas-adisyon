<?php

namespace App\Filament\Resources\Tables\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;

class TableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('team_id')
                    ->default(fn () => Filament::getTenant()?->id),
                TextInput::make('name')
                    ->label('Masa Adı')
                    ->placeholder('Örn: Masa 1, Bahçe Masası 3')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
