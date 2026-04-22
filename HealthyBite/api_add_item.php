<?php
if (!isset($_SESSION)) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

include_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $image = isset($_POST['image']) ? $_POST['image'] : 'assets/hero.png';
    $raw_tags = isset($_POST['tags']) ? $_POST['tags'] : '';
    
    $tags_array = array();
    $exploded = explode(',', $raw_tags);
    foreach($exploded as $tag) {
        $trimmed = trim($tag);
        if(!empty($trimmed)) {
            $tags_array[] = $trimmed;
        }
    }
    
    $tags_json = json_encode(array_values($tags_array));
    
    if (empty($name) || empty($category) || $price <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO menu_items (name, category, description, price, image, tags) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdss", $name, $category, $description, $price, $image, $tags_json);
    
    if ($stmt->execute()) {
        $new_id = $stmt->insert_id;
        
        $data_file_path = __DIR__ . '/includes/data.php';
        if (file_exists($data_file_path)) {
            $current_data = file_get_contents($data_file_path);
            $current_data = preg_replace('/\]\s*\];\s*\?>\s*$/s', '', $current_data);
            
            $tags_php = empty($tags_array) ? '[]' : '["' . implode('", "', $tags_array) . '"]';
            
            $safe_name = addslashes($name);
            $safe_cat = addslashes($category);
            $safe_desc = addslashes($description);
            $safe_img = addslashes($image);
            
            $new_item_php = "    ],\n    [\n        \"id\" => $new_id,\n        \"name\" => \"$safe_name\",\n        \"category\" => \"$safe_cat\",\n        \"description\" => \"$safe_desc\",\n        \"price\" => " . number_format($price, 2, '.', '') . ",\n        \"image\" => \"$safe_img\",\n        \"tags\" => $tags_php\n    ]\n];\n?>";
            
            file_put_contents($data_file_path, $current_data . $new_item_php);
        }

        echo json_encode([
            'status' => 'success', 
            'item' => [
                'id' => $new_id,
                'name' => $name,
                'category' => $category,
                'description' => $description,
                'price' => $price,
                'image' => $image,
                'tags' => $tags_array
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DB error']);
    }
}
