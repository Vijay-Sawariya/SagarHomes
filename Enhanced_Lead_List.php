<?php
/* Multi-select for Interested Area */
<label for="interested_area">Interested Area:</label>
<select id="interested_area" name="interested_area[]" multiple class="form-select">
  <?php 
    $areas = ["Andheri", "Bandra", "Borivali", "Juhu", "Malad"];
    $selectedAreas = explode(", ", $area);
    foreach ($areas as $a) {
      $selected = in_array($a, $selectedAreas) ? "selected" : "";
      echo "<option value='$a' $selected>$a</option>";
    }
  ?>
</select>

<!-- Multi-select for Floor -->
<label for="floor">Floor:</label>
<select id="floor" name="floor[]" multiple class="form-select">
  <?php 
    $floors = range(1, 10);
    $selectedFloors = is_array($floor) ? $floor : explode(", ", $floor);
    foreach ($floors as $f) {
      $selected = in_array((string)$f, $selectedFloors) ? "selected" : "";
      echo "<option value='$f' $selected>$f</option>";
    }
  ?>
</select>

<!-- Multi-select for BHK -->
<label for="bhk">BHK:</label>
<select id="bhk" name="bhk[]" multiple class="form-select">
  <?php 
    $bhks = [1, 2, 3, 4];
    $selectedBhk = is_array($bhk) ? $bhk : explode(", ", $bhk);
    foreach ($bhks as $b) {
      $selected = in_array((string)$b, $selectedBhk) ? "selected" : "";
      echo "<option value='$b' $selected>{$b}BHK</option>";
    }
  ?>
</select>

<?php
// View lead read-only display
if (isset($_GET['view_id'])) {
  // Fetch lead from DB based on ID
  $view_id = $_GET['view_id'];
  $result = $conn->query("SELECT * FROM leads WHERE id = $view_id");
  if ($result->num_rows > 0) {
    $lead = $result->fetch_assoc();
    echo "<h3>View Lead Details</h3>";
    foreach ($lead as $key => $value) {
      echo "<p><strong>" . ucfirst(str_replace('_', ' ', $key)) . ":</strong> $value</p>";
    }
    exit;
  }
}
?>
?>