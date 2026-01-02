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
        
        // Получаем данные
        $name = trim($this->post('name', ''));
        $countryCode = $this->post('country_code', '');
        $emergencyServiceId = !empty($this->post('emergency_service_id')) ? (int)$this->post('emergency_service_id') : null;
        $stages = $this->post('stages', []);
        
        // Валидация
        if (empty($name)) {
            setFlash('error', t('error_trip_name_required'));
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
        
        try {
            db()->beginTransaction();
            
            // Создаём поход (всегда как черновик, start_date будет установлена при активации)
            $tripId = $this->tripModel->createTrip($userId, [
                'name' => $name,
                'country_code' => $countryCode,
                'emergency_service_id' => $emergencyServiceId,
                'start_date' => null
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
            $this->handleTripFiles($tripId);
            
            db()->commit();
            
            logMessage("Trip created: trip_id=$tripId, user_id=$userId");
            setFlash('success', t('success_trip_draft_saved'));
            redirectWithParams('/trip/view', ['id' => $tripId]);
            
        } catch (\Exception $e) {
            db()->rollBack();
            logMessage("Trip creation error: " . $e->getMessage());
            setFlash('error', t('error_trip_create_failed') . ': ' . $e->getMessage());
            redirect('/trip/create');
        }
    }
    
    /**
     * Редактирование похода (только черновики)
     */
    public function edit() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $tripId = $this->get('id', 0);
        
        if (!$this->tripModel->belongsToUser($tripId, $userId)) {
            setFlash('error', t('error_trip_not_found'));
            redirect('/trips');
        }
        
        $trip = $this->tripModel->find($tripId);
        
        if ($trip['status'] !== 'draft') {
            setFlash('error', t('error_trip_cannot_edit'));
            redirect('/trips');
        }
        
        // Получаем этапы
        $stages = $this->stageModel->findByTripId($tripId);
        
        // Получаем данные для формы
        $user = $this->currentUser();
        
        $stmt = db()->query('SELECT * FROM countries ORDER BY name_' . getCurrentLang());
        $countries = $stmt->fetchAll();
        
        $stmt = db()->query('
            SELECT id, country_code, name_' . getCurrentLang() . ' as name, is_default, is_enabled
            FROM emergency_services 
            WHERE is_enabled = TRUE
            ORDER BY country_code, is_default DESC, name_' . getCurrentLang());
        $allServices = $stmt->fetchAll();
        
        // Получаем файлы
        $stmt = db()->prepare('SELECT * FROM trip_files WHERE trip_id = ?');
        $stmt->execute([$tripId]);
        $files = $stmt->fetchAll();
        
        $this->render('trips/edit', [
            'title' => t('trip_edit_title'),
            'user' => $user,
            'trip' => $trip,
            'stages' => $stages,
            'countries' => $countries,
            'allServices' => $allServices,
            'files' => $files
        ]);
    }
    
    /**
     * Обновление похода
     */
    public function update() {
        $this->requireAuth();
        
        if (!$this->verifyCsrf()) {
            redirect('/trips');
        }
        
        $userId = $_SESSION['user_id'];
        $tripId = $this->post('trip_id', 0);
        
        if (!$this->tripModel->belongsToUser($tripId, $userId)) {
            setFlash('error', t('error_trip_not_found'));
            redirect('/trips');
        }
        
        $trip = $this->tripModel->find($tripId);
        
        if ($trip['status'] !== 'draft') {
            setFlash('error', t('error_trip_cannot_edit'));
            redirect('/trips');
        }
        
        // Получаем данные
        $name = trim($this->post('name', ''));
        $countryCode = $this->post('country_code', '');
        $emergencyServiceId = !empty($this->post('emergency_service_id')) ? (int)$this->post('emergency_service_id') : null;
        $stages = $this->post('stages', []);
        
        // Валидация
        if (empty($name)) {
            setFlash('error', t('error_trip_name_required'));
            redirectWithParams('/trip/edit', ['id' => $tripId]);
        }
        
        if (empty($countryCode)) {
            setFlash('error', t('error_trip_country_required'));
            redirectWithParams('/trip/edit', ['id' => $tripId]);
        }
        
        if (empty($stages)) {
            setFlash('error', t('error_trip_stages_required'));
            redirectWithParams('/trip/edit', ['id' => $tripId]);
        }
        
        try {
            db()->beginTransaction();
            
            // Обновляем поход
            $this->tripModel->update($tripId, [
                'name' => $name,
                'country_code' => $countryCode,
                'emergency_service_id' => $emergencyServiceId
            ]);
            
            // Удаляем старые этапы
            $stmt = db()->prepare('DELETE FROM stages WHERE trip_id = ?');
            $stmt->execute([$tripId]);
            
            // Создаём новые этапы
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
            $this->handleTripFiles($tripId);
            
            db()->commit();
            
            logMessage("Trip updated: trip_id=$tripId, user_id=$userId");
            setFlash('success', t('success_trip_updated'));
            redirectWithParams('/trip/view', ['id' => $tripId]);
            
        } catch (\Exception $e) {
            db()->rollBack();
            logMessage("Trip update error: " . $e->getMessage());
            setFlash('error', t('error_trip_update_failed') . ': ' . $e->getMessage());
            redirectWithParams('/trip/edit', ['id' => $tripId]);
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
        
            // Устанавливаем текущую дату как дату начала и активируем поход
            $this->tripModel->update($tripId, [
                'status' => 'active',
                'confirmed' => true,
                'start_date' => date('Y-m-d')
            ]);
        
            // Активируем первый этап
            $user = $this->currentUser();
            TripService::activateStage($stages[0]['id'], $user['timezone']);
        
            // Генерируем токены для действий без авторизации
            $tokenModel = new \Models\ActionToken();
            $tokens = $tokenModel->generateTripTokens($tripId);
        
            db()->commit();
        
            // TODO: Отправить письма контактам и в МЧС о начале похода
            // Включить в письмо короткие ссылки:
            // - Подтвердить активный этап: /action?t={$tokens['confirm_stage']}
            // - Продлить активный этап: /action?t={$tokens['extend_stage']}
            // - Отменить поход: /action?t={$tokens['cancel_trip']}
            // - Завершить поход: /action?t={$tokens['complete_trip']}
        
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
    
    /**
     * Удаление файла похода
     */
    public function deleteFile() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $fileId = $this->get('id', 0);
        $tripId = $this->get('trip_id', 0);
        
        // Получаем файл
        $stmt = db()->prepare('
            SELECT tf.*, t.user_id 
            FROM trip_files tf
            JOIN trips t ON t.id = tf.trip_id
            WHERE tf.id = ?
        ');
        $stmt->execute([$fileId]);
        $file = $stmt->fetch();
        
        if (!$file || $file['user_id'] != $userId) {
            setFlash('error', t('error_file_not_found'));
            redirectWithParams('/trip/edit', ['id' => $tripId]);
        }
        
        // Получаем поход
        $trip = $this->tripModel->find($tripId);
        
        if ($trip['status'] !== 'draft') {
            setFlash('error', t('error_trip_cannot_edit'));
            redirectWithParams('/trip/edit', ['id' => $tripId]);
        }
        
        try {
            // Удаляем файл с диска
            $filePath = ROOT_PATH . '/' . $file['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Удаляем запись из БД
            $stmt = db()->prepare('DELETE FROM trip_files WHERE id = ?');
            $stmt->execute([$fileId]);
            
            logMessage("File deleted: file_id=$fileId, trip_id=$tripId, user_id=$userId");
            setFlash('success', t('success_file_deleted'));
            redirectWithParams('/trip/edit', ['id' => $tripId]);
            
        } catch (\Exception $e) {
            logMessage("File deletion error: " . $e->getMessage());
            setFlash('error', t('error_file_delete_failed'));
            redirectWithParams('/trip/edit', ['id' => $tripId]);
        }
    }
}