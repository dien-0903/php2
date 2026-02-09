<?php

class Contact extends Model {
    protected $table = 'contacts';

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (full_name, email, phone, subject, message) 
                VALUES (:full_name, :email, :phone, :subject, :message)";
        return $this->query($sql, $data);
    }

    public function list($page = 1, $limit = 5, $q = '') {
        $offset = ($page - 1) * $limit;
        $params = [];
        $where = "WHERE 1=1";

        if (!empty($q)) {
            $where .= " AND (full_name LIKE ? OR email LIKE ? OR subject LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        $totalAll = $this->query("SELECT COUNT(*) as total FROM {$this->table}")->fetch()['total'];

        $totalFiltered = $this->query("SELECT COUNT(*) as total FROM {$this->table} $where", $params)->fetch()['total'];

        $sqlData = "SELECT * FROM {$this->table} $where ORDER BY created_at DESC LIMIT $offset, $limit";
        $data = $this->query($sqlData, $params)->fetchAll();

        return [
            'data' => $data,
            'totalPages' => ceil($totalFiltered / $limit),
            'totalAll' => $totalAll,
            'totalFiltered' => $totalFiltered
        ];
    }

    public function delete($id) {
        return $this->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }
}