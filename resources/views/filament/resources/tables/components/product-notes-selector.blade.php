<div style="padding: 1.5rem;">
    <style>
        .product-note-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }
        
        .product-note-item:hover {
            border-color: #10b981;
            background: #f0fdf4;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 0.5rem;
            object-fit: cover;
            background: white;
            border: 2px solid #e5e7eb;
            flex-shrink: 0;
        }
        
        .product-info {
            flex: 1;
            margin: 0 1rem;
        }
        
        .product-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        
        .product-price {
            font-size: 0.875rem;
            color: #10b981;
            font-weight: 600;
        }
        
        .note-input {
            flex: 2;
            padding: 0.625rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .note-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .note-input::placeholder {
            color: #9ca3af;
        }
    </style>
    
    @if(empty($cart))
        <div style="text-align: center; padding: 3rem 0;">
            <svg style="width: 48px; height: 48px; margin: 0 auto; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 style="margin-top: 0.5rem; font-size: 0.875rem; font-weight: 600;">Sepet Boş</h3>
            <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280;">Lütfen önce ürün seçin.</p>
        </div>
    @else
        <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 0.375rem;">
            <p style="font-size: 0.875rem; color: #1e40af; font-weight: 500;">
                <svg style="display: inline-block; width: 16px; height: 16px; margin-right: 0.5rem; vertical-align: text-bottom;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                Aşağıda sadece şu an sepete eklediğiniz ürünler listeleniyor. İstediğiniz ürüne not ekleyin.
            </p>
        </div>
        
        <div style="max-height: 500px; overflow-y: auto; padding-right: 0.5rem;">
            @foreach($cart as $productId => $quantity)
                @php
                    $product = $products->firstWhere('id', $productId);
                @endphp
                
                @if($product)
                    @for($i = 0; $i < $quantity; $i++)
                        <div class="product-note-item">
                            <img 
                                src="{{ $product->image ? asset('storage/' . $product->image) : asset('image/null.png') }}" 
                                alt="{{ $product->name }}"
                                class="product-image"
                            >
                            
                            <div class="product-info">
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-price">{{ number_format($product->price, 2) }} ₺</div>
                            </div>
                            
                            <input 
                                type="text"
                                class="note-input"
                                placeholder="Not ekleyin (opsiyonel)"
                                value="{{ $cartNotes[$productId][$i] ?? '' }}"
                                wire:model.live="cartNotes.{{ $productId }}.{{ $i }}"
                            >
                        </div>
                    @endfor
                @endif
            @endforeach
        </div>
        
        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 2px solid #e5e7eb;">
            <p style="text-align: center; font-size: 0.875rem; color: #6b7280;">
                Toplam <strong>{{ array_sum($cart) }}</strong> ürün listelenmiştir
            </p>
        </div>
    @endif
</div>

