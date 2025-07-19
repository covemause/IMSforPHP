# 在庫管理システム 仕様書

## 1. システム概要

### 1.1 システム名
在庫管理システム (Inventory Management System)

### 1.2 開発技術
- **フレームワーク**: WPF (.NET)
- **データベース**: SQLite
- **データアクセス**: DAO パターン (System.Data.SQLite)
- **言語**: C# 8.0+

### 1.3 アーキテクチャ
```
InventoryManagementSystem/
├── DataBase/
│   ├── Database.cs          # データベース接続管理
│   ├── Model/              # データモデル
│   │   ├── Customer.cs
│   │   ├── Invoice.cs
│   │   ├── Order.cs
│   │   └── Payment.cs
│   └── DAO/                # データアクセス層
│       ├── CustomerDao.cs
│       ├── InvoiceDao.cs
│       ├── OrderDao.cs
│       └── PaymentDao.cs
├── Controller/              # ビジネスロジック層
│   ├── CustomerController.cs
│   ├── InvoiceController.cs
│   ├── OrderController.cs
│   └── PaymentController.cs
├── View/                    # UI層
│   ├── CustomerView.xaml
│   ├── InvoiceView.xaml
│   ├── OrderView.xaml
│   ├── PaymentView.xaml
│   └── SalesSummaryView.xaml
└── MainWindow.xaml          # メインウィンドウ
```

## 2. 機能仕様

### 2.1 顧客管理機能

#### 2.1.1 基本情報
- **機能名**: 顧客管理
- **概要**: 顧客情報の登録、編集、削除、検索機能
- **CPM分析**: 顧客の購買パターン分析機能付き

#### 2.1.2 データモデル (Customer)
```csharp
public class Customer
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
    public string Phone { get; set; } = string.Empty;
    public string Address { get; set; } = string.Empty;
    public DateTime CreatedDate { get; set; }
    public string CreatedBy { get; set; } = string.Empty;
    public DateTime UpdatedDate { get; set; }
    public string UpdatedBy { get; set; } = string.Empty;
}
```

#### 2.1.3 主要機能
- 顧客登録
- 顧客情報編集
- 顧客削除
- 顧客検索（名前、メール、電話番号）
- CPM分析（顧客購買パターン分析）

### 2.2 伝票管理機能

#### 2.2.1 基本情報
- **機能名**: 伝票管理
- **概要**: 売上伝票の作成、編集、削除、検索機能

#### 2.2.2 データモデル (Invoice)
```csharp
public class Invoice
{
    public int Id { get; set; }
    public int CustomerId { get; set; }
    public decimal Amount { get; set; }
    public DateTime InvoiceDate { get; set; }
    public string Status { get; set; } = string.Empty;
    public DateTime CreatedDate { get; set; }
    public string CreatedBy { get; set; } = string.Empty;
    public DateTime UpdatedDate { get; set; }
    public string UpdatedBy { get; set; } = string.Empty;
}
```

#### 2.2.3 主要機能
- 伝票作成
- 伝票編集
- 伝票削除
- 伝票検索（顧客、日付、金額範囲）

### 2.3 注文管理機能

#### 2.3.1 基本情報
- **機能名**: 注文管理
- **概要**: 注文の処理、履歴管理、オンライン受注管理

#### 2.3.2 データモデル (Order)
```csharp
public class Order
{
    public int Id { get; set; }
    public int CustomerId { get; set; }
    public string ProductName { get; set; } = string.Empty;
    public int Quantity { get; set; }
    public decimal UnitPrice { get; set; }
    public decimal TotalAmount { get; set; }
    public DateTime OrderDate { get; set; }
    public string Status { get; set; } = string.Empty;
    public bool IsOnlineOrder { get; set; }
    public DateTime CreatedDate { get; set; }
    public string CreatedBy { get; set; } = string.Empty;
    public DateTime UpdatedDate { get; set; }
    public string UpdatedBy { get; set; } = string.Empty;
}
```

#### 2.3.3 主要機能
- 注文作成
- 注文編集
- 注文削除
- 注文履歴検索
- オンライン受注管理
- CSVファイルからの注文データ取込

### 2.4 入金管理機能

#### 2.4.1 基本情報
- **機能名**: 入金管理
- **概要**: 入金情報の記録、管理、検索機能

#### 2.4.2 データモデル (Payment)
```csharp
public class Payment
{
    public int Id { get; set; }
    public int InvoiceId { get; set; }
    public decimal Amount { get; set; }
    public DateTime PaymentDate { get; set; }
    public string PaymentMethod { get; set; } = string.Empty;
    public string Status { get; set; } = string.Empty;
    public DateTime CreatedDate { get; set; }
    public string CreatedBy { get; set; } = string.Empty;
    public DateTime UpdatedDate { get; set; }
    public string UpdatedBy { get; set; } = string.Empty;
}
```

#### 2.4.3 主要機能
- 入金記録
- 入金編集
- 入金削除
- 入金検索（日付、金額、方法）

### 2.5 売上集計機能

#### 2.5.1 基本情報
- **機能名**: 売上集計
- **概要**: 売上データの集計、分析機能

#### 2.5.2 集計項目
- 日別売上集計
- 月別売上集計
- 顧客別売上集計
- 商品別売上集計

