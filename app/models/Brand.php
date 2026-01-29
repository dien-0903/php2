<?php

class Brand extends Model {
    
    protected $table = 'brands';

    public function list($page = 1, $limit = 8, $search = '') {
        return $this->paginate(
            $this->table,
            $page,
            $limit,
            $search,
            [],
            'name' 
        );
    }

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY name ASC";
        return $this->query($sql)->fetchAll();
    }

    public function show($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL LIMIT 1";
        return $this->query($sql, [$id])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, image, description, created_at) 
                VALUES (:name, :image, :description, NOW())";
        
        return $this->query($sql, [
            'name'        => $data['name'],
            'image'       => $data['image'] ?? 'default.jpg',
            'description' => $data['description'] ?? null
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                name = :name, 
                image = :image, 
                description = :description, 
                updated_at = NOW() 
                WHERE id = :id";
        
        return $this->query($sql, [
            'id'          => $id,
            'name'        => $data['name'],
            'image'       => $data['image'],
            'description' => $data['description'] ?? null
        ]);
    }

    public function delete($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }

    public function exists($name, $excludeId = null) {
        return $this->checkExists($this->table, 'name', $name, $excludeId);
    }
}