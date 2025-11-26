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
        Schema::create('sesiones_api', function (Blueprint $table) {
            $table->id('sesion_api_id')->comment('Identificador único de la sesión API');
            $table->string('uuid', 64)->unique();
            $table->unsignedBigInteger('operador_id');
            $table->foreign('operador_id')
                ->references('operador_id')
                ->on('operadores')
                ->onDelete('cascade')
                ->comment('Identificador del operador asociado');
            $table->text('token')->comment('Token de la sesión ');
            $table->timestamp('fecha_expiracion')->comment('Fehca de expiración de la sesión');
            $table->boolean('estatus')->default(true)->comment('Estatus de la sesión');
            $table->timestamp('fecha_registro')->useCurrent()->comment('Fecha de alta de la relación');
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrentOnUpdate()->comment('Fecha de actualización de la realación');
            $table->timestamp('fecha_baja')->nullable()->comment('Fecha de borrado lógico de la relación');


            $table->index('uuid');
            $table->index('estatus');
            $table->index('fecha_expiracion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesiones_api');
    }
};
