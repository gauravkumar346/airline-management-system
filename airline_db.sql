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

-- Flights table mein class columns add karo
ALTER TABLE flights ADD COLUMN economy_seats INT DEFAULT 0;
ALTER TABLE flights ADD COLUMN economy_price INT DEFAULT 0;
ALTER TABLE flights ADD COLUMN business_seats INT DEFAULT 0;
ALTER TABLE flights ADD COLUMN business_price INT DEFAULT 0;
ALTER TABLE flights ADD COLUMN first_class_seats INT DEFAULT 0;
ALTER TABLE flights ADD COLUMN first_class_price INT DEFAULT 0;

-- Admin table (agar nahi hai)
CREATE TABLE IF NOT EXISTS admin_profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    airline_name VARCHAR(100)
);

-- Default admin data
INSERT INTO admin_profile (name, email, phone, airline_name) 
VALUES ('Admin', 'admin@skywings.com', '+91-9999999999', 'Skywings Airlines');

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    airline_name VARCHAR(100),
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    secret_key VARCHAR(255)
);

INSERT INTO admin_settings (secret_key) VALUES ('tumhari_apni_key');

-- =============================================
-- SKYWINGS - DATABASE FIX 
USE airline_db;
 
-- Step 1: bookings table mein class_type column add karo (agar nahi hai)
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS class_type VARCHAR(20) DEFAULT 'economy';
 
-- Step 2: bookings table mein price_per_seat column add karo (agar nahi hai)
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS price_per_seat INT DEFAULT 0;
 
-- Step 3: Verify karo ke columns aa gaye
DESCRIBE bookings;