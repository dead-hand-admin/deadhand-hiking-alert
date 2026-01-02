<!DOCTYPE html>
<html lang="<?= getCurrentLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? t('app_name') . ' - ' . t('app_tagline')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Навигация -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="text-xl font-bold text-gray-800"><?= t('app_name') ?></a>
                
                <div class="flex gap-4 items-center">
                    <!-- Переключатель языка -->
                    <div class="flex gap-2 text-sm">
                        <a href="?lang=ru" class="<?= getCurrentLang() === 'ru' ? 'font-bold' : '' ?>">RU</a>
                        <span>|</span>
                        <a href="?lang=en" class="<?= getCurrentLang() === 'en' ? 'font-bold' : '' ?>">EN</a>
                    </div>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="/trips" class="text-gray-700 hover:text-gray-900"><?= t('nav_trips') ?></a>
                        <a href="/profile" class="text-gray-700 hover:text-gray-900"><?= t('nav_profile') ?></a>
                        <a href="/logout" class="text-gray-700 hover:text-gray-900"><?= t('nav_logout') ?></a>
                    <?php else: ?>
                        <a href="/login" class="text-gray-700 hover:text-gray-900"><?= t('nav_login') ?></a>
                        <a href="/register" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"><?= t('nav_register') ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash-сообщения -->
    <?php $flash = getFlash(); ?>
    <?php if ($flash): ?>
        <div class="container mx-auto px-4 mt-4">
            <div class="p-4 rounded <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= e($flash['message']) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Контент -->
    <main class="container mx-auto px-4 py-8">
        <?= $content ?>
    </main>

    <!-- Футер -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="container mx-auto px-4 py-6 text-center text-gray-600 text-sm">
            &copy; <?= date('Y') ?> <?= t('footer_copyright') ?>
        </div>
    </footer>
</body>
</html>