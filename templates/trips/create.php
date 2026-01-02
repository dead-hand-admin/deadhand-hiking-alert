<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?= t('trip_create_title') ?></h1>
    
    <form method="POST" action="/trip/create" enctype="multipart/form-data" 
          x-data="tripForm()" @submit="validateForm">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        
        <!-- Основная информация -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold mb-4"><?= t('trip_create_title') ?></h2>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2"><?= t('trip_name') ?> *</label>
                <input type="text" name="name" required
                       placeholder="<?= t('trip_name_placeholder') ?>"
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1"><?= t('trip_name_hint') ?></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2"><?= t('trip_start_date') ?> <span x-show="!isDraft">*</span></label>
                <input type="date" name="start_date"
                       x-bind:required="!isDraft"
                       min="<?= date('Y-m-d') ?>"
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1"><?= t('trip_start_date_hint') ?></p>
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
                                    <?= $user['country_code'] === $c['code'] ? 'selected' : '' ?>>
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
                            <option :value="service.id" x-text="service.name + (service.is_default ? ' (<?= t('profile_emergency_default') ?>)' : '')"></option>
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
                                <input type="time" :name="'stages[' + index + '][deadline_time]'" 
                                       x-model="stage.deadline_time"
                                       required
                                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
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
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2"><?= t('trip_track') ?></label>
                <input type="file" name="track" accept=".gpx,.kml,.kmz"
                       class="w-full px-4 py-2 border rounded">
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2"><?= t('trip_photos') ?></label>
                <input type="file" name="photos[]" accept="image/jpeg,image/jpg,image/png" multiple
                       class="w-full px-4 py-2 border rounded">
                <p class="text-sm text-gray-500 mt-1"><?= t('profile_photo_hint') ?></p>
            </div>
        </div>
        
        <!-- Кнопки -->
        <div class="flex gap-4">
            <button type="submit" name="action" value="create"
                    @click="isDraft = false"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <?= t('trip_submit') ?>
            </button>
            <button type="submit" name="action" value="draft"
                    @click="isDraft = true"
                    class="bg-gray-200 text-gray-800 px-6 py-2 rounded hover:bg-gray-300">
                <?= t('trip_save_draft') ?>
            </button>
            <a href="/trips" class="px-6 py-2 text-gray-600 hover:text-gray-800">
                Отмена
            </a>
        </div>
    </form>
</div>

<script>
const allEmergencyServices = <?= json_encode($allServices) ?>;

function tripForm() {
    return {
        country: '<?= e($user['country_code']) ?>',
        defaultEmergencyService: '<?= $user['default_emergency_service_id'] ?? '' ?>',
        filteredServices: [],
        isDraft: false,
        stages: [
            {
                description: '',
                location: '',
                duration_days: 0,
                deadline_time: '20:00',
                emergency_service_id: '<?= $user['default_emergency_service_id'] ?? '' ?>',
                requires_confirmation: true
            }
        ],
        
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
