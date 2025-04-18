<?php
include 'db_connect.php';

$name = $phone = $email = $lead_type = $area = $property_type = $agent_name = '';
$edit_id = 0;


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Update existing lead
 $edit_id = $_POST['edit_id'] ?? 0;
  $name = $_POST['name'] ?? '';
  $phone = $_POST['phone'] ?? '';
  $email = $_POST['email'] ?? '';
  $leadType = $_POST['lead_type'] ?? '';
  $area = $_POST['area'] ?? '';
  $areas = isset($_POST['areas']) ? implode(', ', $_POST['areas']) : '';
  $bhk = $_POST['bhk'] ?? '';
  $floor = $_POST['floor'] ?? 0;
  $price = $_POST['price'] ?? 0;
  $unit = $_POST['unit'] ?? '';
  $areaSize = $_POST['area_size'] ?? 0;
  $carParking = $_POST['car_parking_number'] ?? 0;
  $lift = $_POST['lift_available'] ?? '';
  $propertyType = $_POST['property_type'] ?? '';
  $propertyTypes = isset($_POST['property_types']) ? implode(', ', $_POST['property_types']) : '';
  $leadStatus = $_POST['lead_status'] ?? '';
  $notes = $_POST['notes'] ?? '';

  // Decide which area/property type to save based on lead type
  $finalArea = $leadType === 'sell' ? $area : $areas;
  $finalPropertyType = $leadType === 'buy' ? $propertyTypes : $propertyType;

    if ($edit_id > 0) {
          $stmt = $conn->prepare("UPDATE leads SET name=?, phone=?, email=?, lead_type=?, area=?, bhk=?, floor=?, price=?, unit=?, area_size=?, car_parking_number=?, lift_available=?, property_type=?, lead_status=?, notes=?, agent_name=?, followup_date=? WHERE lead_id=?");
          $stmt->bind_param("ssssssidsdissss", $name, $phone, $email, $leadType, $finalArea, $bhk, $floor, $price, $unit, $areaSize, $carParking, $lift, $finalPropertyType, $leadStatus, $notes);
        }
        else{
           $stmt = $conn->prepare("INSERT INTO leads (name, phone, email, lead_type, area, bhk, floor, price, unit, area_size, car_parking_number, lift_available, property_type, lead_status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssidsdissss", $name, $phone, $email, $leadType, $finalArea, $bhk, $floor, $price, $unit, $areaSize, $carParking, $lift, $finalPropertyType, $leadStatus, $notes);
        }

   if ($stmt->execute()) {
    $success = true;
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();



    //Print error

} 


// Setup
$search = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Base query
$where = "WHERE 1";
if ($search) {
  $search_safe = $conn->real_escape_string($search);
  $where .= " AND (name LIKE '%$search_safe%' OR phone LIKE '%$search_safe%')";
}
if ($type_filter) {
  $type_safe = $conn->real_escape_string($type_filter);
  $where .= " AND lead_type  = '$type_safe'";
}

// Get total for pagination
$total_res = $conn->query("SELECT COUNT(*) as total FROM leads $where");
$total = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

// Get leads
/*$result = $conn->query("SELECT * FROM leads $where ORDER BY followup_date DESC LIMIT $limit OFFSET $offset");*/


$sql = "SELECT 
    l.lead_id AS lead_id,
    l.name AS name,
    l.lead_type AS lead_type,
    l.phone AS phone,
    l.area AS area,
    l.agent_name AS agent_name,
    l.lead_status AS lead_status,
    l.created_at AS created_at,
    f.new_followup_date AS followup_date
  FROM 
    leads l
  LEFT JOIN (
    SELECT 
      lead_id,
      status,
      MAX(new_followup_date) AS new_followup_date
    FROM 
      tblfollowups
    GROUP BY 
      lead_id
  ) f ON l.lead_id = f.lead_id ORDER BY followup_date DESC LIMIT $limit OFFSET $offset
  ";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Actionable Leads</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="Style/style.css" rel="stylesheet">
  
