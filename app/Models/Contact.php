<?php

namespace Models;

class Contact extends BaseModel {
    
    protected $table = 'contacts';
    
    /**
     * Получить контакты пользователя
     */
    public function findByUserId($userId) {
        return $this->where('user_id', $userId);
    }
    
    /**
     * Создать контакт
     */
    public function createContact($userId, $name, $email, $orderNum = 0) {
        return $this->create([
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'order_num' => $orderNum
        ]);
    }
    
    /**
     * Проверить принадлежность контакта пользователю
     */
    public function belongsToUser($contactId, $userId) {
        $stmt = $this->db()->prepare('SELECT id FROM contacts WHERE id = ? AND user_id = ?');
        $stmt->execute([$contactId, $userId]);
        return $stmt->fetch() !== false;
    }
}
