<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Customer.php';

/**
 * 顧客コントローラークラス
 */
class CustomerController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 全顧客を取得
     */
    public function getAllCustomers(): array {
        $stmt = $this->db->query("SELECT * FROM Customers ORDER BY Name");
        $customers = [];
        while ($row = $stmt->fetch()) {
            $customer = new Customer();
            $customer->fromArray($row);
            $customers[] = $customer;
        }
        return $customers;
    }
    
    /**
     * IDで顧客を取得
     */
    public function getCustomerById(int $id): ?Customer {
        $stmt = $this->db->prepare("SELECT * FROM Customers WHERE Id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            $customer = new Customer();
            $customer->fromArray($row);
            return $customer;
        }
        return null;
    }
    
    /**
     * 顧客を検索
     */
    public function searchCustomers(string $keyword): array {
        $stmt = $this->db->prepare("
            SELECT * FROM Customers 
            WHERE Name LIKE ? OR Email LIKE ? OR Phone LIKE ?
            ORDER BY Name
        ");
        $keyword = "%$keyword%";
        $stmt->execute([$keyword, $keyword, $keyword]);
        
        $customers = [];
        while ($row = $stmt->fetch()) {
            $customer = new Customer();
            $customer->fromArray($row);
            $customers[] = $customer;
        }
        return $customers;
    }
    
    /**
     * 顧客を登録
     */
    public function createCustomer(Customer $customer): bool {
        $sql = "INSERT INTO Customers (Name, Email, Phone, Address, CreatedDate, CreatedBy, UpdatedDate, UpdatedBy) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $customer->Name,
            $customer->Email,
            $customer->Phone,
            $customer->Address,
            $customer->CreatedDate->format('Y-m-d H:i:s'),
            $customer->CreatedBy,
            $customer->UpdatedDate->format('Y-m-d H:i:s'),
            $customer->UpdatedBy
        ]);
    }
    
    /**
     * 顧客を更新
     */
    public function updateCustomer(Customer $customer): bool {
        $sql = "UPDATE Customers SET 
                Name = ?, Email = ?, Phone = ?, Address = ?, 
                UpdatedDate = ?, UpdatedBy = ? 
                WHERE Id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $customer->Name,
            $customer->Email,
            $customer->Phone,
            $customer->Address,
            $customer->UpdatedDate->format('Y-m-d H:i:s'),
            $customer->UpdatedBy,
            $customer->Id
        ]);
    }
    
    /**
     * 顧客を削除
     */
    public function deleteCustomer(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Customers WHERE Id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * CPM分析（顧客購買パターン分析）
     */
    public function analyzeCPM(int $customerId): array {
        $sql = "
            SELECT 
                o.ProductName,
                COUNT(*) as OrderCount,
                SUM(o.Quantity) as TotalQuantity,
                SUM(o.TotalAmount) as TotalAmount,
                AVG(o.UnitPrice) as AvgUnitPrice
            FROM Orders o
            WHERE o.CustomerId = ?
            GROUP BY o.ProductName
            ORDER BY TotalAmount DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$customerId]);
        
        return $stmt->fetchAll();
    }

    public function getCustomersPaged($offset, $limit): array {
        $stmt = $this->db->prepare("SELECT * FROM Customers ORDER BY Id DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        $customers = [];
        while ($row = $stmt->fetch()) {
            $customer = new Customer();
            $customer->fromArray($row);
            $customers[] = $customer;
        }
        return $customers;
    }

    public function countCustomers(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM Customers");
        $row = $stmt->fetch();
        return (int)($row['cnt'] ?? 0);
    }
}
?> 