<?php

namespace App\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Application\UseCases\CreateUsuarioUseCase;
use App\Application\UseCases\GetUsuarioUseCase;
use App\Application\UseCases\UpdateUsuarioUseCase;
use App\Application\UseCases\DeleteUsuarioUseCase;
use App\Application\UseCases\ListUsuariosUseCase;
use App\Application\DTOs\CreateUsuarioRequest;
use App\Application\DTOs\UpdateUsuarioRequest;
use App\Presentation\Requests\CreateUsuarioHttpRequest;
use App\Presentation\Requests\UpdateUsuarioHttpRequest;
use App\Presentation\Resources\UsuarioResource;
use App\Presentation\Resources\UsuarioCollection;
use App\Domain\Exceptions\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(
        private CreateUsuarioUseCase $createUsuarioUseCase,
        private GetUsuarioUseCase $getUsuarioUseCase,
        private UpdateUsuarioUseCase $updateUsuarioUseCase,
        private DeleteUsuarioUseCase $deleteUsuarioUseCase,
        private ListUsuariosUseCase $listUsuariosUseCase
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $page = (int) $request->get('page', 1);
            $perPage = (int) $request->get('per_page', 15);
            
            $result = $this->listUsuariosUseCase->execute($page, $perPage);
            
            return response()->json([
                'data' => UsuarioResource::collection(collect($result['data'])),
                'pagination' => $result['pagination']
            ]);
        } catch (DomainException $e) {
            return $this->handleDomainException($e);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'type' => 'server_error',
                    'message' => 'Error interno del servidor'
                ]
            ], 500);
        }
    }

    public function store(CreateUsuarioHttpRequest $request): JsonResponse
    {
        try {
            $createRequest = new CreateUsuarioRequest(
                nombre: $request->validated('nombre'),
                email: $request->validated('email'),
                password: $request->validated('password'),
                apellidoPaterno: $request->validated('apellido_paterno'),
                apellidoMaterno: $request->validated('apellido_materno'),
                roles: $request->validated('roles', [])
            );

            $response = $this->createUsuarioUseCase->execute($createRequest);

            return response()->json([
                'data' => new UsuarioResource($response),
                'message' => 'Usuario creado exitosamente'
            ], 201);
        } catch (DomainException $e) {
            return $this->handleDomainException($e);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'type' => 'server_error',
                    'message' => 'Error interno del servidor'
                ]
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $response = $this->getUsuarioUseCase->execute($id);

            return response()->json([
                'data' => new UsuarioResource($response)
            ]);
        } catch (DomainException $e) {
            return $this->handleDomainException($e);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'type' => 'server_error',
                    'message' => 'Error interno del servidor'
                ]
            ], 500);
        }
    }

    public function update(UpdateUsuarioHttpRequest $request, int $id): JsonResponse
    {
        try {
            $updateRequest = new UpdateUsuarioRequest(
                id: $id,
                nombre: $request->validated('nombre'),
                email: $request->validated('email'),
                apellidoPaterno: $request->validated('apellido_paterno'),
                apellidoMaterno: $request->validated('apellido_materno')
            );

            $response = $this->updateUsuarioUseCase->execute($updateRequest);

            return response()->json([
                'data' => new UsuarioResource($response),
                'message' => 'Usuario actualizado exitosamente'
            ]);
        } catch (DomainException $e) {
            return $this->handleDomainException($e);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'type' => 'server_error',
                    'message' => 'Error interno del servidor'
                ]
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteUsuarioUseCase->execute($id);

            return response()->json([
                'message' => 'Usuario desactivado exitosamente'
            ]);
        } catch (DomainException $e) {
            return $this->handleDomainException($e);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'type' => 'server_error',
                    'message' => 'Error interno del servidor'
                ]
            ], 500);
        }
    }

    private function handleDomainException(DomainException $e): JsonResponse
    {
        $statusCode = match ($e->getErrorType()) {
            'usuario_not_found' => 404,
            'duplicate_email' => 409,
            'invalid_usuario_data' => 400,
            default => 400
        };

        return response()->json([
            'error' => [
                'type' => $e->getErrorType(),
                'message' => $e->getMessage()
            ]
        ], $statusCode);
    }
}