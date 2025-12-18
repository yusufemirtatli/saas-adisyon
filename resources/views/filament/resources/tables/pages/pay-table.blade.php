<x-filament-panels::page>
    @php
        $shopcart = $this->getShopcart();
        $unpaidItems = $shopcart ? $shopcart->items->where('is_paid', false) : collect([]);
        
        // Ürünleri grupla
        $groupedItems = $unpaidItems->groupBy('product_id')->map(function($items) {
            $firstItem = $items->first();
            return [
                'product' => $firstItem->product,
                'items' => $items,
                'quantity' => $items->count(),
                'unit_price' => $firstItem->unit_price,
                'total_price' => $items->sum('unit_price'),
                'item_ids' => $items->pluck('id')->toArray(),
            ];
        });
    @endphp

    <style>
        .payment-item {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .payment-item:hover {
            border-color: #93c5fd;
        }
        
        .payment-item.selected {
            border-color: #3b82f6;
            background: #dbeafe;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .payment-item input[type="checkbox"] {
            width: 1.1rem;
            height: 1.1rem;
            cursor: pointer;
            flex-shrink: 0;
        }
        
        .payment-method-btn {
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }
        
        .payment-method-btn:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        
        .payment-method-btn.active {
            border-color: #3b82f6;
            background: #dbeafe;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .pay-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(to right, #10b981, #059669);
            color: white;
            font-weight: bold;
            font-size: 1.125rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .pay-button:hover:not(:disabled) {
            transform: scale(1.02);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .pay-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .summary-box {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }
        
        .summary-divider {
            border-top: 2px solid #93c5fd;
            margin: 0.75rem 0;
        }
        
        .quantity-btn {
            transition: all 0.2s;
        }
        
        .quantity-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .quantity-btn:active {
            transform: scale(0.95);
        }
        
        .minus-btn:hover {
            background: #dc2626 !important;
        }
        
        .plus-btn:hover {
            background: #059669 !important;
        }
    </style>

    @if(!$shopcart || $unpaidItems->isEmpty())
        {{-- Ödeme Yapılacak Ürün Yok --}}
        <x-filament::section>
            <div style="text-align: center; padding: 3rem 0;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">
                    Ödeme Yapılacak Ürün Yok
                </h2>
                <p style="font-size: 1rem; color: #6b7280; margin-bottom: 2rem;">
                    Bu masada ödenmemiş ürün bulunmuyor.
                </p>
                <x-filament::button
                    :href="\App\Filament\Resources\Tables\TableResource::getUrl('view', ['record' => $this->record])"
                    color="gray"
                    icon="heroicon-o-arrow-left"
                >
                    Masa Sayfasına Dön
                </x-filament::button>
            </div>
        </x-filament::section>
    @else
        {{-- Ödeme Sayfası --}}
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
            @if($unpaidItems->count() > 0)
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                    {{-- Sol Panel: Ürünler --}}
                    <div>
                        <x-filament::section>
                            <x-slot name="heading">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-weight: 700;">Ödenmemiş Ürünler</span>
                                    <span style="font-size: 0.875rem; color: #6b7280;">({{ $groupedItems->count() }} çeşit - {{ $unpaidItems->count() }} adet)</span>
                                </div>
                            </x-slot>

                            <div id="products-container">
                                {{-- Tümünü Seç --}}
                                <div onclick="toggleAll()" style="background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                    <input 
                                        type="checkbox" 
                                        id="select-all"
                                        onclick="event.stopPropagation(); toggleAll()"
                                        style="width: 1.1rem; height: 1.1rem; cursor: pointer;"
                                    >
                                    <label for="select-all" style="font-weight: 600; cursor: pointer; flex: 1; font-size: 0.9rem;">
                                        Tümünü Seç / Seçimi Kaldır
                                    </label>
                                </div>

                                {{-- Ürün Listesi --}}
                                @foreach($groupedItems as $group)
                                    <div 
                                        class="payment-item" 
                                        data-ids="{{ json_encode($group['item_ids']) }}"
                                        data-max-quantity="{{ $group['quantity'] }}"
                                        data-unit-price="{{ $group['unit_price'] }}"
                                    >
                                        <input 
                                            type="checkbox" 
                                            class="item-checkbox"
                                            data-ids="{{ json_encode($group['item_ids']) }}"
                                            onchange="toggleGroup({{ json_encode($group['item_ids']) }})"
                                        >
                                        <div style="flex: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                                <div style="flex: 1;">
                                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                                        <h4 style="font-weight: 700; color: #111827; font-size: 1.05rem; margin: 0;">
                                                            {{ $group['product']->name }}
                                                        </h4>
                                                        @if($group['product']->productCategory)
                                                            <span style="display: inline-block; background: #f3f4f6; color: #6b7280; font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 0.25rem;">
                                                                {{ $group['product']->productCategory->name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0; font-weight: 500;">
                                                        {{ number_format($group['unit_price'], 2) }} ₺
                                                    </p>
                                                </div>
                                                
                                                {{-- Quantity Controls --}}
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <button 
                                                        type="button"
                                                        class="quantity-btn minus-btn"
                                                        data-ids="{{ json_encode($group['item_ids']) }}"
                                                        onclick="changeQuantity({{ json_encode($group['item_ids']) }}, -1)"
                                                        style="width: 1.75rem; height: 1.75rem; background: #ef4444; color: white; border: none; border-radius: 0.25rem; cursor: pointer; font-weight: bold; font-size: 1rem; display: flex; align-items: center; justify-content: center;"
                                                    >
                                                        −
                                                    </button>
                                                    
                                                    <span 
                                                        class="quantity-display"
                                                        data-ids="{{ json_encode($group['item_ids']) }}"
                                                        style="font-size: 0.95rem; font-weight: 700; color: #3b82f6; min-width: 3.5rem; text-align: center;"
                                                    >
                                                        0 / {{ $group['quantity'] }}
                                                    </span>
                                                    
                                                    <button 
                                                        type="button"
                                                        class="quantity-btn plus-btn"
                                                        data-ids="{{ json_encode($group['item_ids']) }}"
                                                        onclick="changeQuantity({{ json_encode($group['item_ids']) }}, 1)"
                                                        style="width: 1.75rem; height: 1.75rem; background: #10b981; color: white; border: none; border-radius: 0.25rem; cursor: pointer; font-weight: bold; font-size: 1rem; display: flex; align-items: center; justify-content: center;"
                                                    >
                                                        +
                                                    </button>
                                                </div>
                                                
                                                <div style="text-align: center; min-width: 5rem;">
                                                    <p 
                                                        class="item-total-price"
                                                        data-ids="{{ json_encode($group['item_ids']) }}"
                                                        style="font-size: 1.1rem; font-weight: 700; color: #111827; margin: 0;"
                                                    >
                                                        0.00 ₺
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-filament::section>

                        {{-- Alt Panel: Toplam ve İşlem Butonları --}}
                        @php
                            $totalTableAmount = $shopcart->total_amount ?? 0;
                            $paidAmount = $shopcart->paid_amount ?? 0;
                            $remainingAmount = $totalTableAmount - $paidAmount;
                        @endphp
                        <div style="margin-top: 1rem; background: white; border: 2px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem;">
                            {{-- Toplam Tutarlar --}}
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb;">
                                <div>
                                    <p style="color: #6b7280; font-size: 0.7rem; font-weight: 500; margin: 0; text-transform: uppercase;">Masa Toplam</p>
                                    <p style="color: #111827; font-size: 1.5rem; font-weight: 800; margin: 0;">{{ number_format($totalTableAmount, 2) }} ₺</p>
                                </div>
                                <div style="display: flex; gap: 1rem;">
                                    <div style="text-align: center; padding: 0.4rem 0.75rem; background: #dcfce7; border-radius: 0.5rem; border: 1px solid #bbf7d0;">
                                        <p style="color: #166534; font-size: 0.6rem; font-weight: 600; margin: 0;">ÖDENEN</p>
                                        <p style="color: #16a34a; font-size: 0.95rem; font-weight: 700; margin: 0;">{{ number_format($paidAmount, 2) }} ₺</p>
                                    </div>
                                    <div style="text-align: center; padding: 0.4rem 0.75rem; background: #ffedd5; border-radius: 0.5rem; border: 1px solid #fed7aa;">
                                        <p style="color: #9a3412; font-size: 0.6rem; font-weight: 600; margin: 0;">KALAN</p>
                                        <p style="color: #ea580c; font-size: 0.95rem; font-weight: 700; margin: 0;">{{ number_format($remainingAmount, 2) }} ₺</p>
                                    </div>
                                </div>
                            </div>

                            {{-- İşlem Butonları --}}
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; margin-bottom: 0.75rem;">
                                <button 
                                    type="button" 
                                    onclick="closeAccount()"
                                    style="padding: 0.5rem; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.75rem; cursor: pointer; transition: all 0.15s;" 
                                    onmouseover="this.style.transform='translateY(-1px)'" 
                                    onmouseout="this.style.transform='translateY(0)'"
                                >
                                    Hesabı Kapat
                                </button>
                                <button type="button" style="padding: 0.5rem; background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.75rem; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                                    Veresiye Yaz
                                </button>
                            </div>

                            {{-- Hesaptan Düş & Hesabı Böl --}}
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                {{-- Sol: Hesaptan Düş --}}
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #374151; margin-bottom: 0.35rem;">Hesaptan Düş</label>
                                    <div style="display: flex; gap: 0.35rem;">
                                        <div style="flex: 1; position: relative;">
                                            <input 
                                                type="number" 
                                                id="custom-amount-input"
                                                placeholder="0.00"
                                                min="0"
                                                step="0.01"
                                                style="width: 100%; padding: 0.4rem 0.5rem; padding-right: 1.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.85rem; font-weight: 600; outline: none; transition: border-color 0.15s;"
                                                onfocus="this.style.borderColor='#3b82f6'"
                                                onblur="this.style.borderColor='#d1d5db'"
                                            >
                                            <span style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; font-weight: 600;">₺</span>
                                        </div>
                                        <button 
                                            type="button" 
                                            onclick="applyCustomAmount()"
                                            style="padding: 0.4rem 0.6rem; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.7rem; cursor: pointer; transition: all 0.15s;"
                                            onmouseover="this.style.transform='translateY(-1px)'"
                                            onmouseout="this.style.transform='translateY(0)'"
                                        >
                                            Düş
                                        </button>
                                    </div>
                                </div>
                                {{-- Sağ: Hesabı Böl --}}
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #374151; margin-bottom: 0.35rem;">Hesabı Böl</label>
                                    <div style="display: flex; gap: 0.35rem;">
                                        <div style="flex: 1; position: relative;">
                                            <input 
                                                type="number" 
                                                id="split-amount-input"
                                                placeholder="2"
                                                min="1"
                                                step="1"
                                                style="width: 100%; padding: 0.4rem 0.5rem; padding-right: 2rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.85rem; font-weight: 600; outline: none; transition: border-color 0.15s;"
                                                onfocus="this.style.borderColor='#7c3aed'"
                                                onblur="this.style.borderColor='#d1d5db'"
                                                oninput="updateSplitPreview()"
                                            >
                                            <span style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.7rem; font-weight: 600;">kişi</span>
                                        </div>
                                        <button 
                                            type="button" 
                                            onclick="applySplitAmount()"
                                            style="padding: 0.4rem 0.6rem; background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; border: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.7rem; cursor: pointer; transition: all 0.15s;"
                                            onmouseover="this.style.transform='translateY(-1px)'"
                                            onmouseout="this.style.transform='translateY(0)'"
                                        >
                                            Böl
                                        </button>
                                    </div>
                                    <p id="split-preview" style="font-size: 0.65rem; color: #6b7280; margin: 0.25rem 0 0 0;">Kişi başı: <span style="font-weight: 700; color: #7c3aed;">{{ number_format($remainingAmount / 2, 2) }} ₺</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sağ Panel: Ödeme Özeti --}}
                    <div>
                        <x-filament::section>
                            <x-slot name="heading">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-weight: 700;">Ödeme Özeti</span>
                                </div>
                            </x-slot>

                            <div>
                                {{-- Özet Kutusu --}}
                                <div class="summary-box">
                                    <div class="summary-row">
                                        <span style="font-size: 0.875rem; font-weight: 500; color: #1e40af;">Seçili Ürün:</span>
                                        <span id="selected-count" style="font-weight: 700; color: #111827;">0 adet</span>
                                    </div>
                                    <div class="summary-divider"></div>
                                    <div class="summary-row">
                                        <span style="font-size: 1.125rem; font-weight: 700; color: #111827;">TOPLAM:</span>
                                        <span id="total-amount" style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">0.00 ₺</span>
                                    </div>
                                </div>

                                {{-- Ödeme Yöntemi --}}
                                <div style="margin-bottom: 1.5rem;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.75rem;">
                                        Ödeme Yöntemi
                                    </label>
                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                                        <div class="payment-method-btn active" data-method="cash" onclick="selectPaymentMethod('cash')">
                                            <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">💵</div>
                                            <div style="font-size: 0.75rem; font-weight: 500;">Nakit</div>
                                        </div>
                                        <div class="payment-method-btn" data-method="card" onclick="selectPaymentMethod('card')">
                                            <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">💳</div>
                                            <div style="font-size: 0.75rem; font-weight: 500;">Kart</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Ödeme Butonları --}}
                                <div style="padding-top: 1rem; border-top: 2px solid #e5e7eb;">
                                    <button
                                        type="button"
                                        id="pay-button"
                                        class="pay-button"
                                        onclick="submitPayment()"
                                        disabled
                                    >
                                        ÖDEMEYE TAMAMLA
                                    </button>

                                    <div style="margin-top: 0.5rem;">
                                        <x-filament::button
                                            :href="\App\Filament\Resources\Tables\TableResource::getUrl('view', ['record' => $this->record])"
                                            color="gray"
                                            icon="heroicon-o-arrow-left"
                                            style="width: 100%;"
                                        >
                                            İptal / Masaya Dön
                                        </x-filament::button>
                                    </div>
                                </div>

                                {{-- Bilgi Notu --}}
                                <div style="margin-top: 1rem; padding: 0.75rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem;">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <span style="color: #3b82f6;">ℹ️</span>
                                        <div style="font-size: 0.75rem; color: #1e40af;">
                                            <p style="font-weight: 600; margin-bottom: 0.25rem;">Kısmi Ödeme</p>
                                            <p>İstediğiniz ürünleri seçerek ödeme yapabilirsiniz.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-filament::section>
                    </div>
                </div>
            @endif
        </div>

        <script>
            let selectedGroups = {}; // { idsString: { ids: [], quantity: 0, unitPrice: 0 } }
            let paymentMethod = 'cash';
            
            // Hesaptan düş fonksiyonu (şimdilik sadece tasarım)
            function applyCustomAmount() {
                const input = document.getElementById('custom-amount-input');
                const amount = parseFloat(input.value);
                if (amount && amount > 0) {
                    console.log('Hesaptan düşülecek tutar:', amount, '₺');
                    // İleride işlevsellik eklenecek
                }
            }
            
            // Hesabı böl önizleme
            const remainingAmount = {{ $remainingAmount }};
            function updateSplitPreview() {
                const input = document.getElementById('split-amount-input');
                const preview = document.getElementById('split-preview');
                const splitBy = parseInt(input.value) || 2;
                if (splitBy > 0) {
                    const perPerson = remainingAmount / splitBy;
                    preview.innerHTML = 'Kişi başı: <span style="font-weight: 700; color: #7c3aed;">' + perPerson.toFixed(2) + ' ₺</span>';
                }
            }
            
            // Hesabı böl fonksiyonu (şimdilik sadece tasarım)
            function applySplitAmount() {
                const input = document.getElementById('split-amount-input');
                const splitBy = parseInt(input.value) || 2;
                if (splitBy > 0) {
                    const perPerson = remainingAmount / splitBy;
                    console.log('Hesap', splitBy, 'kişiye bölündü. Kişi başı:', perPerson.toFixed(2), '₺');
                    // İleride işlevsellik eklenecek
                }
            }
            
            // Hesabı Kapat - Tüm ürünleri ödenmiş yapar ve shopcart'ı kapatır
            function closeAccount() {
                if (confirm('Hesabı kapatmak istediğinize emin misiniz? Tüm ürünler ödenmiş olarak işaretlenecek.')) {
                    @this.call('closeAccount', paymentMethod);
                }
            }

            function changeQuantity(itemIds, change) {
                const idsString = JSON.stringify(itemIds);
                const item = document.querySelector(`.payment-item[data-ids='${idsString}']`);
                const maxQuantity = parseInt(item.dataset.maxQuantity);
                const unitPrice = parseFloat(item.dataset.unitPrice);
                const checkbox = document.querySelector(`.item-checkbox[data-ids='${idsString}']`);
                const quantityDisplay = document.querySelector(`.quantity-display[data-ids='${idsString}']`);
                const totalPriceDisplay = document.querySelector(`.item-total-price[data-ids='${idsString}']`);
                
                // Mevcut quantity'yi al veya 0'dan başlat
                let currentQuantity = selectedGroups[idsString]?.quantity || 0;
                let newQuantity = currentQuantity + change;
                
                // Min 0, Max maxQuantity
                newQuantity = Math.max(0, Math.min(maxQuantity, newQuantity));
                
                // Quantity'yi güncelle
                if (newQuantity > 0) {
                    selectedGroups[idsString] = {
                        ids: itemIds.slice(0, newQuantity), // İlk n item'ı seç
                        quantity: newQuantity,
                        unitPrice: unitPrice
                    };
                    checkbox.checked = true;
                    item.classList.add('selected');
                } else {
                    delete selectedGroups[idsString];
                    checkbox.checked = false;
                    item.classList.remove('selected');
                }
                
                // UI'ı güncelle
                quantityDisplay.textContent = newQuantity + ' / ' + maxQuantity;
                totalPriceDisplay.textContent = (newQuantity * unitPrice).toFixed(2) + ' ₺';
                
                updateSummary();
            }

            function toggleGroup(itemIds) {
                const idsString = JSON.stringify(itemIds);
                const item = document.querySelector(`.payment-item[data-ids='${idsString}']`);
                const checkbox = document.querySelector(`.item-checkbox[data-ids='${idsString}']`);
                const maxQuantity = parseInt(item.dataset.maxQuantity);
                const unitPrice = parseFloat(item.dataset.unitPrice);
                const quantityDisplay = document.querySelector(`.quantity-display[data-ids='${idsString}']`);
                const totalPriceDisplay = document.querySelector(`.item-total-price[data-ids='${idsString}']`);
                
                if (checkbox.checked) {
                    // Tümünü seç
                    selectedGroups[idsString] = {
                        ids: itemIds,
                        quantity: maxQuantity,
                        unitPrice: unitPrice
                    };
                    item.classList.add('selected');
                    quantityDisplay.textContent = maxQuantity + ' / ' + maxQuantity;
                    totalPriceDisplay.textContent = (maxQuantity * unitPrice).toFixed(2) + ' ₺';
                } else {
                    // Seçimi kaldır
                    delete selectedGroups[idsString];
                    item.classList.remove('selected');
                    quantityDisplay.textContent = '0 / ' + maxQuantity;
                    totalPriceDisplay.textContent = '0.00 ₺';
                }
                
                updateSummary();
            }

            function toggleAll() {
                const selectAllCheckbox = document.getElementById('select-all');
                const allCheckboxes = document.querySelectorAll('.item-checkbox');
                const allItems = document.querySelectorAll('.payment-item');
                
                if (selectAllCheckbox.checked) {
                    selectedGroups = {};
                    allCheckboxes.forEach((checkbox, index) => {
                        const itemIds = JSON.parse(checkbox.dataset.ids);
                        const idsString = JSON.stringify(itemIds);
                        const maxQuantity = parseInt(allItems[index].dataset.maxQuantity);
                        const unitPrice = parseFloat(allItems[index].dataset.unitPrice);
                        
                        checkbox.checked = true;
                        allItems[index].classList.add('selected');
                        
                        selectedGroups[idsString] = {
                            ids: itemIds,
                            quantity: maxQuantity,
                            unitPrice: unitPrice
                        };
                        
                        const quantityDisplay = document.querySelector(`.quantity-display[data-ids='${idsString}']`);
                        const totalPriceDisplay = document.querySelector(`.item-total-price[data-ids='${idsString}']`);
                        quantityDisplay.textContent = maxQuantity + ' / ' + maxQuantity;
                        totalPriceDisplay.textContent = (maxQuantity * unitPrice).toFixed(2) + ' ₺';
                    });
                } else {
                    selectedGroups = {};
                    allCheckboxes.forEach((checkbox, index) => {
                        const itemIds = JSON.parse(checkbox.dataset.ids);
                        const idsString = JSON.stringify(itemIds);
                        const maxQuantity = parseInt(allItems[index].dataset.maxQuantity);
                        
                        checkbox.checked = false;
                        allItems[index].classList.remove('selected');
                        
                        const quantityDisplay = document.querySelector(`.quantity-display[data-ids='${idsString}']`);
                        const totalPriceDisplay = document.querySelector(`.item-total-price[data-ids='${idsString}']`);
                        quantityDisplay.textContent = '0 / ' + maxQuantity;
                        totalPriceDisplay.textContent = '0.00 ₺';
                    });
                }
                
                updateSummary();
            }

            function selectPaymentMethod(method) {
                paymentMethod = method;
                
                document.querySelectorAll('.payment-method-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                document.querySelector(`.payment-method-btn[data-method="${method}"]`).classList.add('active');
            }

            function updateSummary() {
                // Seçili grupların toplam ürün sayısı ve fiyatı
                let totalItemCount = 0;
                let total = 0;
                
                Object.values(selectedGroups).forEach(group => {
                    totalItemCount += group.quantity;
                    total += group.quantity * group.unitPrice;
                });
                
                document.getElementById('selected-count').textContent = totalItemCount + ' adet';
                document.getElementById('total-amount').textContent = total.toFixed(2) + ' ₺';
                
                const payButton = document.getElementById('pay-button');
                payButton.disabled = totalItemCount === 0;
                
                // "Tümünü Seç" checkbox'ını güncelle
                const selectAllCheckbox = document.getElementById('select-all');
                const totalGroups = document.querySelectorAll('.item-checkbox').length;
                const selectedGroupsCount = Object.keys(selectedGroups).length;
                
                // Tüm gruplar seçili mi ve hepsi max quantity'de mi kontrol et
                let allMaxSelected = true;
                document.querySelectorAll('.payment-item').forEach(item => {
                    const idsString = item.dataset.ids;
                    const maxQuantity = parseInt(item.dataset.maxQuantity);
                    const group = selectedGroups[idsString];
                    
                    if (!group || group.quantity !== maxQuantity) {
                        allMaxSelected = false;
                    }
                });
                
                selectAllCheckbox.checked = selectedGroupsCount === totalGroups && allMaxSelected;
            }

            function submitPayment() {
                if (Object.keys(selectedGroups).length === 0) {
                    alert('Lütfen en az bir ürün seçin!');
                    return;
                }
                
                // Tüm seçili item ID'lerini düz bir dizi haline getir
                const itemIds = [];
                Object.values(selectedGroups).forEach(group => {
                    itemIds.push(...group.ids);
                });
                
                @this.call('processPayment', itemIds, paymentMethod);
            }
        </script>
    @endif
</x-filament-panels::page>
