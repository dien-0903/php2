<?php

class Product extends Model {
    
    protected $table = 'products';

    public function list($page = 1, $limit = 8, $search = '', $filters = []) {

        return $this->paginate(
            $this->table,
            $page,
            $limit,
            $search,
            $filters,
            'name', 
            "main_t.*, c.name as category_name, b.name as brand_name",
            "LEFT JOIN category c ON main_t.category_id = c.id 
             LEFT JOIN brands b ON main_t.brand_id = b.id"
        );
    }

    public function show($id) {
        $sql = "SELECT main_t.*, c.name as category_name, b.name as brand_name 
                FROM {$this->table} main_t 
                LEFT JOIN category c ON main_t.category_id = c.id 
                LEFT JOIN brands b ON main_t.brand_id = b.id 
                WHERE main_t.id = ? AND main_t.deleted_at IS NULL LIMIT 1";
        
        return $this->query($sql, [$id])->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, price, image, category_id, brand_id, created_at) 
                VALUES (:name, :price, :image, :category_id, :brand_id, NOW())";
        
        return $this->query($sql, [
            'name'        => $data['name'],
            'price'       => $data['price'],
            'image'       => $data['image'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'brand_id'    => $data['brand_id'] ?? null
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                name = :name, 
                price = :price, 
                image = :image, 
                category_id = :category_id, 
                brand_id = :brand_id,
                updated_at = NOW()
                WHERE id = :id";
        
        $params = [
            'id'          => $id,
            'name'        => $data['name'],
            'price'       => $data['price'],
            'image'       => $data['image'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'brand_id'    => $data['brand_id'] ?? null
        ];

        return $this->query($sql, $params);
    }

    public function delete($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }

    public function exists($name, $excludeId = null) {
        return $this->checkExists($this->table, 'name', $name, $excludeId);
    }
}