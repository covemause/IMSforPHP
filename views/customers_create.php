<div class="container mt-4">
  <h2>新規顧客登録</h2>
  <form method="post" action="?page=customers&action=create">
    <div class="mb-3">
      <label class="form-label">名前 *</label>
      <input type="text" class="form-control" name="name" required>
    </div>
    <div class="mb-3">
      <label class="form-label">メールアドレス</label>
      <input type="email" class="form-control" name="email">
    </div>
    <div class="mb-3">
      <label class="form-label">電話番号</label>
      <input type="tel" class="form-control" name="phone">
    </div>
    <div class="mb-3">
      <label class="form-label">住所</label>
      <textarea class="form-control" name="address" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">登録</button>
    <a href="?page=customers" class="btn btn-secondary">戻る</a>
  </form>
</div> 