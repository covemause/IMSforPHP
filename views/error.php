<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'エラー') ?> - 在庫管理システム</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .error-card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .solution-section {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-top: 1rem;
        }
        .code-block {
            background-color: #e9ecef;
            border-radius: 0.25rem;
            padding: 0.5rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card error-card border-<?= $type ?? 'danger' ?>">
                        <div class="card-header bg-<?= $type ?? 'danger' ?> text-white">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-<?= $icon ?? 'exclamation-triangle' ?> me-2"></i>
                                <h4 class="mb-0"><?= htmlspecialchars($title ?? 'エラー') ?></h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($message)): ?>
                                <p class="lead"><?= htmlspecialchars($message) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($details)): ?>
                                <div class="alert alert-<?= $type ?? 'danger' ?>">
                                    <h6>詳細：</h6>
                                    <ul class="mb-0">
                                        <?php foreach ($details as $detail): ?>
                                            <li><?= htmlspecialchars($detail) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($solutions)): ?>
                                <div class="solution-section">
                                    <h6><i class="fas fa-tools me-2"></i>解決方法：</h6>
                                    <?php foreach ($solutions as $solution): ?>
                                        <div class="mb-3">
                                            <strong><?= htmlspecialchars($solution['title']) ?></strong>
                                            <?php if (!empty($solution['description'])): ?>
                                                <p class="mb-2"><?= htmlspecialchars($solution['description']) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($solution['commands'])): ?>
                                                <?php foreach ($solution['commands'] as $command): ?>
                                                    <div class="code-block mb-1"><?= htmlspecialchars($command) ?></div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($additional_info)): ?>
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>追加情報：</h6>
                                    <?= $additional_info ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <?php if (!empty($back_url)): ?>
                                    <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>戻る
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($home_url)): ?>
                                    <a href="<?= htmlspecialchars($home_url) ?>" class="btn btn-primary">
                                        <i class="fas fa-home me-2"></i>ホームに戻る
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($retry_url)): ?>
                                    <a href="<?= htmlspecialchars($retry_url) ?>" class="btn btn-warning">
                                        <i class="fas fa-redo me-2"></i>再試行
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 