<?php
include 'db.php';

$name = $_POST['name'];
$source = $_POST['source'];
$destination = $_POST['destination'];
$date = $_POST['date'];
$seats = $_POST['seats'];
$price = $_POST['price'];

$conn->query("INSERT INTO flights(flight_name,source,destination,date,seats,price)
VALUES('$name','$source','$destination','$date','$seats','$price')");

header("Location: admin.php");
?>