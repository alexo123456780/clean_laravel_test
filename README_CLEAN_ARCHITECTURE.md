# Clean Architecture Implementation - Laravel

Este proyecto implementa una arquitectura limpia completa en Laravel siguiendo los principios de Robert Martin.

## Estructura del Proyecto

```
app/
├── Domain/                     # Capa de Dominio
│   ├── Entities/              # Entidades del dominio
│   │   └── Usuario.php
│   ├── ValueObjects/          # Objetos de valor
│   │   ├── Email.php
│   │   └── Password.php
│   ├── Repositories/          # Interfaces de repositorios
│   │   └── UsuarioRepositoryInterface.php
│   ├── Services/              # Servicios e interfaces del dominio
│   │   ├── UsuarioService.php
│   │   └── UsuarioServiceInterface.php
│   └── Exceptions/            # Excepciones del dominio
│       ├── DomainException.php
│       ├── UsuarioNotFoundException.php
│       ├── InvalidUsuarioDataException.php
│       └── DuplicateEmailException.php
├── Application/               # Capa de Aplicación
│   ├── UseCases/             # Casos de uso
│   │   ├── CreateUsuarioUseCase.php
│   │   ├── GetUsuarioUseCase.php
│   │   ├── UpdateUsuarioUseCase.php
│   │   ├── DeleteUsuarioUseCase.php
│   │   └── ListUsuariosUseCase.php
│   └── DTOs/                 # Data Transfer Objects
│       ├── CreateUsuarioRequest.php
│       ├── CreateUsuarioResponse.php
│       ├── UpdateUsuarioRequest.php
│       └── UsuarioResponse.php
├── Infrastructure/           # Capa de Infraestructura
│   ├── Models/              # Modelos Eloquent
│   │   ├── User.php
│   │   └── UserRole.php
│   └── Repositories/        # Implementaciones de repositorios
│       └── EloquentUsuarioRepository.php
└── Presentation/            # Capa de Presentación
    ├── Http/Controllers/    # Controladores HTTP
    │   └── UsuarioController.php
    ├── Requests/           # Form Requests
    │   ├── CreateUsuarioHttpRequest.php
    │   └── UpdateUsuarioHttpRequest.php
    └── Resources/          # API Resources
        ├── UsuarioResource.php
        └── UsuarioCollection.php
```

## Características Implementadas

### ✅ Dominio
- **Entidad Usuario** con lógica de negocio rica
- **Value Objects** para Email y Password con validaciones
- **Interfaces de repositorio** para inversión de dependencias
- **Servicios de dominio** para lógica compleja
- **Excepciones específicas** del dominio

### ✅ Aplicación
- **Casos de uso** para todas las operaciones CRUD
- **DTOs** para transferencia de datos
- **Separación clara** entre entrada y salida

### ✅ Infraestructura
- **Repositorio Eloquent** implementando interfaces del dominio
- **Modelos Eloquent** para persistencia
- **Migraciones** para estructura de base de datos
- **Conversión** entre entidades del dominio y modelos

### ✅ Presentación
- **Controlador REST** con manejo de errores
- **Form Requests** para validación HTTP
- **API Resources** para formateo de respuestas
- **Rutas RESTful** configuradas

### ✅ Configuración
- **Service Provider** para inyección de dependencias
- **Binding** de interfaces con implementaciones
- **Autoload** configurado correctamente

### ✅ Testing
- **Tests unitarios** para entidades y value objects
- **Tests de feature** para endpoints HTTP
- **Cobertura completa** de funcionalidad

## API Endpoints

### Usuarios
- `GET /api/usuarios` - Listar usuarios
- `POST /api/usuarios` - Crear usuario
- `GET /api/usuarios/{id}` - Obtener usuario
- `PUT /api/usuarios/{id}` - Actualizar usuario
- `DELETE /api/usuarios/{id}` - Desactivar usuario (soft delete)

### Ejemplo de Uso

#### Crear Usuario
```bash
curl -X POST http://localhost:8000/api/usuarios \
  -H "Content-Type: application/json" \
  -d '{
    "nombre": "Juan",
    "apellido_paterno": "Pérez",
    "apellido_materno": "García",
    "email": "juan@example.com",
    "password": "password123",
    "roles": [{"name": "user"}]
  }'
```

#### Respuesta
```json
{
  "data": {
    "id": 1,
    "nombre": "Juan",
    "apellido_paterno": "Pérez",
    "apellido_materno": "García",
    "full_name": "Juan Pérez García",
    "email": "juan@example.com",
    "roles": [{"name": "user"}],
    "activo": true,
    "created_at": "2025-09-17 06:57:28"
  },
  "message": "Usuario creado exitosamente"
}
```

