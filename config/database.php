<?php
/**
 * データベース接続設定（PostgreSQL版）
 */
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        // PostgreSQLドライバーの確認
        if (!extension_loaded('pdo_pgsql')) {
            throw new Exception('PDO PostgreSQL ドライバーがインストールされていません。php-pgsql をインストールしてください。');
        }
        
        // PostgreSQL接続設定
        $config = require __DIR__ . '/database_config.php';
        $host = $config['host'];
        $port = $config['port'];
        $dbname = $config['dbname'];
        $username = $config['username'];
        $password = $config['password'];
        
        try {
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$username;password=$password";
            $this->pdo = new PDO($dsn);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('データベース接続エラー: ' . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function initDatabase() {
        try {
            $sql = "
            CREATE TABLE IF NOT EXISTS Customers (
                Id SERIAL PRIMARY KEY,
                Name VARCHAR(255) NOT NULL,
                Email VARCHAR(255),
                Phone VARCHAR(50),
                Address TEXT,
                CreatedDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CreatedBy VARCHAR(100) NOT NULL,
                UpdatedDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UpdatedBy VARCHAR(100) NOT NULL
            );
            
            CREATE TABLE IF NOT EXISTS Invoices (
                Id SERIAL PRIMARY KEY,
                CustomerId INTEGER NOT NULL,
                Amount DECIMAL(10,2) NOT NULL,
                InvoiceDate TIMESTAMP NOT NULL,
                Status VARCHAR(50) NOT NULL,
                CreatedDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CreatedBy VARCHAR(100) NOT NULL,
                UpdatedDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UpdatedBy VARCHAR(100) NOT NULL,
                FOREIGN KEY (CustomerId) REFERENCES Customers(Id)
            );
            
            CREATE TABLE IF NOT EXISTS Orders (
                Id SERIAL PRIMARY KEY,
                CustomerId INTEGER NOT NULL,
                ProductName VARCHAR(255) NOT NULL,
                Quantity INTEGER NOT NULL,
                UnitPrice DECIMAL(10,2) NOT NULL,
                TotalAmount DECIMAL(10,2) NOT NULL,
                OrderDate TIMESTAMP NOT NULL,
                Status VARCHAR(50) NOT NULL,
                IsOnlineOrder BOOLEAN NOT NULL DEFAULT FALSE,
                CreatedDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CreatedBy VARCHAR(100) NOT NULL,
                UpdatedDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UpdatedBy VARCHAR(100) NOT NULL,
                FOREIGN KEY (CustomerId) REFERENCES Customers(Id)
            );
            
            CREATE TABLE IF NOT EXISTS Payments (
                Id SERIAL PRIMARY KEY,
                InvoiceId INTEGER NOT NULL,
                Amount DECIMAL(10,2) NOT NULL,
                PaymentDate TIMESTAMP NOT NULL,
                PaymentMethod VARCHAR(100) NOT NULL,
                Status VARCHAR(50) NOT NULL,
                CreatedDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CreatedBy VARCHAR(100) NOT NULL,
                UpdatedDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UpdatedBy VARCHAR(100) NOT NULL,
                FOREIGN KEY (InvoiceId) REFERENCES Invoices(Id)
            );
            ";
            
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new Exception('データベース初期化エラー: ' . $e->getMessage());
        }
    }
    
    private function logSql($sql, $params = []) {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/sql.log';
        $time = date('Y-m-d H:i:s');
        $paramStr = json_encode($params, JSON_UNESCAPED_UNICODE);
        file_put_contents($logFile, "[$time] SQL: $sql PARAMS: $paramStr\n", FILE_APPEND);
    }

    public function query($sql) {
        $this->logSql($sql);
        return $this->pdo->query($sql);
    }

    public function prepare($sql) {
        $this->logSql($sql);
        $stmt = $this->pdo->prepare($sql);
        // execute時にもパラメータをログ出力するため、ラップする
        $parent = $this;
        return new class($stmt, $sql, $parent) {
            private $stmt;
            private $sql;
            private $parent;
            public function __construct($stmt, $sql, $parent) {
                $this->stmt = $stmt;
                $this->sql = $sql;
                $this->parent = $parent;
            }
            public function execute($params = null) {
                $this->parent->logSql($this->sql, $params ?? []);
                return $this->stmt->execute($params);
            }
            public function fetch($mode = null) {
                return $mode ? $this->stmt->fetch($mode) : $this->stmt->fetch();
            }
            public function fetchAll($mode = null) {
                return $mode ? $this->stmt->fetchAll($mode) : $this->stmt->fetchAll();
            }
            public function rowCount() {
                return $this->stmt->rowCount();
            }
            public function bindParam($param, &$var, $type = null, $maxLength = null, $driverOptions = null) {
                return $this->stmt->bindParam($param, $var, $type, $maxLength, $driverOptions);
            }
            public function bindValue($param, $value, $type = null) {
                return $this->stmt->bindValue($param, $value, $type);
            }
            public function errorInfo() {
                return $this->stmt->errorInfo();
            }
            public function __call($name, $arguments) {
                return call_user_func_array([$this->stmt, $name], $arguments);
            }
        };
    }

    public function exec($sql) {
        $this->logSql($sql);
        return $this->pdo->exec($sql);
    }
    

    
    /**
     * データベース接続テスト
     */
    public static function testConnection() {
        try {
            $db = self::getInstance();
            $connection = $db->getConnection();
            $connection->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?> 