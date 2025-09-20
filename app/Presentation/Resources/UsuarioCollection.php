<?php

namespace App\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UsuarioCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => UsuarioResource::collection($this->collection),
            'meta' => [
                'total' => $this->collection->count(),
            ],
        ];
    }
}