<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db.php';

$name = $_POST['name'];
$flight_number = $_POST['flight_number'];
$source = $_POST['source'];
$destination = $_POST['destination'];
$date = $_POST['date'];
$flight_day = $_POST['flight_day'];
$departure_time = $_POST['departure_time'];
$arrival_time = $_POST['arrival_time'];
$seats = $_POST['seats'];
$price = $_POST['price'];

$stmt = $conn->prepare("INSERT INTO flights(flight_name, flight_number, source, destination, date, flight_day, departure_time, arrival_time, seats, price) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssis", $name, $flight_number, $source, $destination, $date, $flight_day, $departure_time, $arrival_time, $seats, $price);
$stmt->execute();

header("Location: admin.php");
?>