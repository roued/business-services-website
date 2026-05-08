<?php
/**
 * Car Rental API
 * واجهة تأجير السيارات
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/session.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_cars':
        getCars();
        break;
    case 'get_car':
        getCarDetails();
        break;
    case 'search':
        searchCars();
        break;
    case 'book':
        bookCar();
        break;
    case 'get_bookings':
        getUserBookings();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getCars() {
    global $conn;
    
    $limit = intval($_GET['limit'] ?? 12);
    $offset = intval($_GET['offset'] ?? 0);
    
    $query = "SELECT * FROM car_rentals WHERE is_available = TRUE ORDER BY daily_price ASC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    
    $cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM car_rentals WHERE is_available = TRUE";
    $total = $conn->query($count_query)->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $cars,
        'total' => $total
    ]);
}

function getCarDetails() {
    global $conn;
    
    $car_id = intval($_GET['car_id'] ?? 0);
    
    if ($car_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Car ID required']);
        return;
    }
    
    $query = "SELECT cr.*, u.first_name, u.last_name, u.phone FROM car_rentals cr 
              JOIN users u ON cr.owner_id = u.id 
              WHERE cr.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $car_id);
    $stmt->execute();
    $car = $stmt->get_result()->fetch_assoc();
    
    if (!$car) {
        echo json_encode(['success' => false, 'message' => 'Car not found']);
        return;
    }
    
    echo json_encode(['success' => true, 'data' => $car]);
}

function searchCars() {
    global $conn;
    
    $car_type = sanitize($_GET['car_type'] ?? '');
    $min_price = floatval($_GET['min_price'] ?? 0);
    $max_price = floatval($_GET['max_price'] ?? 10000);
    
    $query = "SELECT * FROM car_rentals WHERE is_available = TRUE";
    
    if (!empty($car_type)) {
        $query .= " AND car_type = ?";
    }
    
    $query .= " AND daily_price BETWEEN ? AND ? ORDER BY daily_price ASC";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($car_type)) {
        $stmt->bind_param('sdd', $car_type, $min_price, $max_price);
    } else {
        $stmt->bind_param('dd', $min_price, $max_price);
    }
    
    $stmt->execute();
    $cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $cars]);
}

function bookCar() {
    global $conn;
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    $car_id = intval($_POST['car_id'] ?? 0);
    $start_date = sanitize($_POST['start_date'] ?? '');
    $end_date = sanitize($_POST['end_date'] ?? '');
    $pickup_location = sanitize($_POST['pickup_location'] ?? '');
    $dropoff_location = sanitize($_POST['dropoff_location'] ?? '');
    $driver_license = sanitize($_POST['driver_license_number'] ?? '');
    $insurance = isset($_POST['insurance_included']) ? 1 : 0;
    
    // Validation
    if ($car_id === 0 || empty($start_date) || empty($end_date)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    
    // Get car
    $car_query = "SELECT * FROM car_rentals WHERE id = ? AND is_available = TRUE";
    $car_stmt = $conn->prepare($car_query);
    $car_stmt->bind_param('i', $car_id);
    $car_stmt->execute();
    $car = $car_stmt->get_result()->fetch_assoc();
    
    if (!$car) {
        echo json_encode(['success' => false, 'message' => 'Car not available']);
        return;
    }
    
    // Calculate total days and price
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $total_days = $start->diff($end)->days + 1;
    $total_price = $total_days * $car['daily_price'];
    
    // Create booking
    $booking_number = 'BK-' . date('YmdHis') . '-' . rand(100, 999);
    
    $booking_query = "INSERT INTO car_bookings (booking_number, car_id, user_id, start_date, end_date, 
                      pickup_location, dropoff_location, total_days, daily_price, total_price, final_price, 
                      driver_license_number, insurance_included) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $booking_stmt = $conn->prepare($booking_query);
    $booking_stmt->bind_param('siisssisssii', $booking_number, $car_id, $user_id, $start_date, $end_date,
                              $pickup_location, $dropoff_location, $total_days, $car['daily_price'], $total_price, $total_price, $driver_license, $insurance);
    
    if ($booking_stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Booking created successfully',
            'booking_id' => $conn->insert_id,
            'booking_number' => $booking_number,
            'total_price' => formatPrice($total_price)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating booking']);
    }
}

function getUserBookings() {
    global $conn;
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT cb.*, cr.brand, cr.model, cr.year FROM car_bookings cb 
              JOIN car_rentals cr ON cb.car_id = cr.id 
              WHERE cb.user_id = ? ORDER BY cb.created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $bookings]);
}

?>