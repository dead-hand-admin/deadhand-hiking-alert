<?php

namespace Services;

/**
 * Обработка изображений (ресайз, оптимизация)
 */

class ImageProcessor {
    
    /**
     * Ресайз и сохранение изображения
     * 
     * @param string $sourcePath Путь к исходному файлу
     * @param string $targetDir Директория для сохранения
     * @param int $maxWidth Максимальная ширина
     * @param int $maxHeight Максимальная высота
     * @param int $quality Качество JPEG (0-100)
     * @return string Имя сохранённого файла
     * @throws Exception
     */
    public static function resizeAndSave($sourcePath, $targetDir, $maxWidth, $maxHeight, $quality = 85) {
        // Проверка директории
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Определение типа изображения
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new Exception('Invalid image file');
        }
        
        list($origWidth, $origHeight, $type) = $imageInfo;
        
        // Создание исходного изображения
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new Exception('Unsupported image type');
        }
        
        if (!$source) {
            throw new Exception('Failed to create image from source');
        }
        
        // Расчёт новых размеров (сохраняя пропорции)
        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
        
        if ($ratio < 1) {
            $newWidth = (int)($origWidth * $ratio);
            $newHeight = (int)($origHeight * $ratio);
        } else {
            // Если изображение меньше максимальных размеров - не увеличиваем
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }
        
        // Создание нового изображения
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Для PNG сохраняем прозрачность
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Ресайз
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        
        // Генерация уникального имени файла
        $filename = uniqid('photo_', true) . '.jpg';
        $targetPath = $targetDir . '/' . $filename;
        
        // Сохранение (всегда в JPEG для уменьшения размера)
        $saved = imagejpeg($resized, $targetPath, $quality);
        
        // Очистка памяти
        imagedestroy($source);
        imagedestroy($resized);
        
        if (!$saved) {
            throw new Exception('Failed to save resized image');
        }
        
        return $filename;
    }
}
