<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alianza extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'fecha_alta';

    const UPDATED_AT = 'fecha_actualizacion';

    const DELETED_AT = 'fecha_baja';


    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string
     */
    protected $table = 'alianzas';

    /**
     * @var string
     */
    protected $primaryKey = 'alianza_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'nombre',
        'estatus',
        'fecha_uso',
        'fecha_registro',
        'fecha_actualizacion',
        'fecha_baja',
    ];


    /**
     * @return array<string, string>
     */
    protected $casts = [
        'estatus' => 'boolean',
    ];
}
