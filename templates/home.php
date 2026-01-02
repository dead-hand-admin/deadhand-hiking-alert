<div class="max-w-4xl mx-auto">
    <div class="text-center py-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <?= t('home_title') ?>
        </h1>
        <p class="text-xl text-gray-600 mb-8">
            <?= t('home_subtitle') ?>
        </p>
        
        <?php if (!isLoggedIn()): ?>
            <div class="flex gap-4 justify-center">
                <a href="/register" class="px-6 py-3 bg-blue-600 text-white rounded-lg text-lg hover:bg-blue-700">
                    <?= t('home_cta_start') ?>
                </a>
                <a href="/login" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg text-lg hover:bg-gray-300">
                    <?= t('home_cta_login') ?>
                </a>
            </div>
        <?php else: ?>
            <a href="/profile" class="px-6 py-3 bg-blue-600 text-white rounded-lg text-lg hover:bg-blue-700 inline-block">
                <?= t('home_cta_profile') ?>
            </a>
        <?php endif; ?>
    </div>

    <!-- Как это работает -->
    <div class="grid md:grid-cols-3 gap-8 mt-12">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-3xl mb-4">📝</div>
            <h3 class="text-xl font-semibold mb-2"><?= t('home_step1_title') ?></h3>
            <p class="text-gray-600"><?= t('home_step1_desc') ?></p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-3xl mb-4">⏰</div>
            <h3 class="text-xl font-semibold mb-2"><?= t('home_step2_title') ?></h3>
            <p class="text-gray-600"><?= t('home_step2_desc') ?></p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-3xl mb-4">🚨</div>
            <h3 class="text-xl font-semibold mb-2"><?= t('home_step3_title') ?></h3>
            <p class="text-gray-600"><?= t('home_step3_desc') ?></p>
        </div>
    </div>
</div>
