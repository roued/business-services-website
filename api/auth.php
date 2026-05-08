<?php
/**
 * Authentication API
 * واجهة المصادقة
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/session.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    if ($action === 'register') {
        registerUser();
    } elseif ($action === 'login') {
        loginUser();
    } elseif ($action === 'logout') {
        logoutUser();
    }
}

function registerUser() {
    global $conn;
    
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = sanitize($_POST['phone'] ?? '');
    $company_name = sanitize($_POST['company_name'] ?? '');
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'جميع الحقول مطلوبة / All fields are required']);
        return;
    }
    
    if (!validateEmail($email)) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني غير صحيح / Invalid email']);
        return;
    }
    
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'كلمات المرور غير متطابقة / Passwords do not match']);
        return;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل / Password must be at least 6 characters']);
        return;
    }
    
    // Check if email exists
    $check_query = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('s', $email);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني مسجل بالفعل / Email already registered']);
        return;
    }
    
    // Hash password
    $hashed_password = hashPassword($password);
    
    // Insert user
    $insert_query = "INSERT INTO users (first_name, last_name, email, password, phone, company_name, user_type) 
                     VALUES (?, ?, ?, ?, ?, ?, 'customer')";
    
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param('ssssss', $first_name, $last_name, $email, $hashed_password, $phone, $company_name);
    
    if ($insert_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'تم التسجيل بنجاح / Registration successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطأ في التسجيل / Registration error']);
    }
}

function loginUser() {
    global $conn;
    
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني وكلمة المرور مطلوبة']);
        return;
    }
    
    // Get user
    $query = "SELECT id, first_name, last_name, email, password, user_type FROM users WHERE email = ? AND is_active = TRUE";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة']);
        return;
    }
    
    $user = $result->fetch_assoc();
    
    if (!verifyPassword($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة']);
        return;
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_type'] = $user['user_type'];
    
    echo json_encode(['success' => true, 'message' => 'تم تسجيل الدخول بنجاح', 'user_type' => $user['user_type']]);
}

?>