// Authentication Module

const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('register-form');

if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        await handleLogin();
    });
}

if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        await handleRegister();
    });
}

async function handleLogin() {
    const formData = new FormData(loginForm);
    const submitBtn = loginForm.querySelector('button[type="submit"]');

    try {
        setButtonLoading(submitBtn);

        const response = await fetch(API_BASE_URL + 'auth.php?action=login', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showNotification('تم تسجيل الدخول بنجاح', 'success');
            saveUserData({
                email: formData.get('email'),
                user_type: data.user_type
            });
            
            setTimeout(() => {
                if (data.user_type === 'admin') {
                    window.location.href = '/admin/dashboard.html';
                } else {
                    window.location.href = '/dashboard.html';
                }
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('خطأ في الاتصال: ' + error.message, 'error');
    } finally {
        setButtonLoading(submitBtn, false);
    }
}

async function handleRegister() {
    const formData = new FormData(registerForm);
    const submitBtn = registerForm.querySelector('button[type="submit"]');
    submitBtn.dataset.originalText = 'تسجيل';

    // Validation
    const email = formData.get('email');
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');

    if (!validateEmail(email)) {
        showNotification('البريد الإلكتروني غير صحيح', 'error');
        return;
    }

    if (password.length < 6) {
        showNotification('كلمة ال��رور يجب أن تكون 6 أحرف على الأقل', 'error');
        return;
    }

    if (password !== confirmPassword) {
        showNotification('كلمات المرور غير متطابقة', 'error');
        return;
    }

    try {
        setButtonLoading(submitBtn);

        const response = await fetch(API_BASE_URL + 'auth.php?action=register', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                window.location.href = '/login.html';
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('خطأ في الاتصال: ' + error.message, 'error');
    } finally {
        setButtonLoading(submitBtn, false);
    }
}

// Logout function
function logout() {
    if (confirm('هل تريد تسجيل الخروج؟')) {
        clearUserData();
        window.location.href = '/index.html';
    }
}
