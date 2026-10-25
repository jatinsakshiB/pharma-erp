<?php
/**
 * DRXStore - SQLite PDO Adapter
 * High-performance, zero-server embedded database.
 * No MySQL or external server required.
 * Developed by Vineet
 */
class SQLiteDB {
    private PDO $pdo;
    private string $path;

    public function __construct(string $path = '') {
        if ($path === '') {
            $path = (defined('DATA_DIR') ? DATA_DIR : __DIR__ . '/../data') . '/drxstore.sqlite';
        }
        $this->path = $path;
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $dsn = "sqlite:" . $this->path;
        $this->pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // High performance WAL mode for concurrent reads & writes
        $this->pdo->exec("PRAGMA journal_mode = WAL;");
        $this->pdo->exec("PRAGMA synchronous = NORMAL;");
        $this->pdo->exec("PRAGMA foreign_keys = ON;");
        $this->pdo->exec("PRAGMA busy_timeout = 5000;");

        $this->migrate();
    }

    /** Expose raw PDO for complex SQL queries or direct queries */
    public function pdo(): PDO {
        return $this->pdo;
    }

    /** Path to SQLite database file */
    public function getPath(): string {
        return $this->path;
    }

    private static function tableSchema(): array {
        return [
            'settings' => [
                'id'            => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'store_name'    => 'TEXT',
                'store_address' => 'TEXT',
                'store_phone'   => 'TEXT',
                'store_email'   => 'TEXT',
                'store_gst'     => 'TEXT',
                'store_dl'      => 'TEXT',
                'currency'      => 'TEXT DEFAULT "&#8377;"',
                'low_qty'       => 'INTEGER DEFAULT 10',
                'expiry_days'   => 'INTEGER DEFAULT 90',
                'smtp_host'     => 'TEXT',
                'smtp_port'     => 'INTEGER DEFAULT 587',
                'smtp_user'     => 'TEXT',
                'smtp_pass'     => 'TEXT',
                'smtp_from'     => 'TEXT',
                'smtp_name'     => 'TEXT',
                'smtp_secure'   => 'TEXT DEFAULT "tls"',
                'setup_done'    => 'INTEGER DEFAULT 0',
                'storage'       => 'TEXT DEFAULT "sqlite"',
                'updated_at'    => 'TEXT',
                'created_at'    => 'TEXT',
            ],
            'users' => [
                'id'          => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'name'        => 'TEXT',
                'username'    => 'TEXT',
                'email'       => 'TEXT',
                'password'    => 'TEXT',
                'role'        => 'TEXT DEFAULT "staff"',
                'permissions' => 'TEXT',
                'active'      => 'INTEGER DEFAULT 1',
                'last_login'  => 'TEXT',
                'updated_at'  => 'TEXT',
                'created_at'  => 'TEXT',
            ],
            'medicines' => [
                'id'              => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'name'            => 'TEXT',
                'generic_name'    => 'TEXT',
                'company'         => 'TEXT',
                'category'        => 'TEXT',
                'custom_category' => 'TEXT',
                'hsn_code'        => 'TEXT',
                'gst_percent'     => 'REAL DEFAULT 12',
                'description'     => 'TEXT',
                'rack_location'   => 'TEXT',
                'updated_at'      => 'TEXT',
                'created_at'      => 'TEXT',
            ],
            'categories' => [
                'id'   => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'name' => 'TEXT',
                'type' => 'TEXT',
            ],
            'batches' => [
                'id'             => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'medicine_id'    => 'INTEGER',
                'batch_no'       => 'TEXT',
                'mfg_date'       => 'TEXT',
                'expiry_date'    => 'TEXT',
                'quantity'       => 'INTEGER DEFAULT 0',
                'purchase_price' => 'REAL',
                'mrp'            => 'REAL',
                'supplier_id'    => 'INTEGER',
                'updated_at'     => 'TEXT',
                'created_at'     => 'TEXT',
            ],
            'suppliers' => [
                'id'         => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'name'       => 'TEXT',
                'contact'    => 'TEXT',
                'phone'      => 'TEXT',
                'email'      => 'TEXT',
                'address'    => 'TEXT',
                'gst_no'     => 'TEXT',
                'dl_no'      => 'TEXT',
                'updated_at' => 'TEXT',
                'created_at' => 'TEXT',
            ],
            'supplier_users' => [
                'id'          => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'supplier_id' => 'INTEGER',
                'name'        => 'TEXT',
                'username'    => 'TEXT',
                'email'       => 'TEXT',
                'phone'       => 'TEXT',
                'password'    => 'TEXT',
                'active'      => 'INTEGER DEFAULT 1',
                'verified'    => 'INTEGER DEFAULT 0',
                'last_login'  => 'TEXT',
                'updated_at'  => 'TEXT',
                'created_at'  => 'TEXT',
            ],
            'customers' => [
                'id'             => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'name'           => 'TEXT',
                'phone'          => 'TEXT',
                'email'          => 'TEXT',
                'address'        => 'TEXT',
                'dob'            => 'TEXT',
                'password'       => 'TEXT',
                'active'         => 'INTEGER DEFAULT 1',
                'verified'       => 'INTEGER DEFAULT 0',
                'verify_token'   => 'TEXT',
                'verify_sent_at' => 'TEXT',
                'verified_at'    => 'TEXT',
                'last_login'     => 'TEXT',
                'updated_at'     => 'TEXT',
                'created_at'     => 'TEXT',
            ],
            'sales' => [
                'id'              => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'customer_id'     => 'INTEGER',
                'sale_date'       => 'TEXT',
                'total_amount'    => 'REAL',
                'gst_amount'      => 'REAL',
                'discount_amount' => 'REAL',
                'discount_id'     => 'INTEGER',
                'grand_total'     => 'REAL',
                'payment_method'  => 'TEXT',
                'upi_ref'         => 'TEXT',
                'cheque_no'       => 'TEXT',
                'cheque_bank'     => 'TEXT',
                'cheque_date'     => 'TEXT',
                'created_by'      => 'INTEGER',
                'created_at'      => 'TEXT',
            ],
            'sales_items' => [
                'id'          => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'sale_id'     => 'INTEGER',
                'medicine_id' => 'INTEGER',
                'batch_id'    => 'INTEGER',
                'quantity'    => 'INTEGER',
                'mrp'         => 'REAL',
                'price'       => 'REAL',
            ],
            'purchase_orders' => [
                'id'          => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'supplier_id' => 'INTEGER',
                'po_date'     => 'TEXT',
                'status'      => 'TEXT DEFAULT "pending"',
                'total'       => 'REAL',
                'notes'       => 'TEXT',
                'shipped_at'  => 'TEXT',
                'received_at' => 'TEXT',
                'updated_at'  => 'TEXT',
                'created_at'  => 'TEXT',
            ],
            'po_items' => [
                'id'           => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'po_id'        => 'INTEGER',
                'medicine_id'  => 'INTEGER',
                'quantity'     => 'INTEGER',
                'price'        => 'REAL',
                'mrp'          => 'REAL',
                'received_qty' => 'INTEGER DEFAULT 0',
                'updated_at'   => 'TEXT',
            ],
            'returns' => [
                'id'             => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'sale_id'        => 'INTEGER',
                'reason'         => 'TEXT',
                'refund_amount'  => 'REAL',
                'status'         => 'TEXT DEFAULT "pending"',
                'stock_adjusted' => 'INTEGER DEFAULT 0',
                'requested_by'   => 'TEXT',
                'customer_id'    => 'INTEGER',
                'created_by'     => 'INTEGER',
                'updated_at'     => 'TEXT',
                'created_at'     => 'TEXT',
            ],
            'return_items' => [
                'id'           => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'return_id'    => 'INTEGER',
                'sale_item_id' => 'INTEGER',
                'quantity'     => 'INTEGER',
                'price'        => 'REAL',
            ],
            'discounts' => [
                'id'         => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'name'       => 'TEXT',
                'type'       => 'TEXT',
                'value'      => 'REAL',
                'min_amount' => 'REAL DEFAULT 0',
                'active'     => 'INTEGER DEFAULT 1',
                'updated_at' => 'TEXT',
                'created_at' => 'TEXT',
            ],
            'stock_adjustments' => [
                'id'          => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'batch_id'    => 'INTEGER',
                'medicine_id' => 'INTEGER',
                'type'        => 'TEXT',
                'quantity'    => 'INTEGER',
                'reason'      => 'TEXT',
                'old_qty'     => 'INTEGER',
                'new_qty'     => 'INTEGER',
                'user_id'     => 'INTEGER',
                'created_at'  => 'TEXT',
            ],
            'login_attempts' => [
                'id'      => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'ip'      => 'TEXT',
                'context' => 'TEXT',
                'ts'      => 'TEXT',
            ],
            'customer_purchase_log' => [
                'id'          => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'customer_id' => 'INTEGER',
                'sale_id'     => 'INTEGER',
                'amount'      => 'REAL',
                'date'        => 'TEXT',
            ],
            'supplier_messages' => [
                'id'            => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'supplier_id'   => 'INTEGER',
                'supplier_name' => 'TEXT',
                'sender_email'  => 'TEXT',
                'subject'       => 'TEXT',
                'message'       => 'TEXT',
                'direction'     => 'TEXT DEFAULT "in"',
                'reply'         => 'TEXT',
                'replied_at'    => 'TEXT',
                'status'        => 'TEXT DEFAULT "unread"',
                'created_at'    => 'TEXT',
            ],
            'patient_messages' => [
                'id'           => 'INTEGER PRIMARY KEY AUTOINCREMENT',
                'customer_id'  => 'INTEGER NOT NULL',
                'direction'    => 'TEXT DEFAULT "in"',
                'message'      => 'TEXT',
                'file_path'    => 'TEXT',
                'file_name'    => 'TEXT',
                'file_type'    => 'TEXT',
                'is_read'      => 'INTEGER DEFAULT 0',
                'created_by'   => 'INTEGER',
                'created_at'   => 'TEXT',
            ],
        ];
    }

