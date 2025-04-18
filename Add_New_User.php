<!-- --- register.php --- -->
<?php
include 'db_connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  header("Location: login.php?registered=1");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Add New User</title>
   <!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Custom Style -->
<link href="Style/style.css" rel="stylesheet">
<body class="p-4">
  <?php include 'navbar.php'; ?>
<div class="container">
  <h2>Add New User</h2>
  <form method="POST">
    <div class="mb-3">
      <label>Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Create User</button>
  </form>
</div>
</body>
</html>


