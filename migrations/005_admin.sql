-- Админы
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Настройки системы
CREATE TABLE IF NOT EXISTS settings (
    key_name VARCHAR(100) PRIMARY KEY,
    value TEXT,
    description VARCHAR(255),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email шаблоны
CREATE TABLE IF NOT EXISTS email_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_key VARCHAR(50) UNIQUE NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Предзаполнение базовых настроек
INSERT INTO settings (key_name, value, description) VALUES
('app_name', 'DeadHand', 'Название приложения'),
('backup_enabled', 'true', 'Включены ли автоматические бэкапы'),
('backup_retention_days', '7', 'Сколько дней хранить локальные бэкапы'),
('last_backup_at', NULL, 'Дата последнего успешного бэкапа')
ON DUPLICATE KEY UPDATE key_name=key_name;

-- Предзаполнение шаблонов писем
INSERT INTO email_templates (template_key, subject, body, variables) VALUES
('trip_confirmation', 'Подтверждение регистрации похода', 
'Здравствуйте, {{user_name}}!

Ваш поход зарегистрирован:
Маршрут: {{trip_description}}
Этапы: {{stages_count}}

Для активации похода перейдите по ссылке:
{{confirm_link}}

Важно: активируйте поход перед выходом!', 
'{"user_name": "ФИО пользователя", "trip_description": "Описание маршрута", "stages_count": "Количество этапов", "confirm_link": "Ссылка подтверждения"}'),

('alert_missing', 'ТРЕВОГА: Турист не вышел на связь', 
'ВНИМАНИЕ! Требуется помощь.

Турист не подтвердил прохождение контрольной точки.

ДАННЫЕ ТУРИСТА:
ФИО: {{user_name}}
Email: {{user_email}}
Привычки: {{user_habits}}

ДАННЫЕ ПОХОДА:
Маршрут: {{trip_description}}
Этап: {{stage_description}}
Ожидаемое время: {{deadline}}

ФОТОГРАФИИ И ТРЕК ВО ВЛОЖЕНИИ', 
'{"user_name": "ФИО", "user_email": "Email", "user_habits": "Привычки", "trip_description": "Описание", "stage_description": "Этап", "deadline": "Дедлайн"}'),

('found_notification', 'Турист найден / вышел на связь', 
'Добрый день!

Турист {{user_name}} вышел на связь.
Тревога отменена.', 
'{"user_name": "ФИО"}')
ON DUPLICATE KEY UPDATE template_key=template_key;