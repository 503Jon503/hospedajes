<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->enum('pago_estado', ['pendiente', 'retenido', 'liberado', 'reembolsado'])->default('pendiente')->after('estado');
            $table->string('stripe_payment_intent')->nullable()->after('pago_estado');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropColumn(['pago_estado', 'stripe_payment_intent']);
        });
    }
};