</head>
<body class="p-4">
    <div class="d-flex">
      <?php include 'navbar.php'; ?>
   <div class="container-fluid lead-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>All Leads</h2>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeadModal">
        ➕ Add New Lead
      </button>
    </div>
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">Lead added successfully!</div>
    <?php endif; ?>

    <div class="card shadow">
      <div class="card-body">
         <table class="table table-bordered table-striped">
          <thead class="table-primary">
            <tr>
              <th>Lead ID</th>
              <th>Name</th>
              <th>Contact</th>
              <th>Lead Type</th>
              <th>Intersted Area</th>
              <th>Created Date</th>
              <th>Status</th>
              <th>Next Follow-Up date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
           
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>

          <td><?= $row['lead_id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['phone'] ?></td>
                <td><?= $row['lead_type'] ?></td>
                <td><?= $row['area'] ?></td>
                <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($row['lead_status']) ?></span></td>
                <td><?= date('Y-m-d', strtotime($row['followup_date'])) ?></td>
                <td>   
                    <a href="View_Lead.php?id=<?= $row['lead_id'] ?>" class="btn btn-sm btn-secondary">View</a>
                    <button class="btn btn-sm btn-warning"  onclick='openEditModal(<?= json_encode($row) ?>)'>Edit</button>
                  </td>
                </tr>
             
              <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>



    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <nav class="mt-4">
        <ul class="pagination justify-content-center">
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
              <a class="page-link" href="?search=<?= urlencode($search) ?>&type=<?= urlencode($type_filter) ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>

  <!-- Add Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1" aria-hidden="true">

  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addLeadModalLabel">Add New Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body row g-2">
        <div class="col-md-6">
            <input type="hidden" name="edit_id" id="edit_id">
          <label class="form-label">Name</label>
          <input name="name" id="name" required class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone</label>
          <input name="phone" id="phone" required class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input name="email" id="email" type="email" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Lead Type</label>
          <select name="lead_type" id="lead_type" class="form-select" required>
            <option value="">Select</option>
            <option value="Buy">Buy</option>
            <option value="Sell">Sell</option>
            <option value="Rent">Rent</option>
          </select>
        </div>




<hr>
      <h5 class="col-md-6">Property Information</h5>
<div class="row">
      <!-- Area -->
      <div class="col-md-6" id="areaDropdownWrapper">
        <label class="form-label">Interested Area</label>
        <select class="form-select" name="area" id="area">
          <option value="">Select</option>
          <option>Lutyens’ Bungalow Zone</option>
          <option>Vasant Vihar</option>
          <option>Chanakyapuri</option>
          <option>Jor Bagh</option>
          <option>Greater Kailash</option>
          <option>Hauz Khas</option>
          <option>Sunder Nagar</option>
          <option>Shanti Niketan</option>
          <option>Panchsheel Park</option>
          <option>Defence Colony</option>
          <option>New Friends Colony</option>
          <option>Golf Links</option>
          <option>Anand Niketan</option>
        </select>
      </div>

      <div class="col-md-6 d-none" id="areaCheckboxesWrapper">
        <label class="form-label">Interested Areas</label>
        <div class="checkbox-list row">
          <?php 
          $areasList = ["Lutyens’ Bungalow Zone", "Vasant Vihar", "Chanakyapuri", "Jor Bagh", "Greater Kailash", "Hauz Khas", "Sunder Nagar", "Shanti Niketan", "Panchsheel Park", "Defence Colony", "New Friends Colony", "Golf Links", "Anand Niketan"];
          foreach ($areasList as $i => $area) {
            if ($i % 4 === 0) echo '<div class="col-md-4">';
            echo '<div class="form-check"><input class="form-check-input" type="checkbox" value="'.$area.'" name="areas[]"><label class="form-check-label">'.$area.'</label></div>';
            if ($i % 4 === 3 || $i === count($areasList) - 1) echo '</div>';
          }
          ?>
        </div>
      </div>
      
        <div class="col-md-6">
          <label class="form-label">BHK</label>
          <select class="form-select" name="bhk" id="bhk">
            <option value="">Select</option>
            <option value="1 BHK">1 BHK</option>
            <option value="2 BHK">2 BHK</option>
            <option value="3 BHK">3 BHK</option>
            <option value="4 BHK">4 BHK</option>
            <option value="5+ BHK">5+ BHK</option>
          </select>

          </div>
        <div class="col-md-6">
          <label class="form-label">Floor</label>
          <input type="number" name="floor" id="floor" class="form-control" min="0">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Area Size (sq.ft.)</label>
          <input type="number" name="area_size" id="area_size" class="form-control" min="0">
        </div>
       
        
        
        <div class="col-md-6">
                <label class="form-label">Allotted Parking</label>
                <input type="number" name="car_parking_number" id="car_parking_number" class="form-control" min="0">
              </div>
        <div class="col-md-6">
          <label class="form-label">Lift Available?</label>
          <select class="form-select" name="lift_available" id="lift_available">
            <option value="">Select</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
          </select>
        </div>

           
  
        <div class="col-md-6">
        <label class="form-label" for="amount">  In Figure <span style="font-size: 11px; color: #6c757d;">(Ex: Lakh, Crores)</span> </label>
         <div class="input-group">
          <input type="text" class="mb-3 form-control width=50px" id="amount_display" placeholder="Enter amount" oninput="formatIndian(this)">
          <input type="hidden" name="price" id="amount_raw">
          <select class="mb-3 form-select" name="unit">
           <option value="">Select</option>
           <option value="Thousand">Thousands</option>
            <option value="Lakh">Lakhs</option>
            <option value="Crore">CRs</option>
          </select>
        </div>
          </div>
       

      <!-- Lead Status -->
      <div class="col-md-6">
        <label class="form-label">Lead Status</label>
        <select class="form-select" name="lead_status">
          <option>Select</option>
          <option>New</option>
          <option>Contacted</option>
          <option>Site Visit</option>
          <option>Converted</option>
          <option>Not Interested</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Notes</label>
        <textarea class="form-control" name="notes" rows="4"></textarea>
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
    const leadType = document.getElementById('leadType');
    const areaDropdownWrapper = document.getElementById('areaDropdownWrapper');
    const areaCheckboxesWrapper = document.getElementById('areaCheckboxesWrapper');
    const propertyTypeDropdownWrapper = document.getElementById('propertyTypeDropdownWrapper');
    const propertyTypeCheckboxesWrapper = document.getElementById('propertyTypeCheckboxesWrapper');

    leadType.addEventListener('change', () => {
      const isSell = leadType.value === 'sell';
      const isBuy = leadType.value === 'buy';

      areaDropdownWrapper.classList.toggle('d-none', !isSell);
      areaCheckboxesWrapper.classList.toggle('d-none', isSell);

      propertyTypeDropdownWrapper.classList.toggle('d-none', isBuy);
      propertyTypeCheckboxesWrapper.classList.toggle('d-none', !isBuy);
    });

  function formatIndian(input) {
  let clean = input.value.replace(/,/g, '');
  if (!isNaN(clean) && clean.length > 0) {
    let x = clean.split('.');
    let lastThree = x[0].substring(x[0].length - 3);
    let otherNumbers = x[0].substring(0, x[0].length - 3);
    if (otherNumbers != '')
      lastThree = ',' + lastThree;
    input.value = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + (x.length > 1 ? "." + x[1] : "");

    // Set raw value into hidden input
    document.getElementById("amount_raw").value = clean;
  } else {
    document.getElementById("amount_raw").value = '';
  }
}
 const modal = new bootstrap.Modal(document.getElementById('addLeadModal'));

 function openEditModal(lead) {
 console.log(lead);
      document.getElementById("edit_id").value = lead.lead_id;
      document.getElementById("name").value = lead.name;
      document.getElementById("phone").value = lead.phone;
      document.getElementById("email").value = lead.email;
      document.getElementById("lead_type").value = lead.lead_type;
      document.getElementById("area").value = lead.area;
     // document.getElementById("property_type").value = lead.property_type;
      //document.getElementById("agent_name").value = lead.agent_name;
      new bootstrap.Modal(document.getElementById("addLeadModal")).show();
    }
  </script>
</body>
</html>
