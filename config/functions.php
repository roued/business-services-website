<?php
/**
 * Global Functions
 * الوظائف العامة
 */

require_once 'database.php';

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Generate unique order number
function generateOrderNumber() {
    return 'ORD-' . date('YmdHis') . '-' . rand(100, 999);
}

// Generate unique invoice number
function generateInvoiceNumber() {
    return 'INV-' . date('YmdHis') . '-' . rand(100, 999);
}

// Format price
function formatPrice($price, $currency = 'SAR') {
    return number_format($price, 2, '.', ',') . ' ' . $currency;
}

// Get service categories
function getServiceCategories($conn) {
    $query = "SELECT * FROM service_categories WHERE is_active = TRUE ORDER BY display_order ASC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get services by category
function getServicesByCategory($conn, $category_id) {
    $query = "SELECT * FROM services WHERE category_id = ? AND is_active = TRUE";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get service by ID
function getServiceById($conn, $service_id) {
    $query = "SELECT * FROM services WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $service_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Create order
function createOrder($conn, $user_id, $service_id, $quantity, $unit_price) {
    $order_number = generateOrderNumber();
    $total_price = $quantity * $unit_price;
    
    $query = "INSERT INTO orders (order_number, user_id, service_id, quantity, unit_price, total_price, final_price) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('siiidd', $order_number, $user_id, $service_id, $quantity, $unit_price, $total_price, $total_price);
    
    if ($stmt->execute()) {
        return ['success' => true, 'order_id' => $conn->insert_id, 'order_number' => $order_number];
    } else {
        return ['success' => false, 'message' => 'Error creating order: ' . $stmt->error];
    }
}

// Get maintenance centers by city
function getMaintenanceCentersByCity($conn, $city) {
    $query = "SELECT * FROM maintenance_centers WHERE city = ? AND is_active = TRUE ORDER BY rating DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $city);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Calculate distance (simple mock function)
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 69.09;
    return round($miles * 1.60934, 2); // Convert to kilometers
}

// Send email
function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@business-services.com" . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Log admin action
function logAdminAction($conn, $admin_id, $action, $description, $table_name = null, $record_id = null) {
    $query = "INSERT INTO admin_logs (admin_id, action, description, table_name, record_id) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isssi', $admin_id, $action, $description, $table_name, $record_id);
    return $stmt->execute();
}

// Get user by ID
function getUserById($conn, $user_id) {
    $query = "SELECT id, first_name, last_name, email, phone, company_name, user_type FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

?>