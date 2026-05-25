<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->boolean('cliente_confirmo_llegada')->default(false)->after('stripe_payment_intent');
            $table->timestamp('fecha_confirmacion_llegada')->nullable()->after('cliente_confirmo_llegada');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropColumn(['cliente_confirmo_llegada', 'fecha_confirmacion_llegada']);
        });
    }
};