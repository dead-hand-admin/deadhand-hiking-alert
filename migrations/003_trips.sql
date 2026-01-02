-- Походы
CREATE TABLE IF NOT EXISTS trips (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    country_code CHAR(2) NOT NULL,
    emergency_service_id INT,
    
    status ENUM('draft', 'active', 'completed', 'cancelled') DEFAULT 'draft',
    
    confirmed BOOLEAN DEFAULT FALSE,
    confirm_token VARCHAR(64),
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (country_code) REFERENCES countries(code),
    FOREIGN KEY (emergency_service_id) REFERENCES emergency_services(id) ON DELETE SET NULL,
    
    INDEX idx_user_status (user_id, status),
    INDEX idx_status (status),
    INDEX idx_confirm_token (confirm_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Этапы похода
CREATE TABLE IF NOT EXISTS stages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    trip_id INT NOT NULL,
    stage_number TINYINT NOT NULL,
    
    description TEXT,
    location VARCHAR(255),
    
    -- Дедлайн в человеческом формате
    duration_days TINYINT NOT NULL,
    duration_hours TINYINT DEFAULT 0,
    deadline_time TIME NOT NULL,
    
    -- Рассчитанный абсолютный дедлайн (UTC)
    deadline_utc DATETIME,
    
    emergency_service_id INT,
    
    status ENUM('pending', 'active', 'confirmed', 'overdue', 'cancelled') DEFAULT 'pending',
    
    activated_at DATETIME,
    confirmed_at DATETIME,
    confirm_token VARCHAR(64),
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (emergency_service_id) REFERENCES emergency_services(id) ON DELETE SET NULL,
    
    INDEX idx_trip_stage (trip_id, stage_number),
    INDEX idx_status_deadline (status, deadline_utc),
    INDEX idx_confirm_token (confirm_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Файлы похода (треки и фото)
CREATE TABLE IF NOT EXISTS trip_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    trip_id INT NOT NULL,
    file_type ENUM('track', 'photo') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    file_size INT,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    INDEX idx_trip_type (trip_id, file_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
