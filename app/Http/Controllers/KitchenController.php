<?php

namespace App\Http\Controllers;

use App\Models\Shopcart;
use App\Models\ShopcartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    /**
     * Siparişi hazırlamaya başla
     */
    public function startPreparing(int $shopcartId): JsonResponse
    {
        $updated = ShopcartItem::where('shopcart_id', $shopcartId)
            ->where('kitchen_status', 'waiting')
            ->update(['kitchen_status' => 'preparing']);

        $shopcart = Shopcart::with('table')->find($shopcartId);

        return response()->json([
            'success' => $updated > 0,
            'message' => ($shopcart->table->name ?? 'Sipariş') . ' hazırlanmaya başlandı.',
        ]);
    }

    /**
     * Siparişi hazır olarak işaretle
     */
    public function markReady(int $shopcartId): JsonResponse
    {
        $updated = ShopcartItem::where('shopcart_id', $shopcartId)
            ->whereIn('kitchen_status', ['waiting', 'preparing'])
            ->update(['kitchen_status' => 'ready']);

        $shopcart = Shopcart::with('table')->find($shopcartId);

        return response()->json([
            'success' => $updated > 0,
            'message' => ($shopcart->table->name ?? 'Sipariş') . ' hazır!',
        ]);
    }

    /**
     * Siparişi iptal et
     */
    public function cancelOrder(int $shopcartId): JsonResponse
    {
        $updated = ShopcartItem::where('shopcart_id', $shopcartId)
            ->whereIn('kitchen_status', ['waiting', 'preparing'])
            ->update(['kitchen_status' => 'cancelled']);

        $shopcart = Shopcart::with('table')->find($shopcartId);

        return response()->json([
            'success' => $updated > 0,
            'message' => ($shopcart->table->name ?? 'Sipariş') . ' iptal edildi.',
        ]);
    }
}

