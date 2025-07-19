<!-- 成功メッセージ -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        switch ($_GET['success']) {
            case '1':
                echo '顧客が正常に登録されました。';
                break;
            case '2':
                echo '顧客情報が正常に更新されました。';
                break;
            case '3':
                echo '顧客が正常に削除されました。';
                break;
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- エラーメッセージ -->
<?php if (isset($data['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($data['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">顧客管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
            <i class="fas fa-plus"></i> 新規顧客登録
        </button>
    </div>
</div>

<!-- 検索フォーム -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <input type="hidden" name="page" value="customers">
            <input type="hidden" name="action" value="search">
            <div class="row">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="keyword" placeholder="名前、メール、電話番号で検索..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i> 検索
                    </button>
                    <a href="?page=customers" class="btn btn-outline-secondary">リセット</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 顧客一覧 -->
<div class="card">
    <div class="card-header">
        <h5>顧客一覧</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>名前</th>
                        <th>メール</th>
                        <th>電話番号</th>
                        <th>住所</th>
                        <th>登録日</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['customers'])): ?>
                    <tr>
                        <td colspan="7" class="text-center">顧客データがありません</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($data['customers'] as $customer): ?>
                        <tr>
                            <td><?= $customer->Id ?></td>
                            <td><?= htmlspecialchars($customer->Name) ?></td>
                            <td><?= htmlspecialchars($customer->Email) ?></td>
                            <td><?= htmlspecialchars($customer->Phone) ?></td>
                            <td><?= htmlspecialchars($customer->Address) ?></td>
                            <td><?= $customer->CreatedDate->format('Y-m-d') ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editCustomer(<?= $customer->Id ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCustomer(<?= $customer->Id ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="analyzeCPM(<?= $customer->Id ?>)">
                                    <i class="fas fa-chart-line"></i> CPM
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

<?php
$totalPages = isset($data['total_customers'], $data['per_page']) ? ceil($data['total_customers'] / $data['per_page']) : 1;
$current = $data['current_page'] ?? 1;
if ($totalPages > 1): ?>
<nav>
  <ul class="pagination justify-content-center">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <li class="page-item <?= $i === $current ? 'active' : '' ?>">
        <a class="page-link" href="?page=customers&p=<?= $i ?>"> <?= $i ?> </a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>

<!-- 新規顧客登録モーダル -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">新規顧客登録</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCustomerForm" method="post" action="index.php?page=customers&action=create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">名前 *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">メールアドレス</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">電話番号</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">住所</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">登録</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 顧客編集モーダル -->
<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">顧客情報編集</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCustomerForm">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">名前 *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">メールアドレス</label>
                        <input type="email" class="form-control" id="edit_email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">電話番号</label>
                        <input type="tel" class="form-control" id="edit_phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">住所</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="3"></textarea>
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

<!-- 更新中モーダル -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-body">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <div>登録処理中です。しばらくお待ちください...</div>
      </div>
    </div>
  </div>
</div>

<!-- 更新確認モーダル -->
<div class="modal fade" id="confirmUpdateModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">更新内容の確認</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>以下の内容で更新します。よろしいですか？</p>
        <ul>
          <li>名前: <span id="confirm_name"></span></li>
          <li>メール: <span id="confirm_email"></span></li>
          <li>電話番号: <span id="confirm_phone"></span></li>
          <li>住所: <span id="confirm_address"></span></li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
        <button type="button" class="btn btn-primary" id="confirmUpdateBtn">OK</button>
      </div>
    </div>
  </div>
</div>

<script>
function editCustomer(id) {
    // 顧客データを取得してモーダルに設定
    fetch(`index.php?page=customers&action=get&id=${id}`)
        .then(response => response.json())
        .then(customer => {
            document.getElementById('edit_id').value = customer.Id;
            document.getElementById('edit_name').value = customer.Name;
            document.getElementById('edit_email').value = customer.Email;
            document.getElementById('edit_phone').value = customer.Phone;
            document.getElementById('edit_address').value = customer.Address;
            
            // モーダルを表示
            new bootstrap.Modal(document.getElementById('editCustomerModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('顧客データの取得に失敗しました。');
        });
}

function deleteCustomer(id) {
    if (confirm('この顧客を削除しますか？')) {
        // 削除処理
        window.location.href = `?page=customers&action=delete&id=${id}`;
    }
}

function analyzeCPM(id) {
    // CPM分析ページに遷移
    window.location.href = `?page=customers&action=cpm&id=${id}`;
}

// フォーム送信処理
document.getElementById('addCustomerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('page', 'customers');
    formData.append('action', 'create');


    // FormDataの中身を確認（デバッグ用）
    console.log('fetch送信前: method=POST, url=index.php');
for (let [key, value] of formData.entries()) {
    console.log(key, value);
}

    fetch('index.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }
        return response.text();
    })
    .then(data => {
        if (data && data.includes('顧客の登録に失敗しました')) {
            alert('登録に失敗しました');
        }
    })
    .catch(() => {
        alert('通信エラーが発生しました');
    });
});

document.getElementById('editCustomerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // 入力値を取得
    const name = document.getElementById('edit_name').value;
    const email = document.getElementById('edit_email').value;
    const phone = document.getElementById('edit_phone').value;
    const address = document.getElementById('edit_address').value;

    // 確認モーダルに値をセット
    document.getElementById('confirm_name').textContent = name;
    document.getElementById('confirm_email').textContent = email;
    document.getElementById('confirm_phone').textContent = phone;
    document.getElementById('confirm_address').textContent = address;

    // モーダル表示
    var confirmModal = new bootstrap.Modal(document.getElementById('confirmUpdateModal'));
    confirmModal.show();

    // OKボタン押下時の処理
    document.getElementById('confirmUpdateBtn').onclick = function() {
        // フォーカスを外す
        this.blur();

        confirmModal.hide();

        // 実際の更新リクエスト
        const formData = new FormData(document.getElementById('editCustomerForm'));
        formData.append('page', 'customers');
        formData.append('action', 'update');

        fetch('index.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            return response.text();
        })
        .then(data => {
            if (data && data.includes('顧客の更新に失敗しました')) {
                alert('更新に失敗しました');
            }
        })
        .catch(() => {
            alert('通信エラーが発生しました');
        });
    };
});
</script> 