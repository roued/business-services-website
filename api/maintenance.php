<?php
/**
 * Maintenance Centers API
 * واجهة مراكز الصيانة
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/session.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_centers':
        getCenters();
        break;
    case 'get_by_city':
        getCentersByCity();
        break;
    case 'get_center':
        getCenterDetails();
        break;
    case 'book_appointment':
        bookAppointment();
        break;
    case 'get_appointments':
        getUserAppointments();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getCenters() {
    global $conn;
    
    $limit = intval($_GET['limit'] ?? 10);
    $offset = intval($_GET['offset'] ?? 0);
    
    $query = "SELECT * FROM maintenance_centers WHERE is_active = TRUE ORDER BY rating DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    
    $centers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $centers]);
}

function getCentersByCity() {
    global $conn;
    
    $city = sanitize($_GET['city'] ?? '');
    
    if (empty($city)) {
        echo json_encode(['success' => false, 'message' => 'City required']);
        return;
    }
    
    $query = "SELECT * FROM maintenance_centers WHERE city = ? AND is_active = TRUE ORDER BY rating DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $city);
    $stmt->execute();
    
    $centers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $centers]);
}

function getCenterDetails() {
    global $conn;
    
    $center_id = intval($_GET['center_id'] ?? 0);
    
    if ($center_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Center ID required']);
        return;
    }
    
    $query = "SELECT * FROM maintenance_centers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $center_id);
    $stmt->execute();
    
    $center = $stmt->get_result()->fetch_assoc();
    
    if (!$center) {
        echo json_encode(['success' => false, 'message' => 'Center not found']);
        return;
    }
    
    echo json_encode(['success' => true, 'data' => $center]);
}

function bookAppointment() {
    global $conn;
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    $center_id = intval($_POST['center_id'] ?? 0);
    $service_type = sanitize($_POST['service_type'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $appointment_date = sanitize($_POST['appointment_date'] ?? '');
    $appointment_time = sanitize($_POST['appointment_time'] ?? '');
    
    if ($center_id === 0 || empty($service_type) || empty($appointment_date) || empty($appointment_time)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    
    $appointment_number = 'APT-' . date('YmdHis') . '-' . rand(100, 999);
    
    $query = "INSERT INTO maintenance_appointments (appointment_number, center_id, user_id, service_type, description, 
              appointment_date, appointment_time) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('siissss', $appointment_number, $center_id, $user_id, $service_type, $description, $appointment_date, $appointment_time);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Appointment booked successfully',
            'appointment_id' => $conn->insert_id,
            'appointment_number' => $appointment_number
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error booking appointment']);
    }
}

function getUserAppointments() {
    global $conn;
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT ma.*, mc.name, mc.name_ar, mc.phone FROM maintenance_appointments ma 
              JOIN maintenance_centers mc ON ma.center_id = mc.id 
              WHERE ma.user_id = ? ORDER BY ma.appointment_date DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    
    $appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $appointments]);
}

?>