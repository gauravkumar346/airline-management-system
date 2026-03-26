<?php
include 'db.php';

$source = $_GET['source'];
$destination = $_GET['destination'];
$date = $_GET['date'];

$result = $conn->query("SELECT * FROM flights 
WHERE source='$source' AND destination='$destination' AND date='$date'");
?>

<link rel="stylesheet" href="style.css">

<h2 style="text-align:center;color:white;">Search Results</h2>

<div class="container">

<?php 
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
?>

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

<?php } 
} else {
    echo "<h3 style='color:white;text-align:center;'>No Flights Found</h3>";
}
?>

</div>