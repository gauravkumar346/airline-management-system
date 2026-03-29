<?php
session_start();
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id']    = $row['id'];
        $_SESSION['user_name']  = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        header("Location: dashboard.php");
    } else {
        echo "<p style='color:red;text-align:center;'>Wrong password. <a href='login.html'>Try again</a></p>";
    }
} else {
    echo "<p style='color:red;text-align:center;'>User not found. <a href='register.html'>Register</a></p>";
}
?>
