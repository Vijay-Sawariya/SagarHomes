<?php
// --- db_connect.php ---
session_start();
$conn = new mysqli("localhost", "root", "", "SagarHomes_RealEstate");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Redirect to login if not logged in
$currentFile = basename($_SERVER['PHP_SELF']);
if (!in_array($currentFile, ['login.php', 'register.php']) && !isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
?>