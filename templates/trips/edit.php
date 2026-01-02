<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?= t('trip_edit_title') ?></h1>
    
    <form method="POST" action="/trip/update" enctype="multipart/form-data" 
          x-data="tripForm()" @submit="validateForm">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">
        
        <!-- Основная информация -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2"><?= t('trip_name') ?> *</label>
                <input type="text" name="name" required
                       value="<?= e($trip['name']) ?>"
                       placeholder="<?= t('trip_name_placeholder') ?>"
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1"><?= t('trip_name_hint') ?></p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-2"><?= t('trip_country') ?> *</label>
                    <select name="country_code" required
                            x-model="country"
                            @change="filterEmergencyServices"
                            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php foreach ($countries as $c): ?>
                            <option value="<?= e($c['code']) ?>" 
                                    <?= $trip['country_code'] === $c['code'] ? 'selected' : '' ?>>
                                <?= e($c['name_' . getCurrentLang()]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 mb-2"><?= t('trip_emergency_service') ?></label>
                    <select name="emergency_service_id" x-model="defaultEmergencyService"
                            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value=""><?= t('profile_emergency_service_none') ?></option>
                        <template x-for="service in filteredServices" :key="service.id">
                            <option :value="service.id" 
                                    :selected="service.id == <?= $trip['emergency_service_id'] ?? 'null' ?>"
                                    x-text="service.name + (service.is_default ? ' (<?= t('profile_emergency_default') ?>)' : '')"></option>
                        </template>
                    </select>
                    <p class="text-sm text-gray-500 mt-1"><?= t('trip_emergency_service_hint') ?></p>
                </div>
            </div>
        </div>
        
        <!-- Этапы -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-2"><?= t('trip_stages_title') ?></h2>
            <p class="text-gray-600 mb-4"><?= t('trip_stages_description') ?></p>
            
            <div class="space-y-4">
                <template x-for="(stage, index) in stages" :key="index">
                    <div class="border rounded p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-medium"><?= t('trip_stage_number') ?> <span x-text="index + 1"></span></h3>
                            <button type="button" @click="removeStage(index)" 
                                    x-show="stages.length > 1"
                                    class="text-red-600 hover:text-red-800 text-sm">
                                <?= t('trip_stage_remove') ?>
                            </button>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-gray-700 text-sm mb-1"><?= t('trip_stage_description') ?> *</label>
                                <input type="text" :name="'stages[' + index + '][description]'" required
                                       x-model="stage.description"
                                       placeholder="<?= t('trip_stage_description_placeholder') ?>"
                                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm mb-1"><?= t('trip_stage_checkpoint') ?></label>
                                <input type="text" :name="'stages[' + index + '][location]'"
                                       x-model="stage.location"
                                       placeholder="<?= t('trip_stage_checkpoint_placeholder') ?>"
                                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-gray-700 text-sm mb-1"><?= t('trip_stage_duration_full_days') ?> *</label>
                                <input type="number" :name="'stages[' + index + '][duration_days]'" 
                                       x-model="stage.duration_days"
                                       min="0" max="30" required
                                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm mb-1"><?= t('trip_stage_deadline_time') ?> *</label>
                                <select :name="'stages[' + index + '][deadline_time]'" 
                                       x-model="stage.deadline_time"
                                       required
                                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <?php
                                    // Генерация временных слотов с шагом 30 минут
                                    for ($h = 0; $h < 24; $h++) {
                                        for ($m = 0; $m < 60; $m += 30) {
                                            $time = sprintf('%02d:%02d', $h, $m);
                                            echo "<option value=\"{$time}\">{$time}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-gray-700 text-sm mb-1"><?= t('trip_stage_emergency_service') ?></label>
                            <select :name="'stages[' + index + '][emergency_service_id]'"
                                    x-model="stage.emergency_service_id"
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value=""><?= t('profile_emergency_service_none') ?></option>
                                <template x-for="service in filteredServices" :key="service.id">
                                    <option :value="service.id" x-text="service.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="flex items-start">
                                <input type="checkbox" :name="'stages[' + index + '][requires_confirmation]'" 
                                       x-model="stage.requires_confirmation"
                                       value="1"
                                       class="mt-1 mr-2">
                                <span class="text-sm text-gray-700">
                                    <?= t('trip_stage_requires_confirmation') ?>
                                    <span class="block text-gray-500 text-xs"><?= t('trip_stage_requires_confirmation_hint') ?></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </template>
            </div>
            
            <button type="button" @click="addStage" 
                    class="mt-4 bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                + <?= t('trip_stage_add') ?>
            </button>
        </div>
        
        <!-- Файлы -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4"><?= t('trip_files_title') ?></h2>
            
            <!-- Загруженный трек -->
            <?php 
            $track = array_filter($files, fn($f) => $f['file_type'] === 'track');
            $track = !empty($track) ? reset($track) : null;
            ?>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2"><?= t('trip_track') ?></label>
                <?php if ($track): ?>
                    <div class="bg-gray-50 p-3 rounded mb-2 flex justify-between items-center">
                        <div>
                            <span class="font-medium"><?= e($track['original_name']) ?></span>
                            <span class="text-sm text-gray-500 ml-2">(<?= number_format($track['file_size'] / 1024, 1) ?> KB)</span>
                        </div>
                        <a href="/trip/file/delete?id=<?= $track['id'] ?>&trip_id=<?= $trip['id'] ?>" 
                           onclick="return confirm('<?= t('file_delete_confirm') ?>')"
                           class="text-red-600 hover:text-red-800">
                            <?= t('delete') ?>
                        </a>
                    </div>
                <?php endif; ?>
                <input type="file" name="track" accept=".gpx,.kml,.kmz"
                       class="w-full px-4 py-2 border rounded">
            </div>
            
            <!-- Загруженные фото -->
            <?php 
            $photos = array_filter($files, fn($f) => $f['file_type'] === 'photo');
            ?>
            <div>
                <label class="block text-gray-700 mb-2"><?= t('trip_photos') ?></label>
                <?php if (!empty($photos)): ?>
                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <?php foreach ($photos as $photo): ?>
                            <div class="bg-gray-50 p-2 rounded flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <img src="/<?= e($photo['file_path']) ?>" alt="<?= e($photo['original_name']) ?>" 
                                         class="w-16 h-16 object-cover rounded">
                                    <div>
                                        <div class="text-sm font-medium"><?= e($photo['original_name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= number_format($photo['file_size'] / 1024, 1) ?> KB</div>
                                    </div>
                                </div>
                                <a href="/trip/file/delete?id=<?= $photo['id'] ?>&trip_id=<?= $trip['id'] ?>" 
                                   onclick="return confirm('<?= t('file_delete_confirm') ?>')"
                                   class="text-red-600 hover:text-red-800 text-sm">
                                    <?= t('delete') ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <input type="file" name="photos[]" accept="image/jpeg,image/jpg,image/png" multiple
                       class="w-full px-4 py-2 border rounded">
                <p class="text-sm text-gray-500 mt-1"><?= t('profile_photo_hint') ?></p>
            </div>
        </div>
        
        <!-- Кнопки -->
        <div class="flex gap-4">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <?= t('trip_save') ?>
            </button>
            <a href="/trips" class="px-6 py-2 text-gray-600 hover:text-gray-800">
                <?= t('cancel') ?>
            </a>
        </div>
    </form>
</div>

<script>
const allEmergencyServices = <?= json_encode($allServices) ?>;

function tripForm() {
    return {
        country: '<?= e($trip['country_code']) ?>',
        defaultEmergencyService: '<?= $trip['emergency_service_id'] ?? '' ?>',
        filteredServices: [],
        stages: <?= json_encode(array_map(function($s) {
            return [
                'description' => $s['description'],
                'location' => $s['location'] ?? '',
                'duration_days' => (int)$s['duration_days'],
                'deadline_time' => substr($s['deadline_time'], 0, 5),
                'emergency_service_id' => $s['emergency_service_id'] ?? '',
                'requires_confirmation' => (bool)$s['requires_confirmation']
            ];
        }, $stages)) ?>,
        
        init() {
            this.filterEmergencyServices();
        },
        
        filterEmergencyServices() {
            this.filteredServices = allEmergencyServices.filter(s => s.country_code === this.country);
        },
        
        addStage() {
            this.stages.push({
                description: '',
                location: '',
                duration_days: 1,
                deadline_time: '20:00',
                emergency_service_id: this.defaultEmergencyService,
                requires_confirmation: true
            });
        },
        
        removeStage(index) {
            if (this.stages.length > 1) {
                this.stages.splice(index, 1);
            }
        },
        
        validateForm(e) {
            if (this.stages.length === 0) {
                alert('<?= t('error_trip_stages_required') ?>');
                e.preventDefault();
                return false;
            }
            return true;
        }
    }
}
</script>