<x-filament-panels::page>
    {{-- Header Actions (Create button vb.) --}}
    <x-slot name="headerActions">
        <x-filament::actions :actions="$this->getCachedHeaderActions()" />
    </x-slot>

    {{-- Custom Grid View --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
        @forelse($this->getTableRecords() as $table)
            {{-- Masa Kartı - Yeşil Arkaplan (Müsait) - Tıklanabilir --}}
            <a 
                href="{{ \App\Filament\Resources\Tables\TableResource::getUrl('view', ['record' => $table]) }}"
                style="position: relative; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; padding: 2rem 1rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); min-height: 140px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s; cursor: pointer;"
                onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 12px rgba(0, 0, 0, 0.15)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)';"
            >
                
                {{-- Edit Butonu - Sağ Üst --}}
                <span 
                    onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ \App\Filament\Resources\Tables\TableResource::getUrl('edit', ['record' => $table]) }}';"
                    style="position: absolute; top: 8px; right: 8px; width: 32px; height: 32px; background: rgba(255, 255, 255, 0.9); border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s; z-index: 10;"
                    onmouseover="this.style.background='rgba(255, 255, 255, 1)'; this.style.transform='scale(1.1)';"
                    onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.transform='scale(1)';"
                >
                    <svg style="width: 16px; height: 16px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </span>

                {{-- Masa İsmi - Ortada Büyük --}}
                <h3 style="color: white; font-size: 1.5rem; font-weight: 700; text-align: center; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    {{ $table->name }}
                </h3>
            </a>
        @empty
            {{-- Boş Durum --}}
            <div style="grid-column: 1 / -1;">
                <x-filament::section>
                    <div style="text-align: center; padding: 3rem;">
                        <svg style="width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <h3 style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.5rem;">
                            Henüz masa eklenmemiş
                        </h3>
                        <p style="font-size: 0.875rem; opacity: 0.6;">
                            Yeni bir masa eklemek için yukarıdaki "Yeni" butonunu kullanın.
                        </p>
                    </div>
                </x-filament::section>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($this->getTableRecords()->hasPages())
        <div style="margin-top: 1.5rem;">
            {{ $this->getTableRecords()->links() }}
        </div>
    @endif
</x-filament-panels::page>

