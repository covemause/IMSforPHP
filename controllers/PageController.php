<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/logger.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/CustomerController.php';
require_once __DIR__ . '/InvoiceController.php';
require_once __DIR__ . '/OrderController.php';
require_once __DIR__ . '/PaymentController.php';
require_once __DIR__ . '/SalesController.php';

/**
 * ページ処理コントローラークラス（Visitorパターン）
 */
class PageController {
    private $customerController;
    private $invoiceController;
    private $orderController;
    private $paymentController;
    private $salesController;
    
    public function __construct() {
        $this->customerController = new CustomerController();
        $this->invoiceController = new InvoiceController();
        $this->orderController = new OrderController();
        $this->paymentController = new PaymentController();
        $this->salesController = new SalesController();
    }
    
    /**
     * ページ処理を実行
     */
    public function processPage(string $page, string $action, array $data = []): array {
        $method = 'process' . ucfirst($page) . 'Page';
        
        if (method_exists($this, $method)) {
            return $this->$method($action, $data);
        } else {
            // デフォルトはダッシュボード
            return $this->processDashboardPage($action, $data);
        }
    }
    
    /**
     * 顧客管理ページの処理
     */
    private function processCustomersPage(string $action, array $data): array {
        switch ($action) {
            case 'create_form':
                // 新規登録画面表示のみ
                break;
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    Logger::action('顧客新規登録ボタン押下', [
                        'user' => $_SESSION['user'] ?? 'guest',
                        'name' => $_POST['name'] ?? '',
                        'email' => $_POST['email'] ?? '',
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                    ]);
                    $customer = new Customer();
                    $customer->Name = $_POST['name'] ?? '';
                    $customer->Email = $_POST['email'] ?? '';
                    $customer->Phone = $_POST['phone'] ?? '';
                    $customer->Address = $_POST['address'] ?? '';
                    $customer->CreatedBy = 'admin';
                    $customer->UpdatedBy = 'admin';

                    if ($this->customerController->createCustomer($customer)) {
                        header('Location: ?page=customers&success=1');
                        exit;
                    } else {
                        throw new Exception('顧客の登録に失敗しました。');
                    }
                }
                $data['customers'] = $this->customerController->getAllCustomers();
                break;
                
            case 'update':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $customer = new Customer();
                    $customer->Id = (int)($_POST['id'] ?? 0);
                    $customer->Name = $_POST['name'] ?? '';
                    $customer->Email = $_POST['email'] ?? '';
                    $customer->Phone = $_POST['phone'] ?? '';
                    $customer->Address = $_POST['address'] ?? '';
                    $customer->UpdatedBy = 'admin';
                    
                    if ($this->customerController->updateCustomer($customer)) {
                        header('Location: ?page=customers&success=2');
                        exit;
                    } else {
                        $data['error'] = '顧客の更新に失敗しました。';
                    }
                }
                $data['customers'] = $this->customerController->getAllCustomers();
                break;
                
