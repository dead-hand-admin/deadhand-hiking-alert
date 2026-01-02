<?php
/**
 * Фронт-контроллер
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Автозагрузчик классов
spl_autoload_register(function ($class) {
    $file = ROOT_PATH . '/app/' . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Обработка переключения языка
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ru', 'en'])) {
    setLanguage($_GET['lang']);
    $cleanUrl = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $cleanUrl");
    exit;
}

// Получаем URI и метод
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$uri = rtrim($uri, '/');
if (empty($uri)) {
    $uri = '/';
}

// Маршруты
$routes = [
    '/' => ['Controllers\PageController', 'home'],
    
    '/register' => ['Controllers\AuthController', 'register'],
    '/login' => ['Controllers\AuthController', 'login'],
    '/logout' => ['Controllers\AuthController', 'logout'],
    '/confirm-email' => ['Controllers\AuthController', 'confirmEmail'],
    
    '/profile' => ['Controllers\ProfileController', 'index'],
    '/profile/update' => ['Controllers\ProfileController', 'update'],
    '/profile/contact/add' => ['Controllers\ProfileController', 'addContact'],
    '/profile/contact/delete' => ['Controllers\ProfileController', 'deleteContact'],
    '/profile/photo/upload' => ['Controllers\ProfileController', 'uploadPhoto'],
    '/profile/photo/delete' => ['Controllers\ProfileController', 'deletePhoto'],
    
    '/trips' => ['Controllers\TripController', 'index'],
    '/trip/create' => ['Controllers\TripController', 'create'],
    '/trip/edit' => ['Controllers\TripController', 'edit'],
    '/trip/update' => ['Controllers\TripController', 'update'],
    '/trip/view' => ['Controllers\TripController', 'view'],
    '/trip/confirm' => ['Controllers\TripController', 'confirm'],
    '/trip/cancel' => ['Controllers\TripController', 'cancel'],
    '/trip/copy' => ['Controllers\TripController', 'copy'],
    '/trip/delete' => ['Controllers\TripController', 'delete'],
    '/trip/file/delete' => ['Controllers\TripController', 'deleteFile'],
    
    '/stage/confirm' => ['Controllers\TripController', 'confirmStage'],
    
    '/action' => ['Controllers\ActionController', 'handle'],
];

// Роутинг
if (isset($routes[$uri])) {
    list($controllerClass, $methodName) = $routes[$uri];
    
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        
        if (method_exists($controller, $methodName)) {
            $controller->$methodName();
        } else {
            http_response_code(404);
            echo t('error_404');
        }
    } else {
        http_response_code(404);
        echo t('error_404');
    }
} else {
    http_response_code(404);
    echo t('error_404');
}