<?php
$conn = new mysqli("localhost","root","","airline_db");

if($conn->connect_error){
    die("Connection failed");
}
?>