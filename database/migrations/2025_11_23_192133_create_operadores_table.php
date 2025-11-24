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
        Schema::create('operadores', function (Blueprint $table) {
            $table->id('operador_id')->comment('Identificador único del operador');
            $table->string('usuario', 80)->unique()->comment('Nombre de usuario del operador');
            $table->string('contrasena', 120)->comment('Contraseña del operador');
            $table->boolean('estatus')->default(true)->comment('Estatus del operador');
            $table->unsignedBigInteger('programa_id');
            $table->foreign('programa_id')
                ->references('programa_id')
                ->on('programas')
                ->onDelete('restrict');
            $table->timestamp('fecha_registro')->useCurrent()->comment('Fecha de alta del operador');
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrentOnUpdate()->comment('Fecha de actualización del operador');
            $table->timestamp('fecha_baja')->nullable()->comment('Fecha de borrado lógico del operador');

            $table->index(['usuario', 'estatus']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operadores');
    }
};
