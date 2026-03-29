<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id   = $_SESSION['user_id'];
$flight_id = $_POST['flight_id'];
$seats     = $_POST['seats'];

$stmt = $conn->prepare("SELECT seats FROM flights WHERE id = ?");
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && $row['seats'] >= $seats) {

    $stmt2 = $conn->prepare("UPDATE flights SET seats = seats - ? WHERE id = ?");
    $stmt2->bind_param("ii", $seats, $flight_id);
    $stmt2->execute();

    $stmt3 = $conn->prepare("INSERT INTO bookings(user_id, flight_id, seats_booked, status) VALUES(?, ?, ?, 'Booked')");
    $stmt3->bind_param("iii", $user_id, $flight_id, $seats);
    $stmt3->execute();

    echo "<h2 style='color:green;text-align:center;margin-top:50px;'>Ticket Booked Successfully ✅</h2>";
    echo "<p style='text-align:center;'><a href='dashboard.php'>Back to Dashboard</a></p>";

} else {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>Not enough seats ❌</h2>";
    echo "<p style='text-align:center;'><a href='dashboard.php'>Back to Dashboard</a></p>";
}
?>
