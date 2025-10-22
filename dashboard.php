<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard | Baazaar</title>
</head>
<body>
  <h2>Welcome, <?php echo $_SESSION['username']; ?> 🎉</h2>
  <a href="logout.php">Logout</a>
</body>
</html>
