<?php

class User extends Model {
    
    protected $table = 'users';

    public function list($page = 1, $limit = 10, $search = '') {
        return $this->paginate(
            $this->table, 
            $page, 
            $limit, 
            $search, 
            [], 
            'fullname'
        );
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL LIMIT 1";
        return $this->query($sql, [$email])->fetch();
    }

    public function show($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL LIMIT 1";
        return $this->query($sql, [$id])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (fullname, email, password, role, created_at) 
                VALUES (:fullname, :email, :password, :role, NOW())";
 
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        return $this->query($sql, [
            'fullname' => $data['fullname'],
            'email'    => $data['email'],
            'password' => $hashedPassword,
            'role'     => $data['role'] ?? 'user'
        ]);
    }

    /**
     * FIX: Cập nhật hàm update để lưu được phone
     */
    public function update($id, $data) {
        // Lấy dữ liệu cũ để tránh làm mất các trường không truyền vào $data
        $currentUser = $this->show($id);

        $sql = "UPDATE {$this->table} SET 
                fullname = :fullname, 
                email = :email, 
                phone = :phone,  
                role = :role, 
                updated_at = NOW() 
                WHERE id = :id";
        
        $params = [
            'id'       => $id,
            'fullname' => $data['fullname'] ?? $currentUser['fullname'],
            'email'    => $data['email'] ?? $currentUser['email'],
            'phone'    => $data['phone'] ?? $currentUser['phone'],
            'role'     => $data['role'] ?? $currentUser['role']
        ];

        return $this->query($sql, $params);
    }

    public function updatePassword($email, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE {$this->table} SET password = ? WHERE email = ?";
        return $this->query($sql, [$hashed, $email]);
    }

    public function exists($email, $excludeId = null) {
        return $this->checkExists($this->table, 'email', $email, $excludeId);
    }

    public function delete($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }
}