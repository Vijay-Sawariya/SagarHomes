<?php
include 'db_connect.php';

$id = $_GET['id'] ?? 0;
$lead = $conn->query("SELECT * FROM leads WHERE lead_id = $id")->fetch_assoc();
if (!$lead) die("Lead not found.");

$all_areas = ["Hauz Khas", "Greater Kailash", "Golf Links", "Vasant Vihar", "Chanakyapuri", "Jor Bagh", "Defence Colony", "New Friends Colony", "Sunder Nagar", "Anand Niketan", "Shanti Niketan", "Panchsheel Park"];
$all_property_types = ["Builder Flat", "Under Construction", "Ready to Move", "Society Flat"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $name = $_POST['name'] ?? '';
  $phone = $_POST['phone'] ?? '';
  $email = $_POST['email'] ?? '';
  $lead_type = $_POST['lead_type'] ?? '';
  $area = $_POST['area'] ?? '';
  $areas = isset($_POST['areas']) ? implode(', ', $_POST['areas']) : '';
  $bhk = $_POST['bhk'] ?? '';
  $floor = $_POST['floor'] ?? 0;
  $price = $_POST['price'] ?? 0;
  $unit = $_POST['unit'] ?? '';
  $area_size = $_POST['area_size'] ?? 0;
  $car_parking = $_POST['car_parking_number'] ?? 0;
  $lift = $_POST['lift_available'] ?? '';
  $property_type = $_POST['property_type'] ?? '';
  $propertyTypes = isset($_POST['property_types']) ? implode(', ', $_POST['property_types']) : '';
  $lead_status = $_POST['lead_status'] ?? '';
  $notes = $_POST['notes'] ?? '';
  $followup_date = $_POST['followup_date'];
  $followup_remarks = $_POST['followup_remarks'];
  $followup_status = $_POST['followup_status'];

  $stmt = $conn->prepare("UPDATE leads SET name=?, phone=?, email=?, lead_type=?, area=?, bhk=?, floor=?, price=?,unit=?, area_size=?, car_parking_number=?, lift_available=?, property_type=?, lead_status=?, notes=?, followup_date=?, followup_remarks=?, followup_status=? WHERE lead_id=?");
  $stmt->bind_param("ssssssidsdsssssssii", $name, $phone, $email, $lead_type, $areas, $bhk, $floor, $price,$unit, $area_size, $car_parking, $lift, $propertyTypes, $lead_status, $notes, $followup_date, $followup_remarks, $followup_status, $id);
  $stmt->execute();
  $stmt->close();
  header("Location: View_Lead.php?id=$id");
  exit;
}

$selected_areas = array_map('trim', explode(',', $lead['area']));
$selected_types = array_map('trim', explode(',', $lead['property_type']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Lead</title>
    <!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Custom Style -->
<link href="Style/style.css" rel="stylesheet">
  </head>
<body class="p-4">

<div class="d-flex">
  <?php include 'navbar.php'; ?>

 
<div class="container-fluid lead-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="mb-4">Edit Lead</h2>

</div>
  <form method="POST">

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($lead['name']) ?>" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Phone</label>
        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($lead['phone']) ?>" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($lead['email']) ?>">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Lead Type</label>
        <select class="form-select" name="lead_type">
          <option value="buy" <?= $lead['lead_type'] === 'buy' ? 'selected' : $lead['lead_type'] ?>>Buy</option>
          <option value="sell" <?= $lead['lead_type'] === 'sell' ? 'selected' : $lead['lead_type'] ?>>Sell</option>
          <option value="rent" <?= $lead['lead_type'] === 'rent' ? 'selected' : $lead['lead_type'] ?>>Rent</option>
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Interested Areas</label>
      <select class="form-select" name="areas[]" id="interestedAreas" multiple>
        <?php foreach ($all_areas as $area): ?>
          <option value="<?= $area ?>" <?= in_array($area, $selected_areas) ? 'selected' : '' ?>><?= $area ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <hr>
    <h5 class="mb-3">Property Information</h5>
    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label">BHK</label>
        <input type="text" name="bhk" class="form-control" value="<?= $lead['bhk'] ?>">
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Floor</label>
        <input type="number" name="floor" class="form-control" value="<?= $lead['floor'] ?>">
      </div>
      <div class="col-md-4 mb-3">
       <label class="form-label" for="amount"> In Figure <span style="font-size: 11px; color: #6c757d;">(Ex: Lakh, Crores)</span>
        <div class="input-group">
        <input type="text" class="form-control" id="amount_display" placeholder="Enter amount" oninput="formatIndian(this)" value="<?= $lead['price'] ?>">
        <input type="hidden" name="price" id="amount_raw" value="<?= $lead['price'] ?>">
        <select class="form-select" name="unit">
         <option value="">Select</option>
         <option value="Thousand"<?= $lead['unit'] === 'Thousand' ? 'selected' : $lead['unit'] ?>>Thousands</option>
          <option value="Lakh" <?= $lead['unit'] === 'Lakh' ? 'selected' : $lead['unit'] ?>>Lakhs</option>
          <option value="Crore" <?= $lead['unit'] === 'Crore' ? 'selected' : $lead['unit'] ?>>CRs</option>
        </select>
  </div>
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Area Size (sq.ft.)</label>
        <input type="number" name="area_size" class="form-control" value="<?= $lead['area_size']  ?>">
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Allotted Car Parking Number</label>
        <input type="text" name="car_parking_number" class="form-control" value="<?= $lead['car_parking_number'] ?>">
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Lift Available?</label>
        <select class="form-select" name="lift_available">
          <option value="Yes" <?= $lead['lift_available'] === 'Yes' ? 'selected' : '' ?>>Yes</option>
          <option value="No" <?= $lead['lift_available'] === 'No' ? 'selected' : '' ?>>No</option>
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Property Types</label>
      <select class="form-select" name="property_types[]" id="propertyTypes" multiple>
        <?php foreach ($all_property_types as $type): ?>
          <option value="<?= $type ?>" <?= in_array($type, $selected_types) ? 'selected' : '' ?>><?= $type ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Lead Status</label>
      <select name="lead_status" class="form-select">
        <option <?= $lead['lead_status'] === 'New' ? 'selected' : '' ?>>New</option>
        <option <?= $lead['lead_status'] === 'Site Visit' ? 'selected' : '' ?>>Site Visit</option>
        <option <?= $lead['lead_status'] === 'Converted' ? 'selected' : '' ?>>Converted</option>
        <option <?= $lead['lead_status'] === 'Not Valid' ? 'selected' : '' ?>>Not Valid</option>
        <option <?= $lead['lead_status'] === 'Close' ? 'selected' : '' ?>>Close</option>
        <option <?= $lead['lead_status'] === 'Done' ? 'selected' : '' ?>>Done</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Notes</label>
      <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($lead['notes']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Update Lead</button>
    <a href="followups.php?lead_id=<?= $lead['lead_id'] ?>" class="btn btn-primary" onclick="window.open(this.href, 'followups', 'width=1000,height=700'); return false;" class="btn btn-outline-info"> View/Add Follow-Ups </a> 
    <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
   
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $('#interestedAreas').select2({
      placeholder: "Select interested area(s)",
      allowClear: true
    });
    $('#propertyTypes').select2({
      placeholder: "Select property type(s)",
      allowClear: true
    });
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
</script>
</div>
</div>
</body>
</html>
<?php $conn->close(); ?>
