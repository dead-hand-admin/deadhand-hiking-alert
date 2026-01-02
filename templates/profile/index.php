<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?= t('profile_title') ?></h1>
    
    <!-- Навигация по вкладкам -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-4">
            <a href="/profile?tab=personal" 
               class="px-4 py-2 border-b-2 font-medium transition <?= $currentTab === 'personal' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600' ?>">
                <?= t('profile_tab_personal') ?>
            </a>
            <a href="/profile?tab=contacts" 
               class="px-4 py-2 border-b-2 font-medium transition <?= $currentTab === 'contacts' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600' ?>">
                <?= t('profile_tab_contacts') ?>
            </a>
            <a href="/profile?tab=photos" 
               class="px-4 py-2 border-b-2 font-medium transition <?= $currentTab === 'photos' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600' ?>">
                <?= t('profile_tab_photos') ?>
            </a>
        </nav>
    </div>
    
    <!-- Личные данные -->
    <?php if ($currentTab === 'personal'): ?>
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4"><?= t('profile_personal_info') ?></h2>
        
        <form method="POST" action="/profile/update">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 mb-2"><?= t('profile_email') ?></label>
                    <input type="email" value="<?= e($user['email']) ?>" disabled
                           class="w-full px-4 py-2 border rounded bg-gray-50">
                    <p class="text-sm text-gray-500 mt-1"><?= t('profile_email_hint') ?></p>
                </div>
                
                <div>
                    <label class="block text-gray-700 mb-2"><?= t('profile_fio') ?></label>
                    <input type="text" name="fio" value="<?= e($user['fio']) ?>"
                           class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="<?= t('profile_fio_placeholder') ?>">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2"><?= t('profile_habits') ?></label>
                <textarea name="habits" rows="4"
                          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="<?= t('profile_habits_placeholder') ?>"><?= e($user['habits']) ?></textarea>
                <p class="text-sm text-gray-500 mt-1"><?= t('profile_habits_hint') ?></p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 mb-2"><?= t('profile_timezone') ?></label>
                    <select name="timezone" 
                            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php foreach ($timezones as $tz): ?>
                            <option value="<?= e($tz['timezone_name']) ?>" 
                                    <?= $user['timezone'] === $tz['timezone_name'] ? 'selected' : '' ?>>
                                <?= e($tz['display_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 mb-2"><?= t('profile_country') ?></label>
                    <select name="country_code" 
                            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= e($country['code']) ?>" 
                                    <?= $user['country_code'] === $country['code'] ? 'selected' : '' ?>>
                                <?= e($country['name_' . getCurrentLang()]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 mb-2"><?= t('profile_emergency_service') ?></label>
                <select name="default_emergency_service_id" 
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value=""><?= t('profile_emergency_service_none') ?></option>
                    <?php foreach ($emergencyServices as $service): ?>
                        <option value="<?= $service['id'] ?>" 
                                <?= $user['default_emergency_service_id'] == $service['id'] ? 'selected' : '' ?>>
                            <?= e($service['name_' . getCurrentLang()]) ?>
                            <?= $service['is_default'] ? ' (' . t('profile_emergency_default') . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <?= t('profile_save') ?>
            </button>
        </form>
    </div>
    <?php endif; ?>
    
    <!-- Контакты -->
    <?php if ($currentTab === 'contacts'): ?>
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4"><?= t('profile_contacts_title') ?></h2>
        <p class="text-gray-600 mb-4"><?= t('profile_contacts_description') ?></p>
        
        <?php if (count($contacts) > 0): ?>
            <div class="space-y-3 mb-4">
                <?php foreach ($contacts as $contact): ?>
                    <div class="flex items-center justify-between border p-3 rounded">
                        <div>
                            <p class="font-medium"><?= e($contact['name']) ?></p>
                            <p class="text-sm text-gray-600"><?= e($contact['email']) ?></p>
                        </div>
                        <a href="/profile/contact/delete?id=<?= $contact['id'] ?>" 
                           onclick="return confirm('<?= t('profile_contact_delete_confirm') ?>')"
                           class="text-red-600 hover:text-red-800">
                            <?= t('profile_delete') ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 mb-4"><?= t('profile_no_contacts') ?></p>
        <?php endif; ?>
        
        <?php if (count($contacts) < MAX_CONTACTS): ?>
            <form method="POST" action="/profile/contact/add" class="border-t pt-4">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <h3 class="font-medium mb-3"><?= t('profile_add_contact') ?></h3>
                
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 mb-2"><?= t('profile_contact_name') ?></label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2"><?= t('profile_contact_email') ?></label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <?= t('profile_contact_add') ?>
                </button>
            </form>
        <?php else: ?>
            <p class="text-sm text-gray-500"><?= t('profile_contacts_limit') ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Фото -->
    <?php if ($currentTab === 'photos'): ?>
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4"><?= t('profile_photos_title') ?></h2>
        <p class="text-gray-600 mb-4"><?= t('profile_photos_description') ?></p>
        
        <?php if (count($photos) > 0): ?>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                <?php foreach ($photos as $photo): ?>
                    <div class="relative group">
                        <img src="/<?= e($photo['file_path']) ?>" 
                             alt="<?= e($photo['description']) ?>"
                             class="w-full h-48 object-cover rounded">
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <a href="/profile/photo/delete?id=<?= $photo['id'] ?>" 
                               onclick="return confirm('<?= t('profile_photo_delete_confirm') ?>')"
                               class="text-white bg-red-600 px-3 py-1 rounded">
                                <?= t('profile_delete') ?>
                            </a>
                        </div>
                        <?php if ($photo['description']): ?>
                            <p class="text-sm text-gray-600 mt-1"><?= e($photo['description']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 mb-4"><?= t('profile_no_photos') ?></p>
        <?php endif; ?>
        
        <?php if (count($photos) < MAX_PHOTOS_PROFILE): ?>
            <form method="POST" action="/profile/photo/upload" enctype="multipart/form-data" class="border-t pt-4">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <h3 class="font-medium mb-3"><?= t('profile_upload_photo') ?></h3>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2"><?= t('profile_photo_file') ?></label>
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg" required
                           class="w-full px-4 py-2 border rounded">
                    <p class="text-sm text-gray-500 mt-1"><?= t('profile_photo_hint') ?></p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2"><?= t('profile_photo_description') ?></label>
                    <input type="text" name="description" 
                           placeholder="<?= t('profile_photo_description_placeholder') ?>"
                           class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <?= t('profile_photo_upload') ?>
                </button>
            </form>
        <?php else: ?>
            <p class="text-sm text-gray-500"><?= t('profile_photos_limit') ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
