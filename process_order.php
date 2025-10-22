<?php
// Accept JSON POST
header('Content-Type: application/json');

// Read raw POST body
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON payload"]);
    exit;
}

// Database config
$host   = "127.0.0.1";
$user   = "root";
$pass   = "";
$dbname = "customers_db";

// Connect to DB
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Prepare customer data
$name    = $conn->real_escape_string($data['customerName'] ?? '');
$mobile  = $conn->real_escape_string($data['mobile'] ?? '');
$email   = $conn->real_escape_string($data['email'] ?? '');
$address = $conn->real_escape_string($data['address'] ?? '');
$pincode = $conn->real_escape_string($data['pincode'] ?? '');
$total   = floatval($data['total'] ?? 0.0);

// Start transaction
$conn->begin_transaction();

try {
    // Insert customer
    $insertCustomer = $conn->query("
        INSERT INTO customers (name, mobile, email, address, pincode, created_at) 
        VALUES ('$name', '$mobile', '$email', '$address', '$pincode', NOW())
    ");
    if (!$insertCustomer) {
        throw new Exception("Error inserting customer: " . $conn->error);
    }
    $customer_id = $conn->insert_id;

    // Insert order
    $insertOrder = $conn->query("
        INSERT INTO orders (customer_id, total_amount, created_at) 
        VALUES ($customer_id, $total, NOW())
    ");
    if (!$insertOrder) {
        throw new Exception("Error inserting order: " . $conn->error);
    }
    $order_id = $conn->insert_id;

    // Insert order items
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $it) {
            $pid   = $conn->real_escape_string($it['id'] ?? '');
            $pname = $conn->real_escape_string($it['name'] ?? '');
            $price = floatval($it['price'] ?? 0);
            $qty   = intval($it['qty'] ?? 0);
            if ($qty <= 0) continue;

            $insertItem = $conn->query("
                INSERT INTO order_items (order_id, product_id, product_name, price, qty) 
                VALUES ($order_id, '$pid', '$pname', $price, $qty)
            ");
            if (!$insertItem) {
                throw new Exception("Error inserting order item: " . $conn->error);
            }
        }
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Order saved successfully"]);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
