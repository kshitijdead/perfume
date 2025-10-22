<?php
session_start();
if(!isset($_SESSION['user_email'])){
    header('location:login.php');
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Welcome</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Welcome 🎉</h2>
  <p>You are logged in as <b><?php echo $_SESSION['user_email']; ?></b></p>
  <a href="logout.php"><button>Logout</button></a>
</div>
</body>
</html>
