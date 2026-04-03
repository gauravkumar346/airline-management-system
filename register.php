<?php
include 'db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$check = $conn->query("SELECT * FROM users WHERE email='$email'");

if($check->num_rows > 0){
    echo "<script>alert('Email already exists'); window.location='register.html';</script>";
} else {
    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    if($conn->query($sql)){
        echo "<script>alert('Registration Successful'); window.location='login.html';</script>";
    } else {
        echo "Error";
    }
}
?>