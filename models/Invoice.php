<?php
/**
 * 伝票モデルクラス
 */
class Invoice {
    public int $Id = 0;
    public int $CustomerId = 0;
    public float $Amount = 0.0;
    public DateTime $InvoiceDate;
    public string $Status = '';
    public DateTime $CreatedDate;
    public string $CreatedBy = '';
    public DateTime $UpdatedDate;
    public string $UpdatedBy = '';
    
    // 関連データ
    public ?Customer $Customer = null;
    
    public function __construct() {
        $this->InvoiceDate = new DateTime();
        $this->CreatedDate = new DateTime();
        $this->UpdatedDate = new DateTime();
    }
    
    public function fromArray(array $data): void {
        $this->Id = (int)($data['Id'] ?? 0);
        $this->CustomerId = (int)($data['CustomerId'] ?? 0);
        $this->Amount = (float)($data['Amount'] ?? 0.0);
        $this->InvoiceDate = new DateTime($data['InvoiceDate'] ?? 'now');
        $this->Status = $data['Status'] ?? '';
        $this->CreatedDate = new DateTime($data['CreatedDate'] ?? 'now');
        $this->CreatedBy = $data['CreatedBy'] ?? '';
        $this->UpdatedDate = new DateTime($data['UpdatedDate'] ?? 'now');
        $this->UpdatedBy = $data['UpdatedBy'] ?? '';
    }
    
    public function toArray(): array {
        return [
            'Id' => $this->Id,
            'CustomerId' => $this->CustomerId,
            'Amount' => $this->Amount,
            'InvoiceDate' => $this->InvoiceDate->format('Y-m-d H:i:s'),
            'Status' => $this->Status,
            'CreatedDate' => $this->CreatedDate->format('Y-m-d H:i:s'),
            'CreatedBy' => $this->CreatedBy,
            'UpdatedDate' => $this->UpdatedDate->format('Y-m-d H:i:s'),
            'UpdatedBy' => $this->UpdatedBy
        ];
    }
}
?> 