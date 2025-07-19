<?php
session_start();

// エラーレポートを有効化（開発環境用）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 必要なファイルを読み込み
require_once 'config/SystemRequirements.php';
require_once 'config/database.php';
require_once 'controllers/PageController.php';

// システム要件チェック
$requirements = SystemRequirements::checkRequirements();
if (!empty($requirements)) {
    SystemRequirements::displayErrorPage($requirements);
    exit;
}

try {
    // データベース初期化
    $db = Database::getInstance();
    $db->initDatabase();
} catch (Exception $e) {
    $title = 'データベースエラー';
    $message = 'データベースの初期化中にエラーが発生しました：';
    $type = 'danger';
    $icon = 'database';
    $details = [htmlspecialchars($e->getMessage())];
    
    $solutions = [
        [
            'title' => 'PostgreSQL拡張機能の確認',
            'description' => 'PDO PostgreSQL拡張機能がインストールされているか確認してください。'
        ],
        [
            'title' => 'データベース接続設定の確認',
            'description' => 'config/database_config.phpファイルの設定を確認してください。'
        ],
        [
            'title' => 'ディレクトリ権限の確認',
            'description' => 'data/ディレクトリに書き込み権限があるか確認してください。'
        ]
    ];
    
    $retry_url = 'index.php';
    
    include 'views/error.php';
    exit;
}

// ページの取得
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'list';

// ページコントローラーのインスタンス化
try {
    $pageController = new PageController();
} catch (Exception $e) {
    $title = 'システムエラー';
    $message = 'システムの初期化中にエラーが発生しました：';
    $type = 'danger';
    $icon = 'exclamation-triangle';
    $details = [htmlspecialchars($e->getMessage())];
    $retry_url = 'index.php';
    
    include 'views/error.php';
    exit;
}

// データの取得
$data = [];
try {
    $data = $pageController->processPage($page, $action, $data);
} catch (Exception $e) {
    $title = 'データ取得エラー';
    $message = 'データの取得中にエラーが発生しました：';
    $type = 'warning';
    $icon = 'exclamation-triangle';
    $details = [htmlspecialchars($e->getMessage())];
    $home_url = 'index.php';
    
    include 'views/error.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在庫管理システム</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link:hover {
            color: #fff;
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- サイドバー -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>" href="?page=dashboard">
                                <i class="fas fa-tachometer-alt"></i> ダッシュボード
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'customers' ? 'active' : '' ?>" href="?page=customers">
                                <i class="fas fa-users"></i> 顧客管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'invoices' ? 'active' : '' ?>" href="?page=invoices">
                                <i class="fas fa-file-invoice"></i> 伝票管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'orders' ? 'active' : '' ?>" href="?page=orders">
                                <i class="fas fa-shopping-cart"></i> 注文管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'payments' ? 'active' : '' ?>" href="?page=payments">
                                <i class="fas fa-money-bill-wave"></i> 入金管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'sales' ? 'active' : '' ?>" href="?page=sales">
                                <i class="fas fa-chart-bar"></i> 売上集計
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- メインコンテンツ -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <?php
                    if ($page === 'customers' && $action === 'create_form') {
                        include "views/customers_create.php";
                    } else {
                        include "views/{$page}.php";
                    }
                ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html> 