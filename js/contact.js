// Contact Form Module

const contactForm = document.getElementById('contact-form');

if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        await handleContactSubmit();
    });
}

async function handleContactSubmit() {
    const formData = new FormData(contactForm);
    const submitBtn = contactForm.querySelector('button[type="submit"]');
    submitBtn.dataset.originalText = 'إرسال';

    // Validation
    const email = formData.get('email');
    const phone = formData.get('phone');

    if (!validateEmail(email)) {
        showNotification('البريد الإلكتروني غير صحيح', 'error');
        return;
    }

    if (phone && !validatePhone(phone)) {
        showNotification('رقم الجوال غير صحيح', 'error');
        return;
    }

    try {
        setButtonLoading(submitBtn);

        const response = await fetch(API_BASE_URL + 'contact.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showNotification(data.message, 'success');
            contactForm.reset();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('خطأ في الاتصال: ' + error.message, 'error');
    } finally {
        setButtonLoading(submitBtn, false);
    }
}
