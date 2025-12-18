<?php

namespace App\Filament\Resources\Tables\Pages;

use App\Filament\Resources\Tables\TableResource;
use App\Models\Product;
use App\Models\Shopcart;
use App\Models\ShopcartItem;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewTable extends ViewRecord
{
    protected static string $resource = TableResource::class;

    protected string $view = 'filament.resources.tables.pages.view-table';

    // Shopping cart state
    public array $cart = [];
    public array $cartNotes = [];
    
    // Görünüm modu: 'grouped' veya 'detailed'
    public string $viewMode = 'grouped';
    
    // Ödeme filtresi: 'all', 'unpaid', 'paid'
    // Varsayılan olarak sadece ödenmemiş ürünleri göster
    public string $paymentFilter = 'unpaid';

    public function toggleViewMode(): void
    {
        $this->viewMode = $this->viewMode === 'grouped' ? 'detailed' : 'grouped';
    }

    public function getPaymentUrl(): string
    {
        // table_id'ye göre status='open' olan ilk shopcart'ı bul
        $shopcart = Shopcart::where('table_id', $this->record->id)
            ->where('status', 'open')
            ->first();
        
        // Shopcart'ın ID'sini URL'e ekle
        return TableResource::getUrl('pay', [
            'record' => $this->record,
            'shopcart' => $shopcart?->id
        ]);
    }

    public function getShopcart()
    {
        // Her zaman fresh data al (cache'leme)
        return Shopcart::where('table_id', $this->record->id)
            ->where('status', 'open')
            ->with(['items' => function($query) {
                $query->orderBy('created_at', 'desc');
            }, 'items.product.productCategory'])
            ->first();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addProduct')
                ->label('Ürün Ekle')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->modalHeading('Ürün Seçin')
                ->modalDescription('Masaya eklemek istediğiniz ürünleri seçin ve miktarlarını ayarlayın')
                ->modalWidth('5xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('İptal')
                ->modalContent(fn () => view('filament.resources.tables.components.product-selector', [
                    'products' => Product::where('status', true)->with('productCategory')->get(),
                ]))
                ->extraModalFooterActions([
                    Action::make('addNotes')
                        ->label('Not Ekle')
                        ->color('warning')
                        ->disabled(fn () => empty($this->cart))
                        ->modalHeading('Ürünlere Not Ekle')
                        ->modalWidth('3xl')
                        ->modalSubmitActionLabel('Tamam')
                        ->modalCancelActionLabel('İptal')
                        ->modalContent(fn () => view('filament.resources.tables.components.product-notes-selector', [
                            'cart' => $this->cart,
                            'products' => Product::whereIn('id', array_keys($this->cart))->get(),
                            'cartNotes' => $this->cartNotes,
                        ]))
                        ->action(fn () => null),
                    Action::make('saveCart')
                        ->label('Kaydet')
                        ->color('success')
                        ->action('saveCart'),
                ]),
        ];
    }

    public function increaseQuantity(int $productId): void
    {
        if (!isset($this->cart[$productId])) {
            $this->cart[$productId] = 0;
        }
        $this->cart[$productId]++;
    }

    public function decreaseQuantity(int $productId): void
    {
        if (isset($this->cart[$productId]) && $this->cart[$productId] > 0) {
            $this->cart[$productId]--;
            if ($this->cart[$productId] === 0) {
                unset($this->cart[$productId]);
                unset($this->cartNotes[$productId]);
            }
        }
    }

    public function updateNote(int $productId, int $index, string $note): void
    {
        if (!isset($this->cartNotes[$productId])) {
            $this->cartNotes[$productId] = [];
        }
        $this->cartNotes[$productId][$index] = $note;
    }

    public function saveCart(): void
    {
        if (empty($this->cart)) {
            Notification::make()
                ->warning()
                ->title('Sepet Boş')
                ->body('Lütfen en az bir ürün seçin.')
                ->send();
            return;
        }

        // Masanın açık bir shopcart'ı var mı kontrol et
        $shopcartExists = Shopcart::where('table_id', $this->record->id)
            ->where('status', 'open')
            ->exists();
            
        $shopcart = Shopcart::firstOrCreate(
            [
                'table_id' => $this->record->id,
                'status' => 'open',
            ],
            [
                'total_amount' => 0,
                'paid_amount' => 0,
                'created_by' => Auth::id(),
            ]
        );

        // Eğer yeni shopcart oluşturulduysa, masanın statusunu 'closed' yap
        if (!$shopcartExists) {
            $this->record->status = 'closed';
            $this->record->save();
        }

        $totalAmount = 0;
        $itemCount = 0;

        // Her ürünü quantity kadar ekle
        foreach ($this->cart as $productId => $quantity) {
            $product = Product::find($productId);
            if (!$product) continue;

            for ($i = 0; $i < $quantity; $i++) {
                $note = $this->cartNotes[$productId][$i] ?? null;
                
                ShopcartItem::create([
                    'shopcart_id' => $shopcart->id,
                    'product_id' => $product->id,
                    'unit_price' => $product->price,
                    'is_paid' => false,
                    'note' => $note,
                ]);
                $totalAmount += $product->price;
                $itemCount++;
            }
        }

        // Total amount'u güncelle
        $shopcart->total_amount += $totalAmount;
        $shopcart->save();

        // Sepeti temizle
        $this->cart = [];
        $this->cartNotes = [];

        Notification::make()
            ->success()
            ->title('Ürünler Eklendi')
            ->body("{$itemCount} ürün başarıyla eklendi.")
            ->send();

        // Modal'ı kapat ve sayfayı yenile
        $this->dispatch('close-modal', id: 'addProduct');
        $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
    }
}


