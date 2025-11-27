<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OperatorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string,mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when(isset($this->operador_id), $this->operador_id, $this->id),
            'usuario' => $this->usuario,
            'programa' => $this->when(isset($this->program), function () {
                return $this->program->programa_id ?? null;
            }),
        ];
    }
}
