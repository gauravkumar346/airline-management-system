<?php
include 'db.php';

$id = $_GET['id'];

$conn->query("UPDATE bookings SET status='Cancelled' WHERE id='$id'");

echo "Booking Cancelled";
?>