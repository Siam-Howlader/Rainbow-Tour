CREATE DATABASE tourism_management_system;
USE tourism_management_system;

CREATE TABLE tourists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    nid VARCHAR(20) UNIQUE NOT NULL,
    image VARCHAR(2048) NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    dob DATE NOT NULL,
    address TEXT
);

CREATE TABLE tour_packages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(2048) NULL,
    duration_in_days INT NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

CREATE TABLE staffs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    nid VARCHAR(20) UNIQUE NOT NULL,
    image VARCHAR(1000) NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    dob DATE NOT NULL,
    address TEXT,
    role VARCHAR(20) NOT NULL,
    joining_date DATE NOT NULL,
    availability BOOLEAN DEFAULT TRUE
);

CREATE TABLE transportations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transport_type VARCHAR(50) NOT NULL,
    company VARCHAR(100),
    capacity INT,
    departure_location VARCHAR(255),
    departure_time DATETIME,
    driver_contact VARCHAR(50)
);

CREATE TABLE schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    package_id INT NOT NULL,
    staff_id INT NOT NULL,
    transportation_id INT NOT NULL,
    FOREIGN KEY (package_id) REFERENCES tour_packages(id),
    FOREIGN KEY (staff_id) REFERENCES staffs(id),
    FOREIGN KEY (transportation_id) REFERENCES transportations(id)
);

CREATE TABLE hotels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    location VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(50),
    rating INT CHECK (rating BETWEEN 1 AND 5)
);

CREATE TABLE hotel_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    schedule_id INT,
    hotel_id INT,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    status VARCHAR(50) NOT NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id),
    FOREIGN KEY (hotel_id) REFERENCES hotels(id)
);

CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tourist_id INT NOT NULL,
    package_id INT NOT NULL,
    comment TEXT,
    rating INT,
    timestamp DATETIME,
    FOREIGN KEY (tourist_id) REFERENCES tourists(id),
    FOREIGN KEY (package_id) REFERENCES tour_packages(id)
);

CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tourist_id INT NOT NULL,
    schedule_id INT NOT NULL,
    persons INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    timestamp DATETIME,
    FOREIGN KEY (tourist_id) REFERENCES tourists(id),
    FOREIGN KEY (schedule_id) REFERENCES schedules(id)
);

CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    method VARCHAR(50) NOT NULL,
    status VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    timestamp DATETIME,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

CREATE TABLE destinations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    location VARCHAR(255) NOT NULL
);

CREATE TABLE destination_packages (
    destination_id INT NOT NULL,
    package_id INT NOT NULL,
    PRIMARY KEY (destination_id, package_id),
    FOREIGN KEY (destination_id) REFERENCES destinations(id),
    FOREIGN KEY (package_id) REFERENCES tour_packages(id)
);

CREATE TABLE destinations_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT NOT NULL,
    image VARCHAR(2048) NOT NULL,
    FOREIGN KEY (destination_id) REFERENCES destinations(id)
);

CREATE TABLE activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT 
);

CREATE TABLE destination_activities (
    activity_id INT,
    destination_id INT,
    PRIMARY KEY (activity_id, destination_id),
    FOREIGN KEY (activity_id) REFERENCES activities(id),
    FOREIGN KEY (destination_id) REFERENCES destinations(id)
);
