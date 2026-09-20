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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cancha_id')->constrained('canchas')->onDelete('cascade');

            $table->date('fecha_turno'); 
            $table->time('hora_inicio');  
            $table->time('hora_fin');   

            $table->string('nombre_cliente');
            $table->string('telefono_cliente');
            $table->decimal('monto_total', 10, 2); 
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'completada'])->default('confirmada');
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
