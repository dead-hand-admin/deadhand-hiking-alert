-- Короткие ссылки
CREATE TABLE IF NOT EXISTS short_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    token CHAR(8) UNIQUE NOT NULL,
    full_url TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME,
    
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Очередь отправки писем
CREATE TABLE IF NOT EXISTS alert_queue (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stage_id INT NOT NULL,
    recipient_email VARCHAR(255) NOT NULL,
    recipient_type ENUM('contact', 'emergency') NOT NULL,
    
    status ENUM('pending', 'sent', 'failed', 'cancelled') DEFAULT 'pending',
    
    scheduled_at DATETIME NOT NULL,
    sent_at DATETIME,
    error_message TEXT,
    retry_count TINYINT DEFAULT 0,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (stage_id) REFERENCES stages(id) ON DELETE CASCADE,
    INDEX idx_status_scheduled (status, scheduled_at),
    INDEX idx_stage (stage_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Лог отправки тревог
CREATE TABLE IF NOT EXISTS alert_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stage_id INT NOT NULL,
    action ENUM('alert_sent', 'alert_failed', 'alert_cancelled', 'found_notification') NOT NULL,
    recipient_email VARCHAR(255),
    details TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (stage_id) REFERENCES stages(id) ON DELETE CASCADE,
    INDEX idx_stage_created (stage_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

