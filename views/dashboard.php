<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">ダッシュボード</h1>
</div>

<!-- サマリーカード -->
<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">顧客数</h5>
                <p class="card-text display-6"><?= count($data['customers']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">伝票数</h5>
                <p class="card-text display-6"><?= count($data['invoices']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">注文数</h5>
                <p class="card-text display-6"><?= count($data['orders']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">入金数</h5>
                <p class="card-text display-6"><?= count($data['payments']) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- 売上サマリー -->
<?php if (isset($data['sales_summary'])): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>売上サマリー（過去30日）</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h6>総注文数</h6>
                        <p class="h4"><?= number_format($data['sales_summary']['TotalOrders'] ?? 0) ?></p>
                    </div>
                    <div class="col-md-3">
                        <h6>総売上</h6>
                        <p class="h4">¥<?= number_format($data['sales_summary']['TotalSales'] ?? 0) ?></p>
                    </div>
                    <div class="col-md-3">
                        <h6>平均注文金額</h6>
                        <p class="h4">¥<?= number_format($data['sales_summary']['AvgOrderValue'] ?? 0) ?></p>
                    </div>
                    <div class="col-md-3">
                        <h6>顧客数</h6>
                        <p class="h4"><?= number_format($data['sales_summary']['UniqueCustomers'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- 最新データ -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>最新の顧客</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>名前</th>
                                <th>メール</th>
                                <th>電話番号</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($data['customers'], 0, 5) as $customer): ?>
                            <tr>
                                <td><?= htmlspecialchars($customer->Name) ?></td>
                                <td><?= htmlspecialchars($customer->Email) ?></td>
                                <td><?= htmlspecialchars($customer->Phone) ?></td>
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
                <h5>最新の注文</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>商品名</th>
                                <th>数量</th>
                                <th>金額</th>
                                <th>日付</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($data['orders'], 0, 5) as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order->ProductName) ?></td>
                                <td><?= $order->Quantity ?></td>
                                <td>¥<?= number_format($order->TotalAmount) ?></td>
                                <td><?= $order->OrderDate->format('Y-m-d') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 