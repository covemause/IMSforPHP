<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Customer.php';

/**
 * 伝票コントローラークラス
 */
class InvoiceController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 全伝票を取得
     */
    public function getAllInvoices(): array {
        $sql = "
            SELECT i.*, c.Name as CustomerName
            FROM Invoices i
            LEFT JOIN Customers c ON i.CustomerId = c.Id
            ORDER BY i.InvoiceDate DESC
        ";
        
        $stmt = $this->db->query($sql);
        $invoices = [];
        while ($row = $stmt->fetch()) {
            $invoice = new Invoice();
            $invoice->fromArray($row);
            
            // 顧客情報を設定
            if ($row['CustomerName']) {
                $customer = new Customer();
                $customer->Id = $invoice->CustomerId;
                $customer->Name = $row['CustomerName'];
                $invoice->Customer = $customer;
            }
            
            $invoices[] = $invoice;
        }
        return $invoices;
    }
    
    /**
     * IDで伝票を取得
     */
    public function getInvoiceById(int $id): ?Invoice {
        $sql = "
            SELECT i.*, c.Name as CustomerName
            FROM Invoices i
            LEFT JOIN Customers c ON i.CustomerId = c.Id
            WHERE i.Id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            $invoice = new Invoice();
            $invoice->fromArray($row);
            
            // 顧客情報を設定
            if ($row['CustomerName']) {
                $customer = new Customer();
                $customer->Id = $invoice->CustomerId;
                $customer->Name = $row['CustomerName'];
                $invoice->Customer = $customer;
            }
            
            return $invoice;
        }
        return null;
    }
    
    /**
     * 伝票を検索
     */
    public function searchInvoices(array $criteria): array {
        $where = [];
        $params = [];
        
        if (!empty($criteria['customer_id'])) {
            $where[] = "i.CustomerId = ?";
            $params[] = $criteria['customer_id'];
        }
        
        if (!empty($criteria['start_date'])) {
            $where[] = "i.InvoiceDate >= ?";
            $params[] = $criteria['start_date'];
        }
        
        if (!empty($criteria['end_date'])) {
            $where[] = "i.InvoiceDate <= ?";
            $params[] = $criteria['end_date'];
        }
        
        if (!empty($criteria['min_amount'])) {
            $where[] = "i.Amount >= ?";
            $params[] = $criteria['min_amount'];
        }
        
        if (!empty($criteria['max_amount'])) {
            $where[] = "i.Amount <= ?";
            $params[] = $criteria['max_amount'];
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $sql = "
            SELECT i.*, c.Name as CustomerName
            FROM Invoices i
            LEFT JOIN Customers c ON i.CustomerId = c.Id
            $whereClause
            ORDER BY i.InvoiceDate DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $invoices = [];
        while ($row = $stmt->fetch()) {
            $invoice = new Invoice();
            $invoice->fromArray($row);
            
            // 顧客情報を設定
            if ($row['CustomerName']) {
                $customer = new Customer();
                $customer->Id = $invoice->CustomerId;
                $customer->Name = $row['CustomerName'];
                $invoice->Customer = $customer;
            }
            
            $invoices[] = $invoice;
        }
        return $invoices;
    }
    
    /**
     * 伝票を登録
     */
    public function createInvoice(Invoice $invoice): bool {
        $sql = "INSERT INTO Invoices (CustomerId, Amount, InvoiceDate, Status, CreatedDate, CreatedBy, UpdatedDate, UpdatedBy) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $invoice->CustomerId,
            $invoice->Amount,
            $invoice->InvoiceDate->format('Y-m-d H:i:s'),
            $invoice->Status,
            $invoice->CreatedDate->format('Y-m-d H:i:s'),
            $invoice->CreatedBy,
            $invoice->UpdatedDate->format('Y-m-d H:i:s'),
            $invoice->UpdatedBy
        ]);
    }
    
    /**
     * 伝票を更新
     */
    public function updateInvoice(Invoice $invoice): bool {
        $sql = "UPDATE Invoices SET 
                CustomerId = ?, Amount = ?, InvoiceDate = ?, Status = ?, 
                UpdatedDate = ?, UpdatedBy = ? 
                WHERE Id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $invoice->CustomerId,
            $invoice->Amount,
            $invoice->InvoiceDate->format('Y-m-d H:i:s'),
            $invoice->Status,
            $invoice->UpdatedDate->format('Y-m-d H:i:s'),
            $invoice->UpdatedBy,
            $invoice->Id
        ]);
    }
    
    /**
     * 伝票を削除
     */
    public function deleteInvoice(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Invoices WHERE Id = ?");
        return $stmt->execute([$id]);
    }
}
?> 