<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SesionApi extends Model
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
    protected $table = 'sesiones_api';

    /**
     * Clave primaria
     * @var string
     */
    protected $primaryKey = 'sesion_api_id';

    /**
     * Campos asignables
     * @var string[]
     */
    protected $fillable = [
        'uuid',
        'operador_id',
        'token',
        'fecha_expiracion',
        'estatus',
    ];

    /**
     * Casts de atributos
     * @return array<string, string>
     */
    protected $casts = [
        'estatus' => 'boolean',
        'fecha_expiracion' => 'datetime',
    ];

    /**
     * Relación: Una sesión pertenece a un operador.
     *
     * @return BelongsTo
     */

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Crea una nueva sesión API válida.
     *
     * - Genera un UUID único
     * - Asigna el operador
     * - Guarda el token
     * - Define la fecha de expiración
     * - Marca la sesión como (estatus = true)
     *
     * @param Operator $operator
     * @param string   $token
     * @param int      $minutesExpiration
     *
     * @return self
     */

    public static function createSession(Operator $operator, string $token, int $minutesExpiration = 60): self
    {
        return self::create([
            'uuid' => Str::uuid()->toString(),
            'operador_id' => $operator->operador_id,
            'token' => $token,
            'fecha_expiracion' => now()->addMinutes(60),
            'estatus' => true,
        ]);
    }

    /**
     * Verifica si la sesión está expirada.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->fecha_expiracion < now();
    }

     /**
     * Verifica si la sesión está activa:
     * - estatus = true
     * - NO está expirada
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->estatus && !$this->estaExpirada();
    }

    /**
     * Marca la sesión como inactiva.
     *
     * @return bool
     */
    public function invalidate(): bool
    {
        return $this->update(['estatus' => false]);
    }

    /**
     * Obtiene las sesiones activas que aún no expiran.
     *
     * @param $query
     * @return mixed
     */
    public function scopeActives($query)
    {
        return $query->where('estatus', true)
                     ->where('fecha_expiracion', '>', now());
    }

    /**
     * Obtiene las sesiones expiradas.
     *
     * @param $query
     * @return mixed
     */
    public function scopeExpired($query)
    {
        return $query->where('fecha_expiracion', '<=', now());
    }


}
