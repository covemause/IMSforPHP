<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">伝票管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
            <i class="fas fa-plus"></i> 新規伝票作成
        </button>
    </div>
</div>

<!-- 検索フォーム -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <input type="hidden" name="page" value="invoices">
            <input type="hidden" name="action" value="search">
            <div class="row">
                <div class="col-md-2">
                    <label class="form-label">顧客ID</label>
                    <input type="number" class="form-control" name="customer_id" value="<?= htmlspecialchars($_GET['customer_id'] ?? '') ?>">
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
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i> 検索
                        </button>
                        <a href="?page=invoices" class="btn btn-outline-secondary">リセット</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 伝票一覧 -->
<div class="card">
    <div class="card-header">
        <h5>伝票一覧</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>顧客名</th>
                        <th>金額</th>
                        <th>伝票日</th>
                        <th>ステータス</th>
                        <th>作成日</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['invoices'])): ?>
                    <tr>
                        <td colspan="7" class="text-center">伝票データがありません</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($data['invoices'] as $invoice): ?>
                        <tr>
                            <td><?= $invoice->Id ?></td>
                            <td><?= htmlspecialchars($invoice->Customer ? $invoice->Customer->Name : 'N/A') ?></td>
                            <td>¥<?= number_format($invoice->Amount) ?></td>
                            <td><?= $invoice->InvoiceDate->format('Y-m-d') ?></td>
                            <td>
                                <span class="badge bg-<?= $invoice->Status === 'paid' ? 'success' : ($invoice->Status === 'pending' ? 'warning' : 'secondary') ?>">
                                    <?= htmlspecialchars($invoice->Status) ?>
                                </span>
                            </td>
                            <td><?= $invoice->CreatedDate->format('Y-m-d') ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editInvoice(<?= $invoice->Id ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteInvoice(<?= $invoice->Id ?>)">
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

<!-- 新規伝票作成モーダル -->
<div class="modal fade" id="addInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">新規伝票作成</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addInvoiceForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">顧客ID *</label>
                        <input type="number" class="form-control" id="customer_id" name="customer_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">金額 *</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="invoice_date" class="form-label">伝票日 *</label>
                        <input type="date" class="form-control" id="invoice_date" name="invoice_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">ステータス *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending">保留</option>
                            <option value="paid">支払済</option>
                            <option value="cancelled">キャンセル</option>
                        </select>
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

<!-- 伝票編集モーダル -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">伝票編集</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editInvoiceForm">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_customer_id" class="form-label">顧客ID *</label>
                        <input type="number" class="form-control" id="edit_customer_id" name="customer_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_amount" class="form-label">金額 *</label>
                        <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_invoice_date" class="form-label">伝票日 *</label>
                        <input type="date" class="form-control" id="edit_invoice_date" name="invoice_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">ステータス *</label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="pending">保留</option>
                            <option value="paid">支払済</option>
                            <option value="cancelled">キャンセル</option>
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
function editInvoice(id) {
    // 伝票データを取得してモーダルに設定
    document.getElementById('edit_id').value = id;
    // モーダルを表示
    new bootstrap.Modal(document.getElementById('editInvoiceModal')).show();
}

function deleteInvoice(id) {
    if (confirm('この伝票を削除しますか？')) {
        // 削除処理
        window.location.href = `?page=invoices&action=delete&id=${id}`;
    }
}

// フォーム送信処理
document.getElementById('addInvoiceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // フォームデータを送信
});

document.getElementById('editInvoiceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // フォームデータを送信
});
</script> 