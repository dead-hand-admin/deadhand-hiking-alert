<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6"><?= t('register_title') ?></h2>
    
    <form method="POST" action="/register">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2"><?= t('register_email') ?></label>
            <input type="email" name="email" required 
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2"><?= t('register_password') ?></label>
            <input type="password" name="password" required minlength="8"
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 mb-2"><?= t('register_password_confirm') ?></label>
            <input type="password" name="password_confirm" required minlength="8"
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="mb-6">
            <label class="flex items-start">
                <input type="checkbox" name="gdpr_agree" required class="mt-1 mr-2">
                <span class="text-sm text-gray-600"><?= t('register_gdpr_text') ?></span>
            </label>
        </div>
        
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            <?= t('register_submit') ?>
        </button>
    </form>
    
    <p class="mt-4 text-center text-gray-600">
        <?= t('register_have_account') ?> <a href="/login" class="text-blue-600 hover:underline"><?= t('register_login_link') ?></a>
    </p>
</div>
