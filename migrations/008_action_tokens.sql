-- Токены для действий без авторизации (короткие ссылки из писем)
-- Токены многоразовые, привязаны к походу
CREATE TABLE IF NOT EXISTS action_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    token CHAR(8) UNIQUE NOT NULL,
    trip_id INT NOT NULL,
    
    action ENUM('cancel_trip', 'complete_trip', 'confirm_stage', 'extend_stage') NOT NULL,
    
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    
    UNIQUE KEY idx_token (token),
    UNIQUE KEY idx_trip_action (trip_id, action),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;