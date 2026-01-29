<?php

class Coupon extends Model {
    protected $table = 'coupons';


    public function list($page = 1, $limit = 6, $search = '') {
        return $this->paginate($this->table, $page, $limit, $search, [], 'code');
    }

    public function findByCode($code) {
        return $this->query("SELECT * FROM {$this->table} WHERE code = ? AND deleted_at IS NULL LIMIT 1", [strtoupper($code)])->fetch();
    }

    public function show($id) {
        return $this->query("SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$id])->fetch();
    }

    public function exists($code, $excludeId = null) {
        return $this->checkExists($this->table, 'code', strtoupper($code), $excludeId);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (code, type, value, status, created_at) VALUES (:code, :type, :value, :status, NOW())";
        return $this->query($sql, [
            'code'   => strtoupper($data['code']),
            'type'   => $data['type'],
            'value'  => $data['value'],
            'status' => $data['status']
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET type = :type, value = :value, status = :status, updated_at = NOW() WHERE id = :id";
        return $this->query($sql, [
            'id'     => $id,
            'type'   => $data['type'],
            'value'  => $data['value'],
            'status' => $data['status']
        ]);
    }

    public function delete($id) {
        return $this->query("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?", [$id]);
    }

    public function getAvailableList($page = 1, $limit = 6, $search = '') {
        $offset = ($page - 1) * $limit;
        $params = [];
        
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND status = 1";
        

        if (!empty($search)) {
            $sql .= " AND code LIKE ?";
            $params[] = "%$search%";
        }

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL AND status = 1";
        if (!empty($search)) {
            $countSql .= " AND code LIKE ?";
        }

        $sql .= " ORDER BY id DESC LIMIT $offset, $limit";

        $data = $this->query($sql, $params)->fetchAll();
        
        $totalRow = $this->query($countSql, $params)->fetch()['total'] ?? 0;

        return [
            'data' => $data,
            'totalPages' => ceil($totalRow / $limit),
            'currentPage' => $page
        ];
    }
}