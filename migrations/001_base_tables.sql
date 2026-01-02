-- Часовые зоны
CREATE TABLE IF NOT EXISTS timezones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    timezone_name VARCHAR(50) UNIQUE NOT NULL,
    utc_offset VARCHAR(10) NOT NULL,
    country_codes VARCHAR(50),
    display_name VARCHAR(100) NOT NULL,
    
    INDEX idx_timezone_name (timezone_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Страны
CREATE TABLE IF NOT EXISTS countries (
    code CHAR(2) PRIMARY KEY,
    name_ru VARCHAR(100) NOT NULL,
    name_en VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Службы спасения
CREATE TABLE IF NOT EXISTS emergency_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    country_code CHAR(2) NOT NULL,
    name_ru VARCHAR(255) NOT NULL,
    name_en VARCHAR(255),
    email VARCHAR(255),
    is_default BOOLEAN DEFAULT FALSE,
    is_enabled BOOLEAN DEFAULT FALSE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (country_code) REFERENCES countries(code),
    INDEX idx_country_default (country_code, is_default)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Предзаполнение стран
INSERT INTO countries (code, name_ru, name_en) VALUES
('KZ', 'Казахстан', 'Kazakhstan'),
('RU', 'Россия', 'Russia'),
('KG', 'Кыргызстан', 'Kyrgyzstan'),
('UZ', 'Узбекистан', 'Uzbekistan'),
('TJ', 'Таджикистан', 'Tajikistan'),
('GE', 'Грузия', 'Georgia'),
('AM', 'Армения', 'Armenia'),
('AZ', 'Азербайджан', 'Azerbaijan')
ON DUPLICATE KEY UPDATE code=code;

-- Предзаполнение таймзон
INSERT INTO timezones (timezone_name, utc_offset, country_codes, display_name) VALUES
('Asia/Almaty', '+06:00', 'KZ', 'Алматы (UTC+06:00)'),
('Asia/Aqtobe', '+05:00', 'KZ', 'Актобе (UTC+05:00)'),
('Asia/Oral', '+05:00', 'KZ', 'Уральск (UTC+05:00)'),
('Europe/Moscow', '+03:00', 'RU', 'Москва (UTC+03:00)'),
('Asia/Yekaterinburg', '+05:00', 'RU', 'Екатеринбург (UTC+05:00)'),
('Asia/Bishkek', '+06:00', 'KG', 'Бишкек (UTC+06:00)'),
('Asia/Tashkent', '+05:00', 'UZ', 'Ташкент (UTC+05:00)'),
('Asia/Dushanbe', '+05:00', 'TJ', 'Душанбе (UTC+05:00)'),
('Asia/Tbilisi', '+04:00', 'GE', 'Тбилиси (UTC+04:00)'),
('Asia/Yerevan', '+04:00', 'AM', 'Ереван (UTC+04:00)'),
('Asia/Baku', '+04:00', 'AZ', 'Баку (UTC+04:00)')
ON DUPLICATE KEY UPDATE timezone_name=timezone_name;

-- Предзаполнение служб спасения (примеры для Казахстана)
-- Предзаполнение служб спасения (примеры для Казахстана)
INSERT INTO emergency_services (country_code, name_ru, name_en, email, is_default, is_enabled, description) 
SELECT 'KZ', 'МЧС Республики Казахстан', 'Ministry of Emergency Situations of Kazakhstan', NULL, TRUE, TRUE, 'Главное управление МЧС РК'
WHERE NOT EXISTS (
    SELECT 1 FROM emergency_services 
    WHERE country_code = 'KZ' 
    AND name_ru = 'МЧС Республики Казахстан'
);

INSERT INTO emergency_services (country_code, name_ru, name_en, email, is_default, is_enabled, description) 
SELECT 'KZ', 'Горноспасательная служба Алматинской области', 'Mountain Rescue Service of Almaty Region', NULL, FALSE, TRUE, 'Специализированная служба для горных районов'
WHERE NOT EXISTS (
    SELECT 1 FROM emergency_services 
    WHERE country_code = 'KZ' 
    AND name_ru = 'Горноспасательная служба Алматинской области'
);