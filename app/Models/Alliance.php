<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alliance extends Model
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
    protected $table = 'alianzas';

    /**
     * Clave orimaria
     * @var string
     */
    protected $primaryKey = 'alianza_id';

    /**
     * Campos asignables
     * @var string[]
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'estatus',
        'fecha_uso',
        'fecha_registro',
        'fecha_actualizacion',
        'fecha_baja',
    ];


    /**
     * Casts de atributos
     * @return array<string, string>
     */
    protected $casts = [
        'fecha_uso' => 'datetime',
    ];

    // Constantes que representan los posibles estados de una alianza.
    const ESTATUS_ACTIVO = 'activo';
    const ESTATUS_USADO = 'usado';
    const ESTATUS_INACTIVO = 'inactivo';

    /**
     * Relación muchos a muchos con CustomerCategory.
     *
     * Una alianza puede pertenecer a varias categorías de clientes,
     * y una categoría puede tener varias alianzas.
     *
     * Tabla pivote: alianzas_categorias
     * - alianza_id               → Foreign key hacia esta tabla
     * - categoria_cliente_id     → Foreign key hacia categorias_cliente
     *
     * withTimestamps() asegura que se registren:
     * - fecha_registro
     * - fecha_actualizacion
     *
     * @return BelongsToMany
     */
    public function customerCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            CustomerCategory::class,
            'alianzas_categorias',
            'alianza_id',
            'categoria_cliente_id'
        )->withTimestamps();
    }

    /**
     * Scope: Filtra únicamente las alianzas activas.
     */
    public function scopeActives($query)
    {
        return $query->where('estatus', self::ESTATUS_ACTIVO);
    }

    /**
     * Scope: Filtra alianzas por categoría de cliente.
     *
     * @param int $categoriaId  ID de la categoría a buscar.
     */

    public function scopeByCategory($query, $categoriaId)
    {
        return $query->whereHas('customerCategories', function ($q) use ($categoriaId) {
            $q->where('alianzas_categorias.categoria_cliente_id', $categoriaId);
        });
    }

    /**
     * Marca la alianza como usada.
     *
     * Actualiza:
     * - estatus a "usado"
     * - fecha_uso con la fecha y hora actual
     *
     * @return bool  true si se actualizó correctamente
     */
    public function markAsUsed(): bool
    {
        return $this->update([
            'estatus' => self::ESTATUS_USADO,
            'fecha_uso' => now(),
        ]);
    }

    /**
     * Verifica si la alianza está activa.
     *
     * @return bool  true si su estatus es "activo"
     */
    public function isActive(): bool
    {
        return $this->estatus === self::ESTATUS_ACTIVO;
    }
}
