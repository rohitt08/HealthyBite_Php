<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'healthybite';

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($dbname);
} else {
    die("Error creating database: " . $conn->error);
}

$sql = "CREATE TABLE IF NOT EXISTS contacts (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

$sql_menu = "CREATE TABLE IF NOT EXISTS menu_items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(200),
    tags VARCHAR(200)
)";
$conn->query($sql_menu);

$check_menu = "SELECT COUNT(*) as count FROM menu_items";
$result = $conn->query($check_menu);
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    if(file_exists(__DIR__ . '/data.php')) {
        include_once __DIR__ . '/data.php';
        if(isset($menu_items)){
            $stmt = $conn->prepare("INSERT INTO menu_items (id, name, category, description, price, image, tags) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($menu_items as $item) {
                $tags = json_encode($item['tags']);
                $stmt->bind_param("isssdss", $item['id'], $item['name'], $item['category'], $item['description'], $item['price'], $item['image'], $tags);
                $stmt->execute();
            }
            $stmt->close();
        }
    }
}

$sql_cart = "CREATE TABLE IF NOT EXISTS cart (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    menu_item_id INT(6) UNSIGNED NOT NULL,
    quantity INT(4) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
)";
$conn->query($sql_cart);

$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql_users);

$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) DEFAULT 50.00,
    status VARCHAR(50) DEFAULT 'Confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($sql_orders);

$sql_order_items = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(10) UNSIGNED NOT NULL,
    menu_item_id INT(6) UNSIGNED NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    quantity INT(4) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(200),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
)";
$conn->query($sql_order_items);
?>
