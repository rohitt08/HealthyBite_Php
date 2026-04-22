<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'includes/db.php';
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$orders  = [];

$stmt = $conn->prepare("SELECT id, total_amount, delivery_fee, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();

while ($order = $orders_result->fetch_assoc()) {
    $items_stmt = $conn->prepare("SELECT item_name, quantity, price, image FROM order_items WHERE order_id = ?");
    $items_stmt->bind_param("i", $order['id']);
    $items_stmt->execute();
    $order['items'] = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $items_stmt->close();
    $orders[] = $order;
}
$stmt->close();
?>

<section class="orders-section">
    <div class="orders-page-header">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <h1>My Orders</h1>
    </div>

    <?php if (empty($orders)): ?>
        <div class="orders-empty">
            <div class="orders-empty-icon">🛒</div>
            <h2>No orders yet!</h2>
            <p>You haven't placed any orders. Start exploring our menu.</p>
            <a href="menu.php" class="btn btn-primary" style="margin-top: 2rem;">Browse Menu</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
                $status_class = 'status-confirmed';
                if ($order['status'] === 'Delivered') $status_class = 'status-delivered';
                elseif ($order['status'] === 'Pending') $status_class = 'status-pending';
                $date_formatted = date('d M Y, h:i A', strtotime($order['created_at']));
            ?>
            <div class="order-card">
                <div class="order-card-header">
                    <div class="order-id">Order <span>#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></span></div>
                    <div class="order-date">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <?php echo $date_formatted; ?>
                    </div>
                    <span class="order-status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                </div>

                <div class="order-items-list">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="order-item-row">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" class="order-item-img">
                            <div class="order-item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                            <div class="order-item-qty">× <?php echo (int)$item['quantity']; ?></div>
                            <div class="order-item-price">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-card-footer">
                    <span class="order-total-label">Total (incl. ₹<?php echo number_format($order['delivery_fee'], 0); ?> delivery)</span>
                    <span class="order-total-amount">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