## Principios de Clean Architecture Implementados

1. **Inversión de Dependencias**: Las capas externas dependen de las internas
2. **Separación de Responsabilidades**: Cada capa tiene una responsabilidad específica
3. **Independencia de Frameworks**: La lógica de negocio no depende de Laravel
4. **Testabilidad**: Cada capa puede ser testeada independientemente
5. **Independencia de UI**: La lógica no depende de HTTP
6. **Independencia de Base de Datos**: El dominio no conoce Eloquent

## Comandos Útiles

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (poblar base de datos)
php artisan db:seed

# Ejecutar seeder específico
php artisan db:seed --class=UsuarioSeeder

# Refrescar base de datos con seeders
php artisan migrate:fresh --seed

# Crear usuario desde línea de comandos
php artisan usuario:create "Juan" "juan@test.com" "password123" --apellido-paterno="Pérez" --roles="admin" --roles="user"

# Listar usuarios desde consola
php artisan usuario:list --per-page=10
php artisan usuario:list --active-only

# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=UsuarioTest

# Iniciar servidor de desarrollo
php artisan serve

# Limpiar cache
php artisan config:clear
php artisan cache:clear
```

## Seeders y Datos de Prueba

### Usuarios Predefinidos

El sistema incluye usuarios de prueba que puedes usar inmediatamente:

| Email | Contraseña | Roles | Estado |
|-------|------------|-------|--------|
| admin@example.com | admin123 | admin, user | Activo |
| maria@example.com | user123 | user | Activo |
| carlos@example.com | password123 | user | Activo |
| ana@example.com | password123 | user | Inactivo |
| luis@example.com | moderator123 | moderator, user | Activo |

### Seeders Disponibles

- **UsuarioSeeder**: Crea usuarios de prueba con diferentes roles y estados
- **RoleSeeder**: Asigna roles adicionales a usuarios existentes

### Comandos Personalizados

#### Crear Usuario
```bash
php artisan usuario:create "Nombre" "email@example.com" "password" [opciones]

# Opciones disponibles:
--apellido-paterno="Apellido"    # Apellido paterno
--apellido-materno="Apellido"    # Apellido materno  
--roles="admin"                  # Roles (puedes usar múltiples --roles)
```

#### Listar Usuarios
```bash
php artisan usuario:list [opciones]

# Opciones disponibles:
--page=1                         # Página a mostrar
--per-page=10                    # Usuarios por página
--active-only                    # Solo usuarios activos
```

### Ejemplos de Uso de Comandos

#### Crear Usuarios
```bash
# Usuario simple
php artisan usuario:create "Ana" "ana@test.com" "password123"

# Usuario completo con roles
php artisan usuario:create "Carlos Admin" "carlos@admin.com" "admin123" \
  --apellido-paterno="García" \
  --apellido-materno="López" \
  --roles="admin" \
  --roles="moderator" \
  --roles="user"
```

#### Listar Usuarios
```bash
# Listar primeros 5 usuarios
php artisan usuario:list --per-page=5

# Ver página 2 con 10 usuarios por página
php artisan usuario:list --page=2 --per-page=10

# Solo usuarios activos
php artisan usuario:list --active-only
```

## Validaciones Implementadas

### Email
- Formato válido
- Longitud máxima 255 caracteres
- Dominios bloqueados (tempmail, etc.)
- Unicidad en base de datos

### Password
- Longitud mínima 8 caracteres
- Longitud máxima 20 caracteres
- Hashing con Argon2I
- Verificación segura

### Usuario
- Nombre obligatorio (máx 255 caracteres)
- Email único y válido
- Gestión de roles
- Estados activo/inactivo

## Manejo de Errores

La API maneja diferentes tipos de errores:

- **400**: Datos inválidos
- **404**: Usuario no encontrado
- **409**: Email duplicado
- **422**: Errores de validación
- **500**: Error interno del servidor

## Próximos Pasos

Para extender la funcionalidad, considera:

1. **Autenticación y Autorización** con Laravel Sanctum
2. **Paginación avanzada** en listados
3. **Filtros y búsqueda** en endpoints
4. **Eventos del dominio** para notificaciones
5. **Cache** para mejorar performance
6. **Logging** estructurado
7. **Rate limiting** para APIs
8. **Documentación OpenAPI** con Swagger

## Conclusión

Esta implementación demuestra una arquitectura limpia completa y funcional en Laravel, manteniendo la separación de responsabilidades y siguiendo las mejores prácticas de desarrollo de software.