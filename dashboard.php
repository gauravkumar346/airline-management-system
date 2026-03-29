<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$result = $conn->query("SELECT * FROM flights");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div style="display:flex; justify-content:space-between; align-items:center; background:#222; color:white; padding:15px;">
    <h2>✈ Airline Dashboard</h2>

    <div style="position:relative;">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
             width="40" style="cursor:pointer;" onclick="toggleMenu()">

        <div id="profileMenu" style="display:none; position:absolute; right:0; top:50px; background:white; color:black; padding:15px; width:200px; box-shadow:0 0 10px gray; z-index:100;">
            <p><b><?php echo htmlspecialchars($_SESSION['user_name']); ?></b></p>
            <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            <hr>
            <p><a href="my_bookings.php">My Bookings</a></p>
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>
</div>

<div class="container">

<?php while ($row = $result->fetch_assoc()) { ?>

<div class="card">
    <h3><?php echo htmlspecialchars($row['flight_name']); ?></h3>
    <p><?php echo htmlspecialchars($row['source']); ?> → <?php echo htmlspecialchars($row['destination']); ?></p>
    <p>Date: <?php echo $row['date']; ?></p>
    <p>₹<?php echo $row['price']; ?></p>
    <p>Seats Available: <?php echo $row['seats']; ?></p>

    <form action="book.php" method="post" onsubmit="return bookingPopup()">
        <input type="hidden" name="flight_id" value="<?php echo $row['id']; ?>">
        <input type="number" name="seats" placeholder="Seats" min="1" max="<?php echo $row['seats']; ?>" required>
        <button>Book Now</button>
    </form>
</div>

<?php } ?>

</div>

<script src="script.js"></script>
</body>
</html>
