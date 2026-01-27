<?php

class Size extends Model {
    protected $table = 'sizes';

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY name ASC";
        return $this->query($sql)->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, created_at) VALUES (?, NOW())";
        return $this->query($sql, [$data['name']]);
    }

    public function delete($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }

    public function exists($name, $excludeId = null) {
        return $this->checkExists($this->table, 'name', $name, $excludeId);
    }
}