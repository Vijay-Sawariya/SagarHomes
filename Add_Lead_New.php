<?php
// add_lead.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buyer Information Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      padding: 20px;
    }
    .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 15px;
    }
    label {
      margin-bottom: 5px;
    }
    input[type="text"],
    input[type="email"],
    textarea {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f0f0f0;
    }
    .dropdown-multiselect,
    .multiselect-content {
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 10px;
      background: #fff;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Buyer Information Form</h2>
    <form method="POST" action="submit_lead.php">
      <table>
        <tr>
          <td><label>Name</label></td>
          <td><input type="text" name="name" required></td>
          <td><label>Phone</label></td>
          <td><input type="text" name="phone" required></td>
        </tr>
        <tr>
          <td><label>Email</label></td>
          <td><input type="email" name="email"></td>
          <td><label>Property Type</label></td>
          <td>
            <div class="multiselect-content">
              <?php
              $types = ['Under Construction', 'ATM', 'Ready to Move', 'Fresh', 'Resale'];
              foreach ($types as $type) {
                echo "<label><input type='checkbox' name='property_type[]' value='$type'> $type</label><br>";
              }
              ?>
            </div>
          </td>
        </tr>
        <tr>
          <td><label>Requirement</label></td>
          <td><textarea name="requirement" rows="2"></textarea></td>
          <td><label>Preferable Location</label></td>
          <td>
            <?php
            $locations = ['South Delhi', 'North Delhi', 'East Delhi', 'West Delhi', 'Central Delhi', 'Dwarka', 'Rohini', 'Vasant Kunj', 'Karol Bagh', 'Lajpat Nagar', 'Greater Kailash', 'Mayur Vihar', 'Janakpuri'];
            foreach ($locations as $loc) {
              echo "<label><input type='checkbox' name='locations[]' value='$loc'> $loc</label><br>";
            }
            ?>
          </td>
        </tr>
        <tr>
          <td><label>Property Type</label></td>
          <td>
            <?php
            $propTypes = ['Plot', 'Builder', 'Society Floor'];
            foreach ($propTypes as $type) {
              echo "<label><input type='checkbox' name='prop_category[]' value='$type'> $type</label><br>";
            }
            ?>
          </td>
          <td><label>Bedrooms</label></td>
          <td>
            <?php
            $rooms = ['1BHK', '2BHK', '3BHK', '4BHK', '5BHK'];
            foreach ($rooms as $room) {
              echo "<label><input type='checkbox' name='bedrooms[]' value='$room'> $room</label><br>";
            }
            ?>
          </td>
        </tr>
        <tr>
          <td><label>Area / Locality Restriction</label></td>
          <td><input type="text" name="area_restriction"></td>
          <td><label>Car Parking</label></td>
          <td><input type="text" name="car_parking"></td>
        </tr>
        <tr>
          <td><label>Vastu</label></td>
          <td>
            <label><input type="radio" name="vastu" value="Yes"> Yes</label>
            <label><input type="radio" name="vastu" value="No"> No</label>
          </td>
          <td><label>Other Important Points</label></td>
          <td><textarea name="important_points" rows="2"></textarea></td>
        </tr>
        <tr>
          <td><label>Budget</label></td>
          <td><input type="text" name="budget"></td>
          <td><label>Loan Option</label></td>
          <td>
            <label><input type="radio" name="loan_option" value="Yes"> Yes</label>
            <label><input type="radio" name="loan_option" value="No"> No</label>
          </td>
        </tr>
        <tr>
          <td><label>Profession</label></td>
          <td><input type="text" name="profession"></td>
          <td><label>Deadline</label></td>
          <td><input type="text" name="deadline"></td>
        </tr>
        <tr>
          <td><label>Seen Properties Already?</label></td>
          <td colspan="3">
            <label><input type="radio" name="seen_props" value="Yes"> Yes</label>
            <label><input type="radio" name="seen_props" value="No"> No</label>
          </td>
        </tr>
      </table>

      <h3>Suitable Properties</h3>
      <table>
        <thead>
          <tr>
            <th>S.No</th>
            <th>Suitable Properties</th>
            <th>Shown</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td><input type="text" name="suitable_properties[]"></td>
            <td><input type="text" name="shown[]"></td>
            <td><input type="text" name="remarks[]"></td>
          </tr>
        </tbody>
      </table>

      <br>
      <button type="submit">Submit</button>
    </form>
  </div>
</body>
</html>
