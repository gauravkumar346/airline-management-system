<?php
include 'db.php';

$id = $_GET['id'];

$conn->query("DELETE FROM flights WHERE id='$id'");

header("Location: admin.php");
?>