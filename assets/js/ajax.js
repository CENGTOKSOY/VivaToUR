// AJAX işlemleri
function getTours() {
    fetch('/api/get_tours.php')
        .then(response => response.json())
        .then(data => {
            const tourList = document.querySelector('.tour-list');
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
    });
}

// Diğer AJAX fonksiyonları (updateTour, deleteTour) buraya eklenebilir.