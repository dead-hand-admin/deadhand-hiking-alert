<?php

namespace Models;

class User extends BaseModel {
    
    protected $table = 'users';
    
    /**
     * Найти по email
     */
    public function findByEmail($email) {
        return $this->first('email', $email);
    }
    
    /**
     * Найти по токену подтверждения
     */
    public function findByConfirmToken($token) {
        return $this->first('email_confirm_token', $token);
    }
    
    /**
     * Создать пользователя
     */
    public function createUser($email, $password, $gdprToken, $confirmToken, $ip) {
        return $this->create([
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'email_confirm_token' => $confirmToken,
            'gdpr_token' => $gdprToken,
            'gdpr_ip' => $ip,
            'gdpr_agreed' => true,
            'gdpr_agreed_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Подтвердить email
     */
    public function confirmEmail($userId) {
        return $this->update($userId, [
            'email_confirmed' => true,
            'email_confirm_token' => null
        ]);
    }
    
    /**
     * Обновить профиль
     */
    public function updateProfile($userId, $data) {
        return $this->update($userId, $data);
    }
    
    /**
     * Проверить пароль
     */
    public function verifyPassword($user, $password) {
        return password_verify($password, $user['password_hash']);
    }
}
