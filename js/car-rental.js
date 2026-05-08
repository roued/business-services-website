// Car Rental Module

let allCars = [];

document.addEventListener('DOMContentLoaded', async () => {
    await loadCars();
    setupCarFilters();
});

// Load cars
async function loadCars() {
    try {
        const response = await fetchAPI('car_rental.php?action=get_cars&limit=20');
        
        if (response.success) {
            allCars = response.data;
            displayCars(allCars);
        }
    } catch (error) {
        console.error('Error loading cars:', error);
    }
}

// Display cars
function displayCars(cars) {
    const grid = document.getElementById('cars-grid');
    
    if (!grid) return;

    if (cars.length === 0) {
        grid.innerHTML = '<p class="text-center" style="grid-column: 1 / -1; padding: 40px;">لا توجد سيارات متاحة</p>';
        return;
    }

    grid.innerHTML = cars.map(car => `
        <div class="car-card">
            <div class="car-card-image">
                <i class="fas fa-car"></i>
            </div>
            <div class="car-card-content">
                <h3>${car.brand} ${car.model} (${car.year})</h3>
                <div class="car-specs">
                    <div class="car-spec">
                        <i class="fas fa-users"></i>
                        <span>${car.seats} مقاعد</span>
                    </div>
                    <div class="car-spec">
                        <i class="fas fa-gas-pump"></i>
                        <span>${car.fuel_type}</span>
                    </div>
                </div>
                <div class="car-price">${formatCurrency(car.daily_price)} /اليوم</div>
                <button class="btn btn-primary btn-block" onclick="bookCar(${car.id})">
                    احجز الآن
                </button>
            </div>
        </div>
    `).join('');
}

// Setup car filters
function setupCarFilters() {
    const carTypeFilter = document.getElementById('car-type-filter');
    const priceRange = document.getElementById('price-range');
    const priceDisplay = document.getElementById('price-display');

    if (carTypeFilter) {
        carTypeFilter.addEventListener('change', filterCars);
    }

    if (priceRange) {
        priceRange.addEventListener('input', () => {
            const maxPrice = priceRange.value;
            priceDisplay.textContent = `0 - ${maxPrice} ريال`;
            filterCars();
        });
    }
}

// Filter cars
function filterCars() {
    const carType = document.getElementById('car-type-filter')?.value || '';
    const maxPrice = parseInt(document.getElementById('price-range')?.value || 10000);

    let filtered = allCars;

    if (carType) {
        filtered = filtered.filter(car => car.car_type === carType);
    }

    filtered = filtered.filter(car => car.daily_price <= maxPrice);

    displayCars(filtered);
}

// Book car
function bookCar(carId) {
    if (!isUserLoggedIn()) {
        showNotification('يرجى تسجيل الدخول أولاً', 'warning');
        redirectToLogin();
        return;
    }

    localStorage.setItem('selectedCar', carId);
    window.location.href = `/car-booking.html?car_id=${carId}`;
}
