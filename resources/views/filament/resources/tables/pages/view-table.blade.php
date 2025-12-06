<x-filament-panels::page>
    {{-- Masa Detay ve Adisyon Sayfası --}}
    
    {{-- Adisyon İçeriği --}}
    @php
        $shopcart = $this->getShopcart();
        
        // DEBUG: Shopcart yeniden yükle (cache bypass)
        if ($shopcart) {
            $shopcart->refresh();
            $shopcart->load(['items.product.productCategory']);
        }
    @endphp

    @if($shopcart && $shopcart->items->count() > 0)
        {{-- Adisyon Var - Ürünleri Göster --}}
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    {{-- Sol Taraf --}}
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        {{-- Başlık --}}
                        <div>
                            <div style="font-size: 1.125rem; font-weight: 600;">Sipariş Listesi</div>
                            <div style="font-size: 0.875rem; font-weight: 400; color: #6b7280; margin-top: 0.25rem;">
                                Masaya eklenen ürünler
                            </div>
                        </div>
                        
                        {{-- Görünüm Toggle --}}
                        <div style="display: inline-flex; background: #f3f4f6; border-radius: 0.5rem; padding: 0.25rem; border: 1px solid #e5e7eb;">
                            {{-- Gruplu Görünüm İkonu --}}
                            <button
                                type="button"
                                wire:click="$set('viewMode', 'grouped')"
                                style="padding: 0.5rem 0.75rem; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; {{ $this->viewMode === 'grouped' ? 'background: #10b981; color: white; box-shadow: 0 1px 2px rgba(0,0,0,0.1);' : 'background: transparent; color: #6b7280;' }}"
                                title="Gruplu Görünüm"
                            >
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </button>
                            
                            {{-- Detaylı Görünüm İkonu --}}
                            <button
                                type="button"
                                wire:click="$set('viewMode', 'detailed')"
                                style="padding: 0.5rem 0.75rem; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; {{ $this->viewMode === 'detailed' ? 'background: #10b981; color: white; box-shadow: 0 1px 2px rgba(0,0,0,0.1);' : 'background: transparent; color: #6b7280;' }}"
                                title="Detaylı Görünüm"
                            >
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Sağ Taraf - Ödeme Filtreleri --}}
                    <div style="display: inline-flex; background: #f3f4f6; border-radius: 0.5rem; padding: 0.25rem; border: 1px solid #e5e7eb;">
                        {{-- Tümü --}}
                        <button
                            type="button"
                            wire:click="$set('paymentFilter', 'all')"
                            style="padding: 0.5rem 0.75rem; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.875rem; font-weight: 500; {{ $this->paymentFilter === 'all' ? 'background: #10b981; color: white; box-shadow: 0 1px 2px rgba(0,0,0,0.1);' : 'background: transparent; color: #6b7280;' }}"
                        >
                            Tümü
                        </button>
                        
                        {{-- Kalan --}}
                        <button
                            type="button"
                            wire:click="$set('paymentFilter', 'unpaid')"
                            style="padding: 0.5rem 0.75rem; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.875rem; font-weight: 500; {{ $this->paymentFilter === 'unpaid' ? 'background: #f59e0b; color: white; box-shadow: 0 1px 2px rgba(0,0,0,0.1);' : 'background: transparent; color: #6b7280;' }}"
                        >
                            Kalan
                        </button>
                        
                        {{-- Ödenen --}}
                        <button
                            type="button"
                            wire:click="$set('paymentFilter', 'paid')"
                            style="padding: 0.5rem 0.75rem; border-radius: 0.375rem; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.875rem; font-weight: 500; {{ $this->paymentFilter === 'paid' ? 'background: #10b981; color: white; box-shadow: 0 1px 2px rgba(0,0,0,0.1);' : 'background: transparent; color: #6b7280;' }}"
                        >
                            Ödenen
                        </button>
                    </div>
                </div>
            </x-slot>

            @if($this->viewMode === 'grouped')
                {{-- Gruplu Görünüm --}}
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @php
                    // Önce ödeme durumuna göre filtrele
                    $filteredItems = $shopcart->items;
                    if ($this->paymentFilter === 'paid') {
                        $filteredItems = $filteredItems->where('is_paid', 1);
                    } elseif ($this->paymentFilter === 'unpaid') {
                        $filteredItems = $filteredItems->where('is_paid', 0);
                    }
                    
                    // Sonra ürünleri grupla
                    $groupedItems = $filteredItems->groupBy('product_id')->map(function($items) use ($shopcart) {
                        $firstItem = $items->first();
                        
                        // Bu ürünün tüm itemlerini al (filtresiz)
                        $allItemsForProduct = $shopcart->items->where('product_id', $firstItem->product_id);
                        $paidCount = $allItemsForProduct->where('is_paid', 1)->count();
                        
                        return [
                            'product' => $firstItem->product,
                            'unit_price' => $firstItem->unit_price,
                            'quantity' => $items->count(),
                            'total_price' => $items->sum('unit_price'),
                            'has_note' => $items->whereNotNull('note')->isNotEmpty(),
                            'notes' => $items->whereNotNull('note')->pluck('note')->unique(),
                            'paid_count' => $paidCount,
                        ];
                    });
                @endphp

                @if($groupedItems->isEmpty())
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <p style="font-size: 0.875rem;">
                            @if($this->paymentFilter === 'paid')
                                Henüz ödenmiş ürün bulunmuyor.
                            @elseif($this->paymentFilter === 'unpaid')
                                Henüz ödenmemiş ürün bulunmuyor.
                            @else
                                Henüz ürün eklenmemiş.
                            @endif
                        </p>
                    </div>
                @endif

                @foreach($groupedItems as $item)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                {{-- Ürün Görseli --}}
                                <div style="position: relative;">
                                    <img 
                                        src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : asset('image/null.png') }}" 
                                        alt="{{ $item['product']->name }}"
                                        style="width: 64px; height: 64px; object-fit: cover; border-radius: 0.5rem;"
                                    >
                                    {{-- Quantity Badge --}}
                                    @if($item['quantity'] > 1)
                                        <div style="position: absolute; top: -8px; right: -8px; background: #10b981; color: white; border-radius: 9999px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            x{{ $item['quantity'] }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Ürün Bilgileri --}}
                                <div>
                                    <h3 style="font-weight: 600; color: #111827; margin-bottom: 0.25rem;">
                                        {{ $item['product']->name }}
                                        @if($item['quantity'] > 1)
                                            <span style="color: #059669; font-weight: 700;">x{{ $item['quantity'] }}</span>
                                        @endif
                                        {{-- Ödenen sayısını sadece "Tümü" sekmesinde göster --}}
                                        @if($this->paymentFilter === 'all' && $item['paid_count'] > 0)
                                            <span style="display: inline-block; margin-left: 0.5rem; padding: 0.25rem 0.5rem; background: #fee2e2; color: #991b1b; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;">
                                                x{{ $item['paid_count'] }} Ödendi
                                            </span>
                                        @endif
                                    </h3>
                                    <p style="font-size: 0.875rem; color: #6b7280;">
                                        Birim Fiyat: {{ number_format($item['unit_price'], 2) }} ₺
                                    </p>
                                    @if($item['has_note'])
                                        @foreach($item['notes'] as $note)
                                            <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
                                                Not: {{ $note }}
                                            </p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            {{-- Toplam Fiyat --}}
                            <div style="text-align: right;">
                                <p style="font-size: 1.5rem; font-weight: 700; color: #10b981;">
                                    {{ number_format($item['total_price'], 2) }} ₺
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Detaylı Görünüm (Her item ayrı) --}}
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @php
                        // Ödeme durumuna göre filtrele
                        $detailedItems = $shopcart->items;
                        if ($this->paymentFilter === 'paid') {
                            $detailedItems = $detailedItems->where('is_paid', 1);
                        } elseif ($this->paymentFilter === 'unpaid') {
                            $detailedItems = $detailedItems->where('is_paid', 0);
                        }
                    @endphp
                    
                    @if($detailedItems->isEmpty())
                        <div style="text-align: center; padding: 2rem; color: #6b7280;">
                            <p style="font-size: 0.875rem;">
                                @if($this->paymentFilter === 'paid')
                                    Henüz ödenmiş ürün bulunmuyor.
                                @elseif($this->paymentFilter === 'unpaid')
                                    Henüz ödenmemiş ürün bulunmuyor.
                                @else
                                    Henüz ürün eklenmemiş.
                                @endif
                            </p>
                        </div>
                    @endif
                    
                    @foreach($detailedItems as $item)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                {{-- Ürün Görseli --}}
                                <img 
                                    src="{{ $item->product->image ? asset('storage/' . $item->product->image) : asset('image/null.png') }}" 
                                    alt="{{ $item->product->name }}"
                                    style="width: 64px; height: 64px; object-fit: cover; border-radius: 0.5rem;"
                                >

                                {{-- Ürün Bilgileri --}}
                                <div>
                                    <h3 style="font-weight: 600; color: #111827; margin-bottom: 0.25rem;">
                                        {{ $item->product->name }}
                                    </h3>
                                    <p style="font-size: 0.875rem; color: #6b7280;">
                                        Birim Fiyat: {{ number_format($item->unit_price, 2) }} ₺
                                    </p>
                                    @if($item->note)
                                        <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
                                            Not: {{ $item->note }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Fiyat --}}
                            <div style="text-align: right;">
                                <p style="font-size: 1.125rem; font-weight: 700; color: #111827;">
                                    {{ number_format($item->unit_price, 2) }} ₺
                                </p>
                                @if($item->is_paid)
                                    <span style="display: inline-block; margin-top: 0.5rem; padding: 0.25rem 0.75rem; background: #d1fae5; color: #065f46; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                        Ödendi
                                    </span>
                                @else
                                    <span style="display: inline-block; margin-top: 0.5rem; padding: 0.25rem 0.75rem; background: #fef3c7; color: #92400e; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                        Ödenmedi
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Toplam Tutar --}}
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        @php
                            // Filtreye göre toplam hesapla
                            if ($this->paymentFilter === 'paid') {
                                $displayTotal = $shopcart->items->where('is_paid', 1)->sum('unit_price');
                                $label = 'Ödenen Tutar';
                            } elseif ($this->paymentFilter === 'unpaid') {
                                $displayTotal = $shopcart->items->where('is_paid', 0)->sum('unit_price');
                                $label = 'Kalan Tutar';
                            } else {
                                $displayTotal = $shopcart->total_amount;
                                $label = 'Toplam Tutar';
                            }
                        @endphp
                        <p style="font-size: 0.875rem; color: #6b7280;">{{ $label }}</p>
                        @if($this->paymentFilter === 'all' && $shopcart->paid_amount > 0)
                            <p style="font-size: 0.75rem; color: #9ca3af;">
                                Ödenen: {{ number_format($shopcart->paid_amount, 2) }} ₺
                            </p>
                        @endif
                    </div>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #10b981;">
                        {{ number_format($displayTotal, 2) }} ₺
                    </p>
                </div>
            </div>
        </x-filament::section>
    @else
        {{-- Adisyon Yok - Boş Durum --}}
        <x-filament::section>
            <div style="text-align: center; padding: 3rem 0;">
                <svg style="width: 96px; height: 96px; margin: 0 auto 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                
                <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                    Henüz Sipariş Yok
                </h2>
                
                <p style="color: #6b7280; margin-bottom: 1.5rem;">
                    Bu masaya henüz ürün eklenmemiş. Sağ üstteki "Ürün Ekle" butonunu kullanarak sipariş oluşturabilirsiniz.
                </p>
            </div>
        </x-filament::section>
    @endif

    {{-- Alt Butonlar --}}
    <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
        <x-filament::button
            :href="\App\Filament\Resources\Tables\TableResource::getUrl('index')"
            color="gray"
            icon="heroicon-o-arrow-left"
        >
            Masalara Dön
        </x-filament::button>

        @if($shopcart && $shopcart->items->count() > 0)
            <x-filament::button
                :href="$this->getPaymentUrl()"
                color="success"
                icon="heroicon-o-credit-card"
                tag="a"
            >
                Ödeme Yap
            </x-filament::button>
        @endif
    </div>
</x-filament-panels::page>

