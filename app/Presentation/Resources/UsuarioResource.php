<?php

namespace App\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Application\DTOs\UsuarioResponse;

class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->resource->id,
            'nombre' => $this->resource->nombre,
            'apellido_paterno' => $this->resource->apellidoPaterno,
            'apellido_materno' => $this->resource->apellidoMaterno,
            'full_name' => $this->resource->fullName,
            'email' => $this->resource->email,
            'roles' => $this->resource->roles,
            'activo' => $this->resource->activo,
            'created_at' => $this->resource->createdAt,
        ];
        
        // Solo agregar updated_at si existe (UsuarioResponse lo tiene, CreateUsuarioResponse no)
        if (property_exists($this->resource, 'updatedAt')) {
            $data['updated_at'] = $this->resource->updatedAt;
        }
        
        return $data;
    }
}