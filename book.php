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

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$flight_id = $_POST['flight_id'];
$seats_requested = $_POST['seats'];

// Get flight details
$stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();
$flight = $result->fetch_assoc();

if ($flight && $flight['seats'] >= $seats_requested) {
    // Update seats
    $new_seats = $flight['seats'] - $seats_requested;
    $stmt2 = $conn->prepare("UPDATE flights SET seats = ? WHERE id = ?");
    $stmt2->bind_param("ii", $new_seats, $flight_id);
    $stmt2->execute();
    
    // Generate seat numbers and booking reference
    $seat_numbers = [];
    $used_seats = range(1, $seats_requested);
    foreach($used_seats as $seat) {
        $seat_numbers[] = $seat . $flight['flight_number'];
    }
    $seat_numbers_str = implode(', ', $seat_numbers);
    $booking_ref = strtoupper(substr(uniqid(), -8));
    
    // Create booking
    $created_at = date('Y-m-d H:i:s');
    $stmt3 = $conn->prepare("INSERT INTO bookings(user_id, flight_id, seats_booked, seat_number, booking_reference, status, created_at) VALUES(?, ?, ?, ?, ?, 'Booked', ?)");
    $stmt3->bind_param("iiisss", $user_id, $flight_id, $seats_requested, $seat_numbers_str, $booking_ref, $created_at);
    $stmt3->execute();
    $booking_id = $conn->insert_id;
    
    // Show Ticket
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Your Ticket - Skywings</title>
        <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
            body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
            .ticket-container { max-width: 500px; width: 100%; }
            .ticket { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
            .ticket-header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; padding: 20px; text-align: center; }
            .ticket-header h2 { font-size: 28px; }
            .ticket-header p { opacity: 0.8; }
            .ticket-body { padding: 25px; }
            .flight-info { text-align: center; border-bottom: 2px dashed #ddd; padding-bottom: 20px; margin-bottom: 20px; }
            .flight-name { font-size: 24px; font-weight: bold; color: #1e3c72; }
            .route { font-size: 18px; color: #ff4b2b; margin: 10px 0; }
            .detail-row { display: flex; justify-content: space-between; margin: 12px 0; padding: 8px; background: #f8f9fa; border-radius: 8px; }
            .detail-label { font-weight: bold; color: #555; }
            .detail-value { color: #1e3c72; font-weight: 500; }
            .seat-number { background: #2ecc71; color: white; padding: 10px; text-align: center; border-radius: 10px; margin: 15px 0; font-size: 18px; }
            .booking-ref { text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; font-size: 12px; color: #999; }
            .buttons { display: flex; gap: 15px; margin-top: 20px; justify-content: center; }
            .btn { padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
            .btn-primary { background: #ff4b2b; color: white; }
            .btn-secondary { background: #3498db; color: white; }
            .btn-primary:hover, .btn-secondary:hover { opacity: 0.9; }
            @media print {
                body { background: white; }
                .buttons { display: none; }
                .ticket { box-shadow: none; }
            }
        </style>
    </head>
    <body>
        <div class="ticket-container">
            <div class="ticket">
                <div class="ticket-header">
                    <h2><i class="fa-solid fa-plane"></i> Skywings Airlines</h2>
                    <p>Boarding Pass</p>
                </div>
                <div class="ticket-body">
                    <div class="flight-info">
                        <div class="flight-name"><?php echo $flight['flight_name']; ?></div>
                        <div class="route"><?php echo $flight['source']; ?> → <?php echo $flight['destination']; ?></div>
                        <p style="font-size:12px; color:#666;">Flight No: <?php echo $flight['flight_number']; ?></p>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Passenger Name:</span>
                        <span class="detail-value"><?php echo $user_name; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Travel Date:</span>
                        <span class="detail-value"><?php echo date('d M Y', strtotime($flight['date'])); ?> (<?php echo $flight['flight_day']; ?>)</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Departure Time:</span>
                        <span class="detail-value"><?php echo date('h:i A', strtotime($flight['departure_time'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Arrival Time:</span>
                        <span class="detail-value"><?php echo date('h:i A', strtotime($flight['arrival_time'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Number of Seats:</span>
                        <span class="detail-value"><?php echo $seats_requested; ?></span>
                    </div>
                    
                    <div class="seat-number">
                        <i class="fa-solid fa-chair"></i> Seat Number(s): <?php echo $seat_numbers_str; ?>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value" style="color:#2ecc71; font-size:20px;">₹<?php echo number_format($flight['price'] * $seats_requested); ?></span>
                    </div>
                    
                    <div class="booking-ref">
                        Booking Reference: <?php echo $booking_ref; ?>
                    </div>
                </div>
            </div>
            
            <div class="buttons">
                <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Ticket</button>
                <a href="dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-home"></i> Back to Dashboard</a>
                <a href="my_bookings.php" class="btn btn-secondary"><i class="fa-solid fa-ticket"></i> My Bookings</a>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Booking Failed - Skywings</title>
        <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'>
        <style>
            body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; }
            .error-box { background: white; padding: 40px; border-radius: 20px; text-align: center; max-width: 500px; }
            .error-icon { font-size: 80px; color: #e74c3c; margin-bottom: 20px; }
            h2 { color: #e74c3c; margin-bottom: 15px; }
            .btn { display: inline-block; margin-top: 20px; padding: 12px 30px; background: #ff4b2b; color: white; text-decoration: none; border-radius: 8px; }
        </style>
    </head>
    <body>
        <div class='error-box'>
            <div class='error-icon'><i class='fa-solid fa-circle-exclamation'></i></div>
            <h2>Not Enough Seats ❌</h2>
            <p>Sorry, the requested number of seats is not available.</p>
            <a href='dashboard.php' class='btn'>Back to Dashboard</a>
        </div>
    </body>
    </html>
    <?php
}
?>