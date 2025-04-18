<?php
include 'db_connect.php';

$where = [];

if (!empty($_GET['followup_date'])) {
  $where[] = "DATE(new_followup_date) = '" . $conn->real_escape_string($_GET['followup_date']) . "'";
}
if (!empty($_GET['lead_type'])) {
  $where[] = "lead_type = '" . $conn->real_escape_string($_GET['lead_type']) . "'";
}
if (!empty($_GET['area'])) {
  $where[] = "area = '" . $conn->real_escape_string($_GET['area']) . "'";
}
if (!empty($_GET['agent_name'])) {
  $where[] = "agent_name = '" . $conn->real_escape_string($_GET['agent_name']) . "'";
}

$sql = "SELECT 
    l.lead_id,
    l.name,
    l.lead_type,
    l.phone,
    l.area,
    l.agent_name,
    f.status AS followup_status,
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
  ) f ON l.lead_id = f.lead_id
  ";

if (!empty($where)) {
  $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY new_followup_date  ASC";

$result = $conn->query($sql);

$today = date("Y-m-d");
$upcoming = date("Y-m-d", strtotime("+3 days"));
?>

<!DOCTYPE html>
<html lang="en">
<body>
<div class="table-responsive">
<table class="table table-bordered align-middle">
  <thead>
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
      $badge = '';
      if ($row['followup_date'] === $today) {
        $badge = '<span class="badge badge-today">Due Today</span>';
      } elseif ($row['followup_date'] < $today) {
        $badge = '<span class="badge badge-overdue">Overdue</span>';
      } elseif ($row['followup_date'] <= $upcoming) {
        $badge = '<span class="badge badge-upcoming">Upcoming</span>';
      }
    ?>
    <tr>
      <td><?= $row['lead_id'] ?></td>
      <td><?= $row['name'] ?></td>
      <td><?= $row['phone'] ?></td>
      <td><?= $row['followup_date'] ?></td>
      <td><?= $row['lead_type'] ?></td>
      <td><?= $row['area'] ?></td>
      <td><?= $row['agent_name'] ?></td>
      <td>
        <?= $badge ?>
        <!-- <select class="form-select mt-2">
          <option value="">Update Status</option>
          <option <?= $row['followup_status'] == "Interested" ? "selected" : "" ?>>Interested</option>
          <option <?= $row['followup_status'] == "Not Interested" ? "selected" : "" ?>>Not Interested</option>
          <option <?= $row['followup_status'] == "Visited" ? "selected" : "" ?>>Visited</option>
          <option <?= $row['followup_status'] == "Follow-Up Later" ? "selected" : "" ?>>Follow-Up Later</option>
        </select> -->

        <select class="form-select mt-2 followup-status" data-lead-id="<?= $row['lead_id'] ?>">
  <option value="">Update Status</option>
  <option value="Interested" <?= $row['followup_status'] == "Interested" ? "selected" : "" ?>>Interested</option>
  <option value="Not Interested" <?= $row['followup_status'] == "Not Interested" ? "selected" : "" ?>>Not Interested</option>
  <option value="Visited" <?= $row['followup_status'] == "Visited" ? "selected" : "" ?>>Visited</option>
  <option value="Follow-Up Later" <?= $row['followup_status'] == "Follow-Up Later" ? "selected" : "" ?>>Follow-Up Later</option>
</select>
      </td>
      <td>
        <a href="view_lead.php?id=<?= $row['lead_id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
        <a href="followups.php?lead_id=<?= $row['lead_id'] ?>" onclick="window.open(this.href, 'followups', 'width=1000,height=700'); return false;" class="btn btn-sm btn-outline-info">Follow-Ups</a>
      </td>
    </tr>
    <?php endwhile; ?>
  <?php else: ?>
    <tr><td colspan="9" class="text-center">No leads found.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('.followup-status').on('change', function () {
    const newStatus = $(this).val();
    const leadId = $(this).data('lead-id');
    const $select = $(this);

    if (newStatus === "") return;

    if (confirm("Are you sure you want to update the follow-up status to '" + newStatus + "'?")) {
      // Send AJAX request to update
      $.post('update_status.php', {
        lead_id: leadId,
        followup_status: newStatus
      }, function(response) {
        alert(response); // You can show success/failure message here
      });
    } else {
      // Reload or reset the dropdown if cancelled
      location.reload();
    }
  });
</script>
</body>

</html>