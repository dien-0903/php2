<?php

class Order extends Model {
    protected $table = 'orders';

    /**
     * DÀNH CHO ADMIN: Lấy toàn bộ đơn hàng kèm tên khách hàng
     */
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

    /**
     * DÀNH CHO ADMIN: Phân trang danh sách đơn hàng
     */
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

    /**
     * HÀM QUAN TRỌNG: Cập nhật trạng thái kèm HOÀN TỒN KHO nếu hủy đơn
     */
    public function updateStatus($id, $newStatus) {
        try {
            $this->db->beginTransaction();

            // 1. Lấy trạng thái hiện tại của đơn hàng
            $order = $this->show($id);
            if (!$order) throw new Exception("Đơn hàng không tồn tại!");
            
            $oldStatus = (int)$order['status'];
            $newStatus = (int)$newStatus;

            // 2. Logic hoàn kho: Nếu trạng thái chuyển sang Hủy (4) và trước đó chưa phải là Hủy
            if ($newStatus === 4 && $oldStatus !== 4) {
                $items = $this->getOrderItems($id);
                foreach ($items as $item) {
                    $qty = (int)$item['quantity'];
                    if (!empty($item['variant_id'])) {
                        // Cộng lại vào biến thể
                        $this->query("UPDATE product_variants SET stock = stock + ? WHERE id = ?", [$qty, $item['variant_id']]);
                    } else {
                        // Cộng lại vào sản phẩm thường
                        $this->query("UPDATE products SET stock = stock + ? WHERE id = ?", [$qty, $item['product_id']]);
                    }
                }
            }
            
            // Logic trừ kho lại: Nếu admin lỡ tay hủy rồi muốn "Khôi phục" lại đơn hàng
            if ($oldStatus === 4 && $newStatus !== 4) {
                 $items = $this->getOrderItems($id);
                 foreach ($items as $item) {
                    $qty = (int)$item['quantity'];
                    // Kiểm tra xem kho còn đủ để khôi phục không
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

            // 3. Cập nhật trạng thái đơn hàng
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