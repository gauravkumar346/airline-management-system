<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");

include 'db.php';

$email    = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();

    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_name']     = $admin['name'];
        $_SESSION['admin_email']    = $admin['email'];
        $_SESSION['admin_phone']    = $admin['phone'];
        $_SESSION['admin_airline']  = $admin['airline_name'];
        $_SESSION['is_admin']       = true;

        header("Location: admin.php");
        exit();
    } else {
        echo "<script>alert('Wrong Password!'); window.location='admin_login.html';</script>";
    }
} else {
    echo "<script>alert('Admin email not found!'); window.location='admin_login.html';</script>";
}
?>