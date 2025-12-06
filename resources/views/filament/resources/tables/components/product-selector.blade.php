<div style="padding: 1rem;" 
    x-data="{ 
        cart: {},
        selectedCategory: 'all',
        searchQuery: '',
        getQuantity(id) {
            return this.cart[id] || 0;
        },
        increase(id) {
            if (!this.cart[id]) this.cart[id] = 0;
            this.cart[id]++;
            $wire.call('increaseQuantity', id);
            
            // Parlatma efekti
            const card = document.querySelector(`[data-product-id='${id}']`);
            if (card) {
                card.classList.add('flash-effect');
                setTimeout(() => card.classList.remove('flash-effect'), 400);
            }
        },
        decrease(id) {
            if (this.cart[id] && this.cart[id] > 0) {
                this.cart[id]--;
                if (this.cart[id] === 0) {
                    delete this.cart[id];
                }
                $wire.call('decreaseQuantity', id);
                
                // Kırmızı parlatma efekti
                const card = document.querySelector(`[data-product-id='${id}']`);
                if (card) {
                    card.classList.add('flash-effect-red');
                    setTimeout(() => card.classList.remove('flash-effect-red'), 400);
                }
            }
        },
        filterByCategory(categoryId) {
            this.selectedCategory = categoryId;
        },
        matchesSearch(productName) {
            if (!this.searchQuery) return true;
            return productName.toLowerCase().includes(this.searchQuery.toLowerCase());
        }
    }">
    
    <style>
        @keyframes flash {
            0% { background: white; }
            50% { background: #d1fae5; }
            100% { background: white; }
        }
        
        @keyframes flash-red {
            0% { background: white; }
            50% { background: #fee2e2; }
            100% { background: white; }
        }
        
        .flash-effect {
            animation: flash 0.4s ease-in-out;
        }
        
        .flash-effect-red {
            animation: flash-red 0.4s ease-in-out;
        }
        
        .category-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: 2px solid #e5e7eb;
            background: white;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        
        .category-btn:hover {
            border-color: #10b981;
            background: #f0fdf4;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .category-btn.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-color: #059669;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.5);
        }
        
        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-color: #10b981;
        }
    </style>
    
    @if($products->isEmpty())
        <div style="text-align: center; padding: 3rem 0;">
            <svg style="width: 48px; height: 48px; margin: 0 auto; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 style="margin-top: 0.5rem; font-size: 0.875rem; font-weight: 600;">Ürün Bulunamadı</h3>
            <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280;">Henüz aktif ürün bulunmuyor.</p>
        </div>
    @else
        {{-- Kategori Filtreleri --}}
        @php
            $categories = $products->pluck('productCategory')->unique('id')->filter();
        @endphp
        
        @if($categories->isNotEmpty())
            <div style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    {{-- Sol Taraf: Kategoriler --}}
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; flex: 1;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: #6b7280; margin-right: 0.5rem;">Kategoriler:</span>
                        
                        {{-- Hepsi Butonu --}}
                        <button
                            type="button"
                            class="category-btn"
                            :class="{ 'active': selectedCategory === 'all' }"
                            @click="filterByCategory('all')"
                        >
                            Hepsi
                        </button>
                        
                        {{-- Kategori Butonları --}}
                        @foreach($categories as $category)
                            <button
                                type="button"
                                class="category-btn"
                                :class="{ 'active': selectedCategory === {{ $category->id }} }"
                                @click="filterByCategory({{ $category->id }})"
                            >
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                    
                    {{-- Sağ Taraf: Arama Kutusu --}}
                    <div style="position: relative; min-width: 250px;">
                        <input 
                            type="text" 
                            x-model="searchQuery"
                            placeholder="Ürün ara..."
                            style="width: 100%; padding: 0.625rem 2.5rem 0.625rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;"
                            onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)'"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                        >
                        <svg 
                            style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #9ca3af; pointer-events: none;"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        @endif
        
        {{-- Ürün Grid --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
            @foreach($products as $product)
                <div 
                    class="product-card"
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ strtolower($product->name) }}"
                    style="position: relative; display: flex; flex-direction: column; align-items: center; padding: 1rem; background: white; border-radius: 0.5rem; border: 2px solid #e5e7eb;"
                    x-show="(selectedCategory === 'all' || selectedCategory === {{ $product->product_category_id ?? 'null' }}) && matchesSearch('{{ addslashes($product->name) }}')"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                >
                    {{-- Görünmez Tıklanabilir Alanlar --}}
                    {{-- Sol Yarı - Azaltma --}}
                    <div 
                        @click="decrease({{ $product->id }})"
                        style="position: absolute; top: 0; left: 0; width: 50%; height: 100%; cursor: pointer; z-index: 5;"
                        x-show="getQuantity({{ $product->id }}) > 0"
                    ></div>
                    
                    {{-- Sağ Yarı - Arttırma --}}
                    <div 
                        @click="increase({{ $product->id }})"
                        style="position: absolute; top: 0; right: 0; width: 50%; height: 100%; cursor: pointer; z-index: 5;"
                    ></div>
                    
                    {{-- Eksi Butonu (Sol Üst) --}}
                    <button
                        type="button"
                        @click="decrease({{ $product->id }})"
                        style="position: absolute; top: 0.5rem; left: 0.5rem; background: #ef4444; color: white; border-radius: 9999px; padding: 0.25rem; border: none; cursor: pointer; z-index: 10; transition: all 0.2s;"
                        x-show="getQuantity({{ $product->id }}) > 0"
                        onmouseover="this.style.transform='scale(1.1)'"
                        onmouseout="this.style.transform='scale(1)'"
                    >
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>

                    {{-- Plus Butonu (Sağ Üst) --}}
                    <button
                        type="button"
                        @click="increase({{ $product->id }})"
                        style="position: absolute; top: 0.5rem; right: 0.5rem; background: #10b981; color: white; border-radius: 9999px; padding: 0.25rem; border: none; cursor: pointer; z-index: 10; transition: all 0.2s;"
                        onmouseover="this.style.transform='scale(1.1)'"
                        onmouseout="this.style.transform='scale(1)'"
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
                        <div 
                            style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 700; margin-top: 0.25rem;" 
                            x-show="getQuantity({{ $product->id }}) > 0"
                            x-transition
                        >
                            <span x-text="getQuantity({{ $product->id }})"></span>x
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