    private static function indexDefinitions(): array {
        return [
            'sales'                 => ['idx_sq_sale_date'=>'sale_date', 'idx_sq_cust'=>'customer_id', 'idx_sq_sales_created'=>'created_at'],
            'sales_items'           => ['idx_sq_si_sale'=>'sale_id', 'idx_sq_si_med'=>'medicine_id', 'idx_sq_si_batch'=>'batch_id'],
            'batches'               => ['idx_sq_batches_med'=>'medicine_id', 'idx_sq_batches_exp'=>'expiry_date', 'idx_sq_batches_qty'=>'quantity'],
            'medicines'             => ['idx_sq_med_name'=>'name', 'idx_sq_med_cat'=>'category'],
            'purchase_orders'       => ['idx_sq_po_sup'=>'supplier_id', 'idx_sq_po_date'=>'po_date', 'idx_sq_po_status'=>'status'],
            'po_items'              => ['idx_sq_poi_po'=>'po_id', 'idx_sq_poi_med'=>'medicine_id'],
            'returns'               => ['idx_sq_ret_sale'=>'sale_id', 'idx_sq_ret_cust'=>'customer_id', 'idx_sq_ret_status'=>'status'],
            'return_items'          => ['idx_sq_ri_ret'=>'return_id', 'idx_sq_ri_si'=>'sale_item_id'],
            'customers'             => ['idx_sq_cust_email'=>'email', 'idx_sq_cust_phone'=>'phone'],
            'users'                 => ['idx_sq_users_email'=>'email', 'idx_sq_users_username'=>'username'],
            'supplier_users'        => ['idx_sq_su_sup'=>'supplier_id', 'idx_sq_su_un'=>'username'],
            'stock_adjustments'     => ['idx_sq_sa_batch'=>'batch_id', 'idx_sq_sa_med'=>'medicine_id'],
            'customer_purchase_log' => ['idx_sq_cpl_cust'=>'customer_id', 'idx_sq_cpl_sale'=>'sale_id'],
            'patient_messages'      => ['idx_sq_pm_cust'=>'customer_id', 'idx_sq_pm_read'=>'is_read'],
            'supplier_messages'     => ['idx_sq_sm_sup'=>'supplier_id', 'idx_sq_sm_status'=>'status'],
        ];
    }

