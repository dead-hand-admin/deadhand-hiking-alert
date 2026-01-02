<div class="max-w-4xl mx-auto">
    <!-- Заголовок -->
    <div class="mb-6">
        <div class="flex justify-between items-start mb-2">
            <h1 class="text-3xl font-bold"><?= e($trip['name']) ?></h1>
            <span class="px-4 py-2 rounded text-sm font-medium
                <?php
                switch($trip['status']) {
                    case 'active': echo 'bg-green-100 text-green-800'; break;
                    case 'draft': echo 'bg-gray-100 text-gray-800'; break;
                    case 'completed': echo 'bg-blue-100 text-blue-800'; break;
                    case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                }
                ?>">
                <?= t('trip_status_' . $trip['status']) ?>
            </span>
        </div>
        
        <div class="flex gap-4 text-gray-600">
            <?php if ($trip['start_date']): ?>
                <span><?= t('trip_start_date') ?>: <?= date('d.m.Y', strtotime($trip['start_date'])) ?></span>
            <?php endif; ?>
            <span><?= t('trip_created') ?>: <?= date('d.m.Y H:i', strtotime($trip['created_at'])) ?></span>
        </div>
        
        <div class="mt-4 flex gap-2">
            <?php if ($trip['status'] === 'draft'): ?>
                <a href="/trip/confirm?id=<?= $trip['id'] ?>" 
                   onclick="return confirm('<?= t('trip_activate_confirm') ?>')"
                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    <?= t('trip_activate') ?>
                </a>
                <a href="/trip/delete?id=<?= $trip['id'] ?>" 
                   onclick="return confirm('<?= t('trip_delete_confirm') ?>')"
                   class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    <?= t('trip_delete') ?>
                </a>
            <?php endif; ?>
            
            <?php if ($trip['status'] === 'active'): ?>
                <a href="/trip/cancel?id=<?= $trip['id'] ?>" 
                   onclick="return confirm('<?= t('trip_cancel_confirm') ?>')"
                   class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    <?= t('trip_cancel') ?>
                </a>
            <?php endif; ?>
            
            <a href="/trips" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                <?= t('trip_back_to_list') ?>
            </a>
        </div>
    </div>
    
    <!-- Этапы -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h2 class="text-xl font-semibold mb-4"><?= t('trip_stages_title') ?></h2>
        
        <?php if (empty($trip['stages'])): ?>
            <p class="text-gray-500"><?= t('trip_no_stages') ?></p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($trip['stages'] as $stage): ?>
                    <div class="border rounded p-4 <?= $stage['status'] === 'active' ? 'border-blue-500 bg-blue-50' : '' ?>">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold">
                                    <?= t('trip_stage_number') ?> <?= $stage['stage_number'] ?>: <?= e($stage['description']) ?>
                                </h3>
                                <?php if ($stage['location']): ?>
                                    <p class="text-gray-600 text-sm">📍 <?= e($stage['location']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <span class="px-3 py-1 rounded text-sm font-medium
                                <?php
                                switch($stage['status']) {
                                    case 'active': echo 'bg-blue-100 text-blue-800'; break;
                                    case 'pending': echo 'bg-gray-100 text-gray-800'; break;
                                    case 'confirmed': echo 'bg-green-100 text-green-800'; break;
                                    case 'overdue': echo 'bg-red-100 text-red-800'; break;
                                    case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                }
                                ?>">
                                <?= t('stage_status_' . $stage['status']) ?>
                            </span>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-600 mt-3">
                            <div>
                                <strong><?= t('stage_duration') ?>:</strong> 
                                <?= $stage['duration_days'] ?> <?= t('stage_days_unit') ?>
                            </div>
                            <div>
                                <strong><?= t('stage_deadline_time') ?>:</strong> <?= substr($stage['deadline_time'], 0, 5) ?>
                            </div>
                            <?php if ($stage['deadline_utc']): ?>
                                <div>
                                    <strong><?= t('stage_deadline') ?>:</strong> 
                                    <?= date('d.m.Y H:i', strtotime($stage['deadline_utc'])) ?> (UTC)
                                </div>
                            <?php endif; ?>
                            <div>
                                <strong><?= t('stage_requires_confirmation') ?>:</strong> 
                                <?= $stage['requires_confirmation'] ? t('yes') : t('no') . ' (' . t('stage_auto_transition') . ')' ?>
                            </div>
                        </div>
                        
                        <?php if ($stage['status'] === 'active' && $stage['requires_confirmation']): ?>
                            <div class="mt-4">
                                <a href="/stage/confirm?id=<?= $stage['id'] ?>" 
                                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 inline-block">
                                    <?= t('stage_confirm_button') ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Файлы -->
    <?php if (!empty($files)): ?>
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4"><?= t('trip_files_title') ?></h2>
            
            <div class="space-y-4">
                <?php 
                $tracks = array_filter($files, fn($f) => $f['file_type'] === 'track');
                $photos = array_filter($files, fn($f) => $f['file_type'] === 'photo');
                ?>
                
                <?php if (!empty($tracks)): ?>
                    <div>
                        <h3 class="font-medium mb-2"><?= t('trip_tracks') ?>:</h3>
                        <div class="space-y-2">
                            <?php foreach ($tracks as $track): ?>
                                <div class="flex items-center gap-2 text-sm">
                                    <span>📄</span>
                                    <a href="/<?= e($track['file_path']) ?>" target="_blank" 
                                       class="text-blue-600 hover:underline">
                                        <?= e($track['original_name']) ?>
                                    </a>
                                    <span class="text-gray-500">(<?= round($track['file_size'] / 1024, 1) ?> KB)</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($photos)): ?>
                    <div>
                        <h3 class="font-medium mb-2"><?= t('trip_photos_list') ?>:</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <?php foreach ($photos as $photo): ?>
                                <a href="/<?= e($photo['file_path']) ?>" target="_blank">
                                    <img src="/<?= e($photo['file_path']) ?>" 
                                         alt="<?= t('trip_photo_alt') ?>"
                                         class="w-full h-48 object-cover rounded hover:opacity-90">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
