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

    // Shopcart ID'yi component property olarak sakla
    public ?int $shopcartId = null;

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Sayfa yüklendiğinde shopcart ID'yi al ve property'de sakla
        $this->shopcartId = request()->route()->parameter('shopcart');
    }

    public function getShopcart()
    {
        // Property'den shopcart ID'yi al (Livewire AJAX çağrılarında route parametreleri kaybolur)
        $shopcartId = $this->shopcartId;
        
        // Shopcart ID ile shopcart'ı getir
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
            // payment_type değerini belirle (cash veya credit_card)
            $paymentTypeValue = $paymentMethod === 'card' ? 'credit_card' : 'cash';
            
            foreach ($items as $item) {
                $item->is_paid = true;
                $item->payment_type = $paymentTypeValue;
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
            
            // Eğer ödenen tutar toplam tutara eşit veya büyükse
            if ($shopcart->paid_amount >= $shopcart->total_amount) {
                // Shopcart'taki tüm itemlerin is_paid değerini 1 yap
                ShopcartItem::where('shopcart_id', $shopcart->id)
                    ->update(['is_paid' => true]);
                
                // Shopcart'ı kapat
                $shopcart->status = 'closed';
                $shopcart->save();
                
                // Masanın statusunu 'open' yap
                $table = $shopcart->table;
                if ($table) {
                    $table->status = 'open';
                    $table->save();
                }
            }

            DB::commit();

            Notification::make()
                ->success()
                ->title('Ödeme Başarılı!')
                ->body(count($selectedItemIds) . " ürün için " . number_format($totalPaid, 2) . " ₺ ödeme alındı.")
                ->send();

            // Masa sayfasına yönlendir
            $redirectUrl = TableResource::getUrl('view', ['record' => $this->record]);
            
            return redirect($redirectUrl);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->danger()
                ->title('Hata')
                ->body('Ödeme işlemi sırasında bir hata oluştu: ' . $e->getMessage())
                ->send();
        }
    }

    /**
     * Hesabı Kapat - Tüm ürünleri ödenmiş olarak işaretle ve shopcart'ı kapat
     */
    public function closeAccount($paymentMethod = 'cash')
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

        DB::beginTransaction();
        try {
            // payment_type değerini belirle (cash veya credit_card)
            $paymentTypeValue = $paymentMethod === 'card' ? 'credit_card' : 'cash';
            
            // Tüm ödenmemiş ürünleri al
            $unpaidItems = ShopcartItem::where('shopcart_id', $shopcart->id)
                ->where('is_paid', false)
                ->get();
            
            $totalPaid = 0;
            foreach ($unpaidItems as $item) {
                $item->is_paid = true;
                $item->payment_type = $paymentTypeValue;
                $item->save();
                $totalPaid += $item->unit_price;
            }
            
            // Shopcart'ı güncelle
            $shopcart->paid_amount = $shopcart->total_amount;
            $shopcart->status = 'closed';
            $shopcart->save();
            
            // Masanın statusunu 'open' yap
            $table = $shopcart->table;
            if ($table) {
                $table->status = 'open';
                $table->save();
            }

            DB::commit();

            Notification::make()
                ->success()
                ->title('Hesap Kapatıldı!')
                ->body('Toplam ' . number_format($totalPaid, 2) . ' ₺ ödeme alındı. Hesap kapatıldı.')
                ->send();

            // Masa sayfasına yönlendir
            $redirectUrl = TableResource::getUrl('view', ['record' => $this->record]);
            
            return redirect($redirectUrl);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->danger()
                ->title('Hata')
                ->body('Hesap kapatılırken bir hata oluştu: ' . $e->getMessage())
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

