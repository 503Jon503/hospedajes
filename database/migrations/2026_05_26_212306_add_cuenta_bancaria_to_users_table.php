<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cuenta_bancaria')->nullable()->after('rol');
            $table->string('banco')->nullable()->after('cuenta_bancaria');
            $table->string('nombre_cuenta')->nullable()->after('banco');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cuenta_bancaria', 'banco', 'nombre_cuenta']);
        });
    }
};