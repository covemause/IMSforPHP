# 在庫管理システム (PHP版)

## 概要

このプロジェクトは、SPECIFICATION.mdに基づいてPHPで実装された在庫管理システムです。顧客管理、伝票管理、注文管理、入金管理、売上集計の機能を提供します。

## 機能

### 1. 顧客管理機能
- 顧客の登録、編集、削除、検索
- CPM分析（顧客購買パターン分析）

### 2. 伝票管理機能
- 売上伝票の作成、編集、削除、検索
- 顧客情報との連携

### 3. 注文管理機能
- 注文の処理、履歴管理
- オンライン受注管理
- CSVファイルからの注文データ取込

### 4. 入金管理機能
- 入金情報の記録、管理、検索
- 伝票との連携

### 5. 売上集計機能
- 日別・月別売上集計
- 顧客別・商品別売上集計
- グラフ表示
- CSVエクスポート機能

## 技術仕様

### 開発環境
- **PHP**: 8.0+
- **データベース**: PostgreSQL 12+
- **フロントエンド**: HTML5, CSS3, JavaScript
- **UIフレームワーク**: Bootstrap 5.1.3
- **グラフライブラリ**: Chart.js

### アーキテクチャ
```
IMSforPHP/
├── config/
│   └── database.php          # データベース接続設定
├── models/                   # データモデル
│   ├── Customer.php
│   ├── Invoice.php
│   ├── Order.php
│   └── Payment.php
├── controllers/              # ビジネスロジック層
│   ├── CustomerController.php
│   ├── InvoiceController.php
│   ├── OrderController.php
│   ├── PaymentController.php
│   └── SalesController.php
├── views/                    # UI層
│   ├── dashboard.php
│   ├── customers.php
│   ├── invoices.php
│   ├── orders.php
│   ├── payments.php
│   └── sales.php
├── database/
│   └── ims.db               # SQLiteデータベース
└── index.php                 # メインエントリーポイント
```

## セットアップ

### 1. 必要な環境
- PHP 8.0以上
- PostgreSQL 12以上
- PHP PostgreSQL拡張機能（pdo_pgsql）
- Webサーバー（Apache/Nginx）

### 2. インストール手順

1. プロジェクトをクローンまたはダウンロード
```bash
git clone [repository-url]
cd IMSforPHP
```

2. PostgreSQLをインストールし、データベースを作成
   ```sql
   CREATE DATABASE ims_db;
   ```

3. 設定ファイルを確認・編集
   - `config/database_config.php` の接続情報を確認

4. データベース初期化
   ```bash
   php init_db.php
   ```

5. Webサーバーのドキュメントルートに配置

6. ブラウザでアクセス
   ```
   http://localhost/IMSforPHP/
   ```

## 使用方法

### ダッシュボード
- システム全体の概要を表示
- 各機能へのクイックアクセス

### 顧客管理
- 顧客情報の登録・編集・削除
- 名前、メール、電話番号での検索
- CPM分析による購買パターン分析

### 伝票管理
- 売上伝票の作成・編集・削除
- 顧客、日付、金額範囲での検索

### 注文管理
- 注文の作成・編集・削除
- オンライン/店舗注文の区別
- CSVファイルからの一括取込

### 入金管理
- 入金記録の作成・編集・削除
- 支払方法、日付、金額での検索

### 売上集計
- 期間指定による売上分析
- グラフによる視覚化
- CSV形式でのエクスポート

## データベース設計

### テーブル構造
- **Customers**: 顧客情報
- **Invoices**: 伝票情報
- **Orders**: 注文情報
- **Payments**: 入金情報

詳細なテーブル構造は`config/database.php`の`initDatabase()`メソッドを参照してください。

### データベース設定
PostgreSQL接続設定は`config/database_config.php`で管理されています。
本番環境では適切な接続情報に変更してください。

## セキュリティ

- SQLインジェクション対策（プリペアドステートメント使用）
- XSS対策（htmlspecialchars使用）
- 入力値の検証

## 今後の拡張予定

- CTI連携機能
- ヤマト店頭受付システムデータ取込機能
- レポート機能の強化
- バッチ処理機能
- マルチユーザー対応

## ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## 作成者

開発チーム