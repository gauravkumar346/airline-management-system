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

$booking_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$stmt = $conn->prepare("SELECT bookings.*, flights.flight_name, flights.flight_number, flights.source, flights.destination, flights.date, flights.flight_day, flights.departure_time, flights.arrival_time, flights.price 
FROM bookings 
JOIN flights ON bookings.flight_id = flights.id 
WHERE bookings.id = ? AND bookings.user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if(!$booking) {
    header("Location: my_bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Ticket - Skywings</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'>
    <link rel='stylesheet' href='style.css'>
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px;">
    <div class="ticket-container">
        <div class="ticket">
            <div class="ticket-header">
                <h2><i class="fa-solid fa-plane"></i> Skywings Airlines</h2>
                <p>Boarding Pass</p>
            </div>
            <div class="ticket-body">
                <div class="flight-info">
                    <div class="flight-name"><?php echo $booking['flight_name']; ?></div>
                    <div class="route"><?php echo $booking['source']; ?> → <?php echo $booking['destination']; ?></div>
                    <p style="font-size:12px;">Flight No: <?php echo $booking['flight_number']; ?></p>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Passenger Name:</span>
                    <span class="detail-value"><?php echo $user_name; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Travel Date:</span>
                    <span class="detail-value"><?php echo date('d M Y', strtotime($booking['date'])); ?> (<?php echo $booking['flight_day']; ?>)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Departure:</span>
                    <span class="detail-value"><?php echo date('h:i A', strtotime($booking['departure_time'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Arrival:</span>
                    <span class="detail-value"><?php echo date('h:i A', strtotime($booking['arrival_time'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Seats:</span>
                    <span class="detail-value"><?php echo $booking['seats_booked']; ?></span>
                </div>

                <div class="detail-row">
                 <span class="detail-label">Class:</span>
                    <span class="detail-value">
                     <?php 
                    if($booking['class_type'] == 'economy') echo '🟢 Economy Class';
                    elseif($booking['class_type'] == 'business') echo '🔵 Business Class';
                    elseif($booking['class_type'] == 'first_class') echo '🟠 First Class';
                     ?>
                     </span>
                </div>
                
                <div class="seat-number">
                    <i class="fa-solid fa-chair"></i> Seat No: <?php echo $booking['seat_number']; ?>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value" style="color:#2ecc71; font-size:20px;">₹<?php echo number_format($booking['price_per_seat'] * $booking['seats_booked']); ?></span>
                </div>
                
                <div class="booking-ref">
                    Booking Reference: <?php echo $booking['booking_reference']; ?>
                </div>
            </div>
        </div>
        
        <div class="ticket-buttons">
            <button class="btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Ticket</button>
            <a href="my_bookings.php" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    </div>
</body>
</html>