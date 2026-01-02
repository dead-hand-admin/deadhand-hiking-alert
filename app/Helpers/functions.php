<?php
/**
 * Вспомогательные функции
 */

/**
 * Авторизация
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    static $user = null;
    
    if ($user === null) {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
    
    return $user;
}

/**
 * Редиректы
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

function redirectWithParams($url, $params = []) {
    if (!empty($params)) {
        $queryString = http_build_query($params);
        $url .= '?' . $queryString;
    }
    redirect($url);
}

function redirectKeepingParams($url, $override = [], $remove = []) {
    $params = $_GET;
    
    foreach ($remove as $key) {
        unset($params[$key]);
    }
    
    $params = array_merge($params, $override);
    redirectWithParams($url, $params);
}

function getCurrentParams($override = [], $remove = []) {
    $params = $_GET;
    
    foreach ($remove as $key) {
        unset($params[$key]);
    }
    
    $params = array_merge($params, $override);
    return $params;
}

/**
 * Безопасность
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function csrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Flash сообщения
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Валидация
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Логирование
 */
function logMessage($message, $level = 'info') {
    $logFile = LOG_PATH . '/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $line = "[$timestamp] [$level] $message\n";
    file_put_contents($logFile, $line, FILE_APPEND);
}

/**
 * Мультиязычность
 */
$GLOBALS['translations'] = [];
$GLOBALS['current_lang'] = 'ru';

function loadLanguage($lang = 'ru') {
    $langFile = ROOT_PATH . "/lang/$lang.php";
    
    if (!file_exists($langFile)) {
        $lang = 'ru';
        $langFile = ROOT_PATH . "/lang/ru.php";
    }
    
    $GLOBALS['translations'] = require $langFile;
    $GLOBALS['current_lang'] = $lang;
}

function t($key, $default = null) {
    if (isset($GLOBALS['translations'][$key])) {
        return $GLOBALS['translations'][$key];
    }
    return $default ?? $key;
}

function __($key, $default = null) {
    return t($key, $default);
}

function getCurrentLang() {
    return $GLOBALS['current_lang'];
}

function detectLanguage() {
    if (isset($_SESSION['lang'])) {
        return $_SESSION['lang'];
    }
    
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['ru', 'en'])) {
        $_SESSION['lang'] = $_GET['lang'];
        return $_GET['lang'];
    }
    
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $lang = substr($langs[0], 0, 2);
        
        if (in_array($lang, ['ru', 'en'])) {
            return $lang;
        }
    }
    
    return 'ru';
}

function setLanguage($lang) {
    if (in_array($lang, ['ru', 'en'])) {
        $_SESSION['lang'] = $lang;
        loadLanguage($lang);
    }
}