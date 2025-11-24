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
        Schema::create('alianzas', function (Blueprint $table) {
            $table->id('alianza_id')->comment('Identificador único de la alianza');
            $table->string('nombre', 120)->comment('Nombre de la alianza');
            $table->boolean('estatus')->default(true)->comment('Estatus de la alianza');
            $table->timestamp('fecha_uso')->comment('Fecha que se uso la alianza')->nullable();
            $table->timestamp('fecha_registro')->useCurrent()->comment('Fecha de alta de la alianza');
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrentOnUpdate()->comment('Fecha de actualización de la alianza');
            $table->timestamp('fecha_baja')->nullable()->comment('Fecha de borrado lógico de la alianza');
            $table->index('estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alianzas');
    }
};
