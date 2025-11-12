<?php

namespace App\Filament\Resources\Tables\Pages;

use App\Filament\Resources\Tables\TableResource;
use App\Models\Shopcart;
use App\Models\ShopcartItem;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class PayTable extends ViewRecord
{
    protected static string $resource = TableResource::class;

    protected string $view = 'filament.resources.tables.pages.pay-table';

    protected static ?string $title = 'Ödeme';

    public function getShopcart()
    {
        // URL'den shopcart ID'yi al
        $shopcartId = request()->route('shopcart');
        
        // URL'deki shopcart ID ile shopcart'ı getir
        if ($shopcartId) {
            return Shopcart::where('id', $shopcartId)
                ->with(['items.product.productCategory'])
                ->first();
        }
        
        return null;
    }

    public function processPayment($selectedItemIds, $paymentMethod = 'cash')
    {
        $shopcart = $this->getShopcart();
        
        if (!$shopcart) {
            Notification::make()
                ->danger()
                ->title('Hata')
                ->body('Adisyon bulunamadı.')
                ->send();
            return;
        }

        if (empty($selectedItemIds)) {
            Notification::make()
                ->warning()
                ->title('Uyarı')
                ->body('Lütfen en az bir ürün seçin.')
                ->send();
            return;
        }

        DB::beginTransaction();
        try {
            // Seçili ürünleri ödendi olarak işaretle
            $items = ShopcartItem::whereIn('id', $selectedItemIds)
                ->where('shopcart_id', $shopcart->id)
                ->get();

            $totalPaid = 0;
            foreach ($items as $item) {
                $item->is_paid = true;
                $item->save();
                $totalPaid += $item->unit_price;
            }

            // Shopcart'ın paid_amount değerini güncelle
            $shopcart->paid_amount += $totalPaid;
            $shopcart->save();

            // Tüm ürünler ödendiyse shopcart'ı kapat
            $remainingUnpaid = ShopcartItem::where('shopcart_id', $shopcart->id)
                ->where('is_paid', false)
                ->count();

            if ($remainingUnpaid === 0) {
                $shopcart->status = 'closed';
                $shopcart->save();
            }

            DB::commit();

            Notification::make()
                ->success()
                ->title('Ödeme Başarılı!')
                ->body(count($selectedItemIds) . " ürün için " . number_format($totalPaid, 2) . " ₺ ödeme alındı.")
                ->send();

            // Masa sayfasına yönlendir
            return redirect(TableResource::getUrl('view', ['record' => $this->record]));

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->danger()
                ->title('Hata')
                ->body('Ödeme işlemi sırasında bir hata oluştu: ' . $e->getMessage())
                ->send();
        }
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

