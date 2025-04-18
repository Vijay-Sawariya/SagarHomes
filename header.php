<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Lead Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color: #f4f6f9; }
    .card { border-radius: 10px; }
    .stat-box { font-size: 1.5rem; font-weight: bold; }
    .table thead th { background-color: #e9ecef; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">Lead Manager</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="add-lead.php">Add Lead</a></li>
          <li class="nav-item"><a class="nav-link" href="pending-leads.php">Pending Leads</a></li>
          <li class="nav-item"><a class="nav-link" href="followups.php">Follow-Ups</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container mt-4">
