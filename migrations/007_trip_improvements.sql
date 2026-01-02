-- Добавление колонок с проверками (без процедур)

-- Добавляем name (если не существует)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'trips' 
                   AND COLUMN_NAME = 'name');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `trips` ADD COLUMN `name` VARCHAR(255) AFTER `user_id`',
    'SELECT "Column name exists" AS Info');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Добавляем start_date (если не существует)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'trips' 
                   AND COLUMN_NAME = 'start_date');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `trips` ADD COLUMN `start_date` DATE AFTER `emergency_service_id`',
    'SELECT "Column start_date exists" AS Info');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Добавляем индекс idx_start_date (если не существует)
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'trips' 
                   AND INDEX_NAME = 'idx_start_date');

SET @sql = IF(@idx_exists = 0,
    'CREATE INDEX `idx_start_date` ON `trips` (`start_date`)',
    'SELECT "Index idx_start_date exists" AS Info');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Добавляем requires_confirmation (если не существует)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'stages' 
                   AND COLUMN_NAME = 'requires_confirmation');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `stages` ADD COLUMN `requires_confirmation` BOOLEAN DEFAULT TRUE AFTER `emergency_service_id`',
    'SELECT "Column requires_confirmation exists" AS Info');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Удаляем duration_hours (если существует)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'stages' 
                   AND COLUMN_NAME = 'duration_hours');

SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `stages` DROP COLUMN `duration_hours`',
    'SELECT "Column duration_hours not found" AS Info');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Удаляем старый индекс idx_status_deadline (если существует)
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'stages' 
                   AND INDEX_NAME = 'idx_status_deadline');

SET @sql = IF(@idx_exists > 0,
    'DROP INDEX `idx_status_deadline` ON `stages`',
    'SELECT "Index idx_status_deadline not found" AS Info');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Добавляем новый индекс idx_status_deadline_confirm (если не существует)
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'stages' 
                   AND INDEX_NAME = 'idx_status_deadline_confirm');

SET @sql = IF(@idx_exists = 0,
    'CREATE INDEX `idx_status_deadline_confirm` ON `stages` (`status`, `deadline_utc`, `requires_confirmation`)',
    'SELECT "Index idx_status_deadline_confirm exists" AS Info');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
