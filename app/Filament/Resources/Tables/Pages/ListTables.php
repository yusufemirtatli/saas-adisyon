<?php

namespace App\Filament\Resources\Tables\Pages;

use App\Filament\Resources\Tables\TableResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTables extends ListRecords
{
    protected static string $resource = TableResource::class;

    // Custom view kullan (static olmamalı!)
    protected string $view = 'filament.resources.tables.pages.list-tables';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    // Kayıtları getir
    public function getTableRecords(): \Illuminate\Contracts\Pagination\Paginator | \Illuminate\Support\Collection | \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->getFilteredTableQuery()->paginate(20);
    }
}
