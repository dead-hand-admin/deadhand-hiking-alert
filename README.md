# DeadHand - Hiking Safety Alert System

**"Мёртвая рука"** для туристов — система автоматического оповещения спасательных служб при отсутствии связи в контрольных точках маршрута.

## 🎯 Концепция

Турист планирует многодневный поход с этапами. Для каждого этапа указывается контрольное время прибытия. Если турист не подтверждает прохождение этапа вовремя, система автоматически:

1. Отправляет письма экстренным контактам
2. Уведомляет службу спасения (МЧС)
3. Предоставляет GPS-треки и фотографии маршрута

## ✨ Возможности

- 🏔️ **Многоэтапные походы** — разбивка маршрута на контрольные точки
- ⏰ **Автоматический мониторинг** — отслеживание дедлайнов этапов
- 📧 **Каскадное оповещение** — контакты → МЧС → эскалация
- 🌍 **Международная поддержка** — разные страны и спасательные службы
- 🌐 **Мультиязычность** — русский и английский интерфейсы
- 📱 **Мобильная адаптация** — управление походом с телефона
- 📷 **Фото и треки** — прикрепление маршрутов (GPX/KML) и фотографий

## 🛠 Технологии

- **Backend:** PHP 8.3 (MVC-архитектура)
- **Database:** MySQL/MariaDB
- **Frontend:** Tailwind CSS + Alpine.js
- **Email:** Mailgun API
- **Hosting:** InfinityFree (бесплатный хостинг для тестирования)

## 📁 Структура проекта

```
/htdocs/
├── app/
│   ├── Controllers/      # Контроллеры (Auth, Profile, Trip, Page)
│   ├── Models/           # Модели данных (User, Trip, Stage, Contact)
│   ├── Services/         # Бизнес-логика (TripService, ImageProcessor)
│   └── Helpers/          # Вспомогательные функции
├── templates/
│   ├── auth/             # Регистрация, вход
│   ├── profile/          # Профиль пользователя
│   └── trips/            # Управление походами
├── lang/                 # Переводы (ru.php, en.php)
├── migrations/           # Миграции базы данных
├── uploads/              # Загруженные файлы (треки, фото)
├── logs/                 # Логи приложения
├── config.php            # Конфигурация (не в Git)
├── db.php                # Подключение к БД
└── index.php             # Фронт-контроллер (роутинг)
```

## 🚀 Установка

### 1. Клонирование репозитория

```bash
git clone https://github.com/dead-hand-admin/deadhand-hiking-alert.git
cd deadhand-hiking-alert
```

### 2. Настройка окружения

```bash
cp .env.example .env
# Отредактируйте .env — укажите параметры БД и Mailgun
```

### 3. Создание config.php

Создайте `htdocs/config.php` на основе `.env`:

```php
<?php
// Загрузка из .env
$env = parse_ini_file(__DIR__ . '/../.env');

define('ROOT_PATH', __DIR__);
define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASSWORD', $env['DB_PASSWORD']);
// ... остальные константы
```

### 4. База данных

Создайте базу данных и примените миграции:

```sql
CREATE DATABASE deadhand_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Затем откройте в браузере:
```
https://yourdomain.com/migrations/run.php?key=YOUR_MIGRATION_KEY
```

### 5. Настройка веб-сервера

Укажите `DocumentRoot` на `/htdocs`.

**Nginx пример:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/deadhand-hiking-alert/htdocs;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

## 📊 База данных

### Основные таблицы

- `users` — пользователи
- `trips` — походы
- `stages` — этапы походов
- `contacts` — экстренные контакты
- `alert_queue` — очередь оповещений
- `alert_log` — история отправленных писем
- `emergency_services` — службы спасения по странам

### Миграции

Все миграции в папке `/migrations/`:
- `000_helpers.sql` — вспомогательные SQL-процедуры
- `001_base_tables.sql` — основные таблицы
- `002_users.sql` — пользователи и профили
- `003_trips.sql` — походы и этапы
- `004_alerts_and_links.sql` — система оповещений
- `005_admin.sql` — административные функции
- `007_trip_improvements.sql` — улучшения походов

## 🔐 Безопасность

- ✅ CSRF-защита всех форм
- ✅ Хеширование паролей (bcrypt)
- ✅ SQL-инъекции защищены (prepared statements)
- ✅ XSS-защита (htmlspecialchars)
- ✅ GDPR-согласие пользователей

## 📝 TODO

- [ ] Интеграция Mailgun для отправки писем
- [ ] Cron-скрипт для проверки дедлайнов
- [ ] Административная панель
- [ ] Редактирование черновиков походов
- [ ] Телеграм-бот для уведомлений
- [ ] Экспорт данных (GDPR)
- [ ] Двухфакторная аутентификация

## 🧪 Статус разработки

**Alpha** — основная функциональность реализована, идёт тестирование.

## 🤝 Участие в разработке

Проект находится в активной разработке. Pull requests приветствуются!

## 📄 Лицензия

MIT License

## 👨‍💻 Авторы

- Концепция и разработка: dead-hand-admin
- Техническая реализация: при участии Claude (Anthropic)

## 📧 Контакты

GitHub Issues: https://github.com/dead-hand-admin/deadhand-hiking-alert/issues