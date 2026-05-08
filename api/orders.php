<?php
/**
 * Orders API
 * واجهة الطلبات
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/session.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    if ($action === 'create') {
        createNewOrder();
    }
} elseif ($method === 'GET') {
    if ($action === 'list') {
        getUserOrders();
    } elseif ($action === 'get') {
        getOrderDetails();
    }
}

function createNewOrder() {
    global $conn;
    
    $user_id = $_SESSION['user_id'];
    $service_id = intval($_POST['service_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    $start_date = sanitize($_POST['start_date'] ?? '');
    $end_date = sanitize($_POST['end_date'] ?? '');
    
    if ($service_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Service ID required']);
        return;
    }
    
    // Get service
    $service = getServiceById($conn, $service_id);
    if (!$service) {
        echo json_encode(['success' => false, 'message' => 'Service not found']);
        return;
    }
    
    $unit_price = $service['discount_price'] ?? $service['price'];
    $total_price = $quantity * $unit_price;
    
    // Create order
    $order_number = generateOrderNumber();
    $query = "INSERT INTO orders (order_number, user_id, service_id, quantity, unit_price, total_price, final_price, start_date, end_date) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('siiiidss', $order_number, $user_id, $service_id, $quantity, $unit_price, $total_price, $total_price, $start_date, $end_date);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Order created successfully',
            'order_id' => $order_id,
            'order_number' => $order_number
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating order']);
    }
}

function getUserOrders() {
    global $conn;
    
    $user_id = $_SESSION['user_id'];
    $status = sanitize($_GET['status'] ?? '');
    
    $query = "SELECT o.*, s.name, s.name_ar FROM orders o 
              JOIN services s ON o.service_id = s.id 
              WHERE o.user_id = ?";
    
    if (!empty($status)) {
        $query .= " AND o.status = ?";
    }
    
    $query .= " ORDER BY o.created_at DESC";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($status)) {
        $stmt->bind_param('is', $user_id, $status);
    } else {
        $stmt->bind_param('i', $user_id);
    }
    
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $orders]);
}

function getOrderDetails() {
    global $conn;
    
    $user_id = $_SESSION['user_id'];
    $order_id = intval($_GET['order_id'] ?? 0);
    
    if ($order_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Order ID required']);
        return;
    }
    
    $query = "SELECT o.*, s.name, s.name_ar FROM orders o 
              JOIN services s ON o.service_id = s.id 
              WHERE o.id = ? AND o.user_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $order_id, $user_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        return;
    }
    
    // Get invoices
    $invoice_query = "SELECT * FROM invoices WHERE order_id = ?";
    $invoice_stmt = $conn->prepare($invoice_query);
    $invoice_stmt->bind_param('i', $order_id);
    $invoice_stmt->execute();
    $invoices = $invoice_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'order' => $order,
        'invoices' => $invoices
    ]);
}

?>