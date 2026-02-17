<?php
// فایل: dashboard.php
session_start();

// بررسی لاگین بودن کاربر
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل کاربری</title>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Vazirmatn', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .dashboard {
            background: white;
            border-radius: 30px;
            padding: 50px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .welcome-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .welcome-icon i {
            font-size: 50px;
            color: white;
        }

        h1 {
            color: #333;
            margin-bottom: 15px;
        }

        .user-name {
            color: #667eea;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .user-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
            text-align: right;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #e1e1e1;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item i {
            width: 30px;
            color: #667eea;
            font-size: 20px;
        }

        .logout-btn {
            display: inline-block;
            padding: 15px 40px;
            background: #ff4444;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.3);
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .logout-btn:hover {
            background: #ff1111;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 68, 68, 0.4);
        }

        .logout-btn i {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="welcome-icon">
            <i class="fas fa-user-circle"></i>
        </div>
        
        <h1>خوش آمدید</h1>
        <div class="user-name">
            <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            <span style="font-size: 18px; color: #666; display: block;">@<?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>

        <div class="user-info">
            <div class="info-item">
                <i class="fas fa-check-circle" style="color: #00C851;"></i>
                <span>وضعیت حساب: <strong style="color: #00C851;">فعال</strong></span>
            </div>
            <div class="info-item">
                <i class="fas fa-calendar"></i>
                <span>آخرین ورود: امروز</span>
            </div>
        </div>

        <p style="color: #666; margin: 20px 0;">
            به پنل کاربری خود خوش آمدید. از اینجا می‌توانید حساب خود را مدیریت کنید.
        </p>

        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            خروج از حساب
        </a>
    </div>
</body>
</html>