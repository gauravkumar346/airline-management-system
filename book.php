<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$flight_id = $_POST['flight_id'];
$seats = $_POST['seats'];

// current seats check karo
$result = $conn->query("SELECT seats FROM flights WHERE id='$flight_id'");
$row = $result->fetch_assoc();

if($row['seats'] >= $seats){

    // seats kam karo
    $conn->query("UPDATE flights SET seats = seats - $seats WHERE id='$flight_id'");

    // booking save karo
    $conn->query("INSERT INTO bookings(user_id,flight_id,seats_booked,status)
    VALUES('$user_id','$flight_id','$seats','Booked')");

    echo "<h2 style='color:green;text-align:center;'>Ticket Booked Successfully ✅</h2>";

} else {
    echo "<h2 style='color:red;text-align:center;'>Not enough seats ❌</h2>";
}
?>