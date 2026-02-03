<?php

class Address extends Model {
    protected $table = 'addresses';

    public function getByUser($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND deleted_at IS NULL ORDER BY is_default DESC, id DESC";
        return $this->query($sql, [$userId])->fetchAll();
    }

    public function create($data) {
        if (!empty($data['is_default'])) {
            $this->resetDefault($data['user_id']);
        }
        $sql = "INSERT INTO {$this->table} (user_id, recipient_name, phone, address, is_default, status, created_at) 
                VALUES (:user_id, :recipient_name, :phone, :address, :is_default, :status, NOW())";
        return $this->query($sql, [
            'user_id'        => $data['user_id'],
            'recipient_name' => $data['recipient_name'],
            'phone'          => $data['phone'],
            'address'        => $data['address'],
            'is_default'     => !empty($data['is_default']) ? 1 : 0,
            'status'         => $data['status'] ?? 1
        ]);
    }

    public function update($id, $data) {
        $current = $this->query("SELECT user_id FROM {$this->table} WHERE id = ?", [$id])->fetch();
        if (!$current) return false;
        if (!empty($data['is_default'])) $this->resetDefault($current['user_id']);

        $sql = "UPDATE {$this->table} SET 
                recipient_name = :recipient_name,
                phone = :phone,
                address = :address,
                is_default = :is_default,
                updated_at = NOW()
                WHERE id = :id";
        return $this->query($sql, [
            'id'             => $id,
            'recipient_name' => $data['recipient_name'],
            'phone'          => $data['phone'],
            'address'        => $data['address'],
            'is_default'     => !empty($data['is_default']) ? 1 : 0
        ]);
    }

    /**
     * FIX: Thêm hàm thay đổi trạng thái hoạt động
     */
    public function toggleStatus($id) {
        $sql = "UPDATE {$this->table} SET status = 1 - status WHERE id = ?";
        return $this->query($sql, [$id]);
    }

    public function delete($id) {
        return $this->query("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?", [$id]);
    }

    public function setDefault($id, $userId) {
        $this->resetDefault($userId);
        return $this->query("UPDATE {$this->table} SET is_default = 1 WHERE id = ?", [$id]);
    }

    private function resetDefault($userId) {
        $this->query("UPDATE {$this->table} SET is_default = 0 WHERE user_id = ?", [$userId]);
    }
}