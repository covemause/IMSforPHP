<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">売上集計</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-success" onclick="exportCSV()">
            <i class="fas fa-download"></i> CSVエクスポート
        </button>
    </div>
</div>

<!-- 期間選択フォーム -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <input type="hidden" name="page" value="sales">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">開始日</label>
                    <input type="date" class="form-control" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'))) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">終了日</label>
                    <input type="date" class="form-control" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? date('Y-m-d')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">エクスポートタイプ</label>
                    <select class="form-control" id="export_type">
                        <option value="daily">日別売上</option>
                        <option value="monthly">月別売上</option>
                        <option value="customer">顧客別売上</option>
                        <option value="product">商品別売上</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> 集計
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 売上サマリー -->
<?php if (isset($data['sales_summary'])): ?>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">総注文数</h5>
                <p class="card-text display-6"><?= number_format($data['sales_summary']['TotalOrders'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">総売上</h5>
                <p class="card-text display-6">¥<?= number_format($data['sales_summary']['TotalSales'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">平均注文金額</h5>
                <p class="card-text display-6">¥<?= number_format($data['sales_summary']['AvgOrderValue'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">顧客数</h5>
                <p class="card-text display-6"><?= number_format($data['sales_summary']['UniqueCustomers'] ?? 0) ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- グラフ表示 -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>日別売上推移</h5>
            </div>
            <div class="card-body">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>オンライン vs 店舗注文</h5>
            </div>
            <div class="card-body">
                <canvas id="onlineVsStoreChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- 詳細データ -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>顧客別売上</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>顧客名</th>
                                <th>注文数</th>
                                <th>総売上</th>
                                <th>平均注文金額</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($data['customer_sales'], 0, 10) as $customer): ?>
                            <tr>
                                <td><?= htmlspecialchars($customer['CustomerName']) ?></td>
                                <td><?= number_format($customer['OrderCount']) ?></td>
                                <td>¥<?= number_format($customer['TotalSales']) ?></td>
                                <td>¥<?= number_format($customer['AvgOrderValue']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>商品別売上</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>商品名</th>
                                <th>注文数</th>
                                <th>総数量</th>
                                <th>総売上</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($data['product_sales'], 0, 10) as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['ProductName']) ?></td>
                                <td><?= number_format($product['OrderCount']) ?></td>
                                <td><?= number_format($product['TotalQuantity']) ?></td>
                                <td>¥<?= number_format($product['TotalSales']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 日別売上グラフ
const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
new Chart(dailySalesCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($data['daily_sales'], 'Date')) ?>,
        datasets: [{
            label: '売上',
            data: <?= json_encode(array_column($data['daily_sales'], 'TotalSales')) ?>,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// オンライン vs 店舗注文グラフ
const onlineVsStoreCtx = document.getElementById('onlineVsStoreChart').getContext('2d');
new Chart(onlineVsStoreCtx, {
    type: 'doughnut',
    data: {
        labels: ['オンライン', '店舗'],
        datasets: [{
            data: [
                <?= array_sum(array_column(array_filter($data['online_vs_store'], function($item) { return $item['IsOnlineOrder'] == 1; }), 'TotalSales')) ?>,
                <?= array_sum(array_column(array_filter($data['online_vs_store'], function($item) { return $item['IsOnlineOrder'] == 0; }), 'TotalSales')) ?>
            ],
            backgroundColor: [
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true
    }
});

function exportCSV() {
    const exportType = document.getElementById('export_type').value;
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    
    window.location.href = `?page=sales&action=export&type=${exportType}&start_date=${startDate}&end_date=${endDate}`;
}
</script> 