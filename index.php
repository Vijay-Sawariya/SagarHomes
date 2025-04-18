<?php
include 'db_connect.php';

// Fetch leads for the table
$sql = "SELECT * FROM leads ORDER BY followup_date ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Real Estate LMS</title>
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <!-- Bootstrap 5 CDN -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f8f9fa;
    }
    .container {
      margin-top: 50px;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s;
    }
    .card:hover {
      transform: scale(1.01);
    }
    .btn-primary {
      background-color: #0d6efd;
      border-color: #0d6efd;
    }
    .btn-primary:hover {
      background-color: #0b5ed7;
    }
    .table thead {
      background-color: #0d6efd;
      color: white;
    }
    .lead-title {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 20px;
      text-align: center;
      color: #343a40;
    }

  </style>
</head>
<body class="p-4">
  <div class="container">
    <h1 class="mb-4">Real Estate Lead Management System</h1>
    <div class="list-group">
      <a href="add_lead.php" class="list-group-item list-group-item-action">➕ Add New Lead</a>
      <a href="view_all_leads.php" class="list-group-item list-group-item-action">📋 View All Leads</a>
    </div>
  </div>

  <div class="container">
    <!-- <h2 class="mb-4">Follow-Up List</h2> -->
    <div class="lead-title">Follow-Up List</div>

    <div class="card p-4">
      <!-- Filters - static (non-functional for now) -->
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <label class="form-label">Follow-Up Date</label>
          <input type="date" class="form-control" />
        </div>
        <div class="col-md-2">
          <label class="form-label">Lead Type</label>
          <select class="form-select">
            <option value="">All</option>
            <option value="Buy">Buy</option>
            <option value="Sell">Sell</option>
            <option value="Rent">Rent</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Area</label>
          <select class="form-select">
            <option value="">All Areas</option>
            <option value="Vasant Vihar">Vasant Vihar</option>
            <option value="Chanakyapuri">Chanakyapuri</option>
            <option value="Greater Kailash">Greater Kailash</option>
            <option value="Hauz Khas">Hauz Khas</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Agent</label>
          <select class="form-select">
            <option value="">All Agents</option>
            <option value="Amit">Amit</option>
            <option value="Priya">Priya</option>
            <option value="Rohan">Rohan</option>
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-primary w-100">Filter</button>
        </div>
      </div>

      <!-- Leads Table -->
      <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Lead ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Follow-Up Date</th>
            <th>Type</th>
            <th>Area</th>
            <th>Agent</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): 
              $today = date("Y-m-d");
              $badge = '';
              if ($row['followup_date'] === $today) {
                $badge = '<span class="badge badge-today">Due Today</span>';
              } elseif ($row['followup_date'] < $today) {
                $badge = '<span class="badge badge-overdue">Overdue</span>';
              }
            ?>
            <tr>
              <td><?= htmlspecialchars($row['lead_id']) ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td><?= htmlspecialchars($row['followup_date']) ?></td>
              <td><?= htmlspecialchars($row['lead_type']) ?></td>
              <td><?= htmlspecialchars($row['area']) ?></td>
              <td><?= htmlspecialchars($row['agent_name']) ?></td>
              <td>
                <?= $badge ?>
                <select class="form-select mt-2">
                  <option value="">Update Status</option>
                  <option value="Interested" <?= $row['lead_status'] == "Interested" ? "selected" : "" ?>>Interested</option>
                  <option value="Not Interested" <?= $row['lead_status'] == "Not Interested" ? "selected" : "" ?>>Not Interested</option>
                  <option value="Visited" <?= $row['lead_status'] == "Visited" ? "selected" : "" ?>>Visited</option>
                  <option value="Follow-Up Later" <?= $row['lead_status'] == "Follow-Up Later" ? "selected" : "" ?>>Follow-Up Later</option>
                </select>
              </td>
              <td>
                <a href="view_lead.php?id=<?= $row['lead_id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                <a href="followups.php?lead_id=<?= $row['lead_id'] ?>" class="btn btn-sm btn-outline-primary" onclick="window.open(this.href, 'followups', 'width=1000,height=700'); return false;" class="btn btn-outline-info"> Follow-Ups </a> 
              </td>

            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="9" class="text-center">No leads found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    </div>
  </div>
</body>
</html>
<?php $conn->close(); ?>
