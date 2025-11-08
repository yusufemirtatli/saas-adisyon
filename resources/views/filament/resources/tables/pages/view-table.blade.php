<x-filament-panels::page>
    {{-- Masa Detay Sayfası --}}
    <div style="text-align: center; padding: 3rem;">
        
        {{-- Masa İsmi - Büyük Başlık --}}
        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 3rem; font-weight: 700; color: #10b981; margin-bottom: 0.5rem;">
                {{ $this->record->name }}
            </h1>
            <p style="font-size: 1.125rem; opacity: 0.6;">
                Masa Detayları
            </p>
        </div>

        {{-- Placeholder İçerik --}}
        <x-filament::section>
            <div style="padding: 4rem 2rem;">
                <svg style="width: 96px; height: 96px; margin: 0 auto 2rem; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                
                <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem;">
                    Bu sayfa yakında özelleştirilecek
                </h2>
                
                <p style="font-size: 1rem; opacity: 0.7; margin-bottom: 2rem;">
                    Burada masa siparişleri, hesap detayları ve diğer özellikler görünecek.
                </p>

                {{-- Geri Dön Butonu --}}
                <x-filament::button
                    :href="\App\Filament\Resources\Tables\TableResource::getUrl('index')"
                    color="gray"
                >
                    <x-slot name="icon">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </x-slot>
                    Masalara Dön
                </x-filament::button>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>

