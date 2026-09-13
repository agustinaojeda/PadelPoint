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
    Schema::create('canchas', function (Blueprint $table) {
        $table->id();
        
        $table->string('nombre')->unique();
        $table->string('superficie')->nullable(); 
        $table->boolean('es_techada')->default(false);
        $table->boolean('esta_disponible')->default(true);
        $table->decimal('precio', 10, 2); 
        $table->string('imagen_url')->nullable();
        $table->text('descripcion')->nullable();
        
        //turnos
        $table->integer('duracion_turno'); // en minutos
        $table->integer('cantidad_jugadores')->default(4); 
        $table->time('hora_apertura'); 
        $table->time('hora_cierre');  
        $table->json('dias_disponibles'); // del 0 al 6, 0 es domingo y 6 es sábado

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canchas');
    }
};
