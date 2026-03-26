<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

$result = $conn->query("SELECT flights.flight_name, flights.source, flights.destination, flights.date, bookings.seats_booked, bookings.status 
FROM bookings 
JOIN flights ON bookings.flight_id = flights.id
WHERE bookings.user_id='$user_id'");
?>

<link rel="stylesheet" href="style.css">

<div class="navbar">
    <h2>✈️ AirLine</h2>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<h2 style="text-align:center;color:white;">My Bookings</h2>

<div class="container">

<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
?>

<div class="card">
    <h3><?php echo $row['flight_name']; ?></h3>
    <p><?php echo $row['source']; ?> → <?php echo $row['destination']; ?></p>
    <p>Date: <?php echo $row['date']; ?></p>
    <p>Seats: <?php echo $row['seats_booked']; ?></p>
    <p>Status: <?php echo $row['status']; ?></p>
</div>

<?php }
} else {
    echo "<h3 style='color:white;text-align:center;'>No Bookings Yet</h3>";
}
?>

</div>