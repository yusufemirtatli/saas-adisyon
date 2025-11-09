<?php

namespace App\Filament\Resources\Tables\Pages;

use App\Filament\Resources\Tables\TableResource;
use App\Models\Shopcart;
use Filament\Resources\Pages\ViewRecord;

class PayTable extends ViewRecord
{
    protected static string $resource = TableResource::class;

    protected string $view = 'filament.resources.tables.pages.pay-table';

    protected static ?string $title = 'Ödeme';

    public function getShopcart()
    {
        return Shopcart::where('table_id', $this->record->id)
            ->where('status', 'open')
            ->with(['items.product.productCategory'])
            ->first();
    }

    public function getBreadcrumbs(): array
    {
        return [
            TableResource::getUrl('index') => 'Masalar',
            TableResource::getUrl('view', ['record' => $this->record]) => $this->record->name,
            '#' => 'Ödeme',
        ];
    }
}

