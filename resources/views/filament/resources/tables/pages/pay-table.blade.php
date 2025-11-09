<x-filament-panels::page>
    {{-- Ödeme Sayfası --}}
    
    @php
        $shopcart = $this->getShopcart();
    @endphp

    <x-filament::section>
        <div style="text-align: center; padding: 3rem 0;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">💳</div>
            <h2 style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">
                Ödeme Sayfası
            </h2>
            <p style="font-size: 1rem; color: #6b7280; margin-bottom: 2rem;">
                {{ $this->record->name }} için ödeme işlemi
            </p>

            @if($shopcart)
                <div style="background: #f3f4f6; border-radius: 0.5rem; padding: 1.5rem; margin: 2rem auto; max-width: 400px;">
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #6b7280; font-weight: 500;">Toplam Ürün:</span>
                        <span style="font-weight: 700; color: #111827;">{{ $shopcart->items->count() }} adet</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #6b7280; font-weight: 500;">Ödenecek Tutar:</span>
                        <span style="font-weight: 700; color: #111827;">{{ number_format($shopcart->total_amount, 2) }} ₺</span>
                    </div>
                    @if($shopcart->paid_amount > 0)
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e5e7eb;">
                            <span style="color: #6b7280; font-weight: 500;">Ödenen:</span>
                            <span style="font-weight: 700; color: #10b981;">{{ number_format($shopcart->paid_amount, 2) }} ₺</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                            <span style="color: #6b7280; font-weight: 500;">Kalan:</span>
                            <span style="font-weight: 700; color: #ef4444;">{{ number_format($shopcart->total_amount - $shopcart->paid_amount, 2) }} ₺</span>
                        </div>
                    @endif
                </div>

                <p style="font-size: 0.875rem; color: #9ca3af; margin-top: 2rem;">
                    Buraya ödeme yöntemleri ve işlemler eklenecek...
                </p>
            @else
                <p style="color: #ef4444; font-weight: 600;">
                    Bu masada aktif adisyon bulunmuyor.
                </p>
            @endif
        </div>
    </x-filament::section>

    {{-- Geri Dön Butonu --}}
    <div style="margin-top: 1.5rem;">
        <x-filament::button
            :href="\App\Filament\Resources\Tables\TableResource::getUrl('view', ['record' => $this->record])"
            color="gray"
            icon="heroicon-o-arrow-left"
        >
            Masa Sayfasına Dön
        </x-filament::button>
    </div>
</x-filament-panels::page>

