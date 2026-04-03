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

error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// Get cities for dropdown
$cities_result = $conn->query("SELECT city_name FROM cities ORDER BY city_name ASC");
$cities = [];
while($row = $cities_result->fetch_assoc()) {
    $cities[] = $row['city_name'];
}

$result = $conn->query("SELECT * FROM flights ORDER BY date DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Panel</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="admin-container">

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#">Dashboard</a>
        <a href="#">Add Flight</a>
        <a href="#">View Flights</a>
    </div>

    <div class="main-content">

        <h1>Add Flight</h1>

        <div class="form-box">
            <form action="add_flight.php" method="post">
                <input type="text" name="name" placeholder="Flight Name" required>
                <input type="text" name="flight_number" placeholder="Flight Number (e.g., SW-101)" required>
                
                <!-- Source Dropdown -->
                <select name="source" required>
                    <option value="">Select Source City</option>
                    <?php foreach($cities as $city): ?>
                        <option value="<?php echo $city; ?>"><?php echo $city; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Destination Dropdown -->
                <select name="destination" required>
                    <option value="">Select Destination City</option>
                    <?php foreach($cities as $city): ?>
                        <option value="<?php echo $city; ?>"><?php echo $city; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <input type="date" name="date" required>
                <select name="flight_day" required>
                    <option value="">Select Day</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
                <input type="time" name="departure_time" required>
                <input type="time" name="arrival_time" required>
                <input type="number" name="seats" placeholder="Total Seats" required>
                <input type="number" name="price" placeholder="Price (₹)" required>
                <button>Add Flight</button>
            </form>
        </div>

        <h1>Available Flights</h1>

        <div class="table-box">
            <table border="1" cellpadding="10" cellspacing="0" style="width:100%; background:white;">
                <tr style="background:#1e3c72; color:white;">
                    <th>Name</th>
                    <th>Flight No.</th>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Seats</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>

                <?php while($row = $result->fetch_assoc()){ ?>
                <tr>
                    <td><?php echo $row['flight_name']; ?></td>
                    <td><?php echo $row['flight_number']; ?></td>
                    <td><?php echo $row['source']; ?></td>
                    <td><?php echo $row['destination']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['flight_day']; ?></td>
                    <td><?php echo $row['departure_time']; ?></td>
                    <td><?php echo $row['arrival_time']; ?></td>
                    <td><?php echo $row['seats']; ?></td>
                    <td>₹<?php echo $row['price']; ?></td>
                    <td><a href="delete_flight.php?id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this flight?')">Delete</a></td>
                </tr>
                <?php } ?>

            </table>
        </div>

    </div>

</div>

</body>
</html>