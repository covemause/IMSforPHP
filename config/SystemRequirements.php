<?php
/**
 * システム要件チェッククラス
 */
class SystemRequirements {
    
    /**
     * システム要件をチェック
     */
    public static function checkRequirements(): array {
        $errors = [];
        
        // PHPバージョンチェック
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            $errors[] = 'PHP 8.0以上が必要です。現在のバージョン: ' . PHP_VERSION;
        }
        
        // PostgreSQL拡張機能チェック
        if (!extension_loaded('pdo_pgsql')) {
            $errors[] = 'PDO PostgreSQL拡張機能（pdo_pgsql）がインストールされていません。';
        }
        
        // PDO拡張機能チェック
        if (!extension_loaded('pdo')) {
            $errors[] = 'PDO拡張機能がインストールされていません。';
        }
        
        // JSON拡張機能チェック
        if (!extension_loaded('json')) {
            $errors[] = 'JSON拡張機能がインストールされていません。';
        }
        
        // ディレクトリ権限チェック
        $dataDir = __DIR__ . '/../data';
        if (!is_dir($dataDir)) {
            if (!mkdir($dataDir, 0755, true)) {
                $errors[] = 'データディレクトリ（data/）の作成に失敗しました。';
            }
        } elseif (!is_writable($dataDir)) {
            $errors[] = 'データディレクトリ（data/）に書き込み権限がありません。';
        }
        
        // ログディレクトリチェック
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            if (!mkdir($logDir, 0755, true)) {
                $errors[] = 'ログディレクトリ（logs/）の作成に失敗しました。';
            }
        } elseif (!is_writable($logDir)) {
            $errors[] = 'ログディレクトリ（logs/）に書き込み権限がありません。';
        }
        
        // 設定ファイルチェック
        $configFile = __DIR__ . '/database_config.php';
        if (!file_exists($configFile)) {
            $errors[] = 'データベース設定ファイル（config/database_config.php）が存在しません。';
        }
        
        return $errors;
    }
    
    /**
     * システム要件エラーページを表示
     */
    public static function displayErrorPage(array $errors): void {
        // エラーページ用の変数を設定
        $title = 'システム要件エラー';
        $message = 'システムを実行するために以下の問題を解決してください：';
        $type = 'danger';
        $icon = 'exclamation-triangle';
        $details = $errors;
        
        // 解決方法を設定
        $solutions = [
            [
                'title' => 'Ubuntu/Debian系の場合',
                'commands' => [
                    'sudo apt-get install php-pgsql php-pdo-pgsql',
                    'sudo systemctl restart apache2'
                ]
            ],
            [
                'title' => 'CentOS/RHEL系の場合',
                'commands' => [
                    'sudo yum install php-pgsql php-pdo-pgsql',
                    'sudo systemctl restart httpd'
                ]
            ],
            [
                'title' => 'Windowsの場合',
                'description' => 'php.iniファイルで以下の行のコメントアウトを解除：',
                'commands' => [
                    'extension=pdo_pgsql',
                    'extension=pgsql'
                ]
            ],
            [
                'title' => '設定ファイルの作成',
                'description' => 'config/database_config.phpファイルを作成し、PostgreSQL接続情報を設定してください。'
            ]
        ];
        
        $additional_info = 'システム要件を満たした後、ページを再読み込みしてください。';
        $retry_url = $_SERVER['REQUEST_URI'] ?? 'index.php';
        
        // 共通エラー画面を表示
        include __DIR__ . '/../views/error.php';
    }
    
    /**
     * システム情報を取得
     */
    public static function getSystemInfo(): array {
        return [
            'php_version' => PHP_VERSION,
            'pdo_pgsql_loaded' => extension_loaded('pdo_pgsql'),
            'pdo_loaded' => extension_loaded('pdo'),
            'json_loaded' => extension_loaded('json'),
            'data_dir_writable' => is_writable(__DIR__ . '/../data'),
            'logs_dir_writable' => is_writable(__DIR__ . '/../logs'),
            'config_file_exists' => file_exists(__DIR__ . '/database_config.php'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'os' => PHP_OS,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size')
        ];
    }
}
?> 