<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{

    // Se utiliza para el borrado lógico
    use SoftDeletes;

    // Constantes para los nombres de las columnas de timestamps personalizados

    const CREATED_AT = 'fecha_registro';

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
    protected $table = 'programas';

    /**
     * Clave primaria
     * @var string
     */
    protected $primaryKey = 'programa_id';

    /**
     * Campos asignables
     * @var string[]
     */
    protected $fillable = [
        'nombre',
        'estatus',
        'fecha_registro',
        'fecha_actualizacion',
        'fecha_baja',
    ];


    /**
     * Casts de atributos
     * @return array<string, string>
     */
    protected $casts = [
        'estatus' => 'boolean',
    ];

    /**
     * Relación con el modelo Operador
     * @return HasMany
     */
    public function operators(): HasMany
    {
        return $this->hasMany(Operator::class, 'programa_id', 'programa_id');
    }

    /**
     * Relación con el modelo CustomerCategory
     * @return HasMany
     */

    public function customerCategory(): HasMany
    {
        return $this->hasMany(CustomerCategory::class, 'programa_id', 'programa_id');
    }

    /**
     * Scope para filtrar programas activos
     */

    public function scopeActives($query)
    {
        return $query->where('estatus', true);
    }
}
