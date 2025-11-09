<div style="padding: 1rem;" 
    x-data="{ 
        cart: {},
        getQuantity(id) {
            return this.cart[id] || 0;
        },
        increase(id) {
            if (!this.cart[id]) this.cart[id] = 0;
            this.cart[id]++;
            $wire.call('increaseQuantity', id);
        },
        decrease(id) {
            if (this.cart[id] && this.cart[id] > 0) {
                this.cart[id]--;
                if (this.cart[id] === 0) {
                    delete this.cart[id];
                }
                $wire.call('decreaseQuantity', id);
            }
        }
    }">
    @if($products->isEmpty())
        <div style="text-align: center; padding: 3rem 0;">
            <svg style="width: 48px; height: 48px; margin: 0 auto; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 style="margin-top: 0.5rem; font-size: 0.875rem; font-weight: 600;">Ürün Bulunamadı</h3>
            <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280;">Henüz aktif ürün bulunmuyor.</p>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
            @foreach($products as $product)
                <div style="position: relative; display: flex; flex-direction: column; align-items: center; padding: 1rem; background: white; border-radius: 0.5rem; border: 2px solid #e5e7eb; transition: all 0.2s;">
                    
                    {{-- Eksi Butonu (Sol Üst) --}}
                    <button
                        type="button"
                        @click="decrease({{ $product->id }})"
                        style="position: absolute; top: 0.5rem; left: 0.5rem; background: #ef4444; color: white; border-radius: 9999px; padding: 0.25rem; border: none; cursor: pointer; z-index: 10;"
                        x-show="getQuantity({{ $product->id }}) > 0"
                    >
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>

                    {{-- Plus Butonu (Sağ Üst) --}}
                    <button
                        type="button"
                        @click="increase({{ $product->id }})"
                        style="position: absolute; top: 0.5rem; right: 0.5rem; background: #10b981; color: white; border-radius: 9999px; padding: 0.25rem; border: none; cursor: pointer; z-index: 10;"
                    >
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>

                    {{-- Ürün Görseli --}}
                    <div style="width: 100%; padding-bottom: 100%; position: relative; margin-bottom: 0.75rem; border-radius: 0.5rem; overflow: hidden; background: #f3f4f6;">
                        <img 
                            src="{{ $product->image ? asset('storage/' . $product->image) : asset('image/null.png') }}" 
                            alt="{{ $product->name }}"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                        >
                    </div>

                    {{-- Ürün Bilgileri --}}
                    <div style="width: 100%; text-align: center;">
                        <h3 style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $product->name }}
                        </h3>
                        
                        @if($product->productCategory)
                            <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.5rem;">
                                {{ $product->productCategory->name }}
                            </p>
                        @endif

                        <p style="font-size: 1.125rem; font-weight: 700; color: #10b981; margin-bottom: 0.25rem;">
                            {{ number_format($product->price, 2) }} ₺
                        </p>

                        {{-- Quantity Gösterimi --}}
                        <p 
                            style="font-size: 0.875rem; font-weight: 600; color: #059669;" 
                            x-show="getQuantity({{ $product->id }}) > 0"
                            x-text="getQuantity({{ $product->id }}) + 'x'"
                        ></p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

