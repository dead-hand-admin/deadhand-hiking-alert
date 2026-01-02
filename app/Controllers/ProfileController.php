<?php

namespace Controllers;

use Models\User;
use Models\Contact;
use Models\UserPhoto;
use Services\ImageProcessor;

class ProfileController extends BaseController {
    
    private $userModel;
    private $contactModel;
    private $photoModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->contactModel = new Contact();
        $this->photoModel = new UserPhoto();
    }
    
    /**
     * Главная страница профиля
     */
    public function index() {
        $this->requireAuth();
        
        $user = $this->currentUser();
        $contacts = $this->contactModel->findByUserId($user['id']);
        $photos = $this->photoModel->findByUserId($user['id']);
        
        // Получаем таймзоны
        $stmt = db()->query('SELECT * FROM timezones ORDER BY display_name');
        $timezones = $stmt->fetchAll();
        
        // Получаем страны
        $stmt = db()->query('SELECT * FROM countries ORDER BY name_ru');
        $countries = $stmt->fetchAll();
        
        // Получаем службы спасения
        $stmt = db()->prepare('SELECT * FROM emergency_services WHERE country_code = ? ORDER BY is_default DESC, name_ru');
        $stmt->execute([$user['country_code']]);
        $emergencyServices = $stmt->fetchAll();
        
        $this->render('profile/index', [
            'title' => t('profile_title'),
            'user' => $user,
            'contacts' => $contacts,
            'photos' => $photos,
            'timezones' => $timezones,
            'countries' => $countries,
            'emergencyServices' => $emergencyServices,
            'currentTab' => $this->get('tab', 'personal')
        ]);
    }
    
    /**
     * Обновление профиля
     */
    public function update() {
        $this->requireAuth();
        
        if (!$this->verifyCsrf()) {
            redirect('/profile');
        }
        
        $userId = $_SESSION['user_id'];
        $fio = trim($this->post('fio', ''));
        $habits = trim($this->post('habits', ''));
        $timezone = $this->post('timezone', 'Asia/Almaty');
        $countryCode = $this->post('country_code', 'KZ');
        $emergencyServiceId = $this->post('default_emergency_service_id');
        
        if (empty($emergencyServiceId)) {
            $emergencyServiceId = null;
        }
        
        // Валидация
        if (empty($fio)) {
            setFlash('error', t('error_fio_required'));
            redirectWithParams('/profile', ['tab' => 'personal']);
        }
        
        try {
            $this->userModel->updateProfile($userId, [
                'fio' => $fio,
                'habits' => $habits,
                'timezone' => $timezone,
                'country_code' => $countryCode,
                'default_emergency_service_id' => $emergencyServiceId
            ]);
            
            logMessage("Profile updated: user_id=$userId");
            setFlash('success', t('success_profile_updated'));
        } catch (\Exception $e) {
            logMessage("Profile update error: " . $e->getMessage());
            setFlash('error', t('error_profile_update_failed'));
        }
        
        redirectWithParams('/profile', ['tab' => 'personal']);
    }
    
    /**
     * Добавление контакта
     */
    public function addContact() {
        $this->requireAuth();
        
        if (!$this->verifyCsrf()) {
            redirect('/profile');
        }
        
        $userId = $_SESSION['user_id'];
        $name = trim($this->post('name', ''));
        $email = trim($this->post('email', ''));
        
        // Валидация
        if (empty($name) || empty($email)) {
            setFlash('error', t('error_contact_required'));
            redirectWithParams('/profile', ['tab' => 'contacts']);
        }
        
        if (!validateEmail($email)) {
            setFlash('error', t('error_email_invalid'));
            redirectWithParams('/profile', ['tab' => 'contacts']);
        }
        
        // Проверка лимита
        $count = $this->contactModel->count('user_id', $userId);
        if ($count >= MAX_CONTACTS) {
            setFlash('error', t('error_contacts_limit'));
            redirectWithParams('/profile', ['tab' => 'contacts']);
        }
        
        try {
            $this->contactModel->createContact($userId, $name, $email, $count);
            logMessage("Contact added: user_id=$userId, name=$name, email=$email");
            setFlash('success', t('success_contact_added'));
        } catch (\Exception $e) {
            logMessage("Contact add error: " . $e->getMessage());
            setFlash('error', t('error_contact_add_failed'));
        }
        
        redirectWithParams('/profile', ['tab' => 'contacts']);
    }
    
    /**
     * Удаление контакта
     */
    public function deleteContact() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $contactId = $this->get('id', 0);
        
        // Проверка принадлежности
        if (!$this->contactModel->belongsToUser($contactId, $userId)) {
            setFlash('error', t('error_contact_not_found'));
            redirectWithParams('/profile', ['tab' => 'contacts']);
        }
        
        try {
            $this->contactModel->delete($contactId);
            logMessage("Contact deleted: user_id=$userId, contact_id=$contactId");
            setFlash('success', t('success_contact_deleted'));
        } catch (\Exception $e) {
            logMessage("Contact delete error: " . $e->getMessage());
            setFlash('error', t('error_contact_delete_failed'));
        }
        
        redirectWithParams('/profile', ['tab' => 'contacts']);
    }
    
    /**
     * Загрузка фото
     */
    public function uploadPhoto() {
        $this->requireAuth();
        
        if (!$this->verifyCsrf()) {
            redirect('/profile');
        }
        
        $userId = $_SESSION['user_id'];
        $description = trim($this->post('description', ''));
        
        // Проверка лимита
        $count = $this->photoModel->count('user_id', $userId);
        if ($count >= MAX_PHOTOS_PROFILE) {
            setFlash('error', t('error_photos_limit'));
            redirectWithParams('/profile', ['tab' => 'photos']);
        }
        
        // Проверка файла
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', t('error_photo_upload_failed'));
            redirectWithParams('/profile', ['tab' => 'photos']);
        }
        
        $file = $_FILES['photo'];
        
        // Проверка размера
        if ($file['size'] > PHOTO_MAX_SIZE) {
            setFlash('error', t('error_photo_too_large'));
            redirectWithParams('/profile', ['tab' => 'photos']);
        }
        
        // Проверка типа
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            setFlash('error', t('error_photo_invalid_type'));
            redirectWithParams('/profile', ['tab' => 'photos']);
        }
        
        try {
            $filename = ImageProcessor::resizeAndSave(
                $file['tmp_name'],
                UPLOAD_PATH . '/photos',
                PHOTO_RESIZE_WIDTH,
                PHOTO_RESIZE_HEIGHT,
                PHOTO_JPEG_QUALITY
            );
            
            $filePath = 'uploads/photos/' . $filename;
            $this->photoModel->createPhoto($userId, $filePath, $description);
            
            logMessage("Photo uploaded: user_id=$userId, file=$filename");
            setFlash('success', t('success_photo_uploaded'));
            
        } catch (\Exception $e) {
            logMessage("Photo upload error: " . $e->getMessage());
            setFlash('error', t('error_photo_processing_failed'));
        }
        
        redirectWithParams('/profile', ['tab' => 'photos']);
    }
    
    /**
     * Удаление фото
     */
    public function deletePhoto() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $photoId = $this->get('id', 0);
        
        // Проверка принадлежности
        $photo = $this->photoModel->belongsToUser($photoId, $userId);
        
        if (!$photo) {
            setFlash('error', t('error_photo_not_found'));
            redirectWithParams('/profile', ['tab' => 'photos']);
        }
        
        // Удаление файла
        $fullPath = ROOT_PATH . '/' . $photo['file_path'];
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        
        try {
            $this->photoModel->delete($photoId);
            logMessage("Photo deleted: user_id=$userId, photo_id=$photoId");
            setFlash('success', t('success_photo_deleted'));
        } catch (\Exception $e) {
            logMessage("Photo delete error: " . $e->getMessage());
            setFlash('error', t('error_photo_delete_failed'));
        }
        
        redirectWithParams('/profile', ['tab' => 'photos']);
    }
}