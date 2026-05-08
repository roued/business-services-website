<?php
/**
 * Services API
 * واجهة الخدمات
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';
require_once '../config/functions.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_categories':
        getCategories();
        break;
    case 'get_by_category':
        getByCategory();
        break;
    case 'get_service':
        getService();
        break;
    case 'search':
        searchServices();
        break;
    case 'get_all':
        getAllServices();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getCategories() {
    global $conn;
    
    $query = "SELECT id, name, name_ar, description, icon FROM service_categories WHERE is_active = TRUE ORDER BY display_order ASC";
    $result = $conn->query($query);
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $categories]);
}

function getByCategory() {
    global $conn;
    
    $category_id = intval($_GET['category_id'] ?? 0);
    
    if ($category_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Category ID required']);
        return;
    }
    
    $query = "SELECT * FROM services WHERE category_id = ? AND is_active = TRUE ORDER BY id DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $services = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $services]);
}

function getService() {
    global $conn;
    
    $service_id = intval($_GET['service_id'] ?? 0);
    
    if ($service_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Service ID required']);
        return;
    }
    
    // Get service
    $query = "SELECT s.*, c.name as category_name FROM services s 
              JOIN service_categories c ON s.category_id = c.id 
              WHERE s.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $service_id);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
    
    // Get reviews
    $reviews_query = "SELECT r.*, u.first_name, u.last_name FROM reviews r 
                      JOIN users u ON r.user_id = u.id 
                      WHERE r.service_id = ? ORDER BY r.created_at DESC";
    $reviews_stmt = $conn->prepare($reviews_query);
    $reviews_stmt->bind_param('i', $service_id);
    $reviews_stmt->execute();
    $reviews = $reviews_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'service' => $service,
        'reviews' => $reviews
    ]);
}

function searchServices() {
    global $conn;
    
    $search_term = sanitize($_GET['query'] ?? '');
    
    if (empty($search_term)) {
        echo json_encode(['success' => false, 'message' => 'Search query required']);
        return;
    }
    
    $search_term = '%' . $search_term . '%';
    
    $query = "SELECT * FROM services WHERE (name LIKE ? OR description LIKE ?) AND is_active = TRUE ORDER BY views DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $search_term, $search_term);
    $stmt->execute();
    
    $services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $services]);
}

function getAllServices() {
    global $conn;
    
    $limit = intval($_GET['limit'] ?? 12);
    $offset = intval($_GET['offset'] ?? 0);
    
    $query = "SELECT * FROM services WHERE is_active = TRUE ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    
    $services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM services WHERE is_active = TRUE";
    $count_result = $conn->query($count_query);
    $total = $count_result->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $services,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);
}

?>