#### 2.5.3 主要機能
- 期間指定による売上集計
- グラフ表示
- エクスポート機能

## 3. データベース設計

### 3.1 テーブル構造

#### 3.1.1 Customers テーブル
```sql
CREATE TABLE Customers (
    Id INTEGER PRIMARY KEY AUTOINCREMENT,
    Name TEXT NOT NULL,
    Email TEXT,
    Phone TEXT,
    Address TEXT,
    CreatedDate DATETIME NOT NULL,
    CreatedBy TEXT NOT NULL,
    UpdatedDate DATETIME NOT NULL,
    UpdatedBy TEXT NOT NULL
);
```

#### 3.1.2 Invoices テーブル
```sql
CREATE TABLE Invoices (
    Id INTEGER PRIMARY KEY AUTOINCREMENT,
    CustomerId INTEGER NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    InvoiceDate DATETIME NOT NULL,
    Status TEXT NOT NULL,
    CreatedDate DATETIME NOT NULL,
    CreatedBy TEXT NOT NULL,
    UpdatedDate DATETIME NOT NULL,
    UpdatedBy TEXT NOT NULL,
    FOREIGN KEY (CustomerId) REFERENCES Customers(Id)
);
```

#### 3.1.3 Orders テーブル
```sql
CREATE TABLE Orders (
    Id INTEGER PRIMARY KEY AUTOINCREMENT,
    CustomerId INTEGER NOT NULL,
    ProductName TEXT NOT NULL,
    Quantity INTEGER NOT NULL,
    UnitPrice DECIMAL(10,2) NOT NULL,
    TotalAmount DECIMAL(10,2) NOT NULL,
    OrderDate DATETIME NOT NULL,
    Status TEXT NOT NULL,
    IsOnlineOrder BOOLEAN NOT NULL DEFAULT 0,
    CreatedDate DATETIME NOT NULL,
    CreatedBy TEXT NOT NULL,
    UpdatedDate DATETIME NOT NULL,
    UpdatedBy TEXT NOT NULL,
    FOREIGN KEY (CustomerId) REFERENCES Customers(Id)
);
```

#### 3.1.4 Payments テーブル
```sql
CREATE TABLE Payments (
    Id INTEGER PRIMARY KEY AUTOINCREMENT,
    InvoiceId INTEGER NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    PaymentDate DATETIME NOT NULL,
    PaymentMethod TEXT NOT NULL,
    Status TEXT NOT NULL,
    CreatedDate DATETIME NOT NULL,
    CreatedBy TEXT NOT NULL,
    UpdatedDate DATETIME NOT NULL,
    UpdatedBy TEXT NOT NULL,
    FOREIGN KEY (InvoiceId) REFERENCES Invoices(Id)
);
```

## 4. UI仕様

### 4.1 メインウィンドウ
- 各機能へのナビゲーションボタン
- レスポンシブデザイン

### 4.2 共通UI要素
- 検索機能（テキストボックス + 検索ボタン）
- データグリッド表示
- 追加/編集/削除ボタン
- 日付選択コントロール

### 4.3 各画面の機能
- **顧客管理画面**: 顧客一覧、検索、CRUD操作
- **伝票管理画面**: 伝票一覧、検索、CRUD操作
- **注文管理画面**: 注文一覧、検索、CRUD操作、CSV取込
- **入金管理画面**: 入金一覧、検索、CRUD操作
- **売上集計画面**: 集計結果表示、期間選択、グラフ表示

## 5. 技術仕様

### 5.1 開発環境
- Visual Studio 2022
- .NET 6.0 以降
- SQLite 3.x

### 5.2 依存関係
```xml
<PackageReference Include="System.Data.SQLite" Version="1.0.118" />
```

### 5.3 コーディング規約
- C# 8.0+ のnullability機能を活用
- 文字列プロパティは空文字列で初期化
- DAOパターンによるデータアクセス層の分離
- MVVMライクな構造（Model-View-Controller）

## 6. セキュリティ仕様

### 6.1 データ保護
- SQLiteデータベースファイルの適切な配置
- 入力値の検証
- SQLインジェクション対策（パラメータ化クエリ）

### 6.2 監査ログ
- 作成日時、作成者
- 更新日時、更新者
- 削除操作の記録

## 7. パフォーマンス仕様

### 7.1 データベース
- インデックスの適切な設定
- クエリの最適化
- 接続プールの活用

### 7.2 UI応答性
- 非同期処理の活用
- 大量データのページング処理
- 検索結果の制限

## 8. 運用仕様

### 8.1 バックアップ
- SQLiteデータベースファイルの定期バックアップ
- 設定ファイルのバックアップ

### 8.2 ログ管理
- エラーログの記録
- 操作ログの記録
- ログローテーション

## 9. 今後の拡張予定

### 9.1 未実装機能
- CTI連携機能
- ヤマト店頭受付システムデータ取込機能

### 9.2 改善案
- レポート機能の強化
- データエクスポート機能
- バッチ処理機能
- マルチユーザー対応

## 10. トラブルシューティング

### 10.1 よくある問題
- データベース接続エラー
- ファイルアクセス権限エラー
- メモリ不足エラー

### 10.2 対処法
- データベースファイルの存在確認
- 実行権限の確認
- システムリソースの確認

---

**作成日**: 2024年12月
**バージョン**: 1.0
**作成者**: 開発チーム 