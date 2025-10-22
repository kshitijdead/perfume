<?php
session_start();

$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "customers_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $name;
            echo "<script>alert('Hello $name, welcome to Baazaar!'); window.location='index.html';</script>";
        } else {
            echo "<script>alert('Invalid password. Try again.'); window.location='login.html';</script>";
        }
    } else {
        echo "<script>alert('No account found with this email. Please Sign Up.'); window.location='signup.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
