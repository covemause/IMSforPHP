<?php
require_once __DIR__ . '/config/logger.php';

Logger::action('テストログ出力', [
    'user' => 'testuser',
    'info' => 'これはテストです',
    'time' => date('Y-m-d H:i:s')
]);

echo "action.logにテストログを出力しました。\n"; 