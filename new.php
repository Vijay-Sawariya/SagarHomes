<?php
include 'db_connect.php';

$name = $phone = $email = $lead_type = $area = $property_type = $agent_name = '';
$edit_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = $_POST['edit_id'] ?? 0;
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $lead_type = $_POST['lead_type'];
    $area = $_POST['area'];
    $property_type = $_POST['property_type'];
    $agent_name = $_POST['agent_name'];

    if ($edit_id > 0) {
        $stmt = $conn->prepare("UPDATE leads SET name=?, phone=?, email=?, lead_type=?, area=?, property_type=?, agent_name=? WHERE lead_id=?");
        $stmt->bind_param("sssssssi", $name, $phone, $email, $lead_type, $area, $property_type, $agent_name, $edit_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO leads (name, phone, email, lead_type, area, property_type, agent_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $phone, $email, $lead_type, $area, $property_type, $agent_name);
    }

    if ($stmt->execute()) {
        header("Location: view_all_leads.php");
        exit;
    }
}

// Fetch all leads
$result = $conn->query("SELECT * FROM leads ORDER BY lead_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Leads</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f7f7f7; }
    .container { margin-top: 30px; }
    .modal-title { font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <h2 class="mb-4">All Leads</h2>
    <button class="btn btn-primary mb-3" onclick="openAddModal()">➕ Add New Lead</button>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Lead Type</th><th>Area</th><th>Property Type</th><th>Agent</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['lead_id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['lead_type'] ?></td>
            <td><?= $row['area'] ?></td>
            <td><?= $row['property_type'] ?></td>
            <td><?= $row['agent_name'] ?></td>
            <td>
              <button class="btn btn-sm btn-warning"
                onclick='openEditModal(<?= json_encode($row) ?>)'>Edit</button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Add/Edit Lead Modal -->
  <div class="modal fade" id="addLeadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form method="POST" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add/Edit Lead</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <input type="hidden" name="edit_id" id="edit_id">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" id="phone" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email">
          </div>
          <div class="col-md-6">
            <label class="form-label">Lead Type</label>
            <select class="form-select" name="lead_type" id="lead_type">
              <option value="Buy">Buy</option>
              <option value="Sell">Sell</option>
              <option value="Rent">Rent</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Area</label>
            <input type="text" class="form-control" name="area" id="area">
          </div>
          <div class="col-md-6">
            <label class="form-label">Property Type</label>
            <input type="text" class="form-control" name="property_type" id="property_type">
          </div>
          <div class="col-md-6">
            <label class="form-label">Agent Name</label>
            <input type="text" class="form-control" name="agent_name" id="agent_name">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Lead</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const modal = new bootstrap.Modal(document.getElementById('addLeadModal'));

    function openAddModal() {
      document.getElementById("edit_id").value = "";
      document.querySelectorAll("#addLeadModal input, #addLeadModal select").forEach(el => el.value = "");
      modal.show();
    }

    function openEditModal(lead) {
      document.getElementById("edit_id").value = lead.lead_id;
      document.getElementById("name").value = lead.name;
      document.getElementById("phone").value = lead.phone;
      document.getElementById("email").value = lead.email;
      document.getElementById("lead_type").value = lead.lead_type;
      document.getElementById("area").value = lead.area;
      document.getElementById("property_type").value = lead.property_type;
      document.getElementById("agent_name").value = lead.agent_name;
      modal.show();
    }
  </script>
</body>
</html>
