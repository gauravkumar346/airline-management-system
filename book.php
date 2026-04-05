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
 
$user_id         = $_SESSION['user_id'];
$user_name       = $_SESSION['user_name'];
$flight_id       = $_POST['flight_id'];
$seats_requested = intval($_POST['seats']);
$class_type      = $_POST['class_type'];
 
// Flight details lo
$stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();
$flight = $result->fetch_assoc();
 
// Class ke hisaab se seats aur price
if ($class_type == 'economy') {
    $available_seats = $flight['economy_seats'];
    $price_per_seat  = $flight['economy_price'];
    $class_label     = 'Economy Class';
} elseif ($class_type == 'business') {
    $available_seats = $flight['business_seats'];
    $price_per_seat  = $flight['business_price'];
    $class_label     = 'Business Class';
} elseif ($class_type == 'first_class') {
    $available_seats = $flight['first_class_seats'];
    $price_per_seat  = $flight['first_class_price'];
    $class_label     = 'First Class';
} else {
    $available_seats = 0;
    $price_per_seat  = 0;
    $class_label     = 'Unknown';
}
 
if ($flight && $available_seats >= $seats_requested) {
 
    // Seats update karo class ke hisaab se
    if ($class_type == 'economy') {
        $stmt2 = $conn->prepare("UPDATE flights SET economy_seats = economy_seats - ?, seats = seats - ? WHERE id = ?");
    } elseif ($class_type == 'business') {
        $stmt2 = $conn->prepare("UPDATE flights SET business_seats = business_seats - ?, seats = seats - ? WHERE id = ?");
    } else {
        $stmt2 = $conn->prepare("UPDATE flights SET first_class_seats = first_class_seats - ?, seats = seats - ? WHERE id = ?");
    }
    $stmt2->bind_param("iii", $seats_requested, $seats_requested, $flight_id);
    $stmt2->execute();
 
    // Seat numbers aur booking ref generate karo
    $seat_numbers = [];
    foreach(range(1, $seats_requested) as $seat) {
        $seat_numbers[] = $seat . $flight['flight_number'];
    }
    $seat_numbers_str = implode(', ', $seat_numbers);
    $booking_ref      = strtoupper(substr(uniqid(), -8));
    $created_at       = date('Y-m-d H:i:s');
    $total_amount     = $price_per_seat * $seats_requested;
 
    // ✅ FIX: Booking save karo - execute() call thi hi nahi pehle!
    $stmt3 = $conn->prepare("INSERT INTO bookings(user_id, flight_id, seats_booked, seat_number, booking_reference, class_type, price_per_seat, status, created_at) VALUES(?, ?, ?, ?, ?, ?, ?, 'Booked', ?)");
    $stmt3->bind_param("iiisssss", $user_id, $flight_id, $seats_requested, $seat_numbers_str, $booking_ref, $class_type, $price_per_seat, $created_at);
    $stmt3->execute(); // ✅ YE LINE MISSING THI - AB ADD KI
 
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
            .ticket-body { padding: 25px; }
            .flight-info { text-align: center; border-bottom: 2px dashed #ddd; padding-bottom: 20px; margin-bottom: 20px; }
            .flight-name { font-size: 24px; font-weight: bold; color: #1e3c72; }
            .route { font-size: 18px; color: #ff4b2b; margin: 10px 0; }
            .detail-row { display: flex; justify-content: space-between; margin: 12px 0; padding: 8px; background: #f8f9fa; border-radius: 8px; }
            .detail-label { font-weight: bold; color: #555; }
            .detail-value { color: #1e3c72; font-weight: 500; }
            .seat-number { background: #2ecc71; color: white; padding: 10px; text-align: center; border-radius: 10px; margin: 15px 0; font-size: 16px; }
            .class-badge { padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; display: inline-block; margin: 8px 0; }
            .economy-badge { background: #e8f5e9; color: #2e7d32; }
            .business-badge { background: #e3f2fd; color: #1565c0; }
            .first-badge { background: #fff3e0; color: #e65100; }
            .booking-ref { text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; font-size: 12px; color: #999; }
            .buttons { display: flex; gap: 15px; margin-top: 20px; justify-content: center; flex-wrap: wrap; }
            .btn { padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; font-family: 'Poppins', sans-serif; }
            .btn-primary { background: #ff4b2b; color: white; }
            .btn-secondary { background: #3498db; color: white; }
            @media print { body { background: white; } .buttons { display: none; } }
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
                        <div class="flight-name"><?php echo htmlspecialchars($flight['flight_name']); ?></div>
                        <div class="route"><?php echo htmlspecialchars($flight['source']); ?> → <?php echo htmlspecialchars($flight['destination']); ?></div>
                        <p style="font-size:12px; color:#666;">Flight No: <?php echo htmlspecialchars($flight['flight_number']); ?></p>
                        <?php
                            $badge_class = '';
                            if($class_type == 'economy') $badge_class = 'economy-badge';
                            elseif($class_type == 'business') $badge_class = 'business-badge';
                            else $badge_class = 'first-badge';
                        ?>
                        <span class="class-badge <?php echo $badge_class; ?>">
                            <i class="fa-solid fa-star"></i> <?php echo $class_label; ?>
                        </span>
                    </div>
 
                    <div class="detail-row">
                        <span class="detail-label">Passenger:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user_name); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Travel Date:</span>
                        <span class="detail-value"><?php echo date('d M Y', strtotime($flight['date'])); ?> (<?php echo $flight['flight_day']; ?>)</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Departure:</span>
                        <span class="detail-value"><?php echo date('h:i A', strtotime($flight['departure_time'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Arrival:</span>
                        <span class="detail-value"><?php echo date('h:i A', strtotime($flight['arrival_time'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Class:</span>
                        <span class="detail-value"><?php echo $class_label; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Seats:</span>
                        <span class="detail-value"><?php echo $seats_requested; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Price/Seat:</span>
                        <span class="detail-value">₹<?php echo number_format($price_per_seat); ?></span>
                    </div>
 
                    <div class="seat-number">
                        <i class="fa-solid fa-chair"></i> Seat No: <?php echo $seat_numbers_str; ?>
                    </div>
 
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value" style="color:#2ecc71; font-size:20px; font-weight:bold;">₹<?php echo number_format($total_amount); ?></span>
                    </div>
 
                    <div class="booking-ref">
                        <i class="fa-solid fa-barcode"></i> Booking Ref: <strong><?php echo $booking_ref; ?></strong>
                    </div>
                </div>
            </div>
 
            <div class="buttons">
                <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
                <a href="dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-home"></i> Dashboard</a>
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
        <title>Booking Failed</title>
        <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'>
        <style>
            body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea, #764ba2); min-height: 100vh; display: flex; justify-content: center; align-items: center; }
            .error-box { background: white; padding: 40px; border-radius: 20px; text-align: center; max-width: 400px; }
            .error-icon { font-size: 60px; color: #e74c3c; margin-bottom: 15px; }
            h2 { color: #e74c3c; margin-bottom: 10px; }
            .btn { display: inline-block; margin-top: 20px; padding: 12px 30px; background: #ff4b2b; color: white; text-decoration: none; border-radius: 8px; }
        </style>
    </head>
    <body>
        <div class='error-box'>
            <div class='error-icon'><i class='fa-solid fa-circle-exclamation'></i></div>
            <h2>Not Enough Seats!</h2>
            <p>Sorry, requested seats not available in <?php echo htmlspecialchars($class_label); ?>.</p>
            <a href='dashboard.php' class='btn'>Back to Dashboard</a>
        </div>
    </body>
    </html>
    <?php
}
?>