<?php
include 'db.php';

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$cart_json = trim($_POST['cart_json'] ?? '');
$order_total = floatval($_POST['order_total'] ?? 0);

if ($name === '' || $phone === '' || $cart_json === '' || $order_total <= 0) {
    echo "Invalid input. <a href='checkout.html'>Back</a>";
    exit;
}

$json = json_decode($cart_json, true);
if (!is_array($json) || count($json) === 0) {
    echo "Cart data invalid. <a href='index.html'>Shop</a>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, address, cart_json, order_total) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssd", $name, $phone, $address, $cart_json, $order_total);

if ($stmt->execute()) {
    echo "<div style='text-align:center; margin-top:50px;'>
            <h2>Order placed successfully!</h2>
            <p>Thank you, " . htmlspecialchars($name) . ".</p>
            <a href='index.html'>Back to Shop</a>
          </div>
          <script>try { localStorage.removeItem('glowmart_cart'); } catch(e) {}</script>";
} else {
    echo "Error: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$conn->close();
?>
