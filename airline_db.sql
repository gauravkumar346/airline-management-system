-- Create Database
CREATE DATABASE IF NOT EXISTS airline_db;
USE airline_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(100)
);

-- Flights Table (Updated with new columns)
CREATE TABLE IF NOT EXISTS flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_name VARCHAR(100),
    flight_number VARCHAR(20),
    source VARCHAR(100),
    destination VARCHAR(100),
    date DATE,
    flight_day VARCHAR(20),
    departure_time TIME,
    arrival_time TIME,
    seats INT,
    price INT
);

-- Bookings Table (Updated with new columns)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    flight_id INT,
    seats_booked INT,
    seat_number VARCHAR(10),
    booking_reference VARCHAR(20),
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);