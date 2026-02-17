<?php
// فایل: includes/login.php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

// دریافت داده‌های JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'داده‌های ورودی نامعتبر است']);
    exit;
}

$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$remember = $input['remember'] ?? false;

// اعتبارسنجی اولیه
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'لطفاً همه فیلدها را پر کنید']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // جستجوی کاربر با نام کاربری یا ایمیل
    $query = "SELECT * FROM users WHERE username = :username OR email = :username LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch();
        
        // بررسی رمز عبور
        if (password_verify($password, $user['password'])) {
            // رمز درست است
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['logged_in'] = true;
            
            // به‌روزرسانی آخرین ورود
            $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':id', $user['id']);
            $updateStmt->execute();
            
            // اگر "مرا به خاطر بسپار" فعال بود
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                // توکن رو در دیتابیس ذخیره کن (نیاز به فیلد جدید)
                setcookie('remember_token', $token, time() + (86400 * 30), "/"); // 30 روز
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'ورود موفقیت‌آمیز بود',
                'redirect' => 'dashboard.php',
                'user' => [
                    'name' => $user['full_name'],
                    'username' => $user['username']
                ]
            ]);
        } else {
            // رمز اشتباه است
            echo json_encode(['success' => false, 'message' => 'رمز عبور اشتباه است']);
        }
    } else {
        // کاربر یافت نشد
        echo json_encode(['success' => false, 'message' => 'کاربری با این مشخصات یافت نشد']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطا در پایگاه داده: ' . $e->getMessage()]);
}
?>