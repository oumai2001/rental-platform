<?php
/*
|--------------------------------------------------------------------------
| index.php – Front controller
|--------------------------------------------------------------------------
*/

// 1️⃣ Config + BASE_PATH
require_once __DIR__ . '/config/app.php';

// 2️⃣ Session + erreurs DEV
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 3️⃣ Autoloader simple (PSR-4 like)
spl_autoload_register(function ($class) {
    $path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// 4️⃣ Controllers
use Controllers\AuthController;
use Controllers\RentalController;
use Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| Récupération et nettoyage de l’URI
|--------------------------------------------------------------------------
*/
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// supprimer BASE_PATH
if (strpos($uri, BASE_PATH) === 0) {
    $uri = substr($uri, strlen(BASE_PATH));
}

// normalisation
$uri = '/' . trim($uri, '/');
if ($uri === '//') {
    $uri = '/';
}

/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/
try {

    switch ($uri) {

        /* ===================== HOME ===================== */
        case '/':
            (new RentalController())->index();
            break;

        /* ===================== AUTH ===================== */
        case '/login':
            $controller = new AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller->showLogin();
            } else {
                $controller->handleLogin();
            }
            break;

        case '/register':
            $controller = new AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller->showRegister();
            } else {
                $controller->handleRegister();
            }
            break;

        case '/logout':
            (new AuthController())->logout();
            break;

        /* ===================== DASHBOARD ===================== */
        case '/dashboard':
            require __DIR__ . '/views/dashboard/index.php';
            break;

        /* ===================== RENTALS ===================== */
        case '/rentals':
            (new RentalController())->index();
            break;

        case '/rentals/create':
            (new RentalController())->create();
            break;

        case '/my-rentals':
            (new RentalController())->myRentals();
            break;

        /* ===================== BOOKINGS ===================== */
        case '/bookings/create':
            (new BookingController())->create();
            break;

        case '/my-bookings':
            (new BookingController())->myBookings();
            break;

        /* ===================== ROUTES DYNAMIQUES ===================== */
        default:

            if (preg_match('#^/rentals/(\d+)$#', $uri, $m)) {
                (new RentalController())->show((int)$m[1]);
                break;
            }

            if (preg_match('#^/rentals/(\d+)/edit$#', $uri, $m)) {
                (new RentalController())->edit((int)$m[1]);
                break;
            }

            if (preg_match('#^/rentals/(\d+)/delete$#', $uri, $m)) {
                (new RentalController())->delete((int)$m[1]);
                break;
            }

            if (preg_match('#^/bookings/(\d+)/cancel$#', $uri, $m)) {
                (new BookingController())->cancel((int)$m[1]);
                break;
            }

            if (preg_match('#^/bookings/(\d+)/receipt$#', $uri, $m)) {
                (new BookingController())->downloadReceipt((int)$m[1]);
                break;
            }

            // 404 par défaut
            http_response_code(404);
            require __DIR__ . '/views/errors/404.php';
            break;
    }

} catch (Throwable $e) {
    // 500 Internal Server Error
    http_response_code(500);
    require __DIR__ . '/views/errors/500.php';
}
