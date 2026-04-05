<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.html");
    exit();
}

include 'db.php';

$name             = $_POST['name'];
$flight_number    = $_POST['flight_number'];
$source           = $_POST['source'];
$destination      = $_POST['destination'];
$date             = $_POST['date'];
$flight_day       = $_POST['flight_day'];
$departure_time   = $_POST['departure_time'];
$arrival_time     = $_POST['arrival_time'];

$economy_seats    = intval($_POST['economy_seats']);
$economy_price    = intval($_POST['economy_price']);
$business_seats   = intval($_POST['business_seats']);
$business_price   = intval($_POST['business_price']);
$first_class_seats = intval($_POST['first_class_seats']);
$first_class_price = intval($_POST['first_class_price']);

$total_seats = $economy_seats + $business_seats + $first_class_seats;
$base_price  = $economy_price > 0 ? $economy_price : $business_price;

$stmt = $conn->prepare("INSERT INTO flights (flight_name, flight_number, source, destination, date, flight_day, departure_time, arrival_time, seats, price, economy_seats, economy_price, business_seats, business_price, first_class_seats, first_class_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "ssssssssiiiiiiii",
    $name,
    $flight_number,
    $source,
    $destination,
    $date,
    $flight_day,
    $departure_time,
    $arrival_time,
    $total_seats,
    $base_price,
    $economy_seats,
    $economy_price,
    $business_seats,
    $business_price,
    $first_class_seats,
    $first_class_price
);

$stmt->execute();

header("Location: admin.php");
exit();
?>