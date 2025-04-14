/**
 * Admin Panel Genel JavaScript Dosyası
 * Tüm admin sayfalarında kullanılacak genel fonksiyonlar
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle fonksiyonu
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarState', document.querySelector('body').classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded');
        });
    }

    // Başlangıçta sidebar durumunu yükle
    if (localStorage.getItem('sidebarState') === 'collapsed') {
        document.querySelector('body').classList.add('sidebar-collapsed');
    }

    // Tüm silme butonları için onay dialogu
    document.querySelectorAll('.delete-btn, .btn-danger').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const confirmText = this.getAttribute('data-confirm') || 'Bu işlem geri alınamaz. Devam etmek istiyor musunuz?';

            Swal.fire({
                title: 'Emin misiniz?',
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF7A00',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'Vazgeç'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (this.tagName === 'A') {
                        window.location.href = this.href;
                    } else if (this.form) {
                        this.form.submit();
                    }
                }
            });
        });
    });

    // Form gönderimlerini yönetme
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Onay',
                text: form.getAttribute('data-confirm'),
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FF7A00',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Evet',
                cancelButtonText: 'Vazgeç'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Tablo sıralama fonksiyonu
    const sortTable = (table, column, asc = true) => {
        const dirModifier = asc ? 1 : -1;
        const tBody = table.tBodies[0];
        const rows = Array.from(tBody.querySelectorAll('tr'));

        const sortedRows = rows.sort((a, b) => {
            const aColText = a.querySelector(`td:nth-child(${column + 1})`).textContent.trim();
            const bColText = b.querySelector(`td:nth-child(${column + 1})`).textContent.trim();

            return aColText > bColText ? (1 * dirModifier) : (-1 * dirModifier);
        });

        while (tBody.firstChild) {
            tBody.removeChild(tBody.firstChild);
        }

        tBody.append(...sortedRows);

        table.querySelectorAll('th').forEach(th => th.classList.remove('th-sort-asc', 'th-sort-desc'));
        table.querySelector(`th:nth-child(${column + 1})`).classList.toggle('th-sort-asc', asc);
        table.querySelector(`th:nth-child(${column + 1})`).classList.toggle('th-sort-desc', !asc);
    };

    // Tablo sıralama event listener'ları
    document.querySelectorAll('.sortable th').forEach(headerCell => {
        headerCell.addEventListener('click', () => {
            const tableElement = headerCell.parentElement.parentElement.parentElement;
            const headerIndex = Array.prototype.indexOf.call(headerCell.parentElement.children, headerCell);
            const currentIsAscending = headerCell.classList.contains('th-sort-asc');

            sortTable(tableElement, headerIndex, !currentIsAscending);
        });
    });

    // Tarih formatlama fonksiyonu
    window.formatDate = function(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('tr-TR', options);
    };

    // Toast bildirimleri
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                ${type === 'success' ? '<i class="fas fa-check-circle"></i>' : ''}
                ${type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' : ''}
                ${type === 'warning' ? '<i class="fas fa-exclamation-triangle"></i>' : ''}
            </div>
            <div class="toast-message">${message}</div>
            <div class="toast-close"><i class="fas fa-times"></i></div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    };

    // Otomatik toast gösterimi
    if (localStorage.getItem('toastMessage')) {
        const toastData = JSON.parse(localStorage.getItem('toastMessage'));
        showToast(toastData.message, toastData.type);
        localStorage.removeItem('toastMessage');
    }
});

// AJAX istekleri için yardımcı fonksiyon
window.makeRequest = async function(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };

    if (data) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Request failed:', error);
        throw error;
    }
};

// Form verilerini serialize etme
window.serializeForm = function(form) {
    const formData = new FormData(form);
    const object = {};

    formData.forEach((value, key) => {
        if (!object[key]) {
            object[key] = value;
            return;
        }

        if (!Array.isArray(object[key])) {
            object[key] = [object[key]];
        }

        object[key].push(value);
    });

    return object;
};