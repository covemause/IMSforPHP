<?php
require_once __DIR__ . '/../config/database.php';

/**
 * 売上集計コントローラークラス
 */
class SalesController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 日別売上集計
     */
    public function getDailySales(string $startDate, string $endDate): array {
        $sql = "
            SELECT 
                DATE(o.OrderDate) as Date,
                COUNT(*) as OrderCount,
                SUM(o.TotalAmount) as TotalSales,
                AVG(o.TotalAmount) as AvgOrderValue
            FROM Orders o
            WHERE o.OrderDate BETWEEN ? AND ?
            GROUP BY DATE(o.OrderDate)
            ORDER BY Date DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    /**
     * 月別売上集計
     */
    public function getMonthlySales(string $startDate, string $endDate): array {
        $sql = "
            SELECT 
                to_char(o.OrderDate, 'YYYY-MM') as Month,
                COUNT(*) as OrderCount,
                SUM(o.TotalAmount) as TotalSales,
                AVG(o.TotalAmount) as AvgOrderValue
            FROM Orders o
            WHERE o.OrderDate BETWEEN ? AND ?
            GROUP BY to_char(o.OrderDate, 'YYYY-MM')
            ORDER BY Month DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    /**
     * 顧客別売上集計
     */
    public function getCustomerSales(string $startDate, string $endDate): array {
        $sql = "
            SELECT 
                c.Id as CustomerId,
                c.Name as CustomerName,
                COUNT(o.Id) as OrderCount,
                SUM(o.TotalAmount) as TotalSales,
                AVG(o.TotalAmount) as AvgOrderValue
            FROM Customers c
            LEFT JOIN Orders o ON c.Id = o.CustomerId 
                AND o.OrderDate BETWEEN ? AND ?
            GROUP BY c.Id, c.Name
            HAVING COUNT(o.Id) > 0
            ORDER BY TotalSales DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    /**
     * 商品別売上集計
     */
    public function getProductSales(string $startDate, string $endDate): array {
        $sql = "
            SELECT 
                o.ProductName,
                COUNT(*) as OrderCount,
                SUM(o.Quantity) as TotalQuantity,
                SUM(o.TotalAmount) as TotalSales,
                AVG(o.UnitPrice) as AvgUnitPrice
            FROM Orders o
            WHERE o.OrderDate BETWEEN ? AND ?
            GROUP BY o.ProductName
            ORDER BY TotalSales DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    /**
     * 総合売上サマリー
     */
    public function getSalesSummary(string $startDate, string $endDate): array {
        $sql = "
            SELECT 
                COUNT(*) as TotalOrders,
                SUM(o.TotalAmount) as TotalSales,
                AVG(o.TotalAmount) as AvgOrderValue,
                COUNT(DISTINCT o.CustomerId) as UniqueCustomers,
                COUNT(DISTINCT o.ProductName) as UniqueProducts
            FROM Orders o
            WHERE o.OrderDate BETWEEN ? AND ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetch();
    }
    
    /**
     * オンライン注文 vs 店舗注文の比較
     */
    public function getOnlineVsStoreSales(string $startDate, string $endDate): array {
        $sql = "
            SELECT 
                o.IsOnlineOrder,
                COUNT(*) as OrderCount,
                SUM(o.TotalAmount) as TotalSales,
                AVG(o.TotalAmount) as AvgOrderValue
            FROM Orders o
            WHERE o.OrderDate BETWEEN ? AND ?
            GROUP BY o.IsOnlineOrder
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    /**
     * 売上データをCSV形式でエクスポート
     */
    public function exportSalesToCSV(string $startDate, string $endDate, string $type): string {
        $data = [];
        
        switch ($type) {
            case 'daily':
                $data = $this->getDailySales($startDate, $endDate);
                $headers = ['Date', 'OrderCount', 'TotalSales', 'AvgOrderValue'];
                break;
            case 'monthly':
                $data = $this->getMonthlySales($startDate, $endDate);
                $headers = ['Month', 'OrderCount', 'TotalSales', 'AvgOrderValue'];
                break;
            case 'customer':
                $data = $this->getCustomerSales($startDate, $endDate);
                $headers = ['CustomerId', 'CustomerName', 'OrderCount', 'TotalSales', 'AvgOrderValue'];
                break;
            case 'product':
                $data = $this->getProductSales($startDate, $endDate);
                $headers = ['ProductName', 'OrderCount', 'TotalQuantity', 'TotalSales', 'AvgUnitPrice'];
                break;
            default:
                return '';
        }
        
        $output = fopen('php://temp', 'w+');
        
        // BOM for Excel
        fwrite($output, "\xEF\xBB\xBF");
        
        // ヘッダー行
        fputcsv($output, $headers);
        
        // データ行
        foreach ($data as $row) {
            fputcsv($output, array_values($row));
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}
?> 