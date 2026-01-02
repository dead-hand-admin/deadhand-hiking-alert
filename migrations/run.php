<?php
/**
 * Скрипт применения миграций
 */

require_once __DIR__ . '/../config.php';

if (!defined('MIGRATION_KEY')) {
    http_response_code(500);
    die('MIGRATION_KEY not set in .env file');
}

if (!isset($_GET['key']) || $_GET['key'] !== MIGRATION_KEY) {
    http_response_code(403);
    die('Access denied');
}

require_once __DIR__ . '/../db.php';

$migrationFiles = glob(__DIR__ . '/*.sql');
sort($migrationFiles);

if (empty($migrationFiles)) {
    die('No migration files found');
}

echo "<pre>";
echo "Found " . count($migrationFiles) . " migration file(s)\n";
echo str_repeat('=', 60) . "\n\n";

foreach ($migrationFiles as $fullPath) {
    $filename = basename($fullPath);
    echo "Processing: $filename\n";
    
    $sql = file_get_contents($fullPath);
    
    if (empty(trim($sql))) {
        echo "  ⚠️  Empty file, skipping\n\n";
        continue;
    }
    
    try {
        // Выполняем весь файл целиком (для процедур)
        db()->exec($sql);
        echo "  ✅ Success\n";
    } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
        
        // Игнорируем безобидные ошибки
        if (
            strpos($errorMsg, 'already exists') !== false ||
            strpos($errorMsg, 'Duplicate') !== false ||
            strpos($errorMsg, "Can't DROP") !== false
        ) {
            echo "  ℹ️  Already exists (skipped)\n";
        } else {
            echo "  ❌ Error: $errorMsg\n";
        }
    }
    
    echo "\n";
}

echo str_repeat('=', 60) . "\n";
echo "Migration completed!\n";
echo "</pre>";
