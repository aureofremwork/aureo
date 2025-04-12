<?php
/**
 * Archivo principal de entrada de Áureo Framework
 * Este archivo maneja todas las solicitudes entrantes y las dirige al controlador correspondiente
 */

// Cargamos el autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar los helpers del sistema
require_once __DIR__ . '/../system/helpers.php';

// Cargamos las variables de entorno
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Iniciamos la sesión
session_start();

// Definimos la constante de la ruta base
define('BASE_PATH', dirname(__DIR__));

// Iniciamos el enrutador
$router = new \System\Router();

// Rutas de autenticación
$router->get('/login', '\App\Controllers\AuthController@loginForm');
$router->post('/login', '\App\Controllers\AuthController@login');
$router->get('/register', '\App\Controllers\AuthController@registerForm');
$router->post('/register', '\App\Controllers\AuthController@register');
$router->get('/logout', '\App\Controllers\AuthController@logout');

// Rutas públicas
$router->get('/', '\App\Controllers\HomeController@index');

// Rutas protegidas
$router->get('/dashboard', '\App\Controllers\DashboardController@index')
       ->middleware('AuthMiddleware');

// Procesamos la solicitud actual
$router->dispatch();