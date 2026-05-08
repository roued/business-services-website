// Services Module

let allServices = [];
let currentCategory = 'all';

// Load services on page load
document.addEventListener('DOMContentLoaded', async () => {
    await loadServiceCategories();
    await loadAllServices();
    setupServiceFilters();
    setupSearch();
});

// Load service categories
async function loadServiceCategories() {
    try {
        const response = await fetchAPI('services.php?action=get_categories');
        
        if (response.success) {
            const categoryButtons = document.getElementById('category-buttons');
            if (categoryButtons) {
                categoryButtons.innerHTML = response.data.map(cat => `
                    <button class="filter-btn" data-category="${cat.id}">
                        ${cat.name_ar}
                    </button>
                `).join('');

                // Add click handlers
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        currentCategory = this.dataset.category;
                        filterServices();
                    });
                });
            }
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

// Load all services
async function loadAllServices() {
    try {
        const response = await fetchAPI('services.php?action=get_all&limit=100');
        
        if (response.success) {
            allServices = response.data;
            displayServices(allServices);
        }
    } catch (error) {
        console.error('Error loading services:', error);
    }
}

// Display services
function displayServices(services) {
    const grid = document.getElementById('services-grid');
    
    if (!grid) return;

    if (services.length === 0) {
        grid.innerHTML = '<p class="text-center" style="grid-column: 1 / -1; padding: 40px;">لا توجد خدمات متاحة</p>';
        return;
    }

    grid.innerHTML = services.map(service => `
        <div class="service-card" onclick="viewServiceDetails(${service.id})">
            <div class="service-card-image">
                <i class="fas fa-${getServiceIcon(service.category_id)}"></i>
            </div>
            <div class="service-card-content">
                <h3>${service.name_ar}</h3>
                <p>${service.description_ar.substring(0, 100)}...</p>
                <div class="service-rating">
                    <i class="fas fa-star"></i>
                    <span>${service.rating || '0'}</span>
                </div>
                <div class="service-price">${formatCurrency(service.price)}</div>
                <button class="btn btn-primary btn-block" onclick="addToCart(${service.id}, event)">
                    اطلب الآن
                </button>
            </div>
        </div>
    `).join('');
}

// Get service icon based on category
function getServiceIcon(categoryId) {
    const icons = {
        '1': 'cogs',
        '2': 'paint-brush',
        '3': 'bullhorn',
        '4': 'calculator',
        '5': 'gavel',
        '6': 'plane',
        '7': 'car',
        '8': 'wrench'
    };
    return icons[categoryId] || 'briefcase';
}

// Filter services by category
function filterServices() {
    if (currentCategory === 'all') {
        displayServices(allServices);
    } else {
        const filtered = allServices.filter(s => s.category_id == currentCategory);
        displayServices(filtered);
    }
}

// Setup search functionality
function setupSearch() {
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            const filtered = allServices.filter(s => 
                s.name_ar.includes(query) || s.description_ar.includes(query)
            );
            displayServices(filtered);
        });
    }
}

// Setup filter buttons
function setupServiceFilters() {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.dataset.category === 'all' ? 'all' : this.dataset.category;
            filterServices();
        });
    });
}

// View service details
function viewServiceDetails(serviceId) {
    // Store service ID and redirect
    localStorage.setItem('selectedService', serviceId);
    window.location.href = `/service-details.html?id=${serviceId}`;
}

// Add to cart
function addToCart(serviceId, event) {
    event.stopPropagation();
    
    if (!isUserLoggedIn()) {
        showNotification('يرجى تسجيل الدخول أولاً', 'warning');
        redirectToLogin();
        return;
    }

    const service = allServices.find(s => s.id == serviceId);
    if (!service) return;

    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    const existingItem = cart.find(item => item.id === serviceId);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: serviceId,
            name: service.name_ar,
            price: service.price,
            quantity: 1
        });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    showNotification('تمت إضافة الخدمة إلى السلة', 'success');
}
