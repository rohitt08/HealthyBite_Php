<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include_once 'includes/db.php';
include 'includes/header.php';

$session_id = session_id();
$cart_items = [];
$total_price = 0;

if (isset($conn)) {
    $stmt = $conn->prepare("
        SELECT c.id as cart_id, c.quantity, m.id as item_id, m.name, m.price, m.image, m.category 
        FROM cart c 
        JOIN menu_items m ON c.menu_item_id = m.id 
        WHERE c.session_id = ?
    ");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += ($row['price'] * $row['quantity']);
    }
}
?>


<section class="cart-section">
    <h1 class="cart-header">Your Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="cart-empty">
            <h2>Your cart is currently empty.</h2>
            <p style="margin-top: 1rem;"><a href="menu.php" class="btn btn-primary">Browse Our Menu</a></p>
        </div>
    <?php else: ?>
        <div class="cart-grid">
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item" data-id="<?php echo $item['item_id']; ?>">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-img">
                    <div class="cart-item-details">
                        <div class="cart-item-title"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="cart-item-price">₹<?php echo number_format($item['price'], 2); ?></div>
                        <div class="cart-item-actions">
                            <button class="qty-btn minus-qty">-</button>
                            <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>" readonly>
                            <button class="qty-btn plus-qty">+</button>
                            <button class="remove-btn remove-item">Remove</button>
                        </div>
                    </div>
                    <div style="font-weight: 700; color: var(--text-primary); margin-left: auto;">
                        ₹<span class="item-total"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <h3 class="summary-title">Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₹<?php echo number_format($total_price, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Delivery Fee</span>
                    <span>₹50.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>₹<?php echo number_format($total_price + 50, 2); ?></span>
                </div>
                <button class="btn btn-primary checkout-btn" id="checkoutBtn" onclick="placeOrder()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    Proceed to Checkout
                </button>
            </div>
        </div>
    <?php endif; ?>
</section>

<script>
function placeOrder() {
    const btn = document.getElementById('checkoutBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Placing Order...';

    fetch('place_order.php', { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                btn.innerHTML = '✅ Order Placed!';
                document.getElementById('cart-badge').style.display = 'none';
                setTimeout(() => { window.location.href = 'orders.php'; }, 900);
            } else {
                btn.disabled = false;
                btn.innerHTML = 'Proceed to Checkout';
                alert('Error: ' + data.message);
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = 'Proceed to Checkout';
            alert('Something went wrong. Please try again.');
        });
}
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<?php include 'includes/footer.php'; ?>
