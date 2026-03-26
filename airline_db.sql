CREATE DATABASE airline_db;
USE airline_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(100)
);

CREATE TABLE flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_name VARCHAR(100),
    source VARCHAR(100),
    destination VARCHAR(100),
    date DATE,
    seats INT,
    price INT
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    flight_id INT,
    seats_booked INT,
    status VARCHAR(50)
);