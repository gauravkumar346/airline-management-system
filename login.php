<?php
session_start();
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$result = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");

if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $_SESSION['user_id'] = $row['id'];
    header("Location: dashboard.php");
} else {
    echo "Login Failed";
}
?>