<?php
include 'db_connect.php';

// Get lead info
$lead_id = $_GET['id'] ?? 0;
$lead = $conn->query("SELECT * FROM leads WHERE lead_id = $lead_id")->fetch_assoc();
if (!$lead) die("Lead not found.");

$lead_type = strtolower($lead['lead_type']);
$lead_area = $lead['area'];
$lead_price = $lead['price'];
$lead_size = $lead['area_size'];
$lead_property_type = $lead['property_type'];


$lead_area = mysqli_real_escape_string($conn, $lead_area);
$lead_property_type = mysqli_real_escape_string($conn, $lead_property_type);

$min_price = $lead_price * 0.98;
$max_price = $lead_price * 1.02;
$min_size = $lead_size - 100;
$max_size = $lead_size + 100;

// Match logic
$match_sql = "";
if ($lead_type === "sell") {
  // Match Buyers
  $match_sql = "SELECT * FROM leads 
    WHERE 
        lead_type = 'buy'
        AND (FIND_IN_SET('$lead_area', area) > 0
        OR FIND_IN_SET('$lead_property_type', property_type) > 0
        OR (price BETWEEN $min_price AND $max_price))  ";
} else if ($lead_type === "buy" || $lead_type === "rent") {
  // Match Sellers
  $match_sql = "
    SELECT * FROM leads 
    Where
   lead_type = 'sell'
        AND (FIND_IN_SET('$lead_area', area) > 0
        OR FIND_IN_SET('$lead_property_type', property_type) > 0
        OR (price BETWEEN $min_price AND $max_price)
        OR (area_size BETWEEN $min_size AND $max_size))  ";
}

/*var_dump($lead_area);
print_r($match_sql);*/
$matches = $conn->query($match_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Lead & Matches</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.hidden { display: none; }</style>
</head>
<body class="p-4 bg-light">
  <div class="container">
    <h2 class="mb-4">Lead Details</h2>

    <!-- Lead Info -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">Lead Information</div>
      <div class="card-body row">
        <div class="col-md-4"><strong>Name:</strong> <?= htmlspecialchars($lead['name']) ?></div>
        <div class="col-md-4"><strong>Phone:</strong> <?= htmlspecialchars($lead['phone']) ?></div>
        <div class="col-md-4"><strong>Email:</strong> <?= htmlspecialchars($lead['email']) ?></div>
        <div class="col-md-4"><strong>Lead Type:</strong> <?= ucfirst($lead_type) ?></div>
        <div class="col-md-4"><strong>Interested Area:</strong> <?= htmlspecialchars($lead['area']) ?></div>
        <div class="col-md-4"><strong>Property Type:</strong> <?= htmlspecialchars($lead['property_type']) ?></div>
        <div class="col-md-4"><strong>Price:</strong> ₹<?= number_format($lead['price']) ?> <?= htmlspecialchars($lead['unit']) ?></div>
        <div class="col-md-4"><strong>Area Size:</strong> <?= $lead['area_size'] ?> sq.ft</div>
      </div>
    </div>

    <!-- Matching Leads -->
    <div class="card mb-4">
      <div class="card-header bg-<?= $lead_type === 'sell' ? 'info' : 'success' ?> text-white">
        <?= $lead_type === 'sell' ? 'Matching Buyers' : 'Matching Sellers' ?>
      </div>
      <div class="card-body">
        <form>
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Select</th>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Area</th>
                <th>BHK</th>
                <th>Price</th>
                <th>Size</th>
                <th>Property Type</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($matches && $matches->num_rows > 0): ?>
                <?php while($m = $matches->fetch_assoc()): ?>
                <tr>
                  <td><input type="checkbox" name="match_ids[]" value="<?= $m['lead_id'] ?>"></td>
                  <td><?= $m['lead_id'] ?></td>
                  <td><?= htmlspecialchars($m['name']) ?></td>
                  <td><?= htmlspecialchars($m['phone']) ?></td>
                  <td><?= $m['area'] ?></td>
                  <td><?= $m['bhk'] ?></td>
                  <td>₹<?= number_format($m['price']) ?> <?= htmlspecialchars($m['unit']) ?></td>
                  <td><?= $m['area_size'] ?> sq.ft</td>
                  <td><?= $m['property_type'] ?></td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="9" class="text-center">No matching leads found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </form>
      </div>
    </div>

    <!-- Action Buttons -->
    <a href="edit_lead.php?id=<?= $lead['lead_id'] ?>" class="btn btn-primary">Edit Lead</a>
    
      <a href="followups.php?lead_id=<?= $lead['lead_id'] ?>" class="btn btn-primary" onclick="window.open(this.href, 'followups', 'width=1000,height=700'); return false;" class="btn btn-outline-info"> View/Add Follow-Ups </a> 

   <a href="Visited_Properties.php?lead_id=<?= $lead['lead_id'] ?>" class="btn btn-primary" onclick="window.open(this.href, 'VisitedProperties', 'width=1000,height=700'); return false;" class="btn btn-outline-info"> View/Manage Property Visits</a>
   <a href="index.php" class="btn btn-secondary ms-2">Back</a>
  </div>
</body>
</html>
<?php $conn->close(); ?>
