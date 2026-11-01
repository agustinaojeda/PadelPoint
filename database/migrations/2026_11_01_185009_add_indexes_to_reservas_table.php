<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->index(['cancha_id', 'fecha_turno'], 'reservas_cancha_fecha_index');
            $table->index(['fecha_turno', 'estado'], 'reservas_fecha_estado_index');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropIndex('reservas_cancha_fecha_index');
            $table->dropIndex('reservas_fecha_estado_index');
        });
    }
};
