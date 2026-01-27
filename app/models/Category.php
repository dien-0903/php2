<?php

class Category extends Model {
    protected $table = 'category';

    public function list($page = 1, $limit = 10, $search = '') {
        return $this->paginate($this->table, $page, $limit, $search, [], 'name');
    }

    public function getAll() {
        return $this->query("SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY name ASC")->fetchAll();
    }

    public function show($id) {
        return $this->query("SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$id])->fetch();
    }

    public function isDuplicate($name, $excludeId = null) {
        return $this->checkExists($this->table, 'name', $name, $excludeId);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, image, created_at) VALUES (:name, :image, NOW())";
        return $this->query($sql, [
            'name'  => $data['name'],
            'image' => $data['image'] ?? 'default.jpg'
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET name = :name, image = :image, updated_at = NOW() WHERE id = :id";
        return $this->query($sql, [
            'id'    => $id,
            'name'  => $data['name'],
            'image' => $data['image']
        ]);
    }

    public function delete($id) {
        return $this->query("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?", [$id]);
    }
}