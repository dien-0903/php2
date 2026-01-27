<?php

class Color extends Model {
    protected $table = 'colors';

    public function getAll() {
        return $this->query("SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY name ASC")->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, hex_code, created_at) VALUES (?, ?, NOW())";
        return $this->query($sql, [$data['name'], $data['hex_code'] ?? null]);
    }

    public function delete($id) {
        return $this->query("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?", [$id]);
    }
}