<?php
include 'db.php';

$name           = trim($_POST['name']);
$email          = trim($_POST['email']);
$phone          = trim($_POST['phone']);
$airline_name   = trim($_POST['airline_name']);
$password       = $_POST['password'];
$confirm        = $_POST['confirm_password'];

if ($password !== $confirm) {
    echo "<script>alert('Passwords do not match!'); window.location='admin_register.html';</script>";
    exit();
}

$check = $conn->query("SELECT * FROM admins WHERE email='$email'");
if ($check->num_rows > 0) {
    echo "<script>alert('Email already registered!'); window.location='admin_register.html';</script>";
    exit();
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admins (name, email, phone, airline_name, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $phone, $airline_name, $hashed);

if ($stmt->execute()) {
    echo "<script>alert('Admin account created successfully!'); window.location='admin_login.html';</script>";
} else {
    echo "<script>alert('Error! Try again.'); window.location='admin_register.html';</script>";
}
?>