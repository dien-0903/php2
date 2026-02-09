<?php

class Order extends Model {
    protected $table = 'orders';
    
    public function getRevenueStats() {
        $sql = "SELECT 
                    SUM(CASE WHEN status = 3 THEN total_amount ELSE 0 END) as total_revenue,
                    COUNT(id) as total_orders,
                    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as canceled_count
                FROM {$this->table}";
        return $this->query($sql)->fetch();
    }

    public function getRevenueLast7Days() {
        $sql = "SELECT DATE(created_at) as date, SUM(total_amount) as revenue 
                FROM {$this->table} 
                WHERE status = 3 AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(created_at) 
                ORDER BY date ASC";
        return $this->query($sql)->fetchAll();
    }

    public function getTopSellingProducts($limit = 5) {
        $sql = "SELECT product_name, SUM(quantity) as total_qty, SUM(quantity * price) as total_money
                FROM order_items
                GROUP BY product_id, product_name
                ORDER BY total_qty DESC
                LIMIT $limit";
        return $this->query($sql)->fetchAll();
    }

    public function getAllOrders($status = null, $search = '') {
        $sql = "SELECT o.*, u.fullname as user_name 
                FROM {$this->table} o 
                JOIN users u ON o.user_id = u.id 
                WHERE 1=1";
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (o.order_code LIKE ? OR o.recipient_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY o.created_at DESC";
        return $this->query($sql, $params)->fetchAll();
    }

    public function paginateAllOrders($page = 1, $limit = 5, $status = null, $search = '') {
        $offset = ($page - 1) * $limit;
        $params = [];
        $where = "WHERE 1=1";

        if ($status !== null && $status !== '') {
            $where .= " AND o.status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $where .= " AND (o.order_code LIKE ? OR o.recipient_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $totalSql = "SELECT COUNT(*) as total FROM {$this->table} o $where";
        $totalCount = $this->query($totalSql, $params)->fetch()['total'];
        $totalPages = ceil($totalCount / $limit);

        $dataSql = "SELECT o.*, u.fullname as user_name 
                    FROM {$this->table} o 
                    JOIN users u ON o.user_id = u.id 
                    $where 
                    ORDER BY o.created_at DESC 
                    LIMIT $limit OFFSET $offset";
        $data = $this->query($dataSql, $params)->fetchAll();

        return ['data' => $data, 'totalPages' => $totalPages, 'totalCount' => $totalCount];
    }

    public function updateStatus($id, $newStatus) {
        try {
            $this->db->beginTransaction();

            $order = $this->show($id);
            if (!$order) throw new Exception("Đơn hàng không tồn tại!");
            
            $oldStatus = (int)$order['status'];
            $newStatus = (int)$newStatus;

            if ($newStatus === 4 && $oldStatus !== 4) {
                $items = $this->getOrderItems($id);
                foreach ($items as $item) {
                    $qty = (int)$item['quantity'];
                    if (!empty($item['variant_id'])) {
                        $this->query("UPDATE product_variants SET stock = stock + ? WHERE id = ?", [$qty, $item['variant_id']]);
                    } else {
                        $this->query("UPDATE products SET stock = stock + ? WHERE id = ?", [$qty, $item['product_id']]);
                    }
                }
            }
            
            if ($oldStatus === 4 && $newStatus !== 4) {
                 $items = $this->getOrderItems($id);
                 foreach ($items as $item) {
                    $qty = (int)$item['quantity'];
                    if (!empty($item['variant_id'])) {
                        $check = $this->query("SELECT stock FROM product_variants WHERE id = ?", [$item['variant_id']])->fetch();
                        if ($check['stock'] < $qty) throw new Exception("Không đủ hàng trong kho để khôi phục đơn hàng!");
                        $this->query("UPDATE product_variants SET stock = stock - ? WHERE id = ?", [$qty, $item['variant_id']]);
                    } else {
                        $check = $this->query("SELECT stock FROM products WHERE id = ?", [$item['product_id']])->fetch();
                        if ($check['stock'] < $qty) throw new Exception("Không đủ hàng trong kho để khôi phục đơn hàng!");
                        $this->query("UPDATE products SET stock = stock - ? WHERE id = ?", [$qty, $item['product_id']]);
                    }
                 }
            }

            $sql = "UPDATE {$this->table} SET status = ?, updated_at = NOW() WHERE id = ?";
            $this->query($sql, [$newStatus, $id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function paginateByUser($userId, $page = 1, $limit = 5, $status = null, $search = '') {
        $offset = ($page - 1) * $limit;
        $params = [$userId];
        $where = "WHERE user_id = ?";
        if ($status !== null && $status !== '') { $where .= " AND status = ?"; $params[] = $status; }
        if (!empty($search)) { $where .= " AND (order_code LIKE ? OR recipient_name LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

        $totalSql = "SELECT COUNT(*) as total FROM {$this->table} $where";
        $totalCount = $this->query($totalSql, $params)->fetch()['total'];
        $totalPages = ceil($totalCount / $limit);

        $dataSql = "SELECT * FROM {$this->table} $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $data = $this->query($dataSql, $params)->fetchAll();

        return ['data' => $data, 'totalPages' => $totalPages, 'totalCount' => $totalCount];
    }

    public function show($id) {
        $sql = "SELECT o.*, u.fullname as user_name FROM {$this->table} o JOIN users u ON o.user_id = u.id WHERE o.id = ?";
        return $this->query($sql, [$id])->fetch();
    }

    public function getOrderItems($orderId) {
        return $this->query("SELECT * FROM order_items WHERE order_id = ?", [$orderId])->fetchAll();
    }

    public function createOrder($orderData, $cartItems) {
        try {
            $this->db->beginTransaction();
            $sqlOrder = "INSERT INTO {$this->table} (order_code, user_id, total_amount, recipient_name, phone, address, payment_method, note, status, created_at) 
                         VALUES (:code, :user_id, :total, :name, :phone, :address, :method, :note, 0, NOW())";
            $this->query($sqlOrder, $orderData);
            $orderId = $this->db->lastInsertId();

            foreach ($cartItems as $item) {
                $qty = (int)$item['quantity'];
                if (!empty($item['variant_id'])) {
                    $stockCheck = $this->query("SELECT stock FROM product_variants WHERE id = ? FOR UPDATE", [$item['variant_id']])->fetch();
                    if ($stockCheck['stock'] < $qty) throw new Exception("Sản phẩm {$item['name']} không đủ kho!");
                    $this->query("UPDATE product_variants SET stock = stock - ? WHERE id = ?", [$qty, $item['variant_id']]);
                } else {
                    $stockCheck = $this->query("SELECT stock FROM products WHERE id = ? FOR UPDATE", [$item['id']])->fetch();
                    if ($stockCheck['stock'] < $qty) throw new Exception("Sản phẩm {$item['name']} không đủ kho!");
                    $this->query("UPDATE products SET stock = stock - ? WHERE id = ?", [$qty, $item['id']]);
                }

                $this->query("INSERT INTO order_items (order_id, product_id, variant_id, product_name, product_image, variant_info, quantity, price) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
                    $orderId, $item['id'], $item['variant_id'] ?? null, $item['name'], $item['image'], $item['variant_info'] ?? '', $qty, $item['price']
                ]);
            }
            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}