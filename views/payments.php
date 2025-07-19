<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">入金管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
            <i class="fas fa-plus"></i> 新規入金記録
        </button>
    </div>
</div>

<!-- 検索フォーム -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <input type="hidden" name="page" value="payments">
            <input type="hidden" name="action" value="search">
            <div class="row">
                <div class="col-md-2">
                    <label class="form-label">伝票ID</label>
                    <input type="number" class="form-control" name="invoice_id" value="<?= htmlspecialchars($_GET['invoice_id'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">支払方法</label>
                    <select class="form-control" name="payment_method">
                        <option value="">すべて</option>
                        <option value="cash" <?= $_GET['payment_method'] === 'cash' ? 'selected' : '' ?>>現金</option>
                        <option value="credit_card" <?= $_GET['payment_method'] === 'credit_card' ? 'selected' : '' ?>>クレジットカード</option>
                        <option value="bank_transfer" <?= $_GET['payment_method'] === 'bank_transfer' ? 'selected' : '' ?>>銀行振込</option>
                        <option value="online" <?= $_GET['payment_method'] === 'online' ? 'selected' : '' ?>>オンライン決済</option>
                    </select>
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
                    <label class="form-label">最小金額</label>
                    <input type="number" class="form-control" name="min_amount" value="<?= htmlspecialchars($_GET['min_amount'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">最大金額</label>
                    <input type="number" class="form-control" name="max_amount" value="<?= htmlspecialchars($_GET['max_amount'] ?? '') ?>">
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i> 検索
                    </button>
                    <a href="?page=payments" class="btn btn-outline-secondary">リセット</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 入金一覧 -->
<div class="card">
    <div class="card-header">
        <h5>入金一覧</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>伝票ID</th>
                        <th>顧客名</th>
                        <th>金額</th>
                        <th>入金日</th>
                        <th>支払方法</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['payments'])): ?>
                    <tr>
                        <td colspan="8" class="text-center">入金データがありません</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($data['payments'] as $payment): ?>
                        <tr>
                            <td><?= $payment->Id ?></td>
                            <td><?= $payment->InvoiceId ?></td>
                            <td><?= htmlspecialchars($payment->Invoice ? 'Customer' : 'N/A') ?></td>
                            <td>¥<?= number_format($payment->Amount) ?></td>
                            <td><?= $payment->PaymentDate->format('Y-m-d') ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?= htmlspecialchars($payment->PaymentMethod) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $payment->Status === 'completed' ? 'success' : ($payment->Status === 'pending' ? 'warning' : 'secondary') ?>">
                                    <?= htmlspecialchars($payment->Status) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editPayment(<?= $payment->Id ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deletePayment(<?= $payment->Id ?>)">
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

<!-- 新規入金記録モーダル -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">新規入金記録</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPaymentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="invoice_id" class="form-label">伝票ID *</label>
                        <input type="number" class="form-control" id="invoice_id" name="invoice_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">金額 *</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">入金日 *</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">支払方法 *</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="cash">現金</option>
                            <option value="credit_card">クレジットカード</option>
                            <option value="bank_transfer">銀行振込</option>
                            <option value="online">オンライン決済</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">ステータス *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending">保留</option>
                            <option value="completed">完了</option>
                            <option value="failed">失敗</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">記録</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 入金編集モーダル -->
<div class="modal fade" id="editPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">入金編集</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPaymentForm">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_invoice_id" class="form-label">伝票ID *</label>
                        <input type="number" class="form-control" id="edit_invoice_id" name="invoice_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_amount" class="form-label">金額 *</label>
                        <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_payment_date" class="form-label">入金日 *</label>
                        <input type="date" class="form-control" id="edit_payment_date" name="payment_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_payment_method" class="form-label">支払方法 *</label>
                        <select class="form-control" id="edit_payment_method" name="payment_method" required>
                            <option value="cash">現金</option>
                            <option value="credit_card">クレジットカード</option>
                            <option value="bank_transfer">銀行振込</option>
                            <option value="online">オンライン決済</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">ステータス *</label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="pending">保留</option>
                            <option value="completed">完了</option>
                            <option value="failed">失敗</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">更新</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPayment(id) {
    // 入金データを取得してモーダルに設定
    document.getElementById('edit_id').value = id;
    // モーダルを表示
    new bootstrap.Modal(document.getElementById('editPaymentModal')).show();
}

function deletePayment(id) {
    if (confirm('この入金記録を削除しますか？')) {
        // 削除処理
        window.location.href = `?page=payments&action=delete&id=${id}`;
    }
}

// フォーム送信処理
document.getElementById('addPaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // フォームデータを送信
});

document.getElementById('editPaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // フォームデータを送信
});
</script> 