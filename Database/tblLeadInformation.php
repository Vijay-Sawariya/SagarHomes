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


// SQL to drop the table
/*$sql = "DROP TABLE IF EXISTS leads";

f ($conn->query($sql) === TRUE) {
   echo "Table 'users' dropped successfully.";
} else {
   echo "Error dropping table: " . $conn->error;
}*/

// Create table
$sql = "CREATE TABLE leads (
  lead_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  phone VARCHAR(20),
  email VARCHAR(100),
  lead_type VARCHAR(20),
  area VARCHAR(1000),
  bhk VARCHAR(20),
  floor INT,
  price DECIMAL(15,2),
  unit VARCHAR(20),
  area_size DECIMAL(15,2),
  car_parking_number INT,
  lift_available VARCHAR(10),
  property_type VARCHAR(50),
  lead_status VARCHAR(50),
  notes TEXT,
  agent_name VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  followup_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  followup_remarks TEXT,
  followup_status VARCHAR(50)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'leads' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}


$conn->close();
?>