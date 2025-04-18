<?php
include 'db_connect.php';

// Fetch dynamic dropdown values
$leadTypes = $conn->query("SELECT DISTINCT lead_type FROM leads");
$areas = $conn->query("SELECT DISTINCT area FROM leads");
$agents = $conn->query("SELECT DISTINCT agent_name FROM leads");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Real Estate LMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Custom Style -->
<link href="Style/style.css" rel="stylesheet">
</head>
<body class="p-4">
  <?php include 'navbar.php'; ?>
<div class="container">
  <h1 class="mb-4">Real Estate Lead Management System</h1>
   
 <!-- Leads Table -->
 
  <div class="card p-4">
     <div class="lead-title">Lead List</div>
        <div id="leadsTable"></div>
    
<hr>
      <!-- Follow-Up Table -->
  <div class="lead-title">Follow-Up List</div>
  <div class="card p-4">
    <form id="filterForm">
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <label class="form-label">Follow-Up Date</label>
          <input type="date" class="form-control" name="followup_date" />
        </div>
        <div class="col-md-2">
          <label class="form-label">Lead Type</label>
          <select class="form-select" name="lead_type">
            <option value="">All</option>
            <?php while ($lt = $leadTypes->fetch_assoc()): ?>
              <option value="<?= $lt['lead_type'] ?>"><?= $lt['lead_type'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Area</label>
          <select class="form-select" name="area">
            <option value="">All Areas</option>
            <?php while ($ar = $areas->fetch_assoc()): ?>
              <option value="<?= $ar['area'] ?>"><?= $ar['area'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Agent</label>
          <select class="form-select" name="agent_name">
            <option value="">All Agents</option>
            <?php while ($ag = $agents->fetch_assoc()): ?>
              <option value="<?= $ag['agent_name'] ?>"><?= $ag['agent_name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
      </div>
    </form>

    <div id="followupTable"></div>
  </div>
  </div>


    </div>


</div>

<script>
$(document).ready(function () {
  loadFollowUps(); // Initial load
    loadLeads();

  $('#filterForm').on('submit', function (e) {
    e.preventDefault();
    loadFollowUps();
    loadLeads();
  });

  function loadFollowUps() {
    $.ajax({
      url: "fetch_filtered_leads.php",
      type: "GET",
      data: $('#filterForm').serialize(),
      success: function (data) {
        $('#followupTable').html(data);
        print_r(data);
      }
    });
  }
  function loadLeads() {
    $.ajax({
      url: "view_all_leads.php",
      type: "GET",
      data: $('#filterForm').serialize(),
      success: function (data) {
        $('#leadsTable').html(data);
         print_r(data);
      }
    });
  }
});


</script>
</body>
</html>
