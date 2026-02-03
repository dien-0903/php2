<?php

class Product extends Model {
    
    protected $table = 'products';

    // Hàm lấy danh sách sản phẩm (Hỗ trợ phân trang, tìm kiếm, lọc, sắp xếp)
    public function list($page = 1, $limit = 9, $search = '', $sort = 'newest', $categoryId = '', $minPrice = 0, $maxPrice = 0) {
        $offset = ($page - 1) * $limit;
        $params = [];

        // JOIN bảng 'category' và 'brands'
        // p.* sẽ lấy tất cả các cột bao gồm cả stock và description nếu có trong DB
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p 
                LEFT JOIN category c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.deleted_at IS NULL";

        // Lọc theo từ khóa
        if (!empty($search)) {
            $sql .= " AND p.name LIKE ?";
            $params[] = "%$search%";
        }

        // Lọc theo danh mục
        if (!empty($categoryId)) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        // Lọc theo giá
        if ($minPrice > 0) {
            $sql .= " AND p.price >= ?";
            $params[] = $minPrice;
        }
        if ($maxPrice > 0) {
            $sql .= " AND p.price <= ?";
            $params[] = $maxPrice;
        }

        // Sắp xếp
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY p.name ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY p.id DESC";
                break;
        }

        $sqlLimit = $sql . " LIMIT $offset, $limit";
        $data = $this->query($sqlLimit, $params)->fetchAll();

        // Đếm tổng số bản ghi (Logic đếm đơn giản để phân trang)
        $countSql = "SELECT COUNT(*) as total FROM products p WHERE p.deleted_at IS NULL";
        $countParams = [];
        if (!empty($search)) {
            $countSql .= " AND p.name LIKE ?";
            $countParams[] = "%$search%";
        }
        if (!empty($categoryId)) {
            $countSql .= " AND p.category_id = ?";
            $countParams[] = $categoryId;
        }
        
        // Lưu ý: Nếu cần lọc chính xác số trang theo giá, cần thêm điều kiện giá vào đây như hàm list
        if ($minPrice > 0) {
            $countSql .= " AND p.price >= ?";
            $countParams[] = $minPrice;
        }
        if ($maxPrice > 0) {
            $countSql .= " AND p.price <= ?";
            $countParams[] = $maxPrice;
        }

        $totalRecords = $this->query($countSql, $countParams)->fetch()['total'];
        $totalPages = ceil($totalRecords / $limit);

        return [
            'data' => $data,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalRecords' => $totalRecords
        ];
    }

    public function show($id) {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM {$this->table} p 
                LEFT JOIN category c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id = ? AND p.deleted_at IS NULL";
        return $this->query($sql, [$id])->fetch();
    }

    // Hàm lấy sản phẩm theo danh sách ID (cho Wishlist/Compare)
    public function getByIds($ids) {
        if (empty($ids)) return [];
        
        $ids = array_values($ids); 
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM {$this->table} p 
                LEFT JOIN category c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id IN ($placeholders) AND p.deleted_at IS NULL";
        
        return $this->query($sql, $ids)->fetchAll();
    }

    public function getRelated($categoryId, $excludeId, $limit = 4) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category_id = ? AND id != ? AND deleted_at IS NULL
                ORDER BY RAND() 
                LIMIT $limit";
        return $this->query($sql, [$categoryId, $excludeId])->fetchAll();
    }

    // --- CẬP NHẬT: Thêm trường stock và description vào hàm CREATE ---
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, price, stock, image, category_id, brand_id, description, created_at) 
                VALUES (:name, :price, :stock, :image, :category_id, :brand_id, :description, NOW())";
        
        $this->query($sql, [
            'name'        => $data['name'], 
            'price'       => $data['price'], 
            'stock'       => $data['stock'] ?? 0, // Mặc định là 0 nếu không nhập
            'image'       => $data['image'] ?? null,
            'category_id' => $data['category_id'] ?? null, 
            'brand_id'    => $data['brand_id'] ?? null,
            'description' => $data['description'] ?? null
        ]);
        
        // Trả về ID vừa tạo để Controller có thể chuyển hướng đúng
        return $this->db->lastInsertId();
    }

    // --- CẬP NHẬT: Thêm trường stock và description vào hàm UPDATE ---
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                name = :name, 
                price = :price, 
                stock = :stock,
                image = :image, 
                category_id = :category_id, 
                brand_id = :brand_id, 
                description = :description,
                updated_at = NOW() 
                WHERE id = :id";
        
        return $this->query($sql, [
            'id'          => $id, 
            'name'        => $data['name'], 
            'price'       => $data['price'], 
            'stock'       => $data['stock'] ?? 0,
            'image'       => $data['image'] ?? null,
            'category_id' => $data['category_id'] ?? null, 
            'brand_id'    => $data['brand_id'] ?? null,
            'description' => $data['description'] ?? null
        ]);
    }

    public function delete($id) {
        return $this->query("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?", [$id]);
    }

    public function exists($name, $excludeId = null) {
        return $this->checkExists($this->table, 'name', $name, $excludeId);
    }
}