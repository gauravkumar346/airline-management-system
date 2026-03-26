<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$result = $conn->query("SELECT * FROM flights");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Panel</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="admin-container">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#">Dashboard</a>
        <a href="#">Add Flight</a>
        <a href="#">View Flights</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <h1>Add Flight</h1>

        <div class="form-box">
            <form action="add_flight.php" method="post">
                <input type="text" name="name" placeholder="Flight Name" required>
                <input type="text" name="source" placeholder="Source" required>
                <input type="text" name="destination" placeholder="Destination" required>
                <input type="date" name="date" required>
                <input type="number" name="seats" placeholder="Seats" required>
                <input type="number" name="price" placeholder="Price" required>
                <button>Add Flight</button>
            </form>
        </div>

        <h1>Available Flights</h1>

        <div class="table-box">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Date</th>
                </tr>

                <?php while($row = $result->fetch_assoc()){ ?>
                <tr>
                    <td><?php echo $row['flight_name']; ?></td>
                    <td><?php echo $row['source']; ?></td>
                    <td><?php echo $row['destination']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                </tr>
                <?php } ?>

            </table>
        </div>

    </div>

</div>

</body>
</html>