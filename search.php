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

<link rel="stylesheet" href="style.css">

<div class="navbar">
    <h2>✈️ AirLine</h2>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<h2 style="text-align:center; color:white; margin-top:30px;">Search Results</h2>

<div class="container">

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
?>
<div class="card">
    <h3><?php echo htmlspecialchars($row['flight_name']); ?></h3>
    <p><?php echo htmlspecialchars($row['source']); ?> → <?php echo htmlspecialchars($row['destination']); ?></p>
    <p>Date: <?php echo $row['date']; ?></p>
    <p>₹<?php echo $row['price']; ?></p>

    <form action="book.php" method="post">
        <input type="hidden" name="flight_id" value="<?php echo $row['id']; ?>">
        <input type="number" name="seats" placeholder="Seats" min="1" max="<?php echo $row['seats']; ?>" required>
        <button>Book Now</button>
    </form>
</div>
<?php
    }
} else {
    echo "<h3 style='color:white; text-align:center;'>No Flights Found</h3>";
}
?>

</div>
