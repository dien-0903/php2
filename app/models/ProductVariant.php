<?php

class ProductVariant extends Model {
    
    protected $table = 'product_variants';

    public function getByProduct($productId) {
        $sql = "SELECT v.*, c.name as color_name, s.name as size_name 
                FROM {$this->table} v
                LEFT JOIN colors c ON v.color_id = c.id
                LEFT JOIN sizes s ON v.size_id = s.id
                WHERE v.product_id = ? AND v.deleted_at IS NULL 
                ORDER BY v.id DESC";
        
        return $this->query($sql, [$productId])->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (product_id, color_id, size_id, sku, price, stock, image, created_at) 
                VALUES (:product_id, :color_id, :size_id, :sku, :price, :stock, :image, NOW())";
        
        return $this->query($sql, [
            'product_id' => $data['product_id'],
            'color_id'   => $data['color_id'],
            'size_id'    => $data['size_id'],
            'sku'        => $data['sku'],
            'price'      => $data['price'],
            'stock'      => $data['stock'],
            'image'      => $data['image']
        ]);
    }

    public function delete($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }

    public function skuExists($sku, $excludeId = null) {
        return $this->checkExists($this->table, 'sku', $sku, $excludeId);
    }
}