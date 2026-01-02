<?php
/**
 * Конфигурация приложения
 * Загружает переменные из .env файла
 */

define('ROOT_PATH', __DIR__);

// Загрузка .env
function loadEnv($path) {
    if (!file_exists($path)) {
        die('.env file not found');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Пропускаем комментарии
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Парсим KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Убираем кавычки если есть
            $value = trim($value, '"\'');
            
            // Устанавливаем в $_ENV и константу
            $_ENV[$key] = $value;
            if (!defined($key)) {
                define($key, $value);
            }
        }
    }
}

// Загружаем .env
loadEnv(__DIR__ . '/.env');

// Пути
define('ROOT_PATH', __DIR__);
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');

// Создаём необходимые папки если их нет
$dirs = [
    UPLOAD_PATH . '/photos',
    UPLOAD_PATH . '/tracks',
    LOG_PATH
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Лимиты
define('MAX_PHOTOS_PROFILE', 5);
define('MAX_PHOTOS_TRIP', 2);
define('MAX_CONTACTS', 10);
define('PHOTO_MAX_SIZE', 5 * 1024 * 1024); // 5 MB
define('PHOTO_RESIZE_WIDTH', 1200);
define('PHOTO_RESIZE_HEIGHT', 1600);
define('PHOTO_JPEG_QUALITY', 85);

// Лимиты файлов
define('MAX_PHOTOS_TRIP', 2); // Максимум фото для похода

// Ключ для доступа к миграциям (опционально, только если задан в .env)
if (isset($_ENV['MIGRATION_KEY'])) {
    define('MIGRATION_KEY', $_ENV['MIGRATION_KEY']);
}

// Временная зона
date_default_timezone_set('UTC');

// Обработка ошибок
if (APP_DEBUG === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_PATH . '/php_errors.log');
}

// Старт сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Загрузка вспомогательных функций
require_once ROOT_PATH . '/app/Helpers/functions.php';

// Загрузка языка
$currentLang = detectLanguage();
loadLanguage($currentLang);
