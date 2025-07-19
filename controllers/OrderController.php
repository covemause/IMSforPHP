<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Customer.php';

/**
 * 注文コントローラークラス
 */
class OrderController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 全注文を取得（顧客情報付き）
     */
    public function getAllOrders(): array {
        $sql = "
            SELECT o.*, c.Name as CustomerName 
            FROM Orders o
            LEFT JOIN Customers c ON o.CustomerId = c.Id
            ORDER BY o.OrderDate DESC
        ";
        
        $stmt = $this->db->query($sql);
        $orders = [];
        while ($row = $stmt->fetch()) {
            $order = new Order();
            $order->fromArray($row);
            
            if ($row['CustomerName']) {
                $customer = new Customer();
                $customer->Id = $order->CustomerId;
                $customer->Name = $row['CustomerName'];
                $order->Customer = $customer;
            }
            
            $orders[] = $order;
        }
        return $orders;
    }
    
    /**
     * IDで注文を取得
     */
    public function getOrderById(int $id): ?Order {
        $sql = "
            SELECT o.*, c.Name as CustomerName 
            FROM Orders o
            LEFT JOIN Customers c ON o.CustomerId = c.Id
            WHERE o.Id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            $order = new Order();
            $order->fromArray($row);
            
            if ($row['CustomerName']) {
                $customer = new Customer();
                $customer->Id = $order->CustomerId;
                $customer->Name = $row['CustomerName'];
                $order->Customer = $customer;
            }
            
            return $order;
        }
        return null;
    }
    
    /**
     * 注文を検索
     */
    public function searchOrders(array $criteria): array {
        $where = [];
        $params = [];
        
        if (!empty($criteria['customer_id'])) {
            $where[] = "o.CustomerId = ?";
            $params[] = $criteria['customer_id'];
        }
        
        if (!empty($criteria['product_name'])) {
            $where[] = "o.ProductName LIKE ?";
            $params[] = "%{$criteria['product_name']}%";
        }
        
        if (!empty($criteria['start_date'])) {
            $where[] = "o.OrderDate >= ?";
            $params[] = $criteria['start_date'];
        }
        
        if (!empty($criteria['end_date'])) {
            $where[] = "o.OrderDate <= ?";
            $params[] = $criteria['end_date'];
        }
        
        if (isset($criteria['is_online_order'])) {
            $where[] = "o.IsOnlineOrder = ?";
            $params[] = $criteria['is_online_order'] ? 1 : 0;
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $sql = "
            SELECT o.*, c.Name as CustomerName 
            FROM Orders o
            LEFT JOIN Customers c ON o.CustomerId = c.Id
            $whereClause
            ORDER BY o.OrderDate DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $orders = [];
        while ($row = $stmt->fetch()) {
            $order = new Order();
            $order->fromArray($row);
            
            if ($row['CustomerName']) {
                $customer = new Customer();
                $customer->Id = $order->CustomerId;
                $customer->Name = $row['CustomerName'];
                $order->Customer = $customer;
            }
            
            $orders[] = $order;
        }
        return $orders;
    }
    
    /**
     * 注文を登録
     */
    public function createOrder(Order $order): bool {
        $order->calculateTotal(); // 合計金額を計算
        
        $sql = "INSERT INTO Orders (CustomerId, ProductName, Quantity, UnitPrice, TotalAmount, OrderDate, Status, IsOnlineOrder, CreatedDate, CreatedBy, UpdatedDate, UpdatedBy) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $order->CustomerId,
            $order->ProductName,
            $order->Quantity,
            $order->UnitPrice,
            $order->TotalAmount,
            $order->OrderDate->format('Y-m-d H:i:s'),
            $order->Status,
            $order->IsOnlineOrder ? 1 : 0,
            $order->CreatedDate->format('Y-m-d H:i:s'),
            $order->CreatedBy,
            $order->UpdatedDate->format('Y-m-d H:i:s'),
            $order->UpdatedBy
        ]);
    }
    
    /**
     * 注文を更新
     */
    public function updateOrder(Order $order): bool {
        $order->calculateTotal(); // 合計金額を再計算
        
        $sql = "UPDATE Orders SET 
                CustomerId = ?, ProductName = ?, Quantity = ?, UnitPrice = ?, TotalAmount = ?, 
                OrderDate = ?, Status = ?, IsOnlineOrder = ?, UpdatedDate = ?, UpdatedBy = ? 
                WHERE Id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $order->CustomerId,
            $order->ProductName,
            $order->Quantity,
            $order->UnitPrice,
            $order->TotalAmount,
            $order->OrderDate->format('Y-m-d H:i:s'),
            $order->Status,
            $order->IsOnlineOrder ? 1 : 0,
            $order->UpdatedDate->format('Y-m-d H:i:s'),
            $order->UpdatedBy,
            $order->Id
        ]);
    }
    
    /**
     * 注文を削除
     */
    public function deleteOrder(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Orders WHERE Id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * CSVファイルから注文データを取込
     */
    public function importOrdersFromCSV(string $filePath): array {
        $results = ['success' => 0, 'error' => 0, 'errors' => []];
        
        if (!file_exists($filePath)) {
            $results['errors'][] = "ファイルが見つかりません: $filePath";
            return $results;
        }
        
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $results['errors'][] = "ファイルを開けませんでした: $filePath";
            return $results;
        }
        
        // ヘッダー行をスキップ
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 6) {
                $results['error']++;
                $results['errors'][] = "データ形式が正しくありません";
                continue;
            }
            
            try {
                $order = new Order();
                $order->CustomerId = (int)$data[0];
                $order->ProductName = $data[1];
                $order->Quantity = (int)$data[2];
                $order->UnitPrice = (float)$data[3];
                $order->OrderDate = new DateTime($data[4]);
                $order->Status = $data[5];
                $order->IsOnlineOrder = isset($data[6]) ? (bool)$data[6] : false;
                $order->CreatedBy = 'CSV Import';
                $order->UpdatedBy = 'CSV Import';
                
                if ($this->createOrder($order)) {
                    $results['success']++;
                } else {
                    $results['error']++;
                    $results['errors'][] = "注文の登録に失敗しました";
                }
            } catch (Exception $e) {
                $results['error']++;
                $results['errors'][] = "データ処理エラー: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        return $results;
    }
}
?> 