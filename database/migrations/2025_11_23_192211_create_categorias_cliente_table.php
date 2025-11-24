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
        Schema::create('categorias_cliente', function (Blueprint $table) {
            $table->id('categoria_cliente_id')->comment('Identificador único de la categoría del cliente');
            $table->string('nombre', 120)->comment('Nombre de la categoría del cliente');
            $table->unsignedBigInteger('programa_id');
            $table->foreign('programa_id')
                ->references('programa_id')
                ->on('programas')
                ->onDelete('restrict');
            $table->timestamp('fecha_registro')->useCurrent()->comment('Fecha de alta de la categoría del cliente');
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrentOnUpdate()->comment('Fecha de actualización de la categoría del cliente');
            $table->timestamp('fecha_baja')->nullable()->comment('Fecha de borrado lógico de la categoría del cliente');

            $table->index('programa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_cliente');
    }
};
