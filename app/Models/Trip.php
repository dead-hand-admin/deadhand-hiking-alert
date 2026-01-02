<?php

namespace Models;

class Trip extends BaseModel {
    
    protected $table = 'trips';
    
    /**
     * Получить походы пользователя с дополнительной информацией
     */
    public function findByUserId($userId) {
        $stmt = $this->db()->prepare('
            SELECT t.*, 
                   COUNT(s.id) as stages_count,
                   c.name_' . getCurrentLang() . ' as country_name
            FROM trips t
            LEFT JOIN stages s ON t.id = s.trip_id
            LEFT JOIN countries c ON t.country_code = c.code
            WHERE t.user_id = ?
            GROUP BY t.id
            ORDER BY 
                CASE 
                    WHEN t.status = "active" THEN 1
                    WHEN t.status = "draft" THEN 2
                    ELSE 3
                END,
                t.start_date DESC,
                t.created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Получить поход с этапами
     */
    public function findWithStages($tripId) {
        $trip = $this->find($tripId);
        
        if (!$trip) {
            return null;
        }
        
        $stmt = $this->db()->prepare('SELECT * FROM stages WHERE trip_id = ? ORDER BY stage_number');
        $stmt->execute([$tripId]);
        $trip['stages'] = $stmt->fetchAll();
        
        return $trip;
    }
    
    /**
     * Создать поход
     */
    public function createTrip($userId, $data) {
        return $this->create(array_merge([
            'user_id' => $userId,
            'status' => 'draft',
            'confirmed' => false,
            'confirm_token' => generateToken()
        ], $data));
    }
    
    /**
     * Проверить принадлежность похода пользователю
     */
    public function belongsToUser($tripId, $userId) {
        $stmt = $this->db()->prepare('SELECT id FROM trips WHERE id = ? AND user_id = ?');
        $stmt->execute([$tripId, $userId]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Получить активные походы пользователя
     */
    public function getActiveTrips($userId) {
        return $this->where('user_id', $userId) && $this->where('status', 'active');
    }
    
    /**
     * Подсчитать активные походы
     */
    public function countActiveTrips($userId) {
        $stmt = $this->db()->prepare('SELECT COUNT(*) as cnt FROM trips WHERE user_id = ? AND status = "active"');
        $stmt->execute([$userId]);
        return $stmt->fetch()['cnt'];
    }
}
