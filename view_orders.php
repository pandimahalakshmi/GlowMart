<?php
include 'db.php';
$result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>View Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style> body{padding:20px;font-family:Arial,Helvetica,sans-serif} th{background:#f8c8d2;color:#6a3f7c} </style>
</head>
<body>
  <h1>Orders</h1>
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Address</th><th>Items</th><th>Total</th><th>Date</th></tr></thead>
      <tbody>
        <?php if ($result && $result->num_rows): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?=htmlspecialchars($row['id'])?></td>
              <td><?=htmlspecialchars($row['customer_name'])?></td>
              <td><?=htmlspecialchars($row['customer_email'])?></td>
              <td><?=nl2br(htmlspecialchars($row['customer_address']))?></td>
              <td>
                <?php
                  $items = json_decode($row['items_json'], true);
                  if (is_array($items)) {
                    foreach($items as $it) {
                      echo htmlspecialchars($it['name']).' × '.intval($it['quantity']).' (₹'.number_format($it['price']*intval($it['quantity']),2).')<br>';
                    }
                  } else {
                    echo 'No items';
                  }
                ?>
              </td>
              <td>₹<?=number_format($row['total_amount'],2)?></td>
              <td><?=htmlspecialchars($row['order_date'])?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7">No orders found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
<?php $conn->close(); ?>
