<?php
include 'db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

$conn->query("INSERT INTO users(name,email,password)
VALUES('$name','$email','$password')");

echo "Registered Successfully";
?>