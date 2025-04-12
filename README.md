# Áureo Framework

Áureo es un framework PHP moderno y elegante diseñado para crear aplicaciones web robustas y escalables. Combina la simplicidad con características potentes para ofrecer una experiencia de desarrollo excepcional.

## Características Principales ✨

✅ Sistema de rutas intuitivo y middleware
✅ Integración con AdminLTE para interfaces administrativas
✅ Autenticación y autorización con JWT
✅ Sistema de roles y permisos
✅ ORM simple y eficiente
✅ Sistema de plantillas y vistas
✅ Validación de formularios
✅ Gestión de archivos
✅ Sistema de logging
✅ Sistema de caché
✅ Migraciones y seeders
✅ API REST
✅ Internacionalización (i18n)
✅ Tareas programadas

## Requisitos del Sistema 🛠️

- PHP 8.0 o superior
- Composer
- MySQL 5.7 o superior
- Extensiones PHP: PDO, JSON, mbstring

## Instalación 📦

```bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/aureo.git

# Instalar dependencias
composer install

# Configurar el archivo .env
cp .env.example .env

# Generar clave JWT
php aureo jwt:generate

# Ejecutar migraciones
php aureo migrate

# Ejecutar seeders (opcional)
php aureo db:seed
```

## Estructura del Proyecto 📁

```
aureo/
├── app/
│   ├── Controllers/     # Controladores de la aplicación
│   ├── Middleware/      # Middleware personalizado
│   └── Views/           # Vistas y plantillas
├── database/
│   ├── migrations/      # Migraciones de base de datos
│   └── seeders/        # Seeders para datos de prueba
├── public/
│   └── assets/         # Archivos públicos (CSS, JS, imágenes)
├── storage/
│   ├── cache/          # Archivos de caché
│   └── logs/           # Logs de la aplicación
├── system/             # Núcleo del framework
└── vendor/            # Dependencias de Composer
```

## Uso del CLI 🖥️

Áureo incluye una interfaz de línea de comandos potente:

```bash
# Crear un nuevo controlador
php aureo make:controller UserController

# Crear un nuevo modelo
php aureo make:model User

# Crear una nueva migración
php aureo make:migration create_users_table

# Ejecutar migraciones
php aureo migrate

# Limpiar caché
php aureo cache:clear

# Ejecutar seeders
php aureo db:seed
```

## Sistema de Rutas y Middleware 🛣️

```php
// routes.php
$router->get('/', 'HomeController@index');
$router->get('/dashboard', 'DashboardController@index')->middleware('auth');

// API routes
$router->group('/api', function($router) {
    $router->get('/users', 'Api\UserController@index')->middleware('jwt');
});
```

## Plantillas y Vistas con AdminLTE 🎨

```php
// En tu controlador
public function index() {
    return view('dashboard.index', [
        'title' => 'Dashboard',
        'users' => $users
    ]);
}

// En tu vista (dashboard/index.php)
<?php extends('layouts.admin'); ?>

<?php section('content'); ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Dashboard</h5>
            <!-- Tu contenido aquí -->
        </div>
    </div>
<?php endsection(); ?>
```

## Autenticación y JWT 🔐

```php
// Autenticación con JWT
$token = JwtManager::generate([
    'user_id' => $user->id,
    'email' => $user->email
]);

// Middleware de autenticación
class JwtMiddleware extends Middleware {
    public function handle($request) {
        $token = $request->getBearerToken();
        if (!JwtManager::verify($token)) {
            throw new UnauthorizedException();
        }
    }
}
```

## Validación de Formularios ✅

```php
$validator = new Validator($request->all(), [
    'email' => 'required|email',
    'password' => 'required|min:8'
]);

if ($validator->fails()) {
    return back()->withErrors($validator->errors());
}
```

## Manejo de Archivos 📁

```php
$fileManager = new FileManager();
$path = $fileManager->store($request->file('avatar'), 'uploads/avatars');
```

## Tareas Programadas ⏰

```php
// app/Console/Kernel.php
class Kernel extends Schedule {
    public function schedule() {
        $this->daily('10:00', function() {
            // Tarea diaria
        });

        $this->weekly('monday', function() {
            // Tarea semanal
        });
    }
}
```

## Internacionalización 🌍

```php
// Configurar idioma
I18n::setLocale('es');

// Usar traducciones
echo __('messages.welcome');
```

## Sistema de Logging 📝

```php
Logger::info('Usuario registrado', ['user_id' => $user->id]);
Logger::error('Error en el proceso', ['error' => $e->getMessage()]);
```

## Sistema de Caché 💾

```php
Cache::set('users', $users, 3600); // Guardar por 1 hora
$users = Cache::get('users'); // Obtener de caché
Cache::forget('users'); // Eliminar de caché
```

## Migraciones y Seeders 🗃️

```php
// database/migrations/create_users_table.php
class CreateUsersTable extends Migration {
    public function up() {
        return $this->db->query("
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                email VARCHAR(255) UNIQUE,
                password VARCHAR(255),
                created_at TIMESTAMP
            )
        ");
    }
}

// database/seeders/UserSeeder.php
class UserSeeder extends Seeder {
    public function run() {
        $this->db->table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ]);
    }
}
```

## Desarrollo de APIs 🚀

```php
// app/Controllers/Api/UserController.php
class UserController {
    public function index() {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request) {
        $user = User::create($request->all());
        return response()->json($user, 201);
    }
}
```

## Contribución y Buenas Prácticas 🤝

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### Buenas Prácticas

- Sigue PSR-12 para el estilo de código
- Documenta todas las funciones y clases
- Escribe pruebas unitarias para nuevas características
- Mantén los controladores ligeros y la lógica de negocio en servicios
- Usa inyección de dependencias cuando sea posible

## Mejoras Recomendadas (Roadmap) 🎯

- [ ] Implementar sistema de eventos y listeners
- [ ] Agregar soporte para WebSockets
- [ ] Mejorar el sistema de caché con Redis
- [ ] Implementar sistema de notificaciones
- [ ] Agregar soporte para colas de trabajo
- [ ] Mejorar la documentación API con OpenAPI/Swagger
- [ ] Implementar sistema de plugins
- [ ] Agregar más comandos CLI útiles
- [ ] Optimizar el rendimiento del ORM
- [ ] Implementar sistema de backups automáticos

## Licencia 📄

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.# aureofremwork
