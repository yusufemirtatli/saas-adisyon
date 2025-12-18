<?php

namespace App\Filament\Pages;

use App\Models\Shopcart;
use App\Models\ShopcartItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class Kitchen extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFire;

    protected string $view = 'filament.pages.kitchen';

    protected static ?string $title = 'Mutfak';

    protected static ?string $navigationLabel = 'Mutfak';

    protected static ?int $navigationSort = 100;

    /**
     * Aktif siparişleri getir (item bazlı kitchen_status)
     */
    public function getOrders(): array
    {
        // Aktif mutfak durumundaki ürünleri olan shopcart'ları bul
        $shopcartIds = ShopcartItem::whereIn('kitchen_status', ['waiting', 'preparing'])
            ->distinct()
            ->pluck('shopcart_id');

        $shopcarts = Shopcart::with(['table', 'createdBy', 'items.product.productCategory'])
            ->whereIn('id', $shopcartIds)
            ->where('status', 'open')
            ->get();

        $orders = [];

        foreach ($shopcarts as $shopcart) {
            // Sadece aktif mutfak durumundaki ürünleri al
            $activeItems = $shopcart->items->whereIn('kitchen_status', ['waiting', 'preparing']);
            
            if ($activeItems->isEmpty()) {
                continue;
            }

            // En baskın durumu belirle (waiting > preparing > ready)
            $statusPriority = ['waiting' => 1, 'preparing' => 2, 'ready' => 3];
            $dominantStatus = $activeItems->sortBy(fn($item) => $statusPriority[$item->kitchen_status] ?? 99)->first()->kitchen_status;

            $items = [];
            foreach ($activeItems as $item) {
                $items[] = [
                    'id' => $item->id,
                    'name' => $item->product->name ?? 'Ürün',
                    'quantity' => 1,
                    'note' => $item->note,
                    'category' => $item->product->productCategory->name ?? null,
                    'status' => $item->kitchen_status,
                ];
            }

            // Aynı ürünleri ve aynı durumları grupla
            $groupedItems = [];
            foreach ($items as $item) {
                $key = $item['name'] . '|' . ($item['note'] ?? '') . '|' . $item['status'];
                if (!isset($groupedItems[$key])) {
                    $groupedItems[$key] = [
                        'ids' => [],
                        'name' => $item['name'],
                        'quantity' => 0,
                        'note' => $item['note'],
                        'category' => $item['category'],
                        'status' => $item['status'],
                    ];
                }
                $groupedItems[$key]['ids'][] = $item['id'];
                $groupedItems[$key]['quantity']++;
            }

            // En eski aktif item'ın zamanını al
            $oldestItem = $activeItems->sortBy('created_at')->first();
            $minutes = $oldestItem->created_at->diffInMinutes(now());

            $orders[] = [
                'id' => $shopcart->id,
                'table_name' => $shopcart->table->name ?? 'Masa',
                'time' => $this->formatTime($minutes),
                'minutes' => $minutes,
                'status' => $dominantStatus,
                'waiter' => $shopcart->createdBy->name ?? 'Bilinmiyor',
                'items' => array_values($groupedItems),
                'item_ids' => $activeItems->pluck('id')->toArray(),
            ];
        }

        // Duruma ve zamana göre sırala
        usort($orders, function($a, $b) {
            $statusPriority = ['waiting' => 1, 'preparing' => 2, 'ready' => 3];
            $statusDiff = ($statusPriority[$a['status']] ?? 99) - ($statusPriority[$b['status']] ?? 99);
            if ($statusDiff !== 0) return $statusDiff;
            return $b['minutes'] - $a['minutes']; // En eski önce
        });

        return $orders;
    }

    /**
     * Zamanı formatla
     */
    private function formatTime(int $minutes): string
    {
        if ($minutes < 1) {
            return 'Az önce';
        } elseif ($minutes < 60) {
            return $minutes . ' dk önce';
        } else {
            $hours = floor($minutes / 60);
            return $hours . ' saat önce';
        }
    }

    /**
     * Siparişi hazırlamaya başla (tüm aktif itemlar)
     */
    public function startPreparing(int $shopcartId): void
    {
        $updated = ShopcartItem::where('shopcart_id', $shopcartId)
            ->where('kitchen_status', 'waiting')
            ->update(['kitchen_status' => 'preparing']);

        if ($updated > 0) {
            $shopcart = Shopcart::with('table')->find($shopcartId);
            Notification::make()
                ->success()
                ->title('Hazırlanıyor')
                ->body(($shopcart->table->name ?? 'Sipariş') . ' hazırlanmaya başlandı.')
                ->send();
        }
    }

    /**
     * Siparişi hazır olarak işaretle ve listeden kaldır (tüm aktif itemlar)
     */
    public function markReady(int $shopcartId): void
    {
        $updated = ShopcartItem::where('shopcart_id', $shopcartId)
            ->whereIn('kitchen_status', ['waiting', 'preparing'])
            ->update(['kitchen_status' => 'ready']);

        if ($updated > 0) {
            $shopcart = Shopcart::with('table')->find($shopcartId);
            Notification::make()
                ->success()
                ->title('Hazır!')
                ->body(($shopcart->table->name ?? 'Sipariş') . ' hazır, garson bilgilendirildi.')
                ->send();
        }
    }

    /**
     * Siparişi iptal et (tüm aktif itemlar)
     */
    public function cancelOrder(int $shopcartId): void
    {
        $updated = ShopcartItem::where('shopcart_id', $shopcartId)
            ->whereIn('kitchen_status', ['waiting', 'preparing'])
            ->update(['kitchen_status' => 'cancelled']);

        if ($updated > 0) {
            $shopcart = Shopcart::with('table')->find($shopcartId);
            Notification::make()
                ->warning()
                ->title('İptal Edildi')
                ->body(($shopcart->table->name ?? 'Sipariş') . ' iptal edildi.')
                ->send();
        }
    }
}
