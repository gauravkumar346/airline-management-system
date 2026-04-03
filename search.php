<?php
include 'db.php';

$source      = $_GET['source'];
$destination = $_GET['destination'];
$date        = $_GET['date'];

$stmt = $conn->prepare("SELECT * FROM flights WHERE source = ? AND destination = ? AND date = ?");
$stmt->bind_param("sss", $source, $destination, $date);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results - Skywings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        /* Login ke baad wali background image */
        body {
            background: url('images/dashboard-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar h2 {
            color: #60f106;
        }
        
        .navbar h2 i {
            margin-right: 10px;
        }
        
        .navbar a {
            color: white;
            margin-left: 25px;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .navbar a:hover {
            color: #ff4b2b;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .page-title {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            font-size: 36px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .flights-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }
        
        .flight-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            width: 320px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s;
        }
        
        .flight-card:hover {
            transform: translateY(-5px);
        }
        
        .flight-card h3 {
            color: #1e3c72;
            font-size: 22px;
            margin-bottom: 10px;
        }
        
        .flight-card h3 i {
            color: #ff4b2b;
            margin-right: 10px;
        }
        
        .route {
            font-size: 18px;
            font-weight: bold;
            color: #ff4b2b;
            margin: 10px 0;
        }
        
        .detail {
            color: #666;
            margin: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail i {
            width: 20px;
            color: #ff4b2b;
        }
        
        .price {
            font-size: 28px;
            font-weight: bold;
            color: #2ecc71;
            margin: 15px 0;
        }
        
        .booking-form {
            margin-top: 15px;
        }
        
        .booking-form input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .booking-form button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ff4b2b, #ffcc00);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: opacity 0.3s;
        }
        
        .booking-form button:hover {
            opacity: 0.9;
        }
        
        .no-results {
            text-align: center;
            color: white;
            padding: 60px;
        }
        
        .no-results i {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 40px;
        }
        
        .back-link a {
            display: inline-block;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 8px;
            transition: background 0.3s;
        }
        
        .back-link a:hover {
            background: rgba(0, 0, 0, 0.8);
        }
        
        .back-link a i {
            margin-right: 8px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2><i class="fa-solid fa-plane"></i> Skywings</h2>
    <div>
        <a href="dashboard.php"><i class="fa-solid fa-home"></i> Dashboard</a>
        <a href="my_bookings.php"><i class="fa-solid fa-ticket"></i> My Bookings</a>
        <a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <h1 class="page-title">
        <i class="fa-solid fa-magnifying-glass"></i> Search Results
    </h1>
    
    <div class="flights-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="flight-card">
                    <h3><i class="fa-solid fa-plane"></i> <?php echo htmlspecialchars($row['flight_name']); ?></h3>
                    <div class="route">
                        <?php echo htmlspecialchars($row['source']); ?> → <?php echo htmlspecialchars($row['destination']); ?>
                    </div>
                    <div class="detail">
                        <i class="fa-regular fa-calendar"></i>
                        <span>Date: <?php echo date('d M Y', strtotime($row['date'])); ?></span>
                    </div>
                    <div class="detail">
                        <i class="fa-solid fa-chair"></i>
                        <span>Seats Available: <?php echo $row['seats']; ?></span>
                    </div>
                    <div class="price">
                        ₹<?php echo number_format($row['price']); ?>
                    </div>
                    <?php if ($row['seats'] > 0): ?>
                        <form class="booking-form" action="book.php" method="post" onsubmit="return confirm('Confirm booking?')">
                            <input type="hidden" name="flight_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="seats" placeholder="Number of seats" min="1" max="<?php echo $row['seats']; ?>" required>
                            <button type="submit"><i class="fa-solid fa-bookmark"></i> Book Now</button>
                        </form>
                    <?php else: ?>
                        <button style="background: #ccc; cursor: not-allowed;" disabled><i class="fa-solid fa-ban"></i> Sold Out</button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
    <i class="fa-solid fa-calendar-times" style="font-size: 60px; color: #ff4b2b;"></i>
    <h2>No Flights Available!</h2>
    <p>No flights from <?php echo htmlspecialchars($source); ?> → <?php echo htmlspecialchars($destination); ?> on <?php echo htmlspecialchars($date); ?></p>
    <p>📅 Check availability for nearby dates:</p>
    <div style="display: flex; gap: 10px; justify-content: center; margin: 15px 0;">
        <a href="?source=<?php echo $source; ?>&destination=<?php echo $destination; ?>&date=<?php echo date('Y-m-d', strtotime($date . ' -1 day')); ?>" style="padding: 8px 15px; background: #3498db; color: white; border-radius: 5px; text-decoration: none;">← Previous Day</a>
        <a href="?source=<?php echo $source; ?>&destination=<?php echo $destination; ?>&date=<?php echo date('Y-m-d', strtotime($date . ' +1 day')); ?>" style="padding: 8px 15px; background: #3498db; color: white; border-radius: 5px; text-decoration: none;">Next Day →</a>
    </div>
    <div class="back-link">
        <a href="dashboard.php"><i class="fa-solid fa-home"></i> Back to Dashboard</a>
    </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="back-link">
            <a href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>