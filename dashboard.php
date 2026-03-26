<?php
session_start();
$conn = new mysqli("localhost","root","","airline_db");
$result = $conn->query("SELECT * FROM flights");
?>
<a href="my_bookings.php">My Bookings</a>
<link rel="stylesheet" href="style.css">

<div class="navbar">
    <h2>Dashboard</h2>
</div>

<div class="container">

<?php while($row = $result->fetch_assoc()){ ?>

<div class="card">
    <h3><?php echo $row['flight_name']; ?></h3>
    <p><?php echo $row['source']; ?> → <?php echo $row['destination']; ?></p>
    <p>Date: <?php echo $row['date']; ?></p>
    <p>₹<?php echo $row['price']; ?></p>

    <form action="book.php" method="post">
        <input type="hidden" name="flight_id" value="<?php echo $row['id']; ?>">
        <input type="number" name="seats" placeholder="Seats">
        <button>Book Now</button>
    </form>
</div>

<?php } ?>

</div>