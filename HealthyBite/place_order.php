<?php
if (!isset($_SESSION)) {
    session_start();
}
include 'includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$user_id    = $_SESSION['user_id'];
$session_id = session_id();

$stmt = $conn->prepare("
    SELECT c.quantity, m.id as item_id, m.name, m.price, m.image
    FROM cart c
    JOIN menu_items m ON c.menu_item_id = m.id
    WHERE c.session_id = ?
");
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = array();
while($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
$stmt->close();

if (empty($cart_items)) {
    echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
    exit;
}

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$delivery_fee  = 50.00;
$total_amount  = $subtotal + $delivery_fee;

$conn->begin_transaction();

try {
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, delivery_fee, status) VALUES (?, ?, ?, 'Confirmed')");
    $order_stmt->bind_param("idd", $user_id, $total_amount, $delivery_fee);
    $order_stmt->execute();
    $order_id = $conn->insert_id;
    $order_stmt->close();

    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, price, image) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $item_stmt->bind_param("iisiis", $order_id, $item['item_id'], $item['name'], $item['quantity'], $item['price'], $item['image']);
        $item_stmt->execute();
    }
    $item_stmt->close();

    $clear_stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ?");
    $clear_stmt->bind_param("s", $session_id);
    $clear_stmt->execute();
    $clear_stmt->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'order_id' => $order_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Order failed: ' . $e->getMessage()]);
}
?>
