// Main JavaScript File

// API Base URL
const API_BASE_URL = '/api/';

// Mobile Menu Toggle
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

if (hamburger) {
    hamburger.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });

    // Close menu when link is clicked
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });
    });
}

// Fetch wrapper function
async function fetchAPI(endpoint, options = {}) {
    try {
        const response = await fetch(API_BASE_URL + endpoint, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;

    // Insert at top of page
    document.body.insertBefore(alertDiv, document.body.firstChild);

    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Format currency
function formatCurrency(amount, currency = 'SAR') {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: currency,
        minimumFractionDigits: 2
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('ar-SA', options);
}

// Check if user is logged in
function isUserLoggedIn() {
    // This would need to be checked from session
    return localStorage.getItem('userLoggedIn') === 'true';
}

// Redirect to login
function redirectToLogin() {
    window.location.href = '/login.html';
}

// Get user data from localStorage
function getUserData() {
    const userData = localStorage.getItem('userData');
    return userData ? JSON.parse(userData) : null;
}

// Save user data
function saveUserData(data) {
    localStorage.setItem('userData', JSON.stringify(data));
    localStorage.setItem('userLoggedIn', 'true');
}

// Clear user data
function clearUserData() {
    localStorage.removeItem('userData');
    localStorage.removeItem('userLoggedIn');
}

// Smooth scroll
function smoothScroll(target) {
    document.querySelector(target).scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

// Add loading state to button
function setButtonLoading(button, isLoading = true) {
    if (isLoading) {
        button.disabled = true;
        button.innerHTML = '<span class="loading"></span> جاري...';
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || 'إرسال';
    }
}

// Form validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[0-9\-\+\(\) ]{7,}$/;
    return re.test(phone);
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Website loaded successfully');
});
