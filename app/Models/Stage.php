<?php

namespace Models;

class Stage extends BaseModel {
    
    protected $table = 'stages';
    
    /**
     * Получить этапы похода
     */
    public function findByTripId($tripId) {
        $stmt = $this->db()->prepare('SELECT * FROM stages WHERE trip_id = ? ORDER BY stage_number');
        $stmt->execute([$tripId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Создать этап
     */
    public function createStage($tripId, $data) {
        return $this->create(array_merge([
            'trip_id' => $tripId,
            'status' => 'pending',
            'confirm_token' => generateToken()
        ], $data));
    }
    
    /**
     * Получить следующий этап
     */
    public function getNextStage($tripId, $currentStageNumber) {
        $stmt = $this->db()->prepare('
            SELECT * FROM stages 
            WHERE trip_id = ? AND stage_number > ?
            ORDER BY stage_number 
            LIMIT 1
        ');
        $stmt->execute([$tripId, $currentStageNumber]);
        return $stmt->fetch();
    }
    
    /**
     * Активировать этап
     */
    public function activate($stageId, $deadlineUtc) {
        return $this->update($stageId, [
            'status' => 'active',
            'activated_at' => date('Y-m-d H:i:s'),
            'deadline_utc' => $deadlineUtc
        ]);
    }
    
    /**
     * Подтвердить этап
     */
    public function confirm($stageId) {
        return $this->update($stageId, [
            'status' => 'confirmed',
            'confirmed_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Проверить принадлежность этапа пользователю
     */
    public function belongsToUser($stageId, $userId) {
        $stmt = $this->db()->prepare('
            SELECT s.id 
            FROM stages s
            JOIN trips t ON s.trip_id = t.id
            WHERE s.id = ? AND t.user_id = ?
        ');
        $stmt->execute([$stageId, $userId]);
        return $stmt->fetch() !== false;
    }
}
