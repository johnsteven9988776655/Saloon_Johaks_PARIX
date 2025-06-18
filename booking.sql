-- Create database
CREATE DATABASE IF NOT EXISTS salon_booking;
USE salon_booking;

-- Create services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration_minutes INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create stylists table
CREATE TABLE IF NOT EXISTS stylists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stylist_name VARCHAR(100) NOT NULL,
    specialty VARCHAR(100),
    bio TEXT,
    image_path VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service VARCHAR(100) NOT NULL,
    stylist VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time VARCHAR(20) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    notes TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample services
INSERT INTO services (service_name, description, price, duration_minutes) VALUES
('Haircut', 'A fresh cut to suit your style', 45.00, 45),
('Hair Color', 'Full color service with conditioning', 85.00, 90),
('Highlights', 'Partial or full highlights', 120.00, 120),
('Blowout & Style', 'Wash and professional styling', 35.00, 30),
('Manicure', 'Classic or gel polish', 25.00, 30),
('Pedicure', 'Spa pedicure with massage', 40.00, 45);

-- Insert sample stylists
INSERT INTO stylists (stylist_name, specialty, bio) VALUES
('Sarah', 'Hair Specialist', 'Sarah has over 10 years of experience in cutting and styling all hair types.'),
('Michael', 'Color Expert', 'Michael specializes in creative coloring techniques and color correction.'),
('Jessica', 'Nail Technician', 'Jessica provides exceptional manicure and pedicure services with attention to detail.');

-- Create index for better performance on date queries
CREATE INDEX idx_booking_date ON bookings (booking_date);
CREATE INDEX idx_stylist_date ON bookings (stylist, booking_date);