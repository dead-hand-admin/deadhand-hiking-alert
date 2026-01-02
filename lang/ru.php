<?php
/**
 * Русский язык
 */

return [
    // Общее
    'app_name' => 'DeadHand',
    'app_tagline' => 'Система безопасности для туристов',
    'hours' => 'часов',
    'days' => 'дней',
    'save' => 'Сохранить',
    'cancel' => 'Отмена',
    'delete' => 'Удалить',
    'trip_save' => 'Сохранить',
    'file_delete_confirm' => 'Удалить файл?',
    
    // Навигация
    'nav_home' => 'Главная',
    'nav_profile' => 'Профиль',
    'nav_login' => 'Вход',
    'nav_register' => 'Регистрация',
    'nav_logout' => 'Выход',
    
    // Главная страница
    'home_title' => 'Система "Мёртвой руки" для туристов',
    'home_subtitle' => 'Автоматическое оповещение спасателей, если вы не вернулись из похода вовремя',
    'home_cta_start' => 'Начать использовать',
    'home_cta_login' => 'Войти',
    'home_cta_profile' => 'Мой профиль',
    
    'home_step1_title' => '1. Регистрация похода',
    'home_step1_desc' => 'Укажите маршрут, этапы и контрольные точки с датами возврата',
    'home_step2_title' => '2. Автоматический контроль',
    'home_step2_desc' => 'Система следит за дедлайнами и ждёт вашего подтверждения',
    'home_step3_title' => '3. Оповещение',
    'home_step3_desc' => 'Если не подтвердили возврат — письма уходят в МЧС и вашим контактам',
    
    // Регистрация
    'register_title' => 'Регистрация',
    'register_email' => 'Email',
    'register_password' => 'Пароль (минимум 8 символов)',
    'register_password_confirm' => 'Повторите пароль',
    'register_gdpr_text' => 'Продолжая регистрацию, я соглашаюсь с обработкой персональных данных (ФИО, фото, привычки, контакты) для целей оповещения в случае чрезвычайной ситуации',
    'register_submit' => 'Зарегистрироваться',
    'register_have_account' => 'Уже есть аккаунт?',
    'register_login_link' => 'Войти',
    
    // Валидация
    'error_csrf' => 'Неверный токен безопасности',
    'error_email_invalid' => 'Некорректный email',
    'error_password_short' => 'Пароль должен быть минимум 8 символов',
    'error_passwords_mismatch' => 'Пароли не совпадают',
    'error_gdpr_required' => 'Необходимо согласие на обработку персональных данных',
    'error_email_exists' => 'Этот email уже зарегистрирован',
    'error_registration_failed' => 'Ошибка при регистрации. Попробуйте позже.',
    'success_registration' => 'Регистрация успешна! На ваш email отправлено письмо с подтверждением.',
    
    // Футер
    'footer_copyright' => 'DeadHand. Система безопасности для туристов.',
    
    // 404
    'error_404' => 'Страница не найдена',

    // Вход
    'login_title' => 'Вход',
    'login_email' => 'Email',
    'login_password' => 'Пароль',
    'login_submit' => 'Войти',
    'login_no_account' => 'Нет аккаунта?',
    'login_register_link' => 'Зарегистрироваться',

    'error_login_failed' => 'Неверный email или пароль',
    'error_email_not_confirmed' => 'Email не подтверждён. Проверьте почту.',
    'error_invalid_token' => 'Неверная ссылка подтверждения',

    'success_login' => 'Вы успешно вошли',
    'success_logout' => 'Вы вышли из системы',
    'success_email_confirmed' => 'Email подтверждён! Теперь можете войти.',

// Профиль
'profile_title' => 'Мой профиль',
'profile_tab_personal' => 'Личные данные',
'profile_tab_contacts' => 'Контакты',
'profile_tab_photos' => 'Фотографии',

'profile_personal_info' => 'Личная информация',
'profile_email' => 'Email',
'profile_email_hint' => 'Email нельзя изменить',
'profile_fio' => 'ФИО',
'profile_fio_placeholder' => 'Иванов Иван Иванович',
'profile_habits' => 'Привычки и особенности',
'profile_habits_placeholder' => 'Цвет одежды, особые приметы, аллергии, частота рации и т.п.',
'profile_habits_hint' => 'Эта информация поможет спасателям идентифицировать вас',
'profile_timezone' => 'Часовой пояс',
'profile_country' => 'Страна',
'profile_emergency_service' => 'Служба спасения по умолчанию',
'profile_emergency_service_none' => 'Не выбрана',
'profile_emergency_default' => 'по умолчанию',
'profile_save' => 'Сохранить',

'profile_contacts_title' => 'Контакты для оповещения',
'profile_contacts_description' => 'Эти люди получат письма, если вы не вернётесь из похода вовремя',
'profile_no_contacts' => 'Контактов пока нет',
'profile_add_contact' => 'Добавить контакт',
'profile_contact_name' => 'Имя',
'profile_contact_email' => 'Email',
'profile_contact_add' => 'Добавить',
'profile_contacts_limit' => 'Достигнут максимум контактов',
'profile_contact_delete_confirm' => 'Удалить этот контакт?',
'profile_delete' => 'Удалить',

'profile_photos_title' => 'Ваши фотографии',
'profile_photos_description' => 'Фото в походной одежде помогут спасателям быстрее вас найти',
'profile_no_photos' => 'Фотографий пока нет',
'profile_upload_photo' => 'Загрузить фото',
'profile_photo_file' => 'Файл',
'profile_photo_hint' => 'Максимум 5 МБ, JPG или PNG',
'profile_photo_description' => 'Описание (необязательно)',
'profile_photo_description_placeholder' => 'В красной куртке, с рюкзаком',
'profile_photo_upload' => 'Загрузить',
'profile_photos_limit' => 'Достигнут максимум фотографий',
'profile_photo_delete_confirm' => 'Удалить это фото?',

'error_login_required' => 'Необходимо войти в систему',

// Ошибки профиля
'error_fio_required' => 'Укажите ФИО',
'error_profile_update_failed' => 'Ошибка при сохранении профиля',
'error_contact_required' => 'Укажите имя и email контакта',
'error_contacts_limit' => 'Достигнут максимум контактов',
'error_contact_add_failed' => 'Ошибка при добавлении контакта',
'error_contact_not_found' => 'Контакт не найден',
'error_contact_delete_failed' => 'Ошибка при удалении контакта',
'error_photos_limit' => 'Достигнут максимум фотографий',
'error_photo_upload_failed' => 'Ошибка при загрузке файла',
'error_photo_too_large' => 'Файл слишком большой (максимум 5 МБ)',
'error_photo_invalid_type' => 'Неверный формат файла (только JPG, PNG)',
'error_photo_processing_failed' => 'Ошибка при обработке изображения',
'error_photo_not_found' => 'Фото не найдено',
'error_photo_delete_failed' => 'Ошибка при удалении фото',

// Успехи
'success_profile_updated' => 'Профиль обновлён',
'success_contact_added' => 'Контакт добавлен',
'success_contact_deleted' => 'Контакт удалён',
'success_photo_uploaded' => 'Фото загружено',
'success_photo_deleted' => 'Фото удалено',

// Навигация
'nav_trips' => 'Походы',

// Походы - список
'trips_title' => 'Мои походы',
'trips_create' => 'Создать поход',
'trips_no_trips' => 'У вас пока нет походов',
'trips_active' => 'Активные',
'trips_draft' => 'Черновики',
'trips_completed' => 'Завершённые',
'trips_cancelled' => 'Отменённые',

'trip_status_draft' => 'Черновик',
'trip_status_active' => 'Активный',
'trip_status_completed' => 'Завершён',
'trip_status_cancelled' => 'Отменён',

'trip_created' => 'Создан',
'trip_stages' => 'этапов',
'trip_view' => 'Просмотр',

// Создание похода
'trip_create_title' => 'Создать новый поход',
'trip_country' => 'Страна похода',
'trip_emergency_service' => 'Служба спасения',
'trip_emergency_service_hint' => 'Можно изменить для каждого этапа отдельно',

'trip_stages_title' => 'Этапы похода',
'trip_stages_description' => 'Разбейте поход на контрольные точки. В каждой точке вам нужно будет подтвердить, что всё в порядке.',

'trip_stage_number' => 'Этап',
'trip_stage_description' => 'Название',
'trip_stage_description_placeholder' => 'Подъём к базовому лагерю',
'trip_stage_deadline_time' => 'До времени',
'trip_stage_emergency_service' => 'Служба ЧС',
'trip_stage_add' => 'Добавить этап',
'trip_stage_remove' => 'Удалить',

'trip_files_title' => 'Файлы',
'trip_track' => 'Трек маршрута (GPX, KML)',
'trip_photos' => 'Фото маршрута (до 2 шт)',

'trip_submit' => 'Создать поход',
'trip_save_draft' => 'Сохранить черновик',

// Ошибки
'error_trip_country_required' => 'Выберите страну',
'error_trip_stages_required' => 'Добавьте хотя бы один этап',
'error_trip_stage_description_required' => 'Укажите название этапа',
'error_trip_stage_duration_required' => 'Укажите продолжительность этапа',
'error_trip_create_failed' => 'Ошибка при создании похода',
'error_trip_not_found' => 'Поход не найден',
'error_trip_not_active' => 'Поход не активен',
'error_no_active_stage' => 'Нет активного этапа',
'error' => 'Ошибка',

// Успехи
'success_trip_created' => 'Поход создан! Проверьте почту для активации.',
'success_trip_draft_saved' => 'Черновик сохранён',

// Создание похода
'trip_name' => 'Название похода',
'trip_name_placeholder' => 'Поход на Хан-Тенгри',
'trip_name_hint' => 'Используйте понятное название, чтобы различать похожие маршруты',
'trip_start_date' => 'Дата начала',
'trip_start_date_hint' => 'Дата начала первого этапа',

// Список походов
'trip_copy' => 'Копировать',
'success_trip_copied' => 'Поход скопирован в черновики',

// Ошибки
'error_trip_name_required' => 'Укажите название похода',
'error_trip_start_date_required' => 'Укажите дату начала',
'error_trip_start_date_past' => 'Дата начала не может быть в прошлом',

'error_trip_already_active' => 'У вас уже есть активный поход. Завершите его перед созданием нового.',
'error_track_invalid_format' => 'Неверный формат трека (только GPX, KML, KMZ)',
'error_track_too_large' => 'Трек слишком большой (максимум 5 МБ)',

'trip_stage_requires_confirmation' => 'Контролировать прохождение этапа',
'trip_stage_requires_confirmation_hint' => 'Снимите галочку для дневок/технических этапов',
'trip_stage_duration_full_days' => 'Полных суток',
'trip_stage_checkpoint' => 'Контрольная точка (финиш этапа)',
'trip_stage_checkpoint_placeholder' => 'Лагерь на высоте 3200м',

// Просмотр похода
'trip_activate' => 'Активировать поход',
'trip_cancel' => 'Отменить поход',
'trip_cancel_confirm' => 'Вы уверены, что хотите отменить поход?',
'trip_back_to_list' => 'Назад к списку',
'trip_no_stages' => 'Нет этапов',
'trip_tracks' => 'Треки маршрута',
'trip_photos_list' => 'Фотографии маршрута',
'trip_photo_alt' => 'Фото маршрута',

// Статусы этапов
'stage_status_active' => 'Активен',
'stage_status_pending' => 'Ожидает',
'stage_status_confirmed' => 'Пройден',
'stage_status_overdue' => 'Просрочен',
'stage_status_cancelled' => 'Отменён',

// Поля этапов
'stage_duration' => 'Длительность',
'stage_days_unit' => 'суток',
'stage_deadline_time' => 'До времени',
'stage_deadline' => 'Дедлайн',
'stage_auto_transition' => 'автопереход',
'stage_confirm_button' => 'Подтвердить прохождение этапа',

// Общие
'yes' => 'Да',
'no' => 'Нет',

// Успехи
'success_stage_confirmed' => 'Этап подтверждён',

'stage_requires_confirmation' => 'Требует подтверждения',
'trip_edit' => 'Редактировать',
'trip_edit_title' => 'Редактирование похода',

'error_trip_not_draft' => 'Поход уже активирован или завершён',
'error_trip_not_active' => 'Поход не активен',
'error_trip_cannot_edit' => 'Можно редактировать только черновики',
'error_trip_activation_failed' => 'Ошибка при активации похода',
'error_trip_cancel_failed' => 'Ошибка при отмене похода',
'error_trip_update_failed' => 'Ошибка при обновлении похода',
'error_file_not_found' => 'Файл не найден',
'error_file_delete_failed' => 'Ошибка при удалении файла',

'success_trip_activated' => 'Поход активирован! Первый этап запущен.',
'success_trip_cancelled' => 'Поход отменён',
'success_trip_updated' => 'Поход обновлён',
'success_file_deleted' => 'Файл удалён',

'error_trip_no_stages' => 'В походе нет этапов',

'trip_activate_confirm' => 'Убедитесь, что все этапы заполнены. Активировать поход? Дата начала будет установлена на сегодня.',

'trip_delete' => 'Удалить',
'trip_delete_confirm' => 'Удалить черновик похода? Это действие нельзя отменить.',
'error_trip_cannot_delete' => 'Можно удалять только черновики',
'error_trip_delete_failed' => 'Ошибка при удалении похода',
'success_trip_deleted' => 'Поход удалён',

// Действия по токенам (короткие ссылки из писем)
'action_confirm_stage_title' => 'Подтверждение прохождения этапа',
'action_confirm_stage_text' => 'Вы действительно хотите подтвердить завершение этапа "{stage}" и перейти к следующему этапу?',
'action_confirm_stage_last_text' => 'Вы действительно хотите подтвердить завершение последнего этапа "{stage}" и завершить поход?',
'action_confirm_stage_button' => 'Да, подтвердить',
'action_cancel_trip_title' => 'Отмена похода',
'action_cancel_trip_text' => 'Вы действительно хотите отменить поход "{trip}"? Все запланированные оповещения будут отменены.',
'action_cancel_trip_button' => 'Да, отменить поход',
'action_complete_trip_title' => 'Завершение похода',
'action_complete_trip_text' => 'Вы действительно хотите завершить поход "{trip}"? Все запланированные оповещения будут отменены.',
'action_complete_trip_button' => 'Да, завершить поход',
'action_extend_stage_title' => 'Продление срока этапа',
'action_extend_stage_text' => 'Выберите способ продления срока для этапа "{stage}":',
'action_extend_hours' => 'Добавить часов',
'action_extend_to_date' => 'Перенести на дату',
'action_extend_date' => 'Дата',
'action_extend_time' => 'До времени',
'action_extend_button' => 'Продлить',
'action_back_to_trips' => 'Вернуться к походам',

'action_token_invalid' => 'Неверная или просроченная ссылка',
'action_success' => 'Действие выполнено успешно',

'error_extend_hours_required' => 'Укажите количество часов',
'error_extend_date_required' => 'Укажите дату и время',

];