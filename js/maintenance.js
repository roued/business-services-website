// Maintenance Centers Module

let allCenters = [];

document.addEventListener('DOMContentLoaded', async () => {
    await loadMaintenanceCenters();
    setupSearch();
});

// Load maintenance centers
async function loadMaintenanceCenters() {
    try {
        const response = await fetchAPI('maintenance.php?action=get_centers&limit=20');
        
        if (response.success) {
            allCenters = response.data;
            displayCenters(allCenters);
        }
    } catch (error) {
        console.error('Error loading centers:', error);
    }
}

// Display centers
function displayCenters(centers) {
    const grid = document.getElementById('centers-grid');
    
    if (!grid) return;

    if (centers.length === 0) {
        grid.innerHTML = '<p class="text-center" style="grid-column: 1 / -1; padding: 40px;">لا توجد مراكز صيانة متاحة</p>';
        return;
    }

    grid.innerHTML = centers.map(center => `
        <div class="center-card">
            <div class="center-card-image">
                <i class="fas fa-wrench"></i>
            </div>
            <div class="center-card-content">
                <h3>${center.name_ar}</h3>
                <p>${center.address}</p>
                <p><strong>المدينة:</strong> ${center.city}</p>
                <p><strong>التقييم:</strong> <i class="fas fa-star"></i> ${center.rating}</p>
                <p><strong>الهاتف:</strong> ${center.phone}</p>
                <button class="btn btn-primary btn-block" onclick="bookAppointment(${center.id})">
                    احجز موعد
                </button>
            </div>
        </div>
    `).join('');
}

// Setup search
function setupSearch() {
    const searchBtn = document.getElementById('search-btn');
    const citySearch = document.getElementById('city-search');

    if (searchBtn) {
        searchBtn.addEventListener('click', () => {
            const city = citySearch?.value || '';
            if (city) {
                searchByCity(city);
            }
        });
    }

    if (citySearch) {
        citySearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchBtn?.click();
            }
        });
    }
}

// Search by city
async function searchByCity(city) {
    try {
        const response = await fetchAPI(`maintenance.php?action=get_by_city&city=${encodeURIComponent(city)}`);
        
        if (response.success) {
            displayCenters(response.data);
            showNotification(`وجدنا ${response.data.length} مركز صيانة في ${city}`, 'success');
        } else {
            displayCenters([]);
            showNotification('لا توجد مراكز صيانة في هذه المدينة', 'warning');
        }
    } catch (error) {
        console.error('Error searching centers:', error);
        showNotification('خطأ في البحث', 'error');
    }
}

// Book appointment
function bookAppointment(centerId) {
    if (!isUserLoggedIn()) {
        showNotification('يرجى تسجيل الدخول أولاً', 'warning');
        redirectToLogin();
        return;
    }

    localStorage.setItem('selectedCenter', centerId);
    window.location.href = `/appointment-booking.html?center_id=${centerId}`;
}
