// デモ用JavaScript

// ページ読み込み時の初期化
document.addEventListener('DOMContentLoaded', function() {
    // サイドバーのアクティブ状態を設定
    setActiveNavLink();
    
    // アニメーション効果を追加
    addFadeInAnimation();
    
    // モーダルの初期化
    initializeModals();
    
    // フォームの初期化
    initializeForms();
});

// サイドバーのアクティブ状態を設定
function setActiveNavLink() {
    const currentPage = window.location.pathname.split('/').pop().replace('.html', '');
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href').includes(currentPage)) {
            link.classList.add('active');
        }
    });
}

// フェードインアニメーションを追加
function addFadeInAnimation() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in');
    });
}

// モーダルの初期化
function initializeModals() {
    // モーダルを開く
    const modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const target = this.getAttribute('data-bs-target');
            const modal = new bootstrap.Modal(document.querySelector(target));
            modal.show();
        });
    });
}

// フォームの初期化
function initializeForms() {
    // フォーム送信時の処理
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            showSuccessMessage('データが正常に保存されました。');
        });
    });
}

// 成功メッセージを表示
function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.main-content');
    container.insertBefore(alertDiv, container.firstChild);
    
    // 3秒後に自動で消す
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// エラーメッセージを表示
function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.main-content');
    container.insertBefore(alertDiv, container.firstChild);
}

// 確認ダイアログを表示
function showConfirmDialog(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// データの編集
function editItem(id, type) {
    console.log(`編集: ${type} ID ${id}`);
    // 実際の実装ではモーダルを開いてデータを設定
    showSuccessMessage(`${type}の編集を開始しました。`);
}

// データの削除
function deleteItem(id, type) {
    showConfirmDialog(`この${type}を削除しますか？`, function() {
        console.log(`削除: ${type} ID ${id}`);
        showSuccessMessage(`${type}が削除されました。`);
    });
}

// 検索機能
function performSearch() {
    const searchInput = document.querySelector('input[name="keyword"]');
    if (searchInput && searchInput.value.trim()) {
        showSuccessMessage(`検索結果を表示しています: "${searchInput.value}"`);
    }
}

// CSVエクスポート
function exportCSV() {
    showSuccessMessage('CSVファイルのエクスポートを開始しました。');
}

// グラフの初期化
function initializeCharts() {
    // Chart.jsが読み込まれている場合のみ実行
    if (typeof Chart !== 'undefined') {
        // 日別売上グラフ
        const dailySalesCtx = document.getElementById('dailySalesChart');
        if (dailySalesCtx) {
            new Chart(dailySalesCtx, {
                type: 'line',
                data: {
                    labels: ['1月', '2月', '3月', '4月', '5月', '6月'],
                    datasets: [{
                        label: '売上',
                        data: [1200000, 1500000, 1800000, 1600000, 2000000, 2200000],
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
        }
        
        // オンライン vs 店舗注文グラフ
        const onlineVsStoreCtx = document.getElementById('onlineVsStoreChart');
        if (onlineVsStoreCtx) {
            new Chart(onlineVsStoreCtx, {
                type: 'doughnut',
                data: {
                    labels: ['オンライン', '店舗'],
                    datasets: [{
                        data: [65, 35],
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
        }
    }
}

// ページ固有の初期化
function initializePageSpecific() {
    const currentPage = window.location.pathname.split('/').pop();
    
    switch (currentPage) {
        case 'sales.html':
            initializeCharts();
            break;
        case 'customers.html':
            // 顧客管理ページ固有の処理
            break;
        case 'orders.html':
            // 注文管理ページ固有の処理
            break;
    }
}

// 初期化を実行
document.addEventListener('DOMContentLoaded', function() {
    initializePageSpecific();
}); 