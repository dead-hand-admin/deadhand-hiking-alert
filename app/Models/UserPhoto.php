<?php

namespace Models;

class UserPhoto extends BaseModel {
    
    protected $table = 'user_photos';
    
    /**
     * Получить фото пользователя
     */
    public function findByUserId($userId) {
        $stmt = $this->db()->prepare('SELECT * FROM user_photos WHERE user_id = ? ORDER BY uploaded_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Создать фото
     */
    public function createPhoto($userId, $filePath, $description = '') {
        return $this->create([
            'user_id' => $userId,
            'file_path' => $filePath,
            'description' => $description
        ]);
    }
    
    /**
     * Проверить принадлежность фото пользователю
     */
    public function belongsToUser($photoId, $userId) {
        $stmt = $this->db()->prepare('SELECT * FROM user_photos WHERE id = ? AND user_id = ?');
        $stmt->execute([$photoId, $userId]);
        return $stmt->fetch();
    }
}