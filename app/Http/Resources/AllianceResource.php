<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllianceResource extends JsonResource
{
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string,mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when(isset($this->alianza_id), $this->alianza_id, $this->id),
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'estatus' => $this->estatus,
            'fecha_registro' => isset($this->fecha_registro) ? $this->fecha_registro->format('Y-m-d H:i:s') : null,
            'fecha_uso' => isset($this->fecha_uso) ? $this->fecha_uso->format('Y-m-d H:i:s') : null,
            'categorias' => $this->whenLoaded('customerCategories', function () {
                return $this->customerCategories->map(function ($cat) {
                    return [
                        'id' => $cat->categoria_cliente_id,
                        'nombre' => $cat->nombre,
                    ];
                });
            }),
        ];
    }
}
