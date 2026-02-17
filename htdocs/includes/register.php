<?php
// فایل: includes/register.php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

// دریافت داده‌های JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'داده‌های ورودی نامعتبر است']);
    exit;
}

// دریافت داده‌ها
$fullname = trim($input['fullname'] ?? '');
$username = trim($input['username'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$confirm_password = $input['confirm_password'] ?? '';

// اعتبارسنجی
$errors = [];

if (empty($fullname)) {
    $errors[] = 'نام و نام خانوادگی الزامی است';
}

if (empty($username) || strlen($username) < 3) {
    $errors[] = 'نام کاربری باید حداقل 3 کاراکتر باشد';
} elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $errors[] = 'نام کاربری فقط می‌تواند شامل حروف، اعداد و زیرخط باشد';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'ایمیل معتبر وارد کنید';
}

if (empty($password) || strlen($password) < 6) {
    $errors[] = 'رمز عبور باید حداقل 6 کاراکتر باشد';
}

if ($password !== $confirm_password) {
    $errors[] = 'رمز عبور و تکرار آن مطابقت ندارند';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode('<br>', $errors)]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // بررسی تکراری نبودن نام کاربری و ایمیل
    $checkQuery = "SELECT id FROM users WHERE username = :username OR email = :email";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':username', $username);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        $existing = $checkStmt->fetch();
        echo json_encode(['success' => false, 'message' => 'نام کاربری یا ایمیل قبلاً ثبت شده است']);
        exit;
    }
    
    // هش کردن رمز عبور
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // درج کاربر جدید
    $insertQuery = "INSERT INTO users (full_name, username, email, password) VALUES (:fullname, :username, :email, :password)";
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->bindParam(':fullname', $fullname);
    $insertStmt->bindParam(':username', $username);
    $insertStmt->bindParam(':email', $email);
    $insertStmt->bindParam(':password', $hashed_password);
    
    if ($insertStmt->execute()) {
        // ثبت نام موفق
        $user_id = $db->lastInsertId();
        
        // ایجاد سشن برای کاربر
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $fullname;
        $_SESSION['logged_in'] = true;
        
        echo json_encode([
            'success' => true,
            'message' => 'ثبت نام با موفقیت انجام شد',
            'redirect' => 'dashboard.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطا در ثبت نام']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطا در پایگاه داده: ' . $e->getMessage()]);
}
?>