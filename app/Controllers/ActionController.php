<?php

namespace Controllers;

use Models\ActionToken;
use Models\Trip;
use Models\Stage;
use Services\TripService;

class ActionController extends BaseController {
    
    private $tokenModel;
    private $tripModel;
    private $stageModel;
    private $tripService;
    
    public function __construct() {
        $this->tokenModel = new ActionToken();
        $this->tripModel = new Trip();
        $this->stageModel = new Stage();
        $this->tripService = new TripService();
    }
    
    /**
     * Обработка действия по токену
     */
    public function handle() {
        $token = $this->get('t');
        
        if (!$token) {
            $this->render('action/error', [
                'title' => t('action_token_invalid'),
                'message' => t('action_token_invalid')
            ]);
            return;
        }
        
        $actionToken = $this->tokenModel->findByToken($token);
        
        if (!$actionToken) {
            $this->render('action/error', [
                'title' => t('action_token_invalid'),
                'message' => t('action_token_invalid')
            ]);
            return;
        }
        
        // Получаем данные похода
        $trip = $this->tripModel->findById($actionToken['trip_id']);
        if (!$trip) {
            $this->render('action/error', [
                'title' => t('error_trip_not_found'),
                'message' => t('error_trip_not_found')
            ]);
            return;
        }
        
        // Для cancel/complete проверяем, что поход активный
        if (in_array($actionToken['action'], ['cancel_trip', 'complete_trip'])) {
            if ($trip['status'] !== 'active') {
                $this->render('action/error', [
                    'title' => t('error'),
                    'message' => t('error_trip_not_active')
                ]);
                return;
            }
        }
        
        // Получаем активный этап для confirm_stage и extend_stage
        $stage = null;
        if (in_array($actionToken['action'], ['confirm_stage', 'extend_stage'])) {
            global $pdo;
            $stmt = $pdo->prepare("SELECT * FROM stages WHERE trip_id = ? AND status = 'active' ORDER BY stage_number LIMIT 1");
            $stmt->execute([$trip['id']]);
            $stage = $stmt->fetch();
            
            if (!$stage) {
                $this->render('action/error', [
                    'title' => t('error'),
                    'message' => t('error_no_active_stage')
                ]);
                return;
            }
        }
        
        // Обработка POST-запроса
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->executeAction($actionToken, $trip, $stage);
        }
        
        // Отображение формы подтверждения
        $this->showConfirmation($actionToken, $trip, $stage);
    }
    
    /**
     * Отображение формы подтверждения
     */
    private function showConfirmation($actionToken, $trip, $stage) {
        switch ($actionToken['action']) {
            case 'confirm_stage':
                $this->render('action/confirm_stage', [
                    'title' => t('action_confirm_stage_title'),
                    'trip' => $trip,
                    'stage' => $stage,
                    'token' => $actionToken['token']
                ]);
                break;
                
            case 'cancel_trip':
                $this->render('action/cancel_trip', [
                    'title' => t('action_cancel_trip_title'),
                    'trip' => $trip,
                    'token' => $actionToken['token']
                ]);
                break;
                
            case 'complete_trip':
                $this->render('action/complete_trip', [
                    'title' => t('action_complete_trip_title'),
                    'trip' => $trip,
                    'token' => $actionToken['token']
                ]);
                break;
                
            case 'extend_stage':
                $this->render('action/extend_stage', [
                    'title' => t('action_extend_stage_title'),
                    'trip' => $trip,
                    'stage' => $stage,
                    'token' => $actionToken['token']
                ]);
                break;
                
            default:
                $this->render('action/error', [
                    'title' => t('action_token_invalid'),
                    'message' => t('action_token_invalid')
                ]);
        }
    }
    
    /**
     * Выполнение действия
     */
    private function executeAction($actionToken, $trip, $stage) {
        global $pdo;
        
        try {
            $pdo->beginTransaction();
            
            switch ($actionToken['action']) {
                case 'confirm_stage':
                    // Получаем пользователя для timezone
                    $stmt = $pdo->prepare("SELECT u.id, u.timezone FROM users u 
                                           JOIN trips t ON t.user_id = u.id 
                                           WHERE t.id = ?");
                    $stmt->execute([$trip['id']]);
                    $user = $stmt->fetch();
                    
                    $this->tripService->confirmStage($stage['id'], $user['id'], $user['timezone']);
                    $message = t('action_success');
                    break;
                    
                case 'cancel_trip':
                    $this->tripModel->updateStatus($trip['id'], 'cancelled');
                    $message = t('action_success');
                    break;
                    
                case 'complete_trip':
                    $this->tripModel->updateStatus($trip['id'], 'completed');
                    $message = t('action_success');
                    break;
                    
                case 'extend_stage':
                    $this->handleExtendStage($stage);
                    $message = t('action_success');
                    break;
                    
                default:
                    throw new \Exception(t('action_token_invalid'));
            }
            
            $pdo->commit();
            
            $this->render('action/success', [
                'title' => t('action_success'),
                'message' => $message,
                'trip' => $trip
            ]);
            
        } catch (\Exception $e) {
            $pdo->rollBack();
            
            $this->render('action/error', [
                'title' => t('error'),
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Обработка продления этапа
     */
    private function handleExtendStage($stage) {
        $method = $this->post('method'); // 'hours' или 'date'
        
        if ($method === 'hours') {
            $hours = (int)$this->post('hours');
            if ($hours < 1) {
                throw new \Exception(t('error_extend_hours_required'));
            }
            
            $newDeadline = date('Y-m-d H:i:s', strtotime($stage['deadline_utc'] . ' +' . $hours . ' hours'));
            
        } else if ($method === 'date') {
            $date = $this->post('date');
            $time = $this->post('time');
            
            if (!$date || !$time) {
                throw new \Exception(t('error_extend_date_required'));
            }
            
            // Получаем таймзону пользователя
            global $pdo;
            $stmt = $pdo->prepare("SELECT timezone FROM users u 
                                   JOIN trips t ON t.user_id = u.id 
                                   WHERE t.id = ?");
            $stmt->execute([$stage['trip_id']]);
            $userData = $stmt->fetch();
            $userTimezone = $userData['timezone'] ?? 'UTC';
            
            // Конвертируем в UTC
            $userDateTime = new \DateTime("{$date} {$time}", new \DateTimeZone($userTimezone));
            $userDateTime->setTimezone(new \DateTimeZone('UTC'));
            $newDeadline = $userDateTime->format('Y-m-d H:i:s');
            
        } else {
            throw new \Exception(t('error_extend_date_required'));
        }
        
        // Обновляем дедлайн
        global $pdo;
        $stmt = $pdo->prepare("UPDATE stages SET deadline_utc = ? WHERE id = ?");
        $stmt->execute([$newDeadline, $stage['id']]);
    }
}