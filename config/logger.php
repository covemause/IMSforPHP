<?php
class Logger {
    public static function action($message, $context = []) {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/action.log';
        $time = date('Y-m-d H:i:s');
        $contextStr = json_encode($context, JSON_UNESCAPED_UNICODE);
        file_put_contents($logFile, "[$time] $message CONTEXT: $contextStr\n", FILE_APPEND);
    }
} 