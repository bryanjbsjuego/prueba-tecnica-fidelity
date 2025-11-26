<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCategory extends Model
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

    protected $table = 'categorias_cliente';

    /**
     * Clave primaria
     * @var string
     */
    protected $primaryKey = 'categoria_cliente_id';


    /**
     * Campos asignables
     * @var string[]
     */
    protected $fillable = [
        'categoria_cliente_id',
        'nombre',
        'programa_id'
    ];

    /**
     * Relación: Una categoría pertenece a un programa.
     *
     * @return BelongsTo
     */

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Relación muchos a muchos: una categoría puede estar asociada a
     * muchas alianzas, y una alianza puede pertenecer a muchas categorías.
     *
     * Tabla pivote: alianzas_categorias
     *  - categoria_cliente_id (FK hacia este modelo)
     *  - alianza_id (FK hacia este modelo)
     *
     * withTimestamps() asegura que se registren fecha_registro y fecha_actualizacion
     * en la tabla pivote.
     *
     * @return BelongsToMany
     */

    public function alliances(): BelongsToMany
    {
        return $this->belongsToMany(
            Alliance::class,
            'alianzas_categorias',
            'categoria_cliente_id',
            'alianza_id'
        )->withTimestamps();
    }
}
