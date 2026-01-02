-- Пользователи
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    
    -- Профильные данные
    fio VARCHAR(255),
    habits TEXT,
    timezone VARCHAR(50) DEFAULT 'Asia/Almaty',
    country_code CHAR(2) DEFAULT 'KZ',
    default_emergency_service_id INT,
    
    -- Согласие на обработку ПД
    gdpr_agreed BOOLEAN DEFAULT FALSE,
    gdpr_agreed_at DATETIME,
    gdpr_token VARCHAR(64),
    gdpr_ip VARCHAR(45),
    
    -- Статусы
    email_confirmed BOOLEAN DEFAULT FALSE,
    email_confirm_token VARCHAR(64),
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (country_code) REFERENCES countries(code),
    FOREIGN KEY (default_emergency_service_id) REFERENCES emergency_services(id) ON DELETE SET NULL,
    
    INDEX idx_email (email),
    INDEX idx_confirm_token (email_confirm_token),
    INDEX idx_gdpr_token (gdpr_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Контакты для оповещения
CREATE TABLE IF NOT EXISTS contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    telegram_username VARCHAR(50),
    order_num TINYINT DEFAULT 0,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Фото пользователя
CREATE TABLE IF NOT EXISTS user_photos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
