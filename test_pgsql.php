<?php
require_once __DIR__ . '/models/Customer.php';

// 登録APIにPOSTリクエスト
$url = 'http://localhost/index.php?page=customers&action=create';

$postData = [
    'name' => 'テスト太郎',
    'email' => 'test@example.com',
    'phone' => '090-1234-5678',
    'address' => '東京都テスト区1-2-3',
];

$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query($postData),
        'ignore_errors' => true,
    ]
];
$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "APIへのリクエストに失敗しました。\n";
    exit(1);
}

// レスポンス内容を表示
if (isset($http_response_header)) {
    echo "APIレスポンスヘッダ:\n";
    foreach ($http_response_header as $header) {
        echo $header . "\n";
    }
}
echo "\nAPIレスポンス本文:\n";
echo $response . "\n";

// DBから最新の顧客情報を表示
$config = require __DIR__ . '/config/database_config.php';
$host = $config['host'];
$port = $config['port'];
$dbname = $config['dbname'];
$username = $config['username'];
$password = $config['password'];

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "PostgreSQLへの接続に失敗しました: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    $sql = "SELECT * FROM Customers ORDER BY Id DESC LIMIT 1";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $customer = new Customer();
        $customer->fromArray($row);
        echo "\n最新の顧客情報 (Customerモデル):\n";
        echo "  Id: {$customer->Id}\n";
        echo "  Name: {$customer->Name}\n";
        echo "  Email: {$customer->Email}\n";
        echo "  Phone: {$customer->Phone}\n";
        echo "  Address: {$customer->Address}\n";
        echo "  CreatedDate: " . $customer->CreatedDate->format('Y-m-d H:i:s') . "\n";
        echo "  CreatedBy: {$customer->CreatedBy}\n";
        echo "  UpdatedDate: " . $customer->UpdatedDate->format('Y-m-d H:i:s') . "\n";
        echo "  UpdatedBy: {$customer->UpdatedBy}\n";
    } else {
        echo "\n顧客情報が見つかりませんでした。\n";
    }
} catch (PDOException $e) {
    echo "顧客情報の取得中にエラー: " . $e->getMessage() . "\n";
    exit(1);
} 