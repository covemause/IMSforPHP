<?php
/**
 * 注文モデルクラス
 */
class Order {
    public int $Id = 0;
    public int $CustomerId = 0;
    public string $ProductName = '';
    public int $Quantity = 0;
    public float $UnitPrice = 0.0;
    public float $TotalAmount = 0.0;
    public DateTime $OrderDate;
    public string $Status = '';
    public bool $IsOnlineOrder = false;
    public DateTime $CreatedDate;
    public string $CreatedBy = '';
    public DateTime $UpdatedDate;
    public string $UpdatedBy = '';
    
    // 関連データ
    public ?Customer $Customer = null;
    
    public function __construct() {
        $this->OrderDate = new DateTime();
        $this->CreatedDate = new DateTime();
        $this->UpdatedDate = new DateTime();
    }
    
    public function fromArray(array $data): void {
        $this->Id = (int)($data['Id'] ?? 0);
        $this->CustomerId = (int)($data['CustomerId'] ?? 0);
        $this->ProductName = $data['ProductName'] ?? '';
        $this->Quantity = (int)($data['Quantity'] ?? 0);
        $this->UnitPrice = (float)($data['UnitPrice'] ?? 0.0);
        $this->TotalAmount = (float)($data['TotalAmount'] ?? 0.0);
        $this->OrderDate = new DateTime($data['OrderDate'] ?? 'now');
        $this->Status = $data['Status'] ?? '';
        $this->IsOnlineOrder = (bool)($data['IsOnlineOrder'] ?? false);
        $this->CreatedDate = new DateTime($data['CreatedDate'] ?? 'now');
        $this->CreatedBy = $data['CreatedBy'] ?? '';
        $this->UpdatedDate = new DateTime($data['UpdatedDate'] ?? 'now');
        $this->UpdatedBy = $data['UpdatedBy'] ?? '';
    }
    
    public function toArray(): array {
        return [
            'Id' => $this->Id,
            'CustomerId' => $this->CustomerId,
            'ProductName' => $this->ProductName,
            'Quantity' => $this->Quantity,
            'UnitPrice' => $this->UnitPrice,
            'TotalAmount' => $this->TotalAmount,
            'OrderDate' => $this->OrderDate->format('Y-m-d H:i:s'),
            'Status' => $this->Status,
            'IsOnlineOrder' => $this->IsOnlineOrder ? 1 : 0,
            'CreatedDate' => $this->CreatedDate->format('Y-m-d H:i:s'),
            'CreatedBy' => $this->CreatedBy,
            'UpdatedDate' => $this->UpdatedDate->format('Y-m-d H:i:s'),
            'UpdatedBy' => $this->UpdatedBy
        ];
    }
    
    public function calculateTotal(): void {
        $this->TotalAmount = $this->Quantity * $this->UnitPrice;
    }
}
?> 