<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Operator extends Authenticatable implements JWTSubject
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

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'programa_id', 'programa_id');
    }

    /**
     * Relación: Un operador puede tener muchas sesiones API.
     *
     * Esta relación indica que el operador tiene múltiples registros
     * en la tabla `sesiones_api`, representando los tokens generados
     * para este operador.
     *
     * @return HasMany
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(SesionApi::class);
    }

    /**
     * Mutador: Encripta la contraseña antes de guardarla en la base de datos.
     *
     * @param string $value  Contraseña en texto plano.
     * @return void
     */

    public function setPasswordAttribute($value)
    {
        $this->attributes['contrasena'] = Hash::make($value);
    }


    /**
     * Verifica si la contraseña proporcionada coincide con la contraseña almacenada.
     *
     * Utiliza Hash::check para comparar la contraseña ingresada por el usuario
     * con la versión encriptada guardada en la base de datos.
     *
     * @param string $contrasena  Contraseña en texto plano que se desea validar.
     * @return bool  true si coincide, false en caso contrario.
     */

    public function verifyPassword(string $contrasena): bool
    {
        return Hash::check($contrasena, $this->contrasena);
    }

    /**
     * Scope: Filtra operadores activos (estatus = true).
     */
    public function scopeActives($query)
    {
        return $query->where('estatus', true);
    }

    /**
     * Obtiene el identificador que se almacenará en el campo "sub" (subject)
     * dentro del token JWT.
     *
     * Normalmente este valor es la clave primaria del modelo (ID del usuario),
     * y sirve para que el token pueda identificar a qué usuario pertenece.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
       return (string) $this->getKey();
    }

    /**
     * Define los datos personalizados (custom claims) que se agregarán al token JWT.
     *
     * Estos valores se incluirán dentro del payload del token para permitir
     * acceder a ellos sin necesidad de realizar consultas adicionales a la base de datos.
     *
     * Aquí se agregan el programa al que pertenece el operador y su nombre de usuario.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'programa_id' => $this->programa_id !== null ? (int)$this->programa_id : null,
            'usuario' => $this->usuario,
        ];
    }

    public function getAuthPassword()
    {
        return $this->contrasena;
    }
}
