<div class="max-w-6xl mx-auto" x-data="{ expandedTrip: null }">
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
                    <?= t('trips_active') ?>
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
                    <?php
                    // Получаем этапы для этого похода
                    $stmt = db()->prepare('SELECT * FROM stages WHERE trip_id = ? ORDER BY stage_number');
                    $stmt->execute([$trip['id']]);
                    $stages = $stmt->fetchAll();
                    ?>
                    
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <button @click="expandedTrip = (expandedTrip === <?= $trip['id'] ?> ? null : <?= $trip['id'] ?>)"
                                                class="text-gray-400 hover:text-gray-600">
                                            <svg x-show="expandedTrip !== <?= $trip['id'] ?>" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            <svg x-show="expandedTrip === <?= $trip['id'] ?>" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <h3 class="text-lg font-semibold cursor-pointer" 
                                            @click="expandedTrip = (expandedTrip === <?= $trip['id'] ?> ? null : <?= $trip['id'] ?>)">
                                            <?= e($trip['name']) ?>
                                        </h3>
                                    </div>
                                    
                                    <div class="ml-8 flex items-center gap-3 mb-2">
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
                                    
                                    <div class="ml-8">
                                        <?php if ($trip['start_date']): ?>
                                            <p class="text-gray-600 text-sm mb-1">
                                                <?= t('trip_start_date') ?>: <?= date('d.m.Y', strtotime($trip['start_date'])) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <p class="text-gray-600 text-sm mb-1">
                                            <?= t('trip_stages') ?>: <?= count($stages) ?>
                                        </p>
                                        
                                        <p class="text-sm text-gray-500">
                                            <?= t('trip_created') ?>: <?= date('d.m.Y H:i', strtotime($trip['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <?php if ($trip['status'] === 'draft'): ?>
                                        <a href="/trip/edit?id=<?= $trip['id'] ?>" 
                                           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                            <?= t('trip_edit') ?>
                                        </a>
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
                                    <?php elseif ($trip['status'] === 'active'): ?>
                                        <a href="/trip/cancel?id=<?= $trip['id'] ?>" 
                                           onclick="return confirm('<?= t('trip_cancel_confirm') ?>')"
                                           class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                            <?= t('trip_cancel') ?>
                                        </a>
                                    <?php elseif ($trip['status'] === 'completed' || $trip['status'] === 'cancelled'): ?>
                                        <a href="/trip/copy?id=<?= $trip['id'] ?>" 
                                           class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                                            <?= t('trip_copy') ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Раскрытые этапы -->
                            <div x-show="expandedTrip === <?= $trip['id'] ?>" 
                                 x-collapse
                                 class="ml-8 mt-4 border-t pt-4">
                                <h4 class="font-semibold mb-3"><?= t('trip_stages_title') ?>:</h4>
                                <div class="space-y-2">
                                    <?php foreach ($stages as $stage): ?>
                                        <div class="bg-gray-50 p-3 rounded">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="font-medium"><?= t('trip_stage_number') ?> <?= $stage['stage_number'] ?>:</span>
                                                        <span><?= e($stage['description']) ?></span>
                                                        <?php if ($stage['status'] !== 'pending'): ?>
                                                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                                                <?php
                                                                switch($stage['status']) {
                                                                    case 'active': echo 'bg-green-100 text-green-800'; break;
                                                                    case 'confirmed': echo 'bg-blue-100 text-blue-800'; break;
                                                                    case 'overdue': echo 'bg-red-100 text-red-800'; break;
                                                                    case 'cancelled': echo 'bg-gray-100 text-gray-800'; break;
                                                                }
                                                                ?>">
                                                                <?= t('stage_status_' . $stage['status']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        <?= t('trip_stage_duration_full_days') ?>: <?= $stage['duration_days'] ?>
                                                        | <?= t('trip_stage_deadline_time') ?>: <?= substr($stage['deadline_time'], 0, 5) ?>
                                                        <?php if ($stage['deadline_utc']): ?>
                                                            | <?= t('stage_deadline') ?>: <?= date('d.m.Y H:i', strtotime($stage['deadline_utc'])) ?> UTC
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
</div>