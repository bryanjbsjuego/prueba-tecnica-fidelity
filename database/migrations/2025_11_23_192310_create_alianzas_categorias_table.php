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
        Schema::create('alianzas_categorias', function (Blueprint $table) {
            $table->id('alianza_categoria_id')->comment('Identificador único de la relación entre alianza y categoría');


            $table->unsignedBigInteger('alianza_id');
            $table->foreign('alianza_id')
                ->references('alianza_id')
                ->on('alianzas')
                ->onDelete('cascade')
                ->comment('Identificador de la alianza');


            $table->unsignedBigInteger('categoria_cliente_id');
            $table->foreign('categoria_cliente_id')
                ->references('categoria_cliente_id')
                ->on('categorias_cliente')
                ->onDelete('cascade')
                ->comment('Identificador de la categoría del cliente');


            $table->timestamp('fecha_registro')->useCurrent()->comment('Fecha de alta de la relación');
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrentOnUpdate()->comment('Fecha de actualización de la realación');
            $table->timestamp('fecha_baja')->nullable()->comment('Fecha de borrado lógico de la relación');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alianzas_categorias');
    }
};
