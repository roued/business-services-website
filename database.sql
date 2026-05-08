-- Create Database
CREATE DATABASE IF NOT EXISTS business_services;
USE business_services;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    company_name VARCHAR(150),
    address TEXT,
    city VARCHAR(50),
    country VARCHAR(50),
    user_type ENUM('customer', 'admin', 'vendor') DEFAULT 'customer',
    is_active BOOLEAN DEFAULT TRUE,
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX(email),
    INDEX(user_type)
);

-- Services Categories Table
CREATE TABLE IF NOT EXISTS service_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    image VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY(name)
);

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    name_ar VARCHAR(150) NOT NULL,
    description TEXT,
    description_ar TEXT,
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2),
    image VARCHAR(255),
    features JSON,
    duration_days INT,
    is_active BOOLEAN DEFAULT TRUE,
    views INT DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id),
    INDEX(category_id),
    INDEX(is_active)
);

-- Service Providers Table
CREATE TABLE IF NOT EXISTS service_providers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    experience_years INT,
    certifications TEXT,
    rating DECIMAL(3, 2) DEFAULT 0,
    total_projects INT DEFAULT 0,
    hourly_rate DECIMAL(10, 2),
    availability_status ENUM('available', 'busy', 'offline') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id),
    UNIQUE KEY(user_id, service_id)
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    service_id INT,
    provider_id INT,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    final_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    payment_method ENUM('credit_card', 'bank_transfer', 'cash') DEFAULT 'credit_card',
    start_date DATE,
    end_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (provider_id) REFERENCES users(id),
    INDEX(order_number),
    INDEX(user_id),
    INDEX(status),
    INDEX(payment_status)
);

-- Invoices Table
CREATE TABLE IF NOT EXISTS invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    due_date DATE,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX(invoice_number)
);

-- Reviews & Ratings Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    is_verified_purchase BOOLEAN DEFAULT TRUE,
    helpful_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY(service_id, user_id)
);

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_code VARCHAR(50) UNIQUE NOT NULL,
    order_id INT NOT NULL,
    invoice_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('credit_card', 'bank_transfer', 'cash', 'paypal') DEFAULT 'credit_card',
    transaction_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    currency VARCHAR(3) DEFAULT 'SAR',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (invoice_id) REFERENCES invoices(id),
    INDEX(payment_code),
    INDEX(status)
);

-- Car Rental Services Table
CREATE TABLE IF NOT EXISTS car_rentals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT,
    license_plate VARCHAR(20) UNIQUE NOT NULL,
    car_type ENUM('sedan', 'suv', 'coupe', 'van', 'truck') NOT NULL,
    seats INT DEFAULT 5,
    fuel_type ENUM('gasoline', 'diesel', 'hybrid', 'electric') DEFAULT 'gasoline',
    daily_price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    features JSON,
    is_available BOOLEAN DEFAULT TRUE,
    maintenance_center_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id),
    INDEX(owner_id),
    INDEX(is_available)
);

-- Car Rental Bookings Table
CREATE TABLE IF NOT EXISTS car_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_number VARCHAR(50) UNIQUE NOT NULL,
    car_id INT NOT NULL,
    user_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    pickup_location VARCHAR(255),
    dropoff_location VARCHAR(255),
    total_days INT,
    daily_price DECIMAL(10, 2),
    total_price DECIMAL(10, 2),
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    final_price DECIMAL(10, 2),
    status ENUM('pending', 'confirmed', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    driver_license_number VARCHAR(50),
    insurance_included BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES car_rentals(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX(booking_number),
    INDEX(user_id)
);

-- Maintenance Centers Table
CREATE TABLE IF NOT EXISTS maintenance_centers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    name_ar VARCHAR(150) NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    phone VARCHAR(20),
    email VARCHAR(100),
    working_hours VARCHAR(100),
    services_offered JSON,
    rating DECIMAL(3, 2) DEFAULT 0,
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(city),
    INDEX(is_active)
);

-- Maintenance Appointments Table
CREATE TABLE IF NOT EXISTS maintenance_appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_number VARCHAR(50) UNIQUE NOT NULL,
    center_id INT NOT NULL,
    user_id INT NOT NULL,
    car_id INT,
    service_type VARCHAR(100),
    description TEXT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    estimated_cost DECIMAL(10, 2),
    final_cost DECIMAL(10, 2),
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES maintenance_centers(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (car_id) REFERENCES car_rentals(id),
    INDEX(appointment_number),
    INDEX(user_id)
);

-- Contacts Table
CREATE TABLE IF NOT EXISTS contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(150),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    is_replied BOOLEAN DEFAULT FALSE,
    reply_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(email),
    INDEX(is_read)
);

-- Admin Logs Table
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    action VARCHAR(100),
    description TEXT,
    table_name VARCHAR(50),
    record_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id),
    INDEX(admin_id),
    INDEX(action)
);

-- Insert Service Categories
INSERT INTO service_categories (name, name_ar, description, icon, display_order) VALUES
('ERP Systems', 'الأنظمة الإدارية', 'Enterprise Resource Planning Systems', 'fa-cogs', 1),
('Graphic Design', 'تصميم الجرافيك', 'Professional Graphic Design Services', 'fa-paint-brush', 2),
('Digital Marketing', 'التسويق الإلكتروني', 'Digital Marketing and Social Media', 'fa-bullhorn', 3),
('Accounting', 'الخدمات المحاسبية', 'Professional Accounting Services', 'fa-calculator', 4),
('Legal Services', 'الخدمات القانونية', 'Legal Services and Company Registration', 'fa-gavel', 5),
('Travel & Tourism', 'السياحة والسفر', 'Travel and Tourism Services', 'fa-plane', 6),
('Car Rental', 'تأجير السيارات', 'Car Rental Services', 'fa-car', 7),
('Maintenance', 'مراكز الصيانة', 'Maintenance and Repair Centers', 'fa-wrench', 8);

-- Insert Sample Services
INSERT INTO services (category_id, name, name_ar, description, price, image) VALUES
(1, 'ERP System Implementation', 'تطبيق نظام ERP', 'Complete ERP system implementation for your business', 5000.00, '/images/services/erp.jpg'),
(2, 'Logo Design', 'تصميم الشعار', 'Professional logo design services', 500.00, '/images/services/logo.jpg'),
(3, 'Social Media Management', 'إدارة وسائل التواصل', 'Complete social media management', 800.00, '/images/services/social.jpg'),
(4, 'Bookkeeping Services', 'خدمات الدفاتر المحاسبية', 'Professional bookkeeping and accounting', 1500.00, '/images/services/accounting.jpg'),
(5, 'Company Registration', 'تأسيس الشركات', 'Complete company registration services', 2000.00, '/images/services/registration.jpg'),
(6, 'Tour Package', 'حزمة سياحية', 'All-inclusive tour packages', 3000.00, '/images/services/travel.jpg'),
(7, 'Car Rental - Economy', 'تأجير سيارة - اقتصادي', 'Budget-friendly car rental', 100.00, '/images/services/car.jpg'),
(8, 'General Maintenance', 'الصيانة العامة', 'Vehicle maintenance services', 200.00, '/images/services/maintenance.jpg');
