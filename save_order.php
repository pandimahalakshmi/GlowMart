<?php
header('Content-Type: application/json');
include 'db.php';

// read raw JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
    exit;
}

$cart = $data['cart'] ?? null;
$name = trim($data['customerName'] ?? '');
$email = trim($data['customerEmail'] ?? '');
$address = trim($data['customerAddress'] ?? '');

if (!is_array($cart) || count($cart) === 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Cart is empty or missing']);
    exit;
}

if (!$name || !$email || !$address) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing customer details']);
    exit;
}

// calculate total amount
$total = 0.0;
foreach ($cart as $item) {
    $price = floatval($item['price'] ?? 0);
    $qty = intval($item['quantity'] ?? 0);
    $total += $price * $qty;
}

$items_json = json_encode($cart);

$stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_address, items_json, total_amount, order_date) VALUES (?, ?, ?, ?, ?, NOW())");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ssssd", $name, $email, $address, $items_json, $total);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    echo json_encode(['status' => 'success', 'message' => 'Order saved', 'order_id' => $order_id]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database execute failed: ' . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}
?>
