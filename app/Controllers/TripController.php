<?php

namespace Controllers;

use Models\Trip;
use Models\Stage;
use Services\TripService;
use Services\ImageProcessor;

class TripController extends BaseController {
    
    private $tripModel;
    private $stageModel;
    
    public function __construct() {
        $this->tripModel = new Trip();
        $this->stageModel = new Stage();
    }
    
    /**
     * Список походов
     */
    public function index() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $trips = $this->tripModel->findByUserId($userId);
        
        // Группируем по статусам
        $tripsByStatus = [
            'active' => [],
            'draft' => [],
            'completed' => [],
            'cancelled' => []
        ];
        
        foreach ($trips as $trip) {
            $tripsByStatus[$trip['status']][] = $trip;
        }
        
        $this->render('trips/index', [
            'title' => t('trips_title'),
            'tripsByStatus' => $tripsByStatus,
            'currentTab' => $this->get('tab', 'active')
        ]);
    }
    
    /**
     * Создание похода
     */
    public function create() {
        $this->requireAuth();
        
        if ($this->isPost()) {
            return $this->processCreate();
        }
        
        $this->showCreateForm();
    }
    
    private function showCreateForm() {
        $user = $this->currentUser();
        
        // Получаем список стран
        $stmt = db()->query('SELECT * FROM countries ORDER BY name_' . getCurrentLang());
        $countries = $stmt->fetchAll();
        
        // Получаем ВСЕ службы спасения (для JS-фильтрации)
        $stmt = db()->query('
            SELECT id, country_code, name_' . getCurrentLang() . ' as name, is_default, is_enabled
            FROM emergency_services 
            WHERE is_enabled = TRUE
            ORDER BY country_code, is_default DESC, name_' . getCurrentLang());
        $allServices = $stmt->fetchAll();
        
        $this->render('trips/create', [
            'title' => t('trip_create_title'),
            'user' => $user,
            'countries' => $countries,
            'allServices' => $allServices
        ]);
    }
    
    private function processCreate() {
        if (!$this->verifyCsrf()) {
            redirect('/trip/create');
        }
        
        $userId = $_SESSION['user_id'];
        $user = $this->currentUser();
        
        $action = $this->post('action', 'create');
        $isDraft = ($action === 'draft');
        
        // Получаем данные
        $name = trim($this->post('name', ''));
        $startDate = !empty($this->post('start_date')) ? $this->post('start_date') : null;
        $countryCode = $this->post('country_code', '');
        $emergencyServiceId = !empty($this->post('emergency_service_id')) ? (int)$this->post('emergency_service_id') : null;
        $stages = $this->post('stages', []);
        
        // Валидация
        if (empty($name)) {
            setFlash('error', t('error_trip_name_required'));
            redirect('/trip/create');
        }
        
        if (!$isDraft && empty($startDate)) {
            setFlash('error', t('error_trip_start_date_required'));
            redirect('/trip/create');
        }
        
        if (!empty($startDate) && strtotime($startDate) < strtotime('today')) {
            setFlash('error', t('error_trip_start_date_past'));
            redirect('/trip/create');
        }
        
        if (empty($countryCode)) {
            setFlash('error', t('error_trip_country_required'));
            redirect('/trip/create');
        }
        
        if (empty($stages)) {
            setFlash('error', t('error_trip_stages_required'));
            redirect('/trip/create');
        }
        
        // Проверка активных походов
        if (!$isDraft) {
            if ($this->tripModel->countActiveTrips($userId) > 0) {
                setFlash('error', t('error_trip_already_active'));
                redirect('/trip/create');
            }
        }
        
        try {
            db()->beginTransaction();
            
            // Создаём поход
            $tripId = $this->tripModel->createTrip($userId, [
                'name' => $name,
                'country_code' => $countryCode,
                'emergency_service_id' => $emergencyServiceId,
                'start_date' => $startDate
            ]);
            
            // Создаём этапы
            $stageNumber = 1;
            foreach ($stages as $stageData) {
                $description = trim($stageData['description'] ?? '');
                $location = trim($stageData['location'] ?? '');
                $durationDays = (int)($stageData['duration_days'] ?? 0);
                $deadlineTime = $stageData['deadline_time'] ?? '20:00';
                $stageEmergencyServiceId = !empty($stageData['emergency_service_id']) ? (int)$stageData['emergency_service_id'] : $emergencyServiceId;
                $requiresConfirmation = isset($stageData['requires_confirmation']);
                
                if (empty($description)) {
                    throw new \Exception(t('error_trip_stage_description_required'));
                }
                
                if ($durationDays < 0) {
                    throw new \Exception(t('error_trip_stage_duration_required'));
                }
                
                $this->stageModel->createStage($tripId, [
                    'stage_number' => $stageNumber,
                    'description' => $description,
                    'location' => $location,
                    'duration_days' => $durationDays,
                    'deadline_time' => $deadlineTime,
                    'emergency_service_id' => $stageEmergencyServiceId,
                    'requires_confirmation' => $requiresConfirmation ? 1 : 0
                ]);
                
                $stageNumber++;
            }
            
            // Обработка файлов
            if (!$isDraft) {
                $this->handleTripFiles($tripId);
            }
            
            db()->commit();
            
            logMessage("Trip created: trip_id=$tripId, user_id=$userId, is_draft=" . ($isDraft ? 'true' : 'false'));
            
            if ($isDraft) {
                setFlash('success', t('success_trip_draft_saved'));
            } else {
                // TODO: Отправить письмо с подтверждением
                setFlash('success', t('success_trip_created'));
            }
            
            redirectWithParams('/trip/view', ['id' => $tripId]);
            
        } catch (\Exception $e) {
            db()->rollBack();
            logMessage("Trip creation error: " . $e->getMessage());
            setFlash('error', t('error_trip_create_failed') . ': ' . $e->getMessage());
            redirect('/trip/create');
        }
    }
    
    /**
     * Обработка загрузки файлов
     */
    private function handleTripFiles($tripId) {
        // Обработка трека
        if (isset($_FILES['track']) && $_FILES['track']['error'] === UPLOAD_ERR_OK) {
            $track = $_FILES['track'];
            
            $ext = strtolower(pathinfo($track['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['gpx', 'kml', 'kmz'])) {
                throw new \Exception(t('error_track_invalid_format'));
            }
            
            if ($track['size'] > 5 * 1024 * 1024) {
                throw new \Exception(t('error_track_too_large'));
            }
            
            $filename = uniqid('track_', true) . '.' . $ext;
            $targetDir = UPLOAD_PATH . '/tracks';
            
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            $targetPath = $targetDir . '/' . $filename;
            
            if (move_uploaded_file($track['tmp_name'], $targetPath)) {
                $stmt = db()->prepare('
                    INSERT INTO trip_files (trip_id, file_type, file_path, original_name, file_size)
                    VALUES (?, "track", ?, ?, ?)
                ');
                $stmt->execute([
                    $tripId,
                    'uploads/tracks/' . $filename,
                    $track['name'],
                    $track['size']
                ]);
            }
        }
        
        // Обработка фото (до 2 шт)
        if (isset($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
            $photoCount = 0;
            
            foreach ($_FILES['photos']['name'] as $index => $name) {
                if ($_FILES['photos']['error'][$index] !== UPLOAD_ERR_OK) {
                    continue;
                }
                
                if ($photoCount >= MAX_PHOTOS_TRIP) {
                    break;
                }
                
                $tmpName = $_FILES['photos']['tmp_name'][$index];
                $size = $_FILES['photos']['size'][$index];
                
                if ($size > PHOTO_MAX_SIZE) {
                    continue;
                }
                
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $tmpName);
                finfo_close($finfo);
                
                if (!in_array($mimeType, $allowedTypes)) {
                    continue;
                }
                
                $filename = ImageProcessor::resizeAndSave(
                    $tmpName,
                    UPLOAD_PATH . '/tracks',
                    PHOTO_RESIZE_WIDTH,
                    PHOTO_RESIZE_HEIGHT,
                    PHOTO_JPEG_QUALITY
                );
                
                $stmt = db()->prepare('
                    INSERT INTO trip_files (trip_id, file_type, file_path, original_name, file_size)
                    VALUES (?, "photo", ?, ?, ?)
                ');
                $stmt->execute([
                    $tripId,
                    'uploads/tracks/' . $filename,
                    $name,
                    $size
                ]);
                
                $photoCount++;
            }
        }
    }
    
    /**
     * Просмотр похода
     */
    public function view() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $tripId = $this->get('id', 0);
        
        // Проверка принадлежности
        if (!$this->tripModel->belongsToUser($tripId, $userId)) {
            setFlash('error', t('error_trip_not_found'));
            redirect('/trips');
        }
        
        // Получаем поход с этапами
        $trip = $this->tripModel->findWithStages($tripId);
        
        // Получаем файлы
        $stmt = db()->prepare('SELECT * FROM trip_files WHERE trip_id = ? ORDER BY file_type, uploaded_at');
        $stmt->execute([$tripId]);
        $files = $stmt->fetchAll();
        
        $this->render('trips/view', [
            'title' => e($trip['name']),
            'trip' => $trip,
            'files' => $files
        ]);
    }
    
    /**
     * Копирование похода
     */
    public function copy() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $tripId = $this->get('id', 0);
        
        if (!$this->tripModel->belongsToUser($tripId, $userId)) {
            setFlash('error', t('error_trip_not_found'));
            redirect('/trips');
        }
        
        $trip = $this->tripModel->findWithStages($tripId);
        
        try {
            db()->beginTransaction();
            
            // Создаём копию
            $newTripId = $this->tripModel->createTrip($userId, [
                'name' => $trip['name'] . t('trip_copy_suffix'),
                'country_code' => $trip['country_code'],
                'emergency_service_id' => $trip['emergency_service_id'],
                'start_date' => null
            ]);
            
            // Копируем этапы
            foreach ($trip['stages'] as $stage) {
                $this->stageModel->createStage($newTripId, [
                    'stage_number' => $stage['stage_number'],
                    'description' => $stage['description'],
                    'location' => $stage['location'],
                    'duration_days' => $stage['duration_days'],
                    'deadline_time' => $stage['deadline_time'],
                    'emergency_service_id' => $stage['emergency_service_id'],
                    'requires_confirmation' => $stage['requires_confirmation']
                ]);
            }
            
            db()->commit();
            
            logMessage("Trip copied: original_id=$tripId, new_id=$newTripId, user_id=$userId");
            setFlash('success', t('success_trip_copied'));
            redirectWithParams('/trip/view', ['id' => $newTripId]);
            
        } catch (\Exception $e) {
            db()->rollBack();
            logMessage("Trip copy error: " . $e->getMessage());
            setFlash('error', t('error_trip_create_failed'));
            redirect('/trips');
        }
    }
    
    /**
     * Подтверждение этапа
     */
    public function confirmStage() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $stageId = $this->get('id', 0);
        $user = $this->currentUser();
        
        try {
            TripService::confirmStage($stageId, $userId, $user['timezone']);
            setFlash('success', t('success_stage_confirmed'));
        } catch (\Exception $e) {
            setFlash('error', $e->getMessage());
        }
        
        redirect('/trips');
    }

    /**
     * Подтверждение и активация похода
     */
    public function confirm() {
        $this->requireAuth();
    
        $userId = $_SESSION['user_id'];
        $tripId = $this->get('id', 0);
    
        // Проверка принадлежности
        if (!$this->tripModel->belongsToUser($tripId, $userId)) {
            setFlash('error', t('error_trip_not_found'));
            redirect('/trips');
        }
    
        $trip = $this->tripModel->find($tripId);
    
        if ($trip['status'] !== 'draft') {
            setFlash('error', t('error_trip_not_draft'));
            redirect('/trips');
        }
    
        // ВАЖНО: Проверки ДО начала транзакции
        if (empty($trip['start_date'])) {
            setFlash('error', t('error_trip_start_date_required'));
            redirectWithParams('/trip/view', ['id' => $tripId]);
        }
    
        // Проверка активных походов
        if ($this->tripModel->countActiveTrips($userId) > 0) {
            setFlash('error', t('error_trip_already_active'));
            redirect('/trips');
        }
    
        // Проверка наличия этапов
        $stages = $this->stageModel->findByTripId($tripId);
        if (empty($stages)) {
            setFlash('error', t('error_trip_no_stages'));
            redirectWithParams('/trip/view', ['id' => $tripId]);
        }
    
        try {
            db()->beginTransaction();
        
            // Активируем поход
            $this->tripModel->update($tripId, [
                'status' => 'active',
                'confirmed' => true
            ]);
        
            // Активируем первый этап
            $user = $this->currentUser();
            TripService::activateStage($stages[0]['id'], $user['timezone']);
        
            db()->commit();
        
            // TODO: Отправить письма контактам и в МЧС о начале похода
        
            logMessage("Trip activated: trip_id=$tripId, user_id=$userId");
            setFlash('success', t('success_trip_activated'));
            redirectWithParams('/trip/view', ['id' => $tripId]);
        
        } catch (\Exception $e) {
            db()->rollBack();
            logMessage("Trip activation error: " . $e->getMessage());
            setFlash('error', t('error_trip_activation_failed') . ': ' . $e->getMessage());
            redirect('/trips');
        }
    }
    /**
     * Отмена похода
     */
    public function cancel() {
        $this->requireAuth();
    
        $userId = $_SESSION['user_id'];
        $tripId = $this->get('id', 0);
    
        // Проверка принадлежности
        if (!$this->tripModel->belongsToUser($tripId, $userId)) {
            setFlash('error', t('error_trip_not_found'));
            redirect('/trips');
        }
    
        $trip = $this->tripModel->find($tripId);
    
        if ($trip['status'] !== 'active') {
            setFlash('error', t('error_trip_not_active'));
            redirect('/trips');
        }
    
        try {
            db()->beginTransaction();
        
            // Отменяем поход
            $this->tripModel->update($tripId, [
                'status' => 'cancelled'
            ]);
        
            // Отменяем все активные/pending этапы
            $stmt = db()->prepare('
                UPDATE stages 
                SET status = "cancelled" 
                WHERE trip_id = ? AND status IN ("pending", "active")
            ');
            $stmt->execute([$tripId]);
        
            // Отменяем все pending письма
            $stmt = db()->prepare('
                UPDATE alert_queue 
                SET status = "cancelled" 
                WHERE stage_id IN (SELECT id FROM stages WHERE trip_id = ?) 
                AND status = "pending"
            ');
            $stmt->execute([$tripId]);
        
            db()->commit();
        
            // TODO: Отправить письма об отмене похода
        
            logMessage("Trip cancelled: trip_id=$tripId, user_id=$userId");
            setFlash('success', t('success_trip_cancelled'));
            redirect('/trips');
        
        } catch (\Exception $e) {
            db()->rollBack();
            logMessage("Trip cancellation error: " . $e->getMessage());
            setFlash('error', t('error_trip_cancel_failed'));
            redirect('/trips');
        }
    }

    /**
     * Удаление черновика похода
     */
    public function delete() {
        $this->requireAuth();
    
        $userId = $_SESSION['user_id'];
        $tripId = $this->get('id', 0);
    
        if (!$this->tripModel->belongsToUser($tripId, $userId)) {
            setFlash('error', t('error_trip_not_found'));
            redirect('/trips');
        }
    
        $trip = $this->tripModel->find($tripId);
    
        if ($trip['status'] !== 'draft') {
            setFlash('error', t('error_trip_cannot_delete'));
            redirect('/trips');
        }
    
        try {
            // Удаление каскадное (stages, trip_files удалятся автоматически через FOREIGN KEY)
            $this->tripModel->delete($tripId);
        
            logMessage("Trip deleted: trip_id=$tripId, user_id=$userId");
            setFlash('success', t('success_trip_deleted'));
            redirect('/trips');
        
        } catch (\Exception $e) {
            logMessage("Trip deletion error: " . $e->getMessage());
            setFlash('error', t('error_trip_delete_failed'));
            redirect('/trips');
        }
    }
}