<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SagarHomes_RealEstate";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE tblfollowups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lead_id INT NOT NULL,
  new_followup_date DATE,
  remarks TEXT,
  status VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'tblfollowups' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$sql = "CREATE TABLE visited_properties (
  id INT AUTO_INCREMENT PRIMARY KEY,
  property_id VARCHAR(50),
  location VARCHAR(100),
  bhk VARCHAR(10),
  visit_date DATE,
  status VARCHAR(50)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'tblfollowups' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$sql = "CREATE TABLE scheduled_visits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  property VARCHAR(255),
  visit_date DATE,
  status VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  lead_id INT
 )";


if ($conn->query($sql) === TRUE) {
    echo "Table 'tblfollowups' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

 

$conn->close();
?>