<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$id = $_GET['id'];

// Pehle booking ka data lo
$stmt = $conn->prepare("SELECT flight_id, seats_booked FROM bookings WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if ($booking) {
    // Seats wapas karo flights mein
    $stmt2 = $conn->prepare("UPDATE flights SET seats = seats + ? WHERE id = ?");
    $stmt2->bind_param("ii", $booking['seats_booked'], $booking['flight_id']);
    $stmt2->execute();

    // Booking cancel karo
    $stmt3 = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
    $stmt3->bind_param("i", $id);
    $stmt3->execute();

    header("Location: my_bookings.php");
} else {
    echo "Booking not found.";
}
?>
