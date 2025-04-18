<?php
include 'db_connect.php';

$lead_id = $_GET['lead_id'] ?? 0;
if (!$lead_id) die("Lead ID missing.");

// Add or update followup
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['lead_id'])) {
    $stmt = $conn->prepare("UPDATE tblfollowups SET followup_date=?, remarks=?, status=? WHERE id=? AND lead_id=?");
    $stmt->bind_param("sssii", $_POST['followup_date'], $_POST['remarks'], $_POST['status'], $_POST['id'], $lead_id);
  } else {
    $stmt = $conn->prepare("INSERT INTO tblfollowups (lead_id, followup_date, remarks, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $lead_id, $_POST['followup_date'], $_POST['remarks'], $_POST['status']);
  }
  $stmt->execute();
  header("Location: followups.php?lead_id=$lead_id");
  exit;
}

var_dump($lead_id);
print_r($match_sql);
$followups = $conn->query("SELECT * FROM tblfollowups WHERE lead_id = $lead_id ORDER BY followup_date DESC");
$lead = $conn->query("SELECT * FROM leads WHERE lead_id = $lead_id")->fetch_assoc();

//Print error
/*var_dump($lead_id);
print_r($followups);*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  
  <title>Follow-Ups for Lead #<?= $lead['area']  ?></title>
   <!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Custom Style -->
<link href="Style/style.css" rel="stylesheet">
  <script>
    function editFollowup(id, date, remarks, status) {
      document.getElementById('followup_id').value = id;
      document.getElementById('followup_date').value = date;
      document.getElementById('remarks').value = remarks;
      document.getElementById('status').value = status;
      document.getElementById('form-title').innerText = 'Edit Follow-Up';
    }
    function resetForm() {
      document.getElementById('followup_id').value = '';
      document.getElementById('followup_date').value = '';
      document.getElementById('remarks').value = '';
      document.getElementById('status').value = 'Pending';
      document.getElementById('form-title').innerText = 'Add New Follow-Up';
    }
  </script>
</head>
<body class="p-4">
  <?php include 'navbar.php'; ?>
<div class="container">
  <h2 class="mb-4">Follow-Ups for Lead #<?= $lead['area'] ?></h2>

  <div class="card mb-4">
    <div class="card-header">
      <span id="form-title">Add New Follow-Up</span>
    </div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="followup_id" id="followup_id">
        <div class="row g-3">
          <div class="col-md-4">
            <label>Date</label>
            <input type="date" name="followup_date" id="followup_date" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label>Remarks</label>
            <input type="text" name="remarks" id="remarks" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label>Status</label>
            <select name="status" id="status" class="form-select">
              <option value="Pending">Pending</option>
              <option value="Done">Done</option>
              <option value="Next Visit Scheduled">Next Visit Scheduled</option>
            </select>
          </div>
        </div>
        <div class="text-end mt-3">
          <button type="submit" class="btn btn-success">Save Follow-Up</button>
          <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">All Follow-Ups</div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead class="table-light">
          <tr>
            <th>Date</th>
            <th>Remarks</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while($f = $followups->fetch_assoc()): ?>
            <tr>
              <td><?= $f['followup_date'] ?></td>
              <td><?= htmlspecialchars($f['remarks']) ?></td>
              <td><?= $f['status'] ?></td>
              <td>
                <button class="btn btn-sm btn-outline-primary" onclick="editFollowup(<?= $f['id'] ?>, '<?= $f['followup_date'] ?>', '<?= htmlspecialchars($f['remarks'], ENT_QUOTES) ?>', '<?= $f['status'] ?>')">Edit</button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