            case 'get':
                $id = (int)($_GET['id'] ?? 0);
                $customer = $this->customerController->getCustomerById($id);
                if ($customer) {
                    header('Content-Type: application/json');
                    echo json_encode($customer->toArray());
                    exit;
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => '顧客が見つかりません']);
                    exit;
                }
                break;
                
            case 'delete':
                $id = (int)($_GET['id'] ?? 0);
                if ($this->customerController->deleteCustomer($id)) {
                    header('Location: ?page=customers&success=3');
                    exit;
                } else {
                    $data['error'] = '顧客の削除に失敗しました。';
                }
                $data['customers'] = $this->customerController->getAllCustomers();
                break;
                
            case 'list':
                $perPage = 10;
                $pageNum = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
                $offset = ($pageNum - 1) * $perPage;
                $data['customers'] = $this->customerController->getCustomersPaged($offset, $perPage);
                $data['total_customers'] = $this->customerController->countCustomers();
                $data['per_page'] = $perPage;
                $data['current_page'] = $pageNum;
                break;
                
            default:
                $data['customers'] = $this->customerController->getAllCustomers();
                break;
        }
        
        return $data;
    }
    
    /**
     * 伝票管理ページの処理
     */
    private function processInvoicesPage(string $action, array $data): array {
        switch ($action) {
            case 'list':
                $data['invoices'] = $this->invoiceController->getAllInvoices();
                break;
                
            case 'search':
                $criteria = [
                    'customer_id' => $_GET['customer_id'] ?? '',
                    'start_date' => $_GET['start_date'] ?? '',
                    'end_date' => $_GET['end_date'] ?? '',
                    'min_amount' => $_GET['min_amount'] ?? '',
                    'max_amount' => $_GET['max_amount'] ?? ''
                ];
                $data['invoices'] = $this->invoiceController->searchInvoices($criteria);
                break;
                
            default:
                $data['invoices'] = $this->invoiceController->getAllInvoices();
                break;
        }
        
        return $data;
    }
    
    /**
     * 注文管理ページの処理
     */
    private function processOrdersPage(string $action, array $data): array {
        switch ($action) {
            case 'list':
                $data['orders'] = $this->orderController->getAllOrders();
                break;
                
            case 'search':
                $criteria = [
                    'customer_id' => $_GET['customer_id'] ?? '',
                    'product_name' => $_GET['product_name'] ?? '',
                    'start_date' => $_GET['start_date'] ?? '',
                    'end_date' => $_GET['end_date'] ?? '',
                    'is_online_order' => $_GET['is_online_order'] ?? ''
                ];
                $data['orders'] = $this->orderController->searchOrders($criteria);
                break;
                
            default:
                $data['orders'] = $this->orderController->getAllOrders();
                break;
        }
        
        return $data;
    }
    
    /**
     * 入金管理ページの処理
     */
    private function processPaymentsPage(string $action, array $data): array {
        switch ($action) {
            case 'list':
                $data['payments'] = $this->paymentController->getAllPayments();
                break;
                
            case 'search':
                $criteria = [
                    'invoice_id' => $_GET['invoice_id'] ?? '',
                    'payment_method' => $_GET['payment_method'] ?? '',
                    'start_date' => $_GET['start_date'] ?? '',
                    'end_date' => $_GET['end_date'] ?? '',
                    'min_amount' => $_GET['min_amount'] ?? '',
                    'max_amount' => $_GET['max_amount'] ?? ''
                ];
                $data['payments'] = $this->paymentController->searchPayments($criteria);
                break;
                
            default:
                $data['payments'] = $this->paymentController->getAllPayments();
                break;
        }
        
        return $data;
    }
    
    /**
     * 売上集計ページの処理
     */
    private function processSalesPage(string $action, array $data): array {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $data['daily_sales'] = $this->salesController->getDailySales($startDate, $endDate);
        $data['monthly_sales'] = $this->salesController->getMonthlySales($startDate, $endDate);
        $data['customer_sales'] = $this->salesController->getCustomerSales($startDate, $endDate);
        $data['product_sales'] = $this->salesController->getProductSales($startDate, $endDate);
        $data['sales_summary'] = $this->salesController->getSalesSummary($startDate, $endDate);
        $data['online_vs_store'] = $this->salesController->getOnlineVsStoreSales($startDate, $endDate);
        
        return $data;
    }
    
    /**
     * ダッシュボードページの処理
     */
    private function processDashboardPage(string $action, array $data): array {
        $data['customers'] = $this->customerController->getAllCustomers();
        $data['invoices'] = $this->invoiceController->getAllInvoices();
        $data['orders'] = $this->orderController->getAllOrders();
        $data['payments'] = $this->paymentController->getAllPayments();
        
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = date('Y-m-d');
        $data['sales_summary'] = $this->salesController->getSalesSummary($startDate, $endDate);
        
        return $data;
    }
}
?> 