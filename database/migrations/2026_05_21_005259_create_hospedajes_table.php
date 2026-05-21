<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospedajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->enum('tipo', ['hotel', 'rancho', 'casa', 'apartamento']);
            $table->text('descripcion');
            $table->string('ubicacion');
            $table->string('departamento');
            $table->decimal('precio_noche', 10, 2);
            $table->integer('capacidad');
            $table->string('imagen')->nullable();
            $table->enum('estado', ['disponible', 'no_disponible'])->default('disponible');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospedajes');
    }
};