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
        Schema::create('programas', function (Blueprint $table) {
            $table->id('programa_id')->comment('Identificador único del programa');
            $table->string('nombre', 120)->comment('Nombre del programa');
            $table->boolean('estatus')->default(true)->comment('Estatus del programa');
            $table->timestamp('fecha_registro')->useCurrent()->comment('Fecha de alta del programa');
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrentOnUpdate()->comment('Fecha de actualización del programa');
            $table->timestamp('fecha_baja')->nullable()->comment('Fecha de borrado lógico del programa');

            $table->index('estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
