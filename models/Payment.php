<?php
/**
 * 入金モデルクラス
 */
class Payment {
    public int $Id = 0;
    public int $InvoiceId = 0;
    public float $Amount = 0.0;
    public DateTime $PaymentDate;
    public string $PaymentMethod = '';
    public string $Status = '';
    public DateTime $CreatedDate;
    public string $CreatedBy = '';
    public DateTime $UpdatedDate;
    public string $UpdatedBy = '';
    
    // 関連データ
    public ?Invoice $Invoice = null;
    
    public function __construct() {
        $this->PaymentDate = new DateTime();
        $this->CreatedDate = new DateTime();
        $this->UpdatedDate = new DateTime();
    }
    
    public function fromArray(array $data): void {
        $this->Id = (int)($data['Id'] ?? 0);
        $this->InvoiceId = (int)($data['InvoiceId'] ?? 0);
        $this->Amount = (float)($data['Amount'] ?? 0.0);
        $this->PaymentDate = new DateTime($data['PaymentDate'] ?? 'now');
        $this->PaymentMethod = $data['PaymentMethod'] ?? '';
        $this->Status = $data['Status'] ?? '';
        $this->CreatedDate = new DateTime($data['CreatedDate'] ?? 'now');
        $this->CreatedBy = $data['CreatedBy'] ?? '';
        $this->UpdatedDate = new DateTime($data['UpdatedDate'] ?? 'now');
        $this->UpdatedBy = $data['UpdatedBy'] ?? '';
    }
    
    public function toArray(): array {
        return [
            'Id' => $this->Id,
            'InvoiceId' => $this->InvoiceId,
            'Amount' => $this->Amount,
            'PaymentDate' => $this->PaymentDate->format('Y-m-d H:i:s'),
            'PaymentMethod' => $this->PaymentMethod,
            'Status' => $this->Status,
            'CreatedDate' => $this->CreatedDate->format('Y-m-d H:i:s'),
            'CreatedBy' => $this->CreatedBy,
            'UpdatedDate' => $this->UpdatedDate->format('Y-m-d H:i:s'),
            'UpdatedBy' => $this->UpdatedBy
        ];
    }
}
?> 