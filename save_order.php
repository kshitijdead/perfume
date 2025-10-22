<?php
$servername = "127.0.0.1";
$username = "root@localhost";
$password = ""; // default XAMPP MySQL password
$dbname = "perfume_store";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$customerName = $_POST['customerName'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$total = $_POST['total'] ?? 0;

// Save order to database
$stmt = $conn->prepare("INSERT INTO orders (customer_name, mobile, email, address, pincode, total) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssi", $customerName, $mobile, $email, $address, $pincode, $total);

if($stmt->execute()){
    echo "success";
}else{
    echo "error: ".$stmt->error;
}

$stmt->close();
$conn->close();
?>