    public static function createTables(PDO $pdo): void {
        foreach (self::tableSchema() as $name => $cols) {
            $defs = implode(', ', array_map(fn($c, $d) => "\"{$c}\" {$d}", array_keys($cols), $cols));
            $pdo->exec("CREATE TABLE IF NOT EXISTS \"{$name}\" ({$defs})");
        }
    }

    private function migrate(): void {
        foreach (self::tableSchema() as $tbl => $cols) {
            $defs = implode(', ', array_map(fn($c, $d) => "\"{$c}\" {$d}", array_keys($cols), $cols));
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS \"{$tbl}\" ({$defs})");

            $existing = [];
            $pragma = $this->pdo->query("PRAGMA table_info(\"{$tbl}\")")->fetchAll();
            foreach ($pragma as $colInfo) {
                $existing[] = strtolower($colInfo['name']);
            }

            foreach ($cols as $col => $def) {
                if (strtolower($col) === 'id') continue;
                if (!in_array(strtolower($col), $existing)) {
                    try {
                        $this->pdo->exec("ALTER TABLE \"{$tbl}\" ADD COLUMN \"{$col}\" {$def}");
                    } catch (Exception $e) {}
                }
            }
        }

        foreach (self::indexDefinitions() as $tbl => $indexes) {
            foreach ($indexes as $idxName => $col) {
                try {
                    $this->pdo->exec("CREATE INDEX IF NOT EXISTS \"{$idxName}\" ON \"{$tbl}\" ({$col})");
                } catch (Exception $e) {}
            }
        }
    }

    public function table(string $t): array {
        try {
            return $this->pdo->query("SELECT * FROM \"{$t}\"")->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function insert(string $t, array $row): int {
        unset($row['id']);
        $row = $this->sanitize($row);
        if (empty($row)) return 0;
        $cols = implode(',', array_map(fn($k) => "\"{$k}\"", array_keys($row)));
        $phs  = implode(',', array_fill(0, count($row), '?'));
        $stmt = $this->pdo->prepare("INSERT INTO \"{$t}\" ({$cols}) VALUES ({$phs})");
        $stmt->execute(array_values($row));
        return (int)$this->pdo->lastInsertId();
    }

    public function updateById(string $t, int $id, array $p): int {
        unset($p['id']);
        $p = $this->sanitize($p);
        if (empty($p)) return 0;
        $sets = implode(',', array_map(fn($k) => "\"{$k}\"=?", array_keys($p)));
        $stmt = $this->pdo->prepare("UPDATE \"{$t}\" SET {$sets} WHERE id=?");
        $stmt->execute([...array_values($p), $id]);
        return $stmt->rowCount();
    }

    public function deleteById(string $t, int $id): int {
        return $this->pdo->prepare("DELETE FROM \"{$t}\" WHERE id=?")->execute([$id]) ? 1 : 0;
    }

    public function update(string $t, callable $w, array $p): int {
        unset($p['id']);
        $p = $this->sanitize($p);
        if (empty($p)) return 0;

        $rows = $this->table($t);
        $sets = implode(',', array_map(fn($k) => "\"{$k}\"=?", array_keys($p)));
        $vals = array_values($p);
        $n = 0;
        $stmt = $this->pdo->prepare("UPDATE \"{$t}\" SET {$sets} WHERE id=?");

        foreach ($rows as $r) {
            if ($w($r)) {
                $stmt->execute([...$vals, $r['id']]);
                $n++;
            }
        }
        return $n;
    }

    public function delete(string $t, callable $w): int {
        $rows = $this->table($t);
        $n = 0;
        $stmt = $this->pdo->prepare("DELETE FROM \"{$t}\" WHERE id=?");
        foreach ($rows as $r) {
            if ($w($r)) {
                $stmt->execute([$r['id']]);
                $n++;
            }
        }
        return $n;
    }

    public function find(string $t, ?callable $w = null): array {
        $rows = $this->table($t);
        return $w ? array_values(array_filter($rows, $w)) : $rows;
    }

    public function findOne(string $t, callable $w): ?array {
        foreach ($this->table($t) as $r) {
            if ($w($r)) return $r;
        }
        return null;
    }

    public function count(string $t, ?callable $w = null): int {
        if ($w === null) {
            try {
                return (int)$this->pdo->query("SELECT COUNT(*) FROM \"{$t}\"")->fetchColumn();
            } catch (Exception $e) {
                return 0;
            }
        }
        return count($this->find($t, $w));
    }

    public function sum(string $t, string $f, ?callable $w = null): float {
        if ($w === null) {
            try {
                return (float)($this->pdo->query("SELECT COALESCE(SUM(\"{$f}\"),0) FROM \"{$t}\"")->fetchColumn() ?? 0);
            } catch (Exception $e) {
                return 0.0;
            }
        }
        return (float)array_sum(array_column($this->find($t, $w), $f));
    }

    public function flush(string $t): void {}

    private function sanitize(array $row): array {
        foreach ($row as $k => &$v) {
            if ($v === true)  $v = 1;
            if ($v === false) $v = 0;
            if ($v === '' && preg_match('/(_at|_date|dob|last_login|mfg_date|expiry_date|cheque_date|received_at|shipped_at|verified_at)$/', $k)) {
                $v = null;
            }
        }
        unset($v);
        return $row;
    }
}
