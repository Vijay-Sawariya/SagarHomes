<?php
include 'db_connect.php';


$lead_id = $_GET['lead_id'] ?? null;

// Add new scheduled visit
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_visit'])) {
  $property = $_POST['property'];
  $visitDate = $_POST['visitDate'];
  $status = $_POST['status'] ?? 'Visit Scheduled';
  $stmt = $conn->prepare("INSERT INTO scheduled_visits (property, visit_date, status) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $property, $visitDate, $status);
  $stmt->execute();
}

// Update visited property status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_status'])) {
  $id = $_POST['visit_id'];
  $status = $_POST['status'];
  $stmt = $conn->prepare("UPDATE visited_properties SET status=? WHERE id=?");
  $stmt->bind_param("si", $status, $id);
  $stmt->execute();
}

// Fetch data
$visited = $conn->query("SELECT * FROM visited_properties ORDER BY visit_date DESC");
$scheduled = $conn->query("SELECT * FROM scheduled_visits ORDER BY visit_date ASC");

// Fetch leads to populate property dropdown
$leads_result = $conn->query("SELECT lead_id, area, bhk FROM leads ORDER BY lead_id DESC");


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Plan Property Visits</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .section {
      background-color: #fff;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.06);
    }
  </style>
</head>
<body class="p-4">

<div class="container">
  <h2 class="mb-4">Plan Property Visits</h2>

  <!-- Visited Properties Section -->
  <div class="section">
    <h4>1. Visited Properties</h4>
    <table class="table table-bordered mt-3 align-middle">
      <thead class="table-light">
        <tr>
          <th>Property ID</th>
          <th>Location</th>
          <th>BHK</th>
          <th>Visit Date</th>
          <th>Status</th>
          <th>Update</th>
        </tr>
      </thead>
      <tbody>
        <?php while($v = $visited->fetch_assoc()): ?>
        <tr>
          <td><?= $v['property_id'] ?></td>
          <td><?= $v['location'] ?></td>
          <td><?= $v['bhk'] ?></td>
          <td><?= $v['visit_date'] ?></td>
          <form method="POST">
            <td>
              <select class="form-select form-select-sm" name="status">
                <option value="">Select Status</option>
                <option <?= $v['status'] == "Interested" ? 'selected' : '' ?>>Interested</option>
                <option <?= $v['status'] == "Not Interested" ? 'selected' : '' ?>>Not Interested</option>
                <option <?= $v['status'] == "Need Second Visit" ? 'selected' : '' ?>>Need Second Visit</option>
              </select>
            </td>
            <td>
              <input type="hidden" name="visit_id" value="<?= $v['id'] ?>">
              <button type="submit" name="update_status" class="btn btn-sm btn-outline-primary">Save</button>
            </td>
          </form>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Schedule Visit Section -->
  <div class="section">
    <h4>2. Schedule Next Visit</h4>
    <form method="POST" class="row g-3">
      <input type="hidden" name="add_visit" value="1">
      
<div class="col-md-4">
  <label class="form-label">Select Property (from Leads)</label>
  <select class="form-select" name="property" required>
    <option value="">-- Select Property --</option>
    <?php while($lead = $leads_result->fetch_assoc()): ?>
      <?php
        $property_label = "{$lead['area']} - {$lead['bhk']} BHK";
      ?>
      <option value="<?= htmlspecialchars($property_label) ?>"><?= htmlspecialchars($property_label) ?></option>
    <?php endwhile; ?>
  </select>
</div>

      <div class="col-md-4">
        <label class="form-label">Visit Date</label>
        <input type="date" class="form-control" name="visitDate" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Expected Status</label>
        <select class="form-select" name="status">
          <option value="Visit Scheduled">Visit Scheduled</option>
          <option value="Awaiting Confirmation">Awaiting Confirmation</option>
        </select>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-success">Add to Visit List</button>
      </div>
    </form>

    <hr class="my-4">
    <h5>Upcoming Scheduled Visits</h5>
    <table class="table table-bordered mt-3">
      <thead class="table-light">
        <tr><th>Property</th><th>Visit Date</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php while($s = $scheduled->fetch_assoc()): ?>
        <tr>
          <td><?= $s['property'] ?></td>
          <td><?= $s['visit_date'] ?></td>
          <td><?= $s['status'] ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
