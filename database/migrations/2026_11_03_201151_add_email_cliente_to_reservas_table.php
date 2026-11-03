<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->string('email_cliente')->nullable()->after('nombre_cliente');
            $table->string('telefono_cliente', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('reservas')->whereNull('telefono_cliente')->update(['telefono_cliente' => '']);

        Schema::table('reservas', function (Blueprint $table) {
            $table->string('telefono_cliente', 50)->nullable(false)->change();
            $table->dropColumn('email_cliente');
        });
    }
};
