<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operador extends Model
{

    // Se utiliza para el borrado lógico
    use SoftDeletes;

    // Constantes para los nombres de las columnas de timestamps personalizados

    const CREATED_AT = 'fecha_alta';

    const UPDATED_AT = 'fecha_actualizacion';

    const DELETED_AT = 'fecha_baja';


    /**
     * Variable timestamps
     * @var bool
     */
    public $timestamps = true;

    /**
     * Nombre de la tabla
     * @var string
     */
    protected $table = 'operadores';

    /**
     * Clave primaria
     * @var string
     */
    protected $primaryKey = 'operador_id';

    /**
     * Campos asignables
     * @var string[]
     */
    protected $fillable = [
        'usuario',
        'contrasena',
        'estatus',
        'programa_id',
        'fecha_registro',
        'fecha_actualizacion',
        'fecha_baja',
    ];

    /**
     * Campos ocultos
     * @var string[]
     */
    protected $hidden = [
        'contrasena',
    ];

    /**
     * Casts de atributos
     * @return array<string, string>
     */
    protected $casts = [
        'estatus' => 'boolean',
    ];

    /**
     * Relación con el modelo programa
     * @return BelongsTo
     */

    public function programa() : BelongsTo{
        return $this->belongsTo(Programa::class);
    }
}
