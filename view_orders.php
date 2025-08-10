<?php
include 'db.php';
$result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>GlowMart — Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4">All Orders</h2>
    <a href="index.html" class="btn btn-link mb-3">Back to Shop</a>
    <?php if ($result && $result->num_rows): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>ID</th><th>Name</th><th>Phone</th><th>Address</th><th>Items</th><th>Total</th><th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td>
                  <?php
                    $items = json_decode($row['cart_json'], true);
                    if (is_array($items)) {
                      echo "<ul class='mb-0'>";
                      foreach ($items as $it) {
                        $n = htmlspecialchars($it['name'] ?? 'Item');
                        $q = intval($it['quantity'] ?? 1);
                        $p = number_format(floatval($it['price'] ?? 0), 2);
                        echo "<li>{$n} x {$q} — \${$p}</li>";
                      }
                      echo "</ul>";
                    } else {
                      echo htmlspecialchars($row['cart_json']);
                    }
                  ?>
                </td>
                <td>$<?= htmlspecialchars(number_format($row['order_total'], 2)) ?></td>
                <td><?= htmlspecialchars($row['order_date']) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info">No orders yet.</div>
    <?php endif; ?>
  </div>
</body>
</html>
<?php $conn->close(); ?>
