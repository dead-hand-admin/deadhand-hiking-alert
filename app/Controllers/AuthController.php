<?php

namespace Controllers;

use Models\User;

class AuthController extends BaseController {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Регистрация
     */
    public function register() {
        if ($this->isPost()) {
            return $this->processRegister();
        }
        
        $this->render('auth/register', [
            'title' => t('register_title')
        ]);
    }
    
    private function processRegister() {
        if (!$this->verifyCsrf()) {
            redirect('/register');
        }
        
        $email = trim($this->post('email', ''));
        $password = $this->post('password', '');
        $passwordConfirm = $this->post('password_confirm', '');
        $gdprAgree = $this->post('gdpr_agree');
        
        // Валидация
        if (!validateEmail($email)) {
            setFlash('error', t('error_email_invalid'));
            redirect('/register');
        }
        
        if (strlen($password) < 8) {
            setFlash('error', t('error_password_short'));
            redirect('/register');
        }
        
        if ($password !== $passwordConfirm) {
            setFlash('error', t('error_passwords_mismatch'));
            redirect('/register');
        }
        
        if (!$gdprAgree) {
            setFlash('error', t('error_gdpr_required'));
            redirect('/register');
        }
        
        // Проверка существования email
        if ($this->userModel->findByEmail($email)) {
            setFlash('error', t('error_email_exists'));
            redirect('/register');
        }
        
        // Создание пользователя
        try {
            $confirmToken = generateToken();
            $gdprToken = generateToken();
            $userIp = $_SERVER['REMOTE_ADDR'] ?? '';
            
            $userId = $this->userModel->createUser($email, $password, $gdprToken, $confirmToken, $userIp);
            
            // TODO: Отправка письма с подтверждением
            logMessage("User registered: $email (ID: $userId). Confirm token: $confirmToken");
            
            // Пока автоматически подтверждаем email (для тестирования)
            $this->userModel->confirmEmail($userId);
            
            setFlash('success', t('success_registration'));
            redirect('/login');
            
        } catch (\Exception $e) {
            logMessage('Registration error: ' . $e->getMessage());
            setFlash('error', t('error_registration_failed'));
            redirect('/register');
        }
    }
    
    /**
     * Вход
     */
    public function login() {
        if ($this->isPost()) {
            return $this->processLogin();
        }
        
        $this->render('auth/login', [
            'title' => t('login_title')
        ]);
    }
    
    private function processLogin() {
        if (!$this->verifyCsrf()) {
            redirect('/login');
        }
        
        $email = trim($this->post('email', ''));
        $password = $this->post('password', '');
        
        // Поиск пользователя
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($user, $password)) {
            setFlash('error', t('error_login_failed'));
            redirect('/login');
        }
        
        // Проверка подтверждения email
        if (!$user['email_confirmed']) {
            setFlash('error', t('error_email_not_confirmed'));
            redirect('/login');
        }
        
        // Авторизация
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        
        logMessage("User logged in: {$user['email']} (ID: {$user['id']})");
        
        setFlash('success', t('success_login'));
        redirect('/profile');
    }
    
    /**
     * Выход
     */
    public function logout() {
        $email = $_SESSION['user_email'] ?? 'unknown';
        
        session_destroy();
        session_start(); // Перезапускаем для flash
        
        logMessage("User logged out: $email");
        
        setFlash('success', t('success_logout'));
        redirect('/');
    }
    
    /**
     * Подтверждение email
     */
    public function confirmEmail() {
        $token = $this->get('token', '');
        
        if (empty($token)) {
            setFlash('error', t('error_invalid_token'));
            redirect('/');
        }
        
        $user = $this->userModel->findByConfirmToken($token);
        
        if (!$user) {
            setFlash('error', t('error_invalid_token'));
            redirect('/');
        }
        
        // Подтверждаем email
        $this->userModel->confirmEmail($user['id']);
        
        setFlash('success', t('success_email_confirmed'));
        redirect('/login');
    }
}
