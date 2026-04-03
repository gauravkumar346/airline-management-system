<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db.php';

if(!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM flights WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: admin.php");
exit();
?>