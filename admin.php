<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "<script>localStorage.clear(); sessionStorage.clear(); window.location.href='admin_login.html';</script>";
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// Session se admin details
$admin['name']         = $_SESSION['admin_name'];
$admin['email']        = $_SESSION['admin_email'];
$admin['phone']        = $_SESSION['admin_phone'];
$admin['airline_name'] = $_SESSION['admin_airline'];

// Get cities
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
<title>Admin Panel - Skywings</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background: #f0f2f5; }

    .admin-wrapper {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 270px;
        background: linear-gradient(180deg, #1e3c72, #2a5298);
        color: white;
        padding: 0;
        display: flex;
        flex-direction: column;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
    }

    .sidebar-logo {
        padding: 25px 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.15);
    }

    .sidebar-logo h2 {
        font-size: 22px;
        color: #ffcc00;
    }

    .sidebar-logo p {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        margin-top: 4px;
    }

    .admin-profile-card {
        margin: 20px 15px;
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 18px;
        border: 1px solid rgba(255,255,255,0.15);
    }

    .admin-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ff4b2b, #ffcc00);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        margin: 0 auto 12px;
    }

    .admin-profile-card h3 {
        text-align: center;
        font-size: 15px;
        color: white;
        margin-bottom: 4px;
    }

    .admin-badge {
        text-align: center;
        font-size: 11px;
        background: #ffcc00;
        color: #1e3c72;
        padding: 2px 10px;
        border-radius: 20px;
        font-weight: 600;
        width: fit-content;
        margin: 4px auto 14px;
        display: block;
    }

    .admin-detail-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 8px 0;
        font-size: 12px;
        color: rgba(255,255,255,0.85);
        background: rgba(255,255,255,0.08);
        padding: 7px 10px;
        border-radius: 8px;
        word-break: break-all;
    }

    .admin-detail-item i {
        color: #ffcc00;
        width: 14px;
        flex-shrink: 0;
    }

    .sidebar-nav {
        padding: 10px 15px;
        flex: 1;
    }

    .sidebar-nav p {
        font-size: 10px;
        color: rgba(255,255,255,0.4);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 15px 0 8px 5px;
    }

    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255,255,255,0.85);
        text-decoration: none;
        padding: 10px 12px;
        border-radius: 8px;
        margin: 4px 0;
        font-size: 14px;
        transition: all 0.2s;
    }

    .sidebar-nav a:hover {
        background: rgba(255,255,255,0.15);
        color: white;
    }

    .sidebar-nav a i {
        width: 18px;
        color: #ffcc00;
    }

    .sidebar-logout {
        padding: 15px;
        border-top: 1px solid rgba(255,255,255,0.15);
    }

    .sidebar-logout a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 14px;
    }

    .sidebar-logout a:hover {
        background: rgba(231,76,60,0.3);
        color: #e74c3c;
    }

    .main-content {
        margin-left: 270px;
        flex: 1;
        padding: 30px;
    }

    .page-header {
        margin-bottom: 25px;
    }

    .page-header h1 {
        font-size: 24px;
        color: #1e3c72;
    }

    .page-header p {
        color: #888;
        font-size: 13px;
        margin-top: 4px;
    }

    .form-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 30px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.07);
    }

    .form-card h2 {
        color: #1e3c72;
        font-size: 18px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-card h2 i { color: #ff4b2b; }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .form-grid .full-width {
        grid-column: 1 / -1;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group label {
        font-size: 12px;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group input,
    .form-group select {
        padding: 10px 14px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
        transition: border 0.2s;
        background: #fafafa;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #2a5298;
        background: white;
    }

    .class-section {
        background: #f8f9ff;
        border: 1.5px solid #e0e8ff;
        border-radius: 12px;
        padding: 18px;
        margin-top: 5px;
    }

    .class-section h3 {
        font-size: 13px;
        color: #2a5298;
        font-weight: 600;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .class-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
    }

    .class-label {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 8px;
    }

    .economy-label { background: #e8f5e9; color: #2e7d32; }
    .business-label { background: #e3f2fd; color: #1565c0; }
    .first-label { background: #fff3e0; color: #e65100; }

    .submit-btn {
        background: linear-gradient(135deg, #ff4b2b, #ff416c);
        color: white;
        border: none;
        padding: 13px 35px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
        font-family: 'Poppins', sans-serif;
    }

    .submit-btn:hover { opacity: 0.9; }

    .table-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.07);
        overflow-x: auto;
    }

    .table-card h2 {
        color: #1e3c72;
        font-size: 18px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-card h2 i { color: #ff4b2b; }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    thead tr {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
    }

    th, td {
        padding: 12px 14px;
        text-align: left;
    }

    tbody tr:nth-child(even) { background: #f8f9fa; }
    tbody tr:hover { background: #eef2ff; }

    .class-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 600;
        margin: 2px 0;
    }

    .badge-eco { background: #e8f5e9; color: #2e7d32; }
    .badge-bus { background: #e3f2fd; color: #1565c0; }
    .badge-fst { background: #fff3e0; color: #e65100; }

    .delete-btn {
        background: #fee2e2;
        color: #e74c3c;
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .delete-btn:hover { background: #e74c3c; color: white; }
</style>
</head>

<body>
<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <h2><i class="fa-solid fa-plane"></i> Skywings</h2>
            <p>Admin Control Panel</p>
        </div>

        <div class="admin-profile-card">
            <div class="admin-avatar">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <h3><?php echo htmlspecialchars($admin['name']); ?></h3>
            <span class="admin-badge">Administrator</span>

            <div class="admin-detail-item">
                <i class="fa-solid fa-envelope"></i>
                <span><?php echo htmlspecialchars($admin['email']); ?></span>
            </div>
            <div class="admin-detail-item">
                <i class="fa-solid fa-phone"></i>
                <span><?php echo htmlspecialchars($admin['phone']); ?></span>
            </div>
            <div class="admin-detail-item">
                <i class="fa-solid fa-plane-circle-check"></i>
                <span><?php echo htmlspecialchars($admin['airline_name']); ?></span>
            </div>
        </div>

        <div class="sidebar-nav">
            <p>Menu</p>
            <a href="admin.php"><i class="fa-solid fa-plus-circle"></i> Add Flight</a>
            <a href="admin.php"><i class="fa-solid fa-list"></i> View Flights</a>
            <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> User Dashboard</a>
        </div>

        <div class="sidebar-logout">
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <div class="page-header">
            <h1>Add New Flight</h1>
            <p>Fill in the details to add a new flight</p>
        </div>

        <div class="form-card">
            <h2><i class="fa-solid fa-plane-departure"></i> Flight Details</h2>

            <form action="add_flight.php" method="post">
                <div class="form-grid">

                    <div class="form-group">
                        <label>Flight Name</label>
                        <input type="text" name="name" placeholder="e.g. Skywings Express" required>
                    </div>

                    <div class="form-group">
                        <label>Flight Number</label>
                        <input type="text" name="flight_number" placeholder="e.g. SW-101" required>
                    </div>

                    <div class="form-group">
                        <label>Source City</label>
                        <select name="source" required>
                            <option value="">Select Source City</option>
                            <?php foreach($cities as $city): ?>
                                <option value="<?php echo $city; ?>"><?php echo $city; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Destination City</label>
                        <select name="destination" required>
                            <option value="">Select Destination City</option>
                            <?php foreach($cities as $city): ?>
                                <option value="<?php echo $city; ?>"><?php echo $city; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Flight Date</label>
                        <input type="date" name="date" id="flightDate" required onchange="autoSelectDay()">
                    </div>

                    <div class="form-group">
                        <label>Flight Day</label>
                        <select name="flight_day" id="flightDay" required>
                            <option value="">Auto (Select Date First)</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Departure Time</label>
                        <input type="time" name="departure_time" required>
                    </div>

                    <div class="form-group">
                        <label>Arrival Time</label>
                        <input type="time" name="arrival_time" required>
                    </div>

                    <div class="form-group full-width">
                        <label>Seat Classes & Pricing</label>
                        <div class="class-section">
                            <h3><i class="fa-solid fa-info-circle"></i> Set seats and price for each class</h3>
                            <div class="class-row">

                                <div>
                                    <span class="class-label economy-label">Economy Class</span>
                                    <div class="form-group">
                                        <label>Total Seats</label>
                                        <input type="number" name="economy_seats" placeholder="e.g. 100" min="0" value="0">
                                    </div>
                                    <div class="form-group" style="margin-top:8px;">
                                        <label>Price (₹)</label>
                                        <input type="number" name="economy_price" placeholder="e.g. 3500" min="0" value="0">
                                    </div>
                                </div>

                                <div>
                                    <span class="class-label business-label">Business Class</span>
                                    <div class="form-group">
                                        <label>Total Seats</label>
                                        <input type="number" name="business_seats" placeholder="e.g. 40" min="0" value="0">
                                    </div>
                                    <div class="form-group" style="margin-top:8px;">
                                        <label>Price (₹)</label>
                                        <input type="number" name="business_price" placeholder="e.g. 8000" min="0" value="0">
                                    </div>
                                </div>

                                <div>
                                    <span class="class-label first-label">First Class</span>
                                    <div class="form-group">
                                        <label>Total Seats</label>
                                        <input type="number" name="first_class_seats" placeholder="e.g. 10" min="0" value="0">
                                    </div>
                                    <div class="form-group" style="margin-top:8px;">
                                        <label>Price (₹)</label>
                                        <input type="number" name="first_class_price" placeholder="e.g. 18000" min="0" value="0">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <button type="submit" class="submit-btn">
                    <i class="fa-solid fa-plus"></i> Add Flight
                </button>
            </form>
        </div>

        <!-- FLIGHTS TABLE -->
        <div class="table-card">
            <h2><i class="fa-solid fa-list"></i> Available Flights</h2>
            <table>
                <thead>
                    <tr>
                        <th>Flight Name</th>
                        <th>Flight No.</th>
                        <th>Route</th>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Classes & Seats</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $result->fetch_assoc()){ ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['flight_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['flight_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['source']); ?> → <?php echo htmlspecialchars($row['destination']); ?></td>
                    <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                    <td><?php echo $row['flight_day']; ?></td>
                    <td><?php echo date('h:i A', strtotime($row['departure_time'])); ?></td>
                    <td><?php echo date('h:i A', strtotime($row['arrival_time'])); ?></td>
                    <td>
                        <?php if(!empty($row['economy_seats']) && $row['economy_seats'] > 0): ?>
                            <span class="class-badge badge-eco">Eco: <?php echo $row['economy_seats']; ?> | ₹<?php echo $row['economy_price']; ?></span><br>
                        <?php endif; ?>
                        <?php if(!empty($row['business_seats']) && $row['business_seats'] > 0): ?>
                            <span class="class-badge badge-bus">Biz: <?php echo $row['business_seats']; ?> | ₹<?php echo $row['business_price']; ?></span><br>
                        <?php endif; ?>
                        <?php if(!empty($row['first_class_seats']) && $row['first_class_seats'] > 0): ?>
                            <span class="class-badge badge-fst">1st: <?php echo $row['first_class_seats']; ?> | ₹<?php echo $row['first_class_price']; ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="delete_flight.php?id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this flight?')">
                            <i class="fa-solid fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
function autoSelectDay() {
    const dateInput = document.getElementById('flightDate').value;
    if (!dateInput) return;

    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const date = new Date(dateInput);
    const dayName = days[date.getDay()];

    const daySelect = document.getElementById('flightDay');
    for (let i = 0; i < daySelect.options.length; i++) {
        if (daySelect.options[i].value === dayName) {
            daySelect.selectedIndex = i;
            break;
        }
    }
}
</script>

</body>
</html>