<?php

namespace Services;

use DateTime;
use DateTimeZone;

class TripService {
    
    /**
     * Рассчитать дедлайн этапа
     */
    public static function calculateDeadline($activatedAt, $durationDays, $deadlineTime, $userTimezone) {
        $activated = new DateTime($activatedAt, new DateTimeZone($userTimezone));
        
        // Берём только дату старта
        $deadline = new DateTime($activated->format('Y-m-d'), new DateTimeZone($userTimezone));
        
        // Добавляем полные сутки
        if ($durationDays > 0) {
            $deadline->modify("+{$durationDays} days");
        }
        
        // Устанавливаем время финиша
        list($hour, $minute) = explode(':', $deadlineTime);
        $deadline->setTime((int)$hour, (int)$minute, 0);
        
        // Если время уже прошло — добавляем день
        if ($deadline <= $activated) {
            $deadline->modify('+1 day');
        }
        
        // Конвертируем в UTC для хранения в БД
        $deadline->setTimezone(new DateTimeZone('UTC'));
        return $deadline->format('Y-m-d H:i:s');
    }
    
    /**
     * Активировать этап (установить дедлайн)
     */
    public static function activateStage($stageId, $userTimezone) {
        $stmt = db()->prepare('SELECT * FROM stages WHERE id = ?');
        $stmt->execute([$stageId]);
        $stage = $stmt->fetch();
        
        if (!$stage) {
            throw new \Exception('Stage not found');
        }
        
        // Время активации — сейчас
        $now = new DateTime('now', new DateTimeZone($userTimezone));
        $activatedAt = $now->format('Y-m-d H:i:s');
        
        // Рассчитываем дедлайн
        $deadlineUtc = self::calculateDeadline(
            $activatedAt,
            $stage['duration_days'],
            $stage['deadline_time'],
            $userTimezone
        );
        
        // Обновляем этап
        $stmt = db()->prepare('
            UPDATE stages 
            SET status = "active", 
                activated_at = NOW(), 
                deadline_utc = ?
            WHERE id = ?
        ');
        $stmt->execute([$deadlineUtc, $stageId]);
        
        logMessage("Stage activated: stage_id=$stageId, deadline_utc=$deadlineUtc");
    }
    
    /**
     * Подтвердить этап и активировать следующий
     */
    public static function confirmStage($stageId, $userId, $userTimezone) {
        // Получаем этап
        $stmt = db()->prepare('
            SELECT s.*, t.user_id, t.id as trip_id
            FROM stages s
            JOIN trips t ON s.trip_id = t.id
            WHERE s.id = ?
        ');
        $stmt->execute([$stageId]);
        $stage = $stmt->fetch();
        
        if (!$stage) {
            throw new \Exception('Stage not found');
        }
        
        if ($stage['user_id'] != $userId) {
            throw new \Exception('Not your stage');
        }
        
        if ($stage['status'] !== 'active') {
            throw new \Exception('Stage is not active');
        }
        
        // Начинаем транзакцию
        db()->beginTransaction();
        
        try {
            // Подтверждаем текущий этап
            $stmt = db()->prepare('
                UPDATE stages 
                SET status = "confirmed", confirmed_at = NOW() 
                WHERE id = ?
            ');
            $stmt->execute([$stageId]);
            
            // Отменяем письма в очереди
            $stmt = db()->prepare('
                UPDATE alert_queue 
                SET status = "cancelled" 
                WHERE stage_id = ? AND status = "pending"
            ');
            $stmt->execute([$stageId]);
            
            // Ищем следующий этап
            $stmt = db()->prepare('
                SELECT * FROM stages 
                WHERE trip_id = ? AND stage_number > ?
                ORDER BY stage_number 
                LIMIT 1
            ');
            $stmt->execute([$stage['trip_id'], $stage['stage_number']]);
            $nextStage = $stmt->fetch();
            
            if ($nextStage) {
                // Активируем следующий этап
                self::activateStage($nextStage['id'], $userTimezone);
                
                // TODO: Отправить письмо о переходе к следующему этапу
                // Токены уже существуют и работают с любым активным этапом
            } else {
                // Это был последний этап — завершаем поход
                $stmt = db()->prepare('
                    UPDATE trips 
                    SET status = "completed", completed_at = NOW() 
                    WHERE id = ?
                ');
                $stmt->execute([$stage['trip_id']]);
                
                logMessage("Trip completed: trip_id={$stage['trip_id']}");
            }
            
            db()->commit();
            logMessage("Stage confirmed: stage_id=$stageId, user_id=$userId");
            
        } catch (\Exception $e) {
            db()->rollBack();
            throw $e;
        }
    }
}