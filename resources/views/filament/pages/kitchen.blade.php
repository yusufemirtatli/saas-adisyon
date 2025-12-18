<x-filament-panels::page>
    <style>
        .kitchen-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 1.25rem;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .kitchen-layout {
                grid-template-columns: 1fr;
            }
        }

        .kitchen-sidebar {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            position: sticky;
            top: 1rem;
        }

        .sidebar-header {
            padding: 0.85rem 1rem;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
        }

        .sidebar-title {
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-content {
            /* Scroll kaldırıldı */
        }

        .category-group {
            border-bottom: 1px solid #f3f4f6;
        }

        .category-group:last-child {
            border-bottom: none;
        }

        .category-header {
            padding: 0.6rem 1rem;
            background: #f9fafb;
            font-weight: 700;
            font-size: 0.75rem;
            color: #374151;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            transition: background 0.15s;
            user-select: none;
        }

        .category-header:hover {
            background: #f3f4f6;
        }

        .category-header svg {
            width: 0.9rem;
            height: 0.9rem;
            color: #6b7280;
        }

        .category-header .toggle-icon {
            margin-left: auto;
            transition: transform 0.2s;
        }

        .category-header.collapsed .toggle-icon {
            transform: rotate(-90deg);
        }

        .category-items {
            overflow: hidden;
            transition: max-height 0.25s ease-out;
        }

        .category-items.collapsed {
            max-height: 0 !important;
        }

        .product-item {
            padding: 0.5rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f3f4f6;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-item:hover {
            background: #fefce8;
        }

        .product-name {
            font-size: 0.8rem;
            color: #374151;
            font-weight: 500;
        }

        .product-count {
            min-width: 1.75rem;
            height: 1.75rem;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border-radius: 0.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.8rem;
        }

        .product-count.high {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .sidebar-footer {
            padding: 0.75rem 1rem;
            background: #f3f4f6;
            border-top: 1px solid #e5e7eb;
        }

        .total-orders {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
        }

        .total-number {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1f2937;
        }

        .kitchen-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            align-items: start;
        }

        @media (max-width: 1400px) {
            .kitchen-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .kitchen-container {
                grid-template-columns: 1fr;
            }
        }

        .order-card {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            transition: all 0.2s;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
        }

        .order-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
        }

        .order-card.status-waiting {
            border-color: #fbbf24;
        }

        .order-card.status-preparing {
            border-color: #3b82f6;
        }

        .order-header {
            padding: 0.65rem 0.85rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-header.status-waiting {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }

        .order-header.status-preparing {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }

        .table-name {
            font-weight: 800;
            font-size: 1rem;
            color: #1f2937;
        }

        .order-time {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 2rem;
            background: rgba(0, 0, 0, 0.1);
        }

        .order-time.urgent {
            background: #dc2626;
            color: white;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .order-items {
            padding: 0.6rem 0.85rem;
            flex: 1;
        }

        .order-item {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            padding: 0.4rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-quantity {
            min-width: 1.5rem;
            height: 1.5rem;
            background: #111827;
            color: white;
            border-radius: 0.35rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.85rem;
        }

        .item-note {
            font-size: 0.7rem;
            color: #dc2626;
            font-weight: 500;
            margin-top: 0.1rem;
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }

        .order-footer {
            margin-top: auto;
        }

        .waiter-info {
            padding: 0.5rem 0.85rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            color: #6b7280;
        }

        .waiter-info svg {
            width: 0.85rem;
            height: 0.85rem;
            color: #9ca3af;
        }

        .waiter-name {
            font-weight: 600;
            color: #374151;
        }

        .order-actions {
            padding: 0.6rem 0.85rem;
            background: #f3f4f6;
            display: flex;
            gap: 0.35rem;
        }

        .action-btn {
            flex: 1;
            padding: 0.5rem 0.6rem;
            border: none;
            border-radius: 0.4rem;
            font-weight: 700;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .action-btn.success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
        }

        .action-btn.secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.5rem;
            border-radius: 2rem;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-badge.waiting {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.preparing {
            background: #dbeafe;
            color: #1e40af;
        }

        .kitchen-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .kitchen-stats {
            display: flex;
            gap: 0.75rem;
        }

        .stat-box {
            padding: 0.5rem 1rem;
            border-radius: 0.6rem;
            text-align: center;
            min-width: 80px;
        }

        .stat-box.waiting {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }

        .stat-box.preparing {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1f2937;
        }

        .stat-label {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
        }

        .filter-tabs {
            display: flex;
            gap: 0.35rem;
            background: #f3f4f6;
            padding: 0.25rem;
            border-radius: 0.6rem;
        }

        .filter-tab {
            padding: 0.4rem 0.85rem;
            border: none;
            border-radius: 0.4rem;
            font-weight: 600;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.15s;
            background: transparent;
            color: #6b7280;
        }

        .filter-tab.active {
            background: white;
            color: #1f2937;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .filter-tab:hover:not(.active) {
            color: #1f2937;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>

    @php
        $orders = $this->getOrders();
        $waitingCount = collect($orders)->where('status', 'waiting')->count();
        $preparingCount = collect($orders)->where('status', 'preparing')->count();
        
        // Ürün sayılarını hesapla (sadece bekleyen ve hazırlanan siparişler)
        $activeOrders = collect($orders)->whereIn('status', ['waiting', 'preparing']);
        $productCounts = [];
        $productCategories = [];
        
        foreach ($activeOrders as $order) {
            foreach ($order['items'] as $item) {
                $productName = $item['name'];
                $categoryName = $item['category'] ?? 'Diğer';
                
                if (!isset($productCounts[$productName])) {
                    $productCounts[$productName] = 0;
                    $productCategories[$productName] = $categoryName;
                }
                $productCounts[$productName] += $item['quantity'];
            }
        }
        arsort($productCounts); // En çok siparişten en aza sırala
        $totalProducts = array_sum($productCounts);
        
        // Kategorilere göre grupla
        $categories = [];
        foreach ($productCounts as $productName => $count) {
            $categoryName = $productCategories[$productName] ?? 'Diğer';
            if (!isset($categories[$categoryName])) {
                $categories[$categoryName] = [];
            }
            $categories[$categoryName][] = $productName;
        }
    @endphp

    {{-- Header --}}
    <div class="kitchen-header">
        <div class="kitchen-stats">
            <div class="stat-box waiting">
                <div class="stat-number">{{ $waitingCount }}</div>
                <div class="stat-label">Bekleyen</div>
            </div>
            <div class="stat-box preparing">
                <div class="stat-number">{{ $preparingCount }}</div>
                <div class="stat-label">Hazırlanıyor</div>
            </div>
        </div>

        <div class="filter-tabs">
            <button class="filter-tab active">Tümü</button>
            <button class="filter-tab">Bekleyen</button>
            <button class="filter-tab">Hazırlanıyor</button>
        </div>
    </div>

    {{-- Main Layout --}}
    <div class="kitchen-layout">
        {{-- Sol Sidebar: Ürün Özeti --}}
        <div class="kitchen-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.1rem; height: 1.1rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                    </svg>
                    Sipariş Özeti
                </div>
            </div>
            
            <div class="sidebar-content">
                @forelse($categories as $categoryName => $categoryProducts)
                    <div class="category-group">
                        <div class="category-header" onclick="toggleCategory(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            </svg>
                            {{ $categoryName }}
                            <svg class="toggle-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 0.85rem; height: 0.85rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        
                        <div class="category-items">
                            @foreach($categoryProducts as $productName)
                                <div class="product-item">
                                    <span class="product-name">{{ $productName }}</span>
                                    <span class="product-count {{ $productCounts[$productName] >= 5 ? 'high' : '' }}">{{ $productCounts[$productName] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div style="padding: 1.5rem 1rem; text-align: center; color: #9ca3af;">
                        <p style="font-size: 0.8rem;">Aktif sipariş yok</p>
                    </div>
                @endforelse
            </div>
            
            <div class="sidebar-footer">
                <div class="total-orders">
                    <span class="total-label">Toplam Ürün</span>
                    <span class="total-number">{{ $totalProducts }}</span>
                </div>
            </div>
        </div>

        {{-- Sağ: Sipariş Kartları --}}
        <div class="kitchen-container">
        @foreach($orders as $order)
            <div class="order-card status-{{ $order['status'] }}">
                {{-- Header --}}
                <div class="order-header status-{{ $order['status'] }}">
                    <div>
                        <div class="table-name">{{ $order['table_name'] }} <span style="font-weight: 500; font-size: 0.75rem; color: #6b7280;">#{{ $order['id'] }}</span></div>
                        <span class="status-badge {{ $order['status'] }}">
                            @if($order['status'] === 'waiting')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.9rem; height: 0.9rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Bekliyor
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.9rem; height: 0.9rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                </svg>
                                Hazırlanıyor
                            @endif
                        </span>
                    </div>
                    <div class="order-time {{ $order['minutes'] >= 15 ? 'urgent' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $order['time'] }}
                    </div>
                </div>

                {{-- Items --}}
                <div class="order-items">
                    @foreach($order['items'] as $item)
                        <div class="order-item">
                            <div class="item-quantity">{{ $item['quantity'] }}</div>
                            <div class="item-details">
                                <div class="item-name">{{ $item['name'] }}</div>
                                @if($item['note'])
                                    <div class="item-note">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.85rem; height: 0.85rem;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                        {{ $item['note'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer (Garson + Butonlar) --}}
                <div class="order-footer">
                    {{-- Garson Bilgisi --}}
                    <div class="waiter-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Garson: <span class="waiter-name">{{ $order['waiter'] }}</span>
                    </div>

                    {{-- Actions --}}
                    <div class="order-actions">
                        @if($order['status'] === 'waiting')
                            <button class="action-btn primary" onclick="startPreparing({{ $order['id'] }}, this)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.9rem; height: 0.9rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                </svg>
                                Hazırla
                            </button>
                        @else
                            <button class="action-btn success" onclick="markReady({{ $order['id'] }}, this)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.9rem; height: 0.9rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Hazır
                            </button>
                            <button class="action-btn secondary" onclick="cancelOrder({{ $order['id'] }}, this)">
                                İptal
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if(count($orders) === 0)
        <div class="empty-state">
            <div class="empty-state-icon">🍳</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #374151; margin-bottom: 0.5rem;">Sipariş Yok</h3>
            <p>Şu anda bekleyen sipariş bulunmuyor.</p>
        </div>
    @endif

    <script>
        // CSRF Token
        const csrfToken = '{{ csrf_token() }}';

        function toggleCategory(header) {
            const categoryItems = header.nextElementSibling;
            const isCollapsed = header.classList.contains('collapsed');
            
            if (isCollapsed) {
                header.classList.remove('collapsed');
                categoryItems.classList.remove('collapsed');
                categoryItems.style.maxHeight = categoryItems.scrollHeight + 'px';
            } else {
                header.classList.add('collapsed');
                categoryItems.style.maxHeight = categoryItems.scrollHeight + 'px';
                categoryItems.offsetHeight;
                categoryItems.classList.add('collapsed');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.category-items').forEach(function(items) {
                items.style.maxHeight = items.scrollHeight + 'px';
            });
        });

        // Kitchen Actions - Hızlı JavaScript ile
        function getCard(button) {
            return button.closest('.order-card');
        }

        function showToast(message, type = 'success') {
            // Basit toast notification
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed; top: 1rem; right: 1rem; z-index: 9999;
                padding: 0.75rem 1.25rem; border-radius: 0.5rem;
                color: white; font-weight: 600; font-size: 0.875rem;
                animation: slideIn 0.3s ease;
                background: ${type === 'success' ? '#22c55e' : type === 'warning' ? '#f59e0b' : '#ef4444'};
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

        async function startPreparing(shopcartId, button) {
            const card = getCard(button);
            
            // Anında UI güncelle
            card.classList.remove('status-waiting');
            card.classList.add('status-preparing');
            card.querySelector('.order-header').classList.remove('status-waiting');
            card.querySelector('.order-header').classList.add('status-preparing');
            card.querySelector('.status-badge').classList.remove('waiting');
            card.querySelector('.status-badge').classList.add('preparing');
            card.querySelector('.status-badge').innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.9rem; height: 0.9rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                </svg>
                Hazırlanıyor
            `;
            
            // Butonları güncelle
            const actionsDiv = card.querySelector('.order-actions');
            actionsDiv.innerHTML = `
                <button class="action-btn success" onclick="markReady(${shopcartId}, this)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.9rem; height: 0.9rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Hazır
                </button>
                <button class="action-btn secondary" onclick="cancelOrder(${shopcartId}, this)">
                    İptal
                </button>
            `;

            // İstatistikleri güncelle
            updateStats();

            // API çağrısı (arka planda)
            try {
                const response = await fetch(`/api/kitchen/start-preparing/${shopcartId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showToast(data.message, 'success');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function markReady(shopcartId, button) {
            const card = getCard(button);
            
            // Anında kartı kaldır (animasyonlu)
            card.style.transform = 'scale(0.9)';
            card.style.opacity = '0';
            card.style.transition = 'all 0.3s ease';
            
            setTimeout(() => {
                card.remove();
                updateStats();
                checkEmptyState();
            }, 300);

            // API çağrısı (arka planda)
            try {
                const response = await fetch(`/api/kitchen/mark-ready/${shopcartId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showToast(data.message, 'success');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function cancelOrder(shopcartId, button) {
            // Onay popup'ı
            if (!confirm('Bu siparişi iptal etmek istediğinize emin misiniz?')) {
                return;
            }

            const card = getCard(button);
            
            // Anında kartı kaldır (animasyonlu)
            card.style.transform = 'scale(0.9)';
            card.style.opacity = '0';
            card.style.transition = 'all 0.3s ease';
            
            setTimeout(() => {
                card.remove();
                updateStats();
                checkEmptyState();
            }, 300);

            // API çağrısı (arka planda)
            try {
                const response = await fetch(`/api/kitchen/cancel/${shopcartId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showToast(data.message, 'warning');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function updateStats() {
            const waitingCards = document.querySelectorAll('.order-card.status-waiting').length;
            const preparingCards = document.querySelectorAll('.order-card.status-preparing').length;
            
            const waitingStat = document.querySelector('.stat-box.waiting .stat-number');
            const preparingStat = document.querySelector('.stat-box.preparing .stat-number');
            
            if (waitingStat) waitingStat.textContent = waitingCards;
            if (preparingStat) preparingStat.textContent = preparingCards;
        }

        function checkEmptyState() {
            const container = document.querySelector('.kitchen-container');
            const cards = container.querySelectorAll('.order-card');
            
            if (cards.length === 0) {
                // Sayfayı yenile boş state görmek için
                location.reload();
            }
        }
    </script>
</x-filament-panels::page>

