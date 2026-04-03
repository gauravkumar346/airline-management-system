<?php
session_start();
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$result = $conn->query("SELECT * FROM users WHERE email='$email'");

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Wrong Password'); window.location='login.html';</script>";
    }
} else {
    echo "<script>alert('Email not found'); window.location='login.html';</script>";
}
?>