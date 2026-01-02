<?php

namespace Models;

class ActionToken extends BaseModel {
    
    protected $table = 'action_tokens';
    
    /**
     * Генерация токена для действия
     * Использует do-while для корректной обработки race conditions
     */
    public function generateToken($tripId, $action) {
        global $pdo;
        
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        $maxAttempts = 10;
        $attempt = 0;
        
        do {
            $attempt++;
            
            // Проверяем, есть ли уже токен для этого trip_id + action
            $stmt = $pdo->prepare("SELECT token FROM {$this->table} WHERE trip_id = ? AND action = ?");
            $stmt->execute([$tripId, $action]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Нашли существующий — возвращаем
                return $existing['token'];
            }
            
            // Не нашли — генерируем и пытаемся вставить
            $token = $this->generateRandomToken();
            
            $sql = "INSERT IGNORE INTO {$this->table} 
                    (token, trip_id, action, expires_at) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$token, $tripId, $action, $expiresAt]);
            
            if ($attempt >= $maxAttempts) {
                throw new \Exception("Failed to generate unique token after $maxAttempts attempts");
            }
            
        } while ($stmt->rowCount() === 0); // Повторяем, пока вставка не удалась
        
        // Вставка удалась — возвращаем токен
        return $token;
    }
    
    /**
     * Генерация случайного токена (8 символов без похожих)
     */
    private function generateRandomToken() {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $token = '';
        for ($i = 0; $i < 8; $i++) {
            $token .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $token;
    }
    
    /**
     * Получение токена
     */
    public function findByToken($token) {
        global $pdo;
        
        $sql = "SELECT * FROM {$this->table} WHERE token = ? AND expires_at > NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token]);
        
        return $stmt->fetch();
    }
    
    /**
     * Генерация всех токенов для похода
     */
    public function generateTripTokens($tripId) {
        return [
            'cancel_trip' => $this->generateToken($tripId, 'cancel_trip'),
            'complete_trip' => $this->generateToken($tripId, 'complete_trip'),
            'confirm_stage' => $this->generateToken($tripId, 'confirm_stage'),
            'extend_stage' => $this->generateToken($tripId, 'extend_stage'),
        ];
    }
    
    /**
     * Удаление просроченных токенов
     */
    public function cleanExpired() {
        global $pdo;
        
        $sql = "DELETE FROM {$this->table} WHERE expires_at < NOW()";
        return $pdo->exec($sql);
    }
}