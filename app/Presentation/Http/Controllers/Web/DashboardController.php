<?php

namespace App\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Application\Services\AuthenticationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(
        private AuthenticationService $authenticationService
    ) {}

    /**
     * Show the application dashboard.
     */
    public function index(Request $request): View
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            
            if (!$user) {
                // This shouldn't happen due to auth middleware, but handle gracefully
                abort(401, 'Usuario no autenticado');
            }

            // Get additional user data from domain layer
            $usuarioResponse = $this->authenticationService->getAuthenticatedUser($user->id);
            
            if (!$usuarioResponse) {
                Log::warning('User authenticated but not found in domain layer', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                // Fallback to basic user data from Laravel Auth
                $userData = [
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'apellidoPaterno' => $user->apellido_paterno,
                    'apellidoMaterno' => $user->apellido_materno,
                    'email' => $user->email,
                    'fullName' => trim($user->nombre . ' ' . $user->apellido_paterno . ' ' . $user->apellido_materno),
                    'activo' => $user->activo,
                    'roles' => [],
                    'createdAt' => $user->created_at?->format('d/m/Y'),
                    'updatedAt' => $user->updated_at?->format('d/m/Y')
                ];
            } else {
                // Use domain data
                $userData = [
                    'id' => $usuarioResponse->id,
                    'nombre' => $usuarioResponse->nombre,
                    'apellidoPaterno' => $usuarioResponse->apellidoPaterno,
                    'apellidoMaterno' => $usuarioResponse->apellidoMaterno,
                    'email' => $usuarioResponse->email,
                    'fullName' => $usuarioResponse->fullName,
                    'activo' => $usuarioResponse->activo,
                    'roles' => $usuarioResponse->roles,
                    'createdAt' => \Carbon\Carbon::parse($usuarioResponse->createdAt)->format('d/m/Y'),
                    'updatedAt' => \Carbon\Carbon::parse($usuarioResponse->updatedAt)->format('d/m/Y')
                ];
            }

            // Get some basic statistics for the dashboard
            $dashboardData = [
                'user' => $userData,
                'stats' => [
                    'memberSince' => $userData['createdAt'],
                    'lastUpdate' => $userData['updatedAt'],
                    'rolesCount' => count($userData['roles']),
                    'isActive' => $userData['activo']
                ],
                'quickActions' => [
                    [
                        'title' => 'Ver Perfil',
                        'description' => 'Consulta y edita tu información personal',
                        'icon' => 'user',
                        'url' => '#', // Will be implemented later
                        'color' => 'blue'
                    ],
                    [
                        'title' => 'Configuración',
                        'description' => 'Ajusta las preferencias de tu cuenta',
                        'icon' => 'settings',
                        'url' => '#', // Will be implemented later
                        'color' => 'gray'
                    ],
                    [
                        'title' => 'Ayuda',
                        'description' => 'Encuentra respuestas a tus preguntas',
                        'icon' => 'help',
                        'url' => '#', // Will be implemented later
                        'color' => 'green'
                    ]
                ]
            ];

            return view('auth.dashboard', compact('dashboardData'));

        } catch (\Exception $e) {
            Log::error('Error loading dashboard', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return a basic dashboard view with minimal data
            $basicData = [
                'user' => [
                    'nombre' => Auth::user()?->nombre ?? 'Usuario',
                    'email' => Auth::user()?->email ?? '',
                    'fullName' => Auth::user()?->nombre ?? 'Usuario'
                ],
                'stats' => [
                    'memberSince' => 'N/A',
                    'lastUpdate' => 'N/A',
                    'rolesCount' => 0,
                    'isActive' => true
                ],
                'quickActions' => [],
                'error' => 'Hubo un problema al cargar algunos datos del dashboard.'
            ];

            return view('auth.dashboard', ['dashboardData' => $basicData]);
        }
    }
}