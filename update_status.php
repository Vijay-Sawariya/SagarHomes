<?php
include 'db_connect.php';

if (isset($_POST['lead_id']) && isset($_POST['followup_status'])) {
    $lead_id = intval($_POST['lead_id']);
    $status = $conn->real_escape_string($_POST['followup_status']);

    $sql = "UPDATE leads SET lead_status = '$status' WHERE lead_id = $lead_id";
    if ($conn->query($sql) === TRUE) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
