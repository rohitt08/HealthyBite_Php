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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    } else {
        $action = '';
    }
    
    $session_id = session_id();
    
    if ($action === 'add') {
        $item_id = intval($_POST['item_id']);
        
        $check_stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND menu_item_id = ?");
        $check_stmt->bind_param("si", $session_id, $item_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $new_qty = $row['quantity'] + 1;
            $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $new_qty, $row['id']);
            $update_stmt->execute();
        } else {
            $insert_stmt = $conn->prepare("INSERT INTO cart (session_id, menu_item_id, quantity) VALUES (?, ?, 1)");
            $insert_stmt->bind_param("si", $session_id, $item_id);
            $insert_stmt->execute();
        }
    } else if ($action === 'remove') {
        $item_id = intval($_POST['item_id']);
        $delete_stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ? AND menu_item_id = ?");
        $delete_stmt->bind_param("si", $session_id, $item_id);
        $delete_stmt->execute();
    } else if ($action === 'update_qty') {
        $item_id = intval($_POST['item_id']);
        $quantity = intval($_POST['quantity']);
        if ($quantity > 0) {
            $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE session_id = ? AND menu_item_id = ?");
            $update_stmt->bind_param("isi", $quantity, $session_id, $item_id);
            $update_stmt->execute();
        } else {
            $delete_stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ? AND menu_item_id = ?");
            $delete_stmt->bind_param("si", $session_id, $item_id);
            $delete_stmt->execute();
        }
    }
    
    $cc_query = $conn->prepare("SELECT SUM(quantity) as t_count FROM cart WHERE session_id = ?");
    $cc_query->bind_param("s", $session_id);
    $cc_query->execute();
    $res = $cc_query->get_result();
    $row = $res->fetch_assoc();
    $cart_count = $row['t_count'] ?? 0;
    
    echo json_encode(['status' => 'success', 'cart_count' => $cart_count]);
    exit;
}
echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>
