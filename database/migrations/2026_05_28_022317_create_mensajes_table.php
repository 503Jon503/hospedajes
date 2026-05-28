<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained()->onDelete('cascade');
            $table->foreignId('remitente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('destinatario_id')->constrained('users')->onDelete('cascade');
            $table->text('mensaje');
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};