<?php
/**
 * 顧客モデルクラス
 */
class Customer {
    public int $Id = 0;
    public int $CustomerId = 0;
    public string $Name = '';
    public string $Email = '';
    public string $Phone = '';
    public string $Address = '';
    public ?DateTime $InvoiceDate = null;
    public string $Status = '';
    public DateTime $CreatedDate;
    public string $CreatedBy = '';
    public DateTime $UpdatedDate;
    public string $UpdatedBy = '';
    
    public function __construct() {
        $this->CreatedDate = new DateTime();
        $this->UpdatedDate = new DateTime();
    }
    
    public function fromArray(array $data): void {
        // キーを小文字化
        $data = array_change_key_case($data, CASE_LOWER);
        $this->Id = (int)($data['id'] ?? 0);
        $this->CustomerId = (int)($data['customerid'] ?? 0);
        $this->Name = $data['name'] ?? '';
        $this->Email = $data['email'] ?? '';
        $this->Phone = $data['phone'] ?? '';
        $this->Address = $data['address'] ?? '';
        $this->InvoiceDate = isset($data['invoicedate']) ? new DateTime($data['invoicedate']) : new DateTime();
        $this->Status = $data['status'] ?? '';
        $this->CreatedDate = isset($data['createddate']) ? new DateTime($data['createddate']) : new DateTime();
        $this->CreatedBy = $data['createdby'] ?? '';
        $this->UpdatedDate = isset($data['updateddate']) ? new DateTime($data['updateddate']) : new DateTime();
        $this->UpdatedBy = $data['updatedby'] ?? '';
    }
    
    public function toArray(): array {
        return [
            'Id' => $this->Id,
            'Name' => $this->Name,
            'Email' => $this->Email,
            'Phone' => $this->Phone,
            'Address' => $this->Address,
            'CreatedDate' => $this->CreatedDate->format('Y-m-d H:i:s'),
            'CreatedBy' => $this->CreatedBy,
            'UpdatedDate' => $this->UpdatedDate->format('Y-m-d H:i:s'),
            'UpdatedBy' => $this->UpdatedBy
        ];
    }
}
?> 