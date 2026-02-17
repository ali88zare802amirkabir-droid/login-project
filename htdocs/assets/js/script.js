// فایل: assets/js/script.js

document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // نمایش لودینگ
    const submitBtn = document.getElementById('loginBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> در حال ورود...';
    submitBtn.disabled = true;
    
    // مخفی کردن پیام قبلی
    const messageDiv = document.getElementById('message');
    messageDiv.style.display = 'none';
    
    // جمع‌آوری داده‌ها
    const formData = {
        username: document.getElementById('username').value,
        password: document.getElementById('password').value,
        remember: document.getElementById('remember').checked
    };
    
    try {
        const response = await fetch('includes/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // نمایش پیام موفقیت
            showMessage('success', result.message);
            
            // اگر کاربر وجود داشت، به داشبورد برو
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1000);
        } else {
            // نمایش خطا
            showMessage('error', result.message);
            
            // برگردوندن دکمه به حالت اول
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
        
    } catch (error) {
        showMessage('error', 'خطا در ارتباط با سرور');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// نمایش پیام
function showMessage(type, text) {
    const messageDiv = document.getElementById('message');
    messageDiv.className = `message ${type}`;
    messageDiv.innerHTML = text;
    messageDiv.style.display = 'block';
    
    // اسکرول به بالا
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// چشم برای نمایش پسورد
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target') || 'password';
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    });
});