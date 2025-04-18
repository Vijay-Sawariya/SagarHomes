<?php
include 'db_connect.php';

$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

  $stmt = $conn->prepare("INSERT INTO leads (name, phone, email, lead_type, area, bhk, floor, price, unit, area_size, car_parking_number, lift_available, property_type, lead_status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssidsdissss", $name, $phone, $email, $leadType, $finalArea, $bhk, $floor, $price, $unit, $areaSize, $carParking, $lift, $finalPropertyType, $leadStatus, $notes);

  if ($stmt->execute()) {
    $success = true;
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Lead</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Custom Style -->
<link href="Style/style.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="d-flex">
  <?php include 'navbar.php'; ?>
  <div class="container">
    <h2 align="center">Add Lead</h2>
        
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
      <div class="alert alert-success">
        Lead submitted successfully!
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Phone</label>
          <input type="tel" name="phone" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Lead Type</label>
          <select class="form-select" name="lead_type" id="leadType" required>
            <option value="" disabled selected>Select</option>
            <option value="buy">Buy</option>
            <option value="sell">Sell</option>
            <option value="rent">Rent</option>
          </select>
        </div>
      </div>

      <!-- Area -->
      <div class="mb-3" id="areaDropdownWrapper">
        <label class="form-label">Interested Area</label>
        <select class="form-select" name="area">
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

      <div class="mb-3 d-none" id="areaCheckboxesWrapper">
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

      <hr>
      <h5 class="mb-3">Property Information</h5>
      <div class="row">
        <div class="col-md-4 mb-3">

          <label class="form-label">BHK</label>
          <select class="form-select" name="bhk">
            <option value="">Select</option>
            <option value="1 BHK">1 BHK</option>
            <option value="2 BHK">2 BHK</option>
            <option value="3 BHK">3 BHK</option>
            <option value="4 BHK">4 BHK</option>
            <option value="5+ BHK">5+ BHK</option>
          </select>

          
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Floor</label>
          <input type="number" name="floor" class="form-control" min="0">
        </div>
        <div class="col-md-4 mb-3">
            
         <div class="mb-3">
           <label class="form-label" for="amount">
    In Figure <span style="font-size: 11px; color: #6c757d;">(Ex: Lakh, Crores)</span>
  </label>
  <div class="input-group">
    <input type="text" class="form-control" id="amount_display" placeholder="Enter amount" oninput="formatIndian(this)">
    <input type="hidden" name="price" id="amount_raw">
    <select class="form-select" name="unit">
     <option value="">Select</option>
     <option value="Thousand">Thousands</option>
      <option value="Lakh">Lakhs</option>
      <option value="Crore">CRs</option>
    </select>
  </div>
          </div>
          
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Area Size (sq.ft.)</label>
          <input type="number" name="area_size" class="form-control" min="0">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Allotted Car Parking Number</label>
          <input type="number" name="car_parking_number" class="form-control" min="0">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Lift Available?</label>
          <select class="form-select" name="lift_available">
            <option value="">Select</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
          </select>
        </div>

        <!-- Property Type -->
        <div class="col-md-12">
          <div class="mb-3" id="propertyTypeDropdownWrapper">
            <label class="form-label">Property Type</label>
            <select class="form-select" name="property_type">
              <option value="">Select</option>
              <option value="Builder Flat">Builder Flat</option>
              <option value="Under Construction">Under Construction</option>
              <option value="Ready to Move">Ready to Move</option>
              <option value="Plot">Plot</option>
              <option value="Society Flat">Society Flat</option>
            </select>
          </div>

          <div class="mb-3 d-none" id="propertyTypeCheckboxesWrapper">
            <label class="form-label">Property Types</label>
            <div class="checkbox-list row">
              <div class="col-md-6">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="property_types[]" value="Builder Flat"><label class="form-check-label">Builder Flat</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="property_types[]" value="Under Construction"><label class="form-check-label">Under Construction</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="property_types[]" value="Ready to Move"><label class="form-check-label">Ready to Move</label></div>
              </div>
              <div class="col-md-6">
                <div class="form-check"><input class="form-check-input" type="checkbox" name="property_types[]" value="Plot"><label class="form-check-label">Plot</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="property_types[]" value="Society Flat"><label class="form-check-label">Society Flat</label></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Lead Status -->
      <div class="mb-3">
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

      <button type="submit" class="btn btn-primary">Save Lead</button>
      <a href="index.php" class="btn btn-secondary ms-2">Back</a>
    </form>
  </div>

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
  </script>
</body>
</html>
