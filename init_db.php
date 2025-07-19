<?php
require_once __DIR__ . '/config/SystemRequirements.php';
require_once __DIR__ . '/config/database.php';

// システム要件チェック
$requirements = SystemRequirements::checkRequirements();
if (!empty($requirements)) {
    echo "\n【システム要件エラー】\n";
    foreach ($requirements as $error) {
        echo "- {$error}\n";
    }
    exit(1);
}

// データベース初期化
try {
    $db = Database::getInstance();
    $db->initDatabase();
    echo "\nデータベースの初期化が完了しました。\n";
} catch (Exception $e) {
    echo "\nデータベース初期化エラー: " . $e->getMessage() . "\n";
    exit(1);
} 