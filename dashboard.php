<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");

// Strong session check
if (!isset($_SESSION['user_id'])) {
    echo "<script>localStorage.clear(); sessionStorage.clear(); window.location.href='login.html';</script>";
    exit();
}


// ✅ Database connection - YE IMPORTANT HAI
include 'db.php';

// Get cities for dropdown
$cities_result = $conn->query("SELECT city_name FROM cities ORDER BY city_name ASC");
$cities = [];
while($row = $cities_result->fetch_assoc()) {
    $cities[] = $row['city_name'];
}

// Search filter
$search_source = isset($_GET['source']) ? $_GET['source'] : '';
$search_destination = isset($_GET['destination']) ? $_GET['destination'] : '';
$search_date = isset($_GET['date']) ? $_GET['date'] : '';

$sql = "SELECT * FROM flights WHERE 1=1";
if(!empty($search_source)) {
    $sql .= " AND source LIKE '%$search_source%'";
}
if(!empty($search_destination)) {
    $sql .= " AND destination LIKE '%$search_destination%'";
}
if(!empty($search_date)) {
    $sql .= " AND date = '$search_date'";
}
$sql .= " ORDER BY date ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Skywings - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .search-section select {
            padding: 10px 15px;
            border-radius: 8px;
            border: none;
            background: white;
            min-width: 150px;
        }
    </style>
</head>

<body class="dashboard-body">

<div class="user-nav">
    <h2><i class="fa-solid fa-plane"></i> Skywings</h2>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="my_bookings.php">My Bookings</a>
    </div>
    <div class="user-profile" onclick="toggleMenu()">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">
        <div id="profileMenu" class="profile-dropdown">
            <p><strong><?php echo $_SESSION['user_name'] ?? 'User'; ?></strong></p>
            <p><?php echo $_SESSION['user_email'] ?? ''; ?></p>
            <hr>
            <a href="my_bookings.php">My Bookings</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="hero-section">
    <h1>Welcome <?php echo $_SESSION['user_name'] ?? 'User'; ?>!</h1>
    <p>Search & Book flights easily</p>
</div>

<!-- Search Section with Dropdown -->
<div class="search-section">
    <form method="GET" action="dashboard.php">
        <select name="source">
            <option value="">From (Select City)</option>
            <?php foreach($cities as $city): ?>
                <option value="<?php echo $city; ?>" <?php echo ($search_source == $city) ? 'selected' : ''; ?>>
                    <?php echo $city; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="destination">
            <option value="">To (Select City)</option>
            <?php foreach($cities as $city): ?>
                <option value="<?php echo $city; ?>" <?php echo ($search_destination == $city) ? 'selected' : ''; ?>>
                    <?php echo $city; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="date" name="date" value="<?php echo $search_date; ?>">
        <button type="submit"><i class="fa-solid fa-search"></i> Search Flights</button>
        <a href="dashboard.php" style="padding:10px 15px; background:#666; color:white; border-radius:8px; text-decoration:none;">Clear</a>
    </form>
</div>

<!-- Flights -->
<div class="flights-section">
    <h2>Available Flights</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="flight-card">
                <h3><?php echo $row['flight_name']; ?> (<?php echo $row['flight_number']; ?>)</h3>
                <p><?php echo $row['source']; ?> → <?php echo $row['destination']; ?></p>
                <p class="flight-time">
                    <i class="fa-regular fa-calendar"></i>
                    <?php echo date('d M Y', strtotime($row['date'])); ?> (<?php echo $row['flight_day']; ?>)
                </p>
                <p class="flight-time">
                    <i class="fa-regular fa-clock"></i>
                    Departure: <?php echo date('h:i A', strtotime($row['departure_time'])); ?> |
                    Arrival: <?php echo date('h:i A', strtotime($row['arrival_time'])); ?>
                </p>
                <p>Seats Available: <?php echo $row['seats']; ?></p>
                <p class="flight-price">
    <?php if($row['economy_seats'] > 0): ?>
        Economy: ₹<?php echo $row['economy_price']; ?> &nbsp;
    <?php endif; ?>
    <?php if($row['business_seats'] > 0): ?>
        Business: ₹<?php echo $row['business_price']; ?> &nbsp;
    <?php endif; ?>
    <?php if($row['first_class_seats'] > 0): ?>
        First: ₹<?php echo $row['first_class_price']; ?>
    <?php endif; ?>
</p>

                <form action="book.php" method="post">
    <input type="hidden" name="flight_id" value="<?php echo $row['id']; ?>">
    
    <!-- Class Select -->
    <select name="class_type" required style="width:100%; padding:8px; margin:8px 0; border-radius:6px; border:1px solid #ddd;">
        <option value="">Select Class</option>
        <?php if($row['economy_seats'] > 0): ?>
            <option value="economy">Economy — ₹<?php echo $row['economy_price']; ?> (<?php echo $row['economy_seats']; ?> seats left)</option>
        <?php endif; ?>
        <?php if($row['business_seats'] > 0): ?>
            <option value="business">Business — ₹<?php echo $row['business_price']; ?> (<?php echo $row['business_seats']; ?> seats left)</option>
        <?php endif; ?>
        <?php if($row['first_class_seats'] > 0): ?>
            <option value="first_class">First Class — ₹<?php echo $row['first_class_price']; ?> (<?php echo $row['first_class_seats']; ?> seats left)</option>
        <?php endif; ?>
    </select>

    <input type="number" name="seats" placeholder="Number of Seats" min="1" required style="width:100%; padding:8px; margin:4px 0; border-radius:6px; border:1px solid #ddd;">
    <button type="submit">Book Now</button>
</form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <!-- No Flights Found Box -->
        <div style="text-align: center; padding: 30px 20px; background: rgba(0,0,0,0.6); border-radius: 15px; margin: 10px auto; max-width: 450px;">
            <i class="fa-solid fa-plane-slash" style="font-size: 40px; color: #ff4b2b; margin-bottom: 15px; display: block;"></i>
            <h3 style="color: white; margin-bottom: 8px; font-size: 20px;">No Flights Found!</h3>
            <p style="color: #ccc; font-size: 14px;">No flights matching your search criteria.</p>
            <p style="color: #aaa; margin-top: 8px; font-size: 13px;">Try changing source, destination, or date.</p>
            <a href="dashboard.php" style="display: inline-block; margin-top: 15px; padding: 8px 20px; background: #ff4b2b; color: white; text-decoration: none; border-radius: 6px; font-size: 14px;">
                <i class="fa-solid fa-arrow-left"></i> Clear Search
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleMenu() {
    let menu = document.getElementById("profileMenu");
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}

// Close dropdown when clicking outside
document.addEventListener("click", function(event) {
    let menu = document.getElementById("profileMenu");
    let profile = document.querySelector(".user-profile");
    if (profile && !profile.contains(event.target)) {
        menu.style.display = "none";
    }
});
</script>

</body>
</html>