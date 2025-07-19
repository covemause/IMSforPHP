<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Invoice.php';

/**
 * 入金コントローラークラス
 */
class PaymentController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 全入金を取得（伝票情報付き）
     */
    public function getAllPayments(): array {
        $sql = "
            SELECT p.*, i.Amount as InvoiceAmount, i.InvoiceDate, c.Name as CustomerName
            FROM Payments p
            LEFT JOIN Invoices i ON p.InvoiceId = i.Id
            LEFT JOIN Customers c ON i.CustomerId = c.Id
            ORDER BY p.PaymentDate DESC
        ";
        
        $stmt = $this->db->query($sql);
        $payments = [];
        while ($row = $stmt->fetch()) {
            $payment = new Payment();
            $payment->fromArray($row);
            
            // 伝票情報を設定
            if ($row['InvoiceAmount']) {
                $invoice = new Invoice();
                $invoice->Id = $payment->InvoiceId;
                $invoice->Amount = (float)$row['InvoiceAmount'];
                $invoice->InvoiceDate = new DateTime($row['InvoiceDate']);
                $payment->Invoice = $invoice;
            }
            
            $payments[] = $payment;
        }
        return $payments;
    }
    
    /**
     * IDで入金を取得
     */
    public function getPaymentById(int $id): ?Payment {
        $sql = "
            SELECT p.*, i.Amount as InvoiceAmount, i.InvoiceDate, c.Name as CustomerName
            FROM Payments p
            LEFT JOIN Invoices i ON p.InvoiceId = i.Id
            LEFT JOIN Customers c ON i.CustomerId = c.Id
            WHERE p.Id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            $payment = new Payment();
            $payment->fromArray($row);
            
            if ($row['InvoiceAmount']) {
                $invoice = new Invoice();
                $invoice->Id = $payment->InvoiceId;
                $invoice->Amount = (float)$row['InvoiceAmount'];
                $invoice->InvoiceDate = new DateTime($row['InvoiceDate']);
                $payment->Invoice = $invoice;
            }
            
            return $payment;
        }
        return null;
    }
    
    /**
     * 入金を検索
     */
    public function searchPayments(array $criteria): array {
        $where = [];
        $params = [];
        
        if (!empty($criteria['invoice_id'])) {
            $where[] = "p.InvoiceId = ?";
            $params[] = $criteria['invoice_id'];
        }
        
        if (!empty($criteria['payment_method'])) {
            $where[] = "p.PaymentMethod = ?";
            $params[] = $criteria['payment_method'];
        }
        
        if (!empty($criteria['start_date'])) {
            $where[] = "p.PaymentDate >= ?";
            $params[] = $criteria['start_date'];
        }
        
        if (!empty($criteria['end_date'])) {
            $where[] = "p.PaymentDate <= ?";
            $params[] = $criteria['end_date'];
        }
        
        if (!empty($criteria['min_amount'])) {
            $where[] = "p.Amount >= ?";
            $params[] = $criteria['min_amount'];
        }
        
        if (!empty($criteria['max_amount'])) {
            $where[] = "p.Amount <= ?";
            $params[] = $criteria['max_amount'];
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $sql = "
            SELECT p.*, i.Amount as InvoiceAmount, i.InvoiceDate, c.Name as CustomerName
            FROM Payments p
            LEFT JOIN Invoices i ON p.InvoiceId = i.Id
            LEFT JOIN Customers c ON i.CustomerId = c.Id
            $whereClause
            ORDER BY p.PaymentDate DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $payments = [];
        while ($row = $stmt->fetch()) {
            $payment = new Payment();
            $payment->fromArray($row);
            
            if ($row['InvoiceAmount']) {
                $invoice = new Invoice();
                $invoice->Id = $payment->InvoiceId;
                $invoice->Amount = (float)$row['InvoiceAmount'];
                $invoice->InvoiceDate = new DateTime($row['InvoiceDate']);
                $payment->Invoice = $invoice;
            }
            
            $payments[] = $payment;
        }
        return $payments;
    }
    
    /**
     * 入金を登録
     */
    public function createPayment(Payment $payment): bool {
        $sql = "INSERT INTO Payments (InvoiceId, Amount, PaymentDate, PaymentMethod, Status, CreatedDate, CreatedBy, UpdatedDate, UpdatedBy) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $payment->InvoiceId,
            $payment->Amount,
            $payment->PaymentDate->format('Y-m-d H:i:s'),
            $payment->PaymentMethod,
            $payment->Status,
            $payment->CreatedDate->format('Y-m-d H:i:s'),
            $payment->CreatedBy,
            $payment->UpdatedDate->format('Y-m-d H:i:s'),
            $payment->UpdatedBy
        ]);
    }
    
    /**
     * 入金を更新
     */
    public function updatePayment(Payment $payment): bool {
        $sql = "UPDATE Payments SET 
                InvoiceId = ?, Amount = ?, PaymentDate = ?, PaymentMethod = ?, Status = ?, 
                UpdatedDate = ?, UpdatedBy = ? 
                WHERE Id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $payment->InvoiceId,
            $payment->Amount,
            $payment->PaymentDate->format('Y-m-d H:i:s'),
            $payment->PaymentMethod,
            $payment->Status,
            $payment->UpdatedDate->format('Y-m-d H:i:s'),
            $payment->UpdatedBy,
            $payment->Id
        ]);
    }
    
    /**
     * 入金を削除
     */
    public function deletePayment(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Payments WHERE Id = ?");
        return $stmt->execute([$id]);
    }
}
?> 