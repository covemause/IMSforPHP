<?php
/**
 * PostgreSQLデータベースセットアップスクリプト
 */

// エラーレポートを有効化
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PostgreSQLデータベースセットアップを開始します...\n\n";

// 設定ファイルの確認
$configFile = __DIR__ . '/config/database_config.php';
if (!file_exists($configFile)) {
    echo "エラー: 設定ファイルが見つかりません: $configFile\n";
    exit(1);
}

$config = require $configFile;

echo "データベース設定:\n";
echo "ホスト: " . $config['host'] . "\n";
echo "ポート: " . $config['port'] . "\n";
echo "データベース名: " . $config['dbname'] . "\n";
echo "ユーザー名: " . $config['username'] . "\n\n";

echo "以下の手順でPostgreSQLをセットアップしてください:\n\n";

echo "1. PostgreSQLのインストール:\n";
echo "   - Windows: https://www.postgresql.org/download/windows/\n";
echo "   - または Chocolatey: choco install postgresql\n\n";

echo "2. データベースの作成:\n";
echo "   psql -U postgres\n";
echo "   CREATE DATABASE ims_db;\n";
echo "   \\q\n\n";

echo "3. PHP PostgreSQL拡張機能のインストール:\n";
echo "   - Windows: php.iniで extension=pdo_pgsql のコメントアウトを解除\n";
echo "   - または Chocolatey: choco install php-pgsql\n\n";

echo "4. 設定の確認:\n";
echo "   - config/database_config.php の接続情報を確認\n";
echo "   - 必要に応じてパスワードを変更\n\n";

echo "5. データベース初期化:\n";
echo "   php init_db.php\n\n";

echo "セットアップが完了したら、ブラウザで http://localhost/IMSforPHP/ にアクセスしてください。\n";
?> 