// AJAX işlemleri
function getTours() {
    fetch('/api/get_tours.php')
        .then(response => response.json())
        .then(data => {
            const tourList = document.querySelector('.tour-list');
            if (!tourList) return; // Eğer sayfada .tour-list yoksa devam etme
            tourList.innerHTML = '';
            data.forEach(tour => {
                const tourCard = document.createElement('div');
                tourCard.className = 'tour-card';
                tourCard.innerHTML = `
                    <h3>${tour.name}</h3>
                    <p>${tour.description}</p>
                    <p><strong>Fiyat:</strong> ${tour.price} TL</p>
                `;
                tourList.appendChild(tourCard);
            });
        })
        .catch(error => {
            console.error('Turlar yüklenirken hata:', error);
        });
}

function addTour(tourData) {
    fetch('/api/add_tour.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(tourData)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                getTours();
            }
        })
        .catch(error => {
            console.error('Tur eklenirken hata:', error);
        });
}

// Diğer AJAX fonksiyonları (updateTour, deleteTour) buraya eklenebilir.

// Sayfa yüklendiğinde turları getir
document.addEventListener('DOMContentLoaded', function() {
    getTours();
});
