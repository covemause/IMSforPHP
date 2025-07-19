<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">注文管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
            <i class="fas fa-plus"></i> 新規注文作成
        </button>
        <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#importCSVModal">
            <i class="fas fa-file-csv"></i> CSV取込
        </button>
    </div>
</div>

<!-- 検索フォーム -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <input type="hidden" name="page" value="orders">
            <input type="hidden" name="action" value="search">
            <div class="row">
                <div class="col-md-2">
                    <label class="form-label">顧客ID</label>
                    <input type="number" class="form-control" name="customer_id" value="<?= htmlspecialchars($_GET['customer_id'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">商品名</label>
                    <input type="text" class="form-control" name="product_name" value="<?= htmlspecialchars($_GET['product_name'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">開始日</label>
                    <input type="date" class="form-control" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">終了日</label>
                    <input type="date" class="form-control" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">注文タイプ</label>
                    <select class="form-control" name="is_online_order">
                        <option value="">すべて</option>
                        <option value="1" <?= $_GET['is_online_order'] === '1' ? 'selected' : '' ?>>オンライン</option>
                        <option value="0" <?= $_GET['is_online_order'] === '0' ? 'selected' : '' ?>>店舗</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i> 検索
                        </button>
                        <a href="?page=orders" class="btn btn-outline-secondary">リセット</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 注文一覧 -->
<div class="card">
    <div class="card-header">
        <h5>注文一覧</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>顧客名</th>
                        <th>商品名</th>
                        <th>数量</th>
                        <th>単価</th>
                        <th>合計金額</th>
                        <th>注文日</th>
                        <th>ステータス</th>
                        <th>タイプ</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['orders'])): ?>
                    <tr>
                        <td colspan="10" class="text-center">注文データがありません</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($data['orders'] as $order): ?>
                        <tr>
                            <td><?= $order->Id ?></td>
                            <td><?= htmlspecialchars($order->Customer ? $order->Customer->Name : 'N/A') ?></td>
                            <td><?= htmlspecialchars($order->ProductName) ?></td>
                            <td><?= $order->Quantity ?></td>
                            <td>¥<?= number_format($order->UnitPrice) ?></td>
                            <td>¥<?= number_format($order->TotalAmount) ?></td>
                            <td><?= $order->OrderDate->format('Y-m-d') ?></td>
                            <td>
                                <span class="badge bg-<?= $order->Status === 'completed' ? 'success' : ($order->Status === 'pending' ? 'warning' : 'secondary') ?>">
                                    <?= htmlspecialchars($order->Status) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $order->IsOnlineOrder ? 'info' : 'secondary' ?>">
                                    <?= $order->IsOnlineOrder ? 'オンライン' : '店舗' ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editOrder(<?= $order->Id ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteOrder(<?= $order->Id ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 新規注文作成モーダル -->
<div class="modal fade" id="addOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">新規注文作成</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addOrderForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">顧客ID *</label>
                        <input type="number" class="form-control" id="customer_id" name="customer_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_name" class="form-label">商品名 *</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">数量 *</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="unit_price" class="form-label">単価 *</label>
                        <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" required>
                    </div>
                    <div class="mb-3">
                        <label for="order_date" class="form-label">注文日 *</label>
                        <input type="date" class="form-control" id="order_date" name="order_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">ステータス *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending">保留</option>
                            <option value="processing">処理中</option>
                            <option value="completed">完了</option>
                            <option value="cancelled">キャンセル</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_online_order" name="is_online_order" value="1">
                            <label class="form-check-label" for="is_online_order">
                                オンライン注文
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">作成</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CSV取込モーダル -->
<div class="modal fade" id="importCSVModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CSVファイル取込</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="importCSVForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">CSVファイル *</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                    </div>
                    <div class="alert alert-info">
                        <h6>CSVファイル形式:</h6>
                        <p>顧客ID,商品名,数量,単価,注文日,ステータス,オンライン注文(0/1)</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-success">取込</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editOrder(id) {
    // 注文データを取得してモーダルに設定
    document.getElementById('edit_id').value = id;
    // モーダルを表示
    new bootstrap.Modal(document.getElementById('editOrderModal')).show();
}

function deleteOrder(id) {
    if (confirm('この注文を削除しますか？')) {
        // 削除処理
        window.location.href = `?page=orders&action=delete&id=${id}`;
    }
}

// フォーム送信処理
document.getElementById('addOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // フォームデータを送信
});

document.getElementById('importCSVForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // CSVファイルをアップロード
});
</script> 