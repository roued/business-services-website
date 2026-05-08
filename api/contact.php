<?php
/**
 * Contact Form API
 * واجهة نموذج الاتصال
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';
require_once '../config/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    submitContact();
}

function submitContact() {
    global $conn;
    
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'الحقول المطلوبة غير مكتملة']);
        return;
    }
    
    if (!validateEmail($email)) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني غير صحيح']);
        return;
    }
    
    // Insert contact message
    $query = "INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssss', $name, $email, $phone, $subject, $message);
    
    if ($stmt->execute()) {
        // Send email notification
        $admin_email = 'admin@business-services.com';
        $email_subject = 'رسالة اتصال جديدة / New Contact Message';
        $email_body = "<h2>رسالة اتصال جديدة / New Contact Message</h2>
                       <p><strong>الاسم / Name:</strong> $name</p>
                       <p><strong>البريد / Email:</strong> $email</p>
                       <p><strong>الهاتف / Phone:</strong> $phone</p>
                       <p><strong>الموضوع / Subject:</strong> $subject</p>
                       <p><strong>الرسالة / Message:</strong></p>
                       <p>$message</p>";
        
        sendEmail($admin_email, $email_subject, $email_body);
        
        echo json_encode(['success' => true, 'message' => 'شكراً لتواصلك معنا / Thank you for contacting us']);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطأ في إرسال الرسالة / Error sending message']);
    }
}

?>