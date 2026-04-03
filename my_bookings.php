<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Rest of your code...


$user_id = $_SESSION['user_id'];

$result = $conn->query("SELECT bookings.*, flights.flight_name, flights.flight_number, flights.source, flights.destination, flights.date, flights.flight_day, flights.departure_time, flights.arrival_time, flights.price 
FROM bookings 
JOIN flights ON bookings.flight_id = flights.id
WHERE bookings.user_id = '$user_id' 
ORDER BY bookings.created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings - Skywings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .bookings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-title {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            font-size: 36px;
        }

        .page-title i {
            color: #ff4b2b;
            margin-right: 15px;
        }

        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 25px;
        }

        .booking-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        /* Card Header */
        .booking-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 20px;
            position: relative;
        }

        .flight-name-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .flight-number {
            font-size: 12px;
            opacity: 0.8;
        }

        .status-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-booked {
            background: #2ecc71;
            color: white;
        }

        .status-cancelled {
            background: #e74c3c;
            color: white;
        }

        /* Card Body */
        .booking-body {
            padding: 20px;
        }

        .route-info {
            text-align: center;
            padding: 15px 0;
            border-bottom: 1px dashed #ddd;
            margin-bottom: 15px;
        }

        .city-from, .city-to {
            font-size: 18px;
            font-weight: bold;
            color: #1e3c72;
        }

        .flight-icon {
            font-size: 24px;
            color: #ff4b2b;
            margin: 0 15px;
        }

        .date-info {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .details-label {
            font-weight: 600;
            color: #555;
        }

        .details-value {
            color: #1e3c72;
            font-weight: 500;
        }

        .seat-info {
            background: #f0f8ff;
            padding: 12px;
            border-radius: 10px;
            margin: 15px 0;
            text-align: center;
        }

        .seat-number {
            font-size: 16px;
            font-weight: bold;
            color: #2ecc71;
        }

        .price-total {
            font-size: 20px;
            font-weight: bold;
            color: #ff4b2b;
            text-align: right;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #f0f0f0;
        }

        /* Card Footer */
        .booking-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .btn-view {
            flex: 1;
            background: #3498db;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn-view:hover {
            background: #2980b9;
        }

        .btn-cancel {
            flex: 1;
            background: #e74c3c;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn-cancel:hover {
            background: #c0392b;
        }

        .no-bookings {
            text-align: center;
            padding: 60px;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .no-bookings i {
            font-size: 60px;
            color: #ff4b2b;
            margin-bottom: 20px;
        }

        .no-bookings h3 {
            color: white;
            margin-bottom: 10px;
        }

        .no-bookings p {
            color: #ccc;
        }

        .back-btn {
            text-align: center;
            margin-top: 30px;
        }

        .back-btn a {
            display: inline-block;
            padding: 12px 30px;
            background: #ff4b2b;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .back-btn a:hover {
            background: #e63e1f;
        }

        .booking-ref-small {
            font-size: 11px;
            color: #999;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body class="dashboard-body">

<div class="user-nav">
    <h2><i class="fa-solid fa-plane"></i> Skywings</h2>
    <div>
        <a href="dashboard.php"><i class="fa-solid fa-home"></i> Home</a>
        <a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="bookings-container">
    <h1 class="page-title">
        <i class="fa-solid fa-ticket"></i> My Bookings
    </h1>

    <div class="bookings-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="booking-card">
                    <!-- Header -->
                    <div class="booking-header">
                        <div class="flight-name-header">
                            <i class="fa-solid fa-plane"></i> <?php echo htmlspecialchars($row['flight_name']); ?>
                        </div>
                        <div class="flight-number">
                            Flight No: <?php echo $row['flight_number']; ?>
                        </div>
                        <div class="status-badge <?php echo $row['status'] == 'Booked' ? 'status-booked' : 'status-cancelled'; ?>">
                            <?php echo $row['status']; ?>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="booking-body">
                        <div class="route-info">
                            <span class="city-from"><?php echo htmlspecialchars($row['source']); ?></span>
                            <i class="fa-solid fa-plane flight-icon"></i>
                            <span class="city-to"><?php echo htmlspecialchars($row['destination']); ?></span>
                            <div class="date-info">
                                <i class="fa-regular fa-calendar"></i>
                                <?php echo date('d M Y', strtotime($row['date'])); ?> (<?php echo $row['flight_day']; ?>)
                            </div>
                        </div>

                        <div class="details-row">
                            <span class="details-label"><i class="fa-regular fa-clock"></i> Departure:</span>
                            <span class="details-value"><?php echo date('h:i A', strtotime($row['departure_time'])); ?></span>
                        </div>

                        <div class="details-row">
                            <span class="details-label"><i class="fa-regular fa-clock"></i> Arrival:</span>
                            <span class="details-value"><?php echo date('h:i A', strtotime($row['arrival_time'])); ?></span>
                        </div>

                        <div class="details-row">
                            <span class="details-label"><i class="fa-solid fa-chair"></i> Seats Booked:</span>
                            <span class="details-value"><?php echo $row['seats_booked']; ?></span>
                        </div>

                        <div class="seat-info">
                            <i class="fa-solid fa-ticket"></i>
                            <span class="seat-number">Seat No: <?php echo $row['seat_number']; ?></span>
                        </div>

                        <div class="price-total">
                            Total: ₹<?php echo number_format($row['price'] * $row['seats_booked']); ?>
                        </div>

                        <div class="booking-ref-small">
                            <i class="fa-solid fa-barcode"></i> Booking Ref: <?php echo $row['booking_reference']; ?>
                        </div>
                    </div>

                    <!-- Footer -->
                    <?php if ($row['status'] == 'Booked'): ?>
                        <div class="booking-footer">
                            <button class="btn-view" onclick="viewTicket(<?php echo $row['id']; ?>)">
                                <i class="fa-solid fa-eye"></i> View Ticket
                            </button>
                            <button class="btn-cancel" onclick="cancelBooking(<?php echo $row['id']; ?>)">
                                <i class="fa-solid fa-times"></i> Cancel Booking
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-bookings">
                <i class="fa-solid fa-face-frown"></i>
                <h3>No Bookings Found!</h3>
                <p>You haven't booked any flights yet.</p>
                <a href="dashboard.php" style="display: inline-block; margin-top: 20px; padding: 10px 25px; background: #ff4b2b; color: white; text-decoration: none; border-radius: 8px;">
                    <i class="fa-solid fa-plane"></i> Book a Flight
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="back-btn">
            <a href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<script>
function cancelBooking(id) {
    if(confirm("Are you sure you want to cancel this booking? Seats will be restored.")) {
        window.location.href = "cancel.php?id=" + id;
    }
}

function viewTicket(id) {
    window.location.href = "view_ticket.php?id=" + id;
}
</script>

</body>
</html>