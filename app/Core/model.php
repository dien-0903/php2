<?php

class Model
{
    protected $db;

    public function __construct()
    {
        $host     = $_ENV['HOST'] ?? '127.0.0.1';
        $database = $_ENV['DATABASE'] ?? 'php-lop';
        $username = $_ENV['USERNAME'] ?? 'root';
        $password = $_ENV['PASSWORD'] ?? '';
        $charset  = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    
            PDO::ATTR_EMULATE_PREPARES   => false,                
        ];

        try {
            $this->db = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die("Lỗi kết nối Cơ sở dữ liệu: " . $e->getMessage());
        }
    }

    public function query(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function paginate(
        string $table, 
        int $page = 1, 
        int $limit = 10, 
        string $search = '', 
        array $filters = [], 
        string $searchCol = 'name',
        string $customSelect = 'main_t.*',
        string $customJoin = ''
    ) {
        $offset = ($page - 1) * $limit;
        $params = [];
        $where = " WHERE main_t.deleted_at IS NULL";

        if (!empty($search)) {
            $where .= " AND main_t.{$searchCol} LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        foreach ($filters as $col => $value) {
            if ($value !== '' && $value !== null) {
                $parts = explode(' ', trim($col));
                $operator = $parts[1] ?? '=';
                $pureCol = $parts[0];
                
                $paramKey = "f_" . str_replace('.', '_', $pureCol);
                $where .= " AND main_t.{$pureCol} {$operator} :{$paramKey}";
                $params[":{$paramKey}"] = $value;
            }
        }

        $sql = "SELECT $customSelect FROM $table main_t $customJoin $where 
                ORDER BY main_t.id DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) FROM $table main_t $customJoin $where";
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) { $countStmt->bindValue($k, $v); }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return [
            'data'        => $data,
            'total'       => $total,
            'totalPages'  => ceil($total / $limit)
        ];
    }

    public function checkExists(string $table, string $column, $value, $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM $table WHERE $column = ? AND deleted_at IS NULL";
        $params = [$value];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->query($sql, $params);
        return (int)$stmt->fetchColumn() > 0;
    }
}