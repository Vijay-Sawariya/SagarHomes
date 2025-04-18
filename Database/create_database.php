<?php
$servername = "localhost";
$username = "root"; // Default for XAMPP
$password = "";     // Default for XAMPP (no password)

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create database
$sql = "CREATE DATABASE SagarHomes_RealEstate";
if ($conn->query($sql) === TRUE) {
    echo "Database 'SagarHomes_RealEstate' created successfully.";
} else {
    echo "Error creating database: " . $conn->error;
}


$conn->close();
?>
