<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shopcart_items', function (Blueprint $table) {
            $table->string('kitchen_status')->default('waiting')->after('payment_type');
            // waiting = Bekliyor (mutfağa düştü)
            // preparing = Hazırlanıyor
            // ready = Hazır
            // served = Teslim Edildi (mutfakta görünmez)
            // cancelled = İptal (mutfakta görünmez)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopcart_items', function (Blueprint $table) {
            $table->dropColumn('kitchen_status');
        });
    }
};

