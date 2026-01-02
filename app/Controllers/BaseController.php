<?php

namespace Controllers;

abstract class BaseController {
    
    /**
     * Рендеринг шаблона
     */
    protected function render($template, $data = []) {
        extract($data);
        
        $title = $data['title'] ?? t('app_name');
        
        ob_start();
        require ROOT_PATH . "/templates/{$template}.php";
        $content = ob_get_clean();
        
        require ROOT_PATH . '/templates/layout.php';
    }
    
    /**
     * JSON ответ
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Проверка авторизации
     */
    protected function requireAuth() {
        if (!isLoggedIn()) {
            setFlash('error', t('error_login_required'));
            redirect('/login');
        }
    }
    
    /**
     * Проверка CSRF
     */
    protected function verifyCsrf() {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', t('error_csrf'));
            return false;
        }
        return true;
    }
    
    /**
     * Получить текущего пользователя
     */
    protected function currentUser() {
        return currentUser();
    }
    
    /**
     * GET параметр
     */
    protected function get($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * POST параметр
     */
    protected function post($key, $default = null) {
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Является ли запрос POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Является ли запрос GET
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}