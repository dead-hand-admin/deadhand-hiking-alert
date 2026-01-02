<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold"><?= t('trips_title') ?></h1>
        <a href="/trip/create" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            <?= t('trips_create') ?>
        </a>
    </div>
    
    <?php if (empty(array_merge(...array_values($tripsByStatus)))): ?>
        <div class="bg-white p-8 rounded-lg shadow text-center">
            <p class="text-gray-600 mb-4"><?= t('trips_no_trips') ?></p>
            <a href="/trip/create" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <?= t('trips_create') ?>
            </a>
        </div>
    <?php else: ?>
        
        <!-- Вкладки -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex gap-4">
                <a href="/trips?tab=active" 
                   class="px-4 py-2 border-b-2 font-medium transition <?= $currentTab === 'active' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600' ?>">
                    <?= t('trips_active') ?> (<?= count($tripsByStatus['active']) ?>)
                </a>
                <a href="/trips?tab=draft" 
                   class="px-4 py-2 border-b-2 font-medium transition <?= $currentTab === 'draft' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600' ?>">
                    <?= t('trips_draft') ?> (<?= count($tripsByStatus['draft']) ?>)
                </a>
                <a href="/trips?tab=completed" 
                   class="px-4 py-2 border-b-2 font-medium transition <?= $currentTab === 'completed' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600' ?>">
                    <?= t('trips_completed') ?> (<?= count($tripsByStatus['completed']) ?>)
                </a>
                <a href="/trips?tab=cancelled" 
                   class="px-4 py-2 border-b-2 font-medium transition <?= $currentTab === 'cancelled' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600' ?>">
                    <?= t('trips_cancelled') ?> (<?= count($tripsByStatus['cancelled']) ?>)
                </a>
            </nav>
        </div>
        
        <!-- Список походов -->
        <?php if (empty($tripsByStatus[$currentTab])): ?>
            <p class="text-gray-500"><?= t('trips_no_trips') ?></p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($tripsByStatus[$currentTab] as $trip): ?>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold mb-2"><?= e($trip['name']) ?></h3>
                                
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-3 py-1 rounded text-sm font-medium
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
                                    <span class="text-gray-600"><?= e($trip['country_name']) ?></span>
                                </div>
                                
                                <?php if ($trip['start_date']): ?>
                                    <p class="text-gray-600 mb-1">
                                        <?= t('trip_start_date') ?>: <?= date('d.m.Y', strtotime($trip['start_date'])) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <p class="text-gray-600 mb-2">
                                    <?= t('trip_stages') ?>: <?= $trip['stages_count'] ?>
                                </p>
                                
                                <p class="text-sm text-gray-500">
                                    <?= t('trip_created') ?>: <?= date('d.m.Y H:i', strtotime($trip['created_at'])) ?>
                                </p>
                            </div>
                            
                            <div class="flex gap-2">
                                <?php if ($trip['status'] === 'draft'): ?>
                                    <a href="/trip/confirm?id=<?= $trip['id'] ?>" 
                                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                        <?= t('trip_activate') ?>
                                    </a>
                                    <a href="/trip/view?id=<?= $trip['id'] ?>" 
                                       class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                                        <?= t('trip_edit') ?>
                                    </a>
                                <?php elseif ($trip['status'] === 'completed'): ?>
                                    <a href="/trip/copy?id=<?= $trip['id'] ?>" 
                                       class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                                        <?= t('trip_copy') ?>
                                    </a>
                                    <a href="/trip/view?id=<?= $trip['id'] ?>" 
                                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                        <?= t('trip_view') ?>
                                    </a>
                                <?php else: ?>
                                    <a href="/trip/view?id=<?= $trip['id'] ?>" 
                                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                        <?= t('trip_view') ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
</div>
