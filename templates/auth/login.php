<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6"><?= t('login_title') ?></h2>
    
    <form method="POST" action="/login">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2"><?= t('login_email') ?></label>
            <input type="email" name="email" required 
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 mb-2"><?= t('login_password') ?></label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            <?= t('login_submit') ?>
        </button>
    </form>
    
    <p class="mt-4 text-center text-gray-600">
        <?= t('login_no_account') ?> <a href="/register" class="text-blue-600 hover:underline"><?= t('login_register_link') ?></a>
    </p>
</div>
