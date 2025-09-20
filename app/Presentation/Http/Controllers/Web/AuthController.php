<?php

namespace App\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Application\Services\AuthenticationService;
use App\Application\UseCases\CreateUsuarioUseCase;
use App\Application\DTOs\CreateUsuarioRequest;
use App\Presentation\Requests\Web\LoginRequest;
use App\Presentation\Requests\Web\RegisterRequest;
use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\UsuarioNotFoundException;
use App\Domain\Exceptions\DuplicateEmailException;
use App\Domain\Exceptions\InvalidUsuarioDataException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private AuthenticationService $authenticationService,
        private CreateUsuarioUseCase $createUsuarioUseCase
    ) {}

    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            // Attempt to authenticate the user
            $usuarioResponse = $this->authenticationService->authenticate(
                $request->getEmail(),
                $request->getPassword()
            );

            if (!$usuarioResponse) {
                return back()
                    ->withErrors(['email' => 'Las credenciales proporcionadas no son correctas.'])
                    ->withInput($request->only('email'));
            }

            // Convert to Laravel User model for authentication
            $user = $this->authenticationService->convertToAuthUser($usuarioResponse);

            // Log the user in
            Auth::login($user, $request->boolean('remember'));

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            // Redirect to intended page or dashboard
            return redirect()->intended(route('dashboard'))
                ->with('success', '¡Bienvenido de vuelta, ' . $usuarioResponse->nombre . '!');

        } catch (UsuarioNotFoundException $e) {
            return back()
                ->withErrors(['email' => 'Las credenciales proporcionadas no son correctas.'])
                ->withInput($request->only('email'));
        } catch (DomainException $e) {
            Log::error('Domain exception during login', [
                'error' => $e->getMessage(),
                'type' => $e->getErrorType(),
                'email' => $request->getEmail()
            ]);

            return back()
                ->withErrors(['general' => 'Ha ocurrido un error. Por favor, inténtalo de nuevo.'])
                ->withInput($request->only('email'));
        } catch (\Exception $e) {
            Log::error('Unexpected error during login', [
                'error' => $e->getMessage(),
                'email' => $request->getEmail()
            ]);

            return back()
                ->withErrors(['general' => 'Ha ocurrido un error interno. Por favor, inténtalo más tarde.'])
                ->withInput($request->only('email'));
        }
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            // Create the user using the existing Use Case
            $createUsuarioRequest = new CreateUsuarioRequest(
                nombre: $request->getNombre(),
                email: $request->getEmail(),
                password: $request->getPassword(),
                apellidoPaterno: $request->getApellidoPaterno(),
                apellidoMaterno: $request->getApellidoMaterno(),
                roles: [] // Default empty roles for web registration
            );

            $createUsuarioResponse = $this->createUsuarioUseCase->execute($createUsuarioRequest);

            // Convert CreateUsuarioResponse to UsuarioResponse for authentication
            $usuarioResponse = new \App\Application\DTOs\UsuarioResponse(
                id: $createUsuarioResponse->id,
                nombre: $createUsuarioResponse->nombre,
                email: $createUsuarioResponse->email,
                apellidoPaterno: $createUsuarioResponse->apellidoPaterno,
                apellidoMaterno: $createUsuarioResponse->apellidoMaterno,
                fullName: $createUsuarioResponse->fullName,
                roles: $createUsuarioResponse->roles,
                activo: $createUsuarioResponse->activo,
                createdAt: $createUsuarioResponse->createdAt,
                updatedAt: $createUsuarioResponse->createdAt // Use createdAt as updatedAt for new users
            );

            // Convert to Laravel User model for authentication
            $user = $this->authenticationService->convertToAuthUser($usuarioResponse);

            // Automatically log in the user after registration
            Auth::login($user);

            // Regenerate session
            $request->session()->regenerate();

            // Redirect to dashboard with success message
            return redirect()->route('dashboard')
                ->with('success', '¡Cuenta creada exitosamente! Bienvenido, ' . $createUsuarioResponse->nombre . '!');

        } catch (DuplicateEmailException $e) {
            return back()
                ->withErrors(['email' => 'Este email ya está registrado en el sistema.'])
                ->withInput($request->except('password', 'password_confirmation'));
        } catch (InvalidUsuarioDataException $e) {
            // Map domain validation errors to form fields
            $errors = $this->mapDomainValidationErrors($e->getMessage());
            
            return back()
                ->withErrors($errors)
                ->withInput($request->except('password', 'password_confirmation'));
        } catch (DomainException $e) {
            Log::error('Domain exception during registration', [
                'error' => $e->getMessage(),
                'type' => $e->getErrorType(),
                'email' => $request->getEmail()
            ]);

            return back()
                ->withErrors(['general' => 'Ha ocurrido un error durante el registro. Por favor, verifica tus datos.'])
                ->withInput($request->except('password', 'password_confirmation'));
        } catch (\Exception $e) {
            Log::error('Unexpected error during registration', [
                'error' => $e->getMessage(),
                'email' => $request->getEmail()
            ]);

            return back()
                ->withErrors(['general' => 'Ha ocurrido un error interno. Por favor, inténtalo más tarde.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        // Get user name for goodbye message
        $userName = Auth::user()?->nombre ?? 'Usuario';

        // Log out the user
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the CSRF token
        $request->session()->regenerateToken();

        // Redirect to login with success message
        return redirect()->route('login')
            ->with('success', '¡Hasta luego, ' . $userName . '! Has cerrado sesión correctamente.');
    }

    /**
     * Map domain validation errors to form field errors.
     *
     * @param string $domainError
     * @return array<string, string>
     */
    private function mapDomainValidationErrors(string $domainError): array
    {
        // Map common domain validation errors to form fields
        $errorMappings = [
            'El nombre no debe estar vacio' => ['nombre' => 'El nombre es obligatorio.'],
            'El email no debe estar vacio' => ['email' => 'El email es obligatorio.'],
            'El email no es valido' => ['email' => 'El formato del email no es válido.'],
            'El password no debe estar vacio' => ['password' => 'La contraseña es obligatoria.'],
            'El password debe tener al menos 8 caracteres' => ['password' => 'La contraseña debe tener al menos 8 caracteres.'],
            'El password no puede tener mas de 255 caracteres' => ['password' => 'La contraseña no puede tener más de 255 caracteres.'],
        ];

        foreach ($errorMappings as $domainMessage => $formError) {
            if (str_contains($domainError, $domainMessage)) {
                return $formError;
            }
        }

        // Default to general error if no specific mapping found
        return ['general' => $domainError];
    }
}