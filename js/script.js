// ============================================================
// script.js — RecipeNest Main JavaScript
// ============================================================

// ---- NAV DROPDOWN ----
function toggleDropdown() {
    const d = document.getElementById('navDropdown');
    if (d) d.classList.toggle('open');
}
document.addEventListener('click', function (e) {
    const avatar = document.querySelector('.nav-avatar');
    const dropdown = document.getElementById('navDropdown');
    if (dropdown && avatar && !avatar.contains(e.target)) {
        dropdown.classList.remove('open');
    }
});

// ---- MOBILE MENU ----
function toggleMobileMenu() {
    const m = document.getElementById('mobileMenu');
    if (m) m.classList.toggle('open');
}

// ---- IMAGE PREVIEW (Add/Edit forms) ----
function previewImage(input) {
    if (!input.files || !input.files[0]) return;
    const file   = input.files[0];
    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById('imagePreview');
        const inner   = document.getElementById('uploadInner');
        if (preview) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        }
        if (inner) inner.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

// ---- TOGGLE FAVORITE (AJAX) ----
function toggleFavorite(recipeId, btn) {
    const formData = new FormData();
    formData.append('recipe_id', recipeId);

    fetch('../php/toggle_favorite.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.redirect) {
            window.location.href = '../php/' + data.redirect;
            return;
        }
        if (data.success) {
            if (data.action === 'added') {
                btn.classList.add('active');
                btn.innerHTML = btn.tagName === 'BUTTON' && btn.classList.contains('btn-fav-lg')
                    ? '⭐ Saved'
                    : '⭐';
                showToast('Added to favorites!', 'success');
            } else {
                btn.classList.remove('active');
                btn.innerHTML = btn.tagName === 'BUTTON' && btn.classList.contains('btn-fav-lg')
                    ? '♡ Save'
                    : '♡';
                showToast('Removed from favorites', 'info');
                // If on favorites page, hide the card
                if (window.location.pathname.includes('favorites.php')) {
                    const card = btn.closest('article');
                    if (card) {
                        card.style.transition = 'opacity 0.3s, transform 0.3s';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => card.remove(), 350);
                    }
                }
            }
        } else {
            showToast(data.message || 'Something went wrong', 'error');
        }
    })
    .catch(() => showToast('Network error. Try again.', 'error'));
}

// ---- TOAST NOTIFICATIONS ----
function showToast(message, type = 'info') {
    // Remove existing toast
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 9999;
        padding: 13px 22px;
        border-radius: 28px;
        font-size: 14px;
        font-weight: 500;
        font-family: 'DM Sans', sans-serif;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        animation: slideUp 0.3s ease;
        cursor: pointer;
        max-width: 320px;
        line-height: 1.4;
    `;

    const colors = {
        success: { bg: '#f0faf5', color: '#1a7f4b', border: '#b3e8cc' },
        error:   { bg: '#fff0f1', color: '#c0392b', border: '#ffc0c0' },
        info:    { bg: '#f0eeff', color: '#6c63ff', border: '#c9c4ff' },
    };
    const c = colors[type] || colors.info;
    toast.style.background   = c.bg;
    toast.style.color        = c.color;
    toast.style.border       = `1px solid ${c.border}`;

    toast.addEventListener('click', () => toast.remove());
    document.body.appendChild(toast);

    setTimeout(() => {
        if (toast.parentNode) {
            toast.style.animation = 'slideDown 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }
    }, 3000);
}

// Add toast animation styles
const toastStyle = document.createElement('style');
toastStyle.textContent = `
    @keyframes slideUp   { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    @keyframes slideDown { from { opacity:1; transform:translateY(0); } to { opacity:0; transform:translateY(20px); } }
`;
document.head.appendChild(toastStyle);

// ---- FLASH MESSAGES from URL params ----
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    if (params.get('added'))   showToast('Recipe published successfully!', 'success');
    if (params.get('edited'))  showToast('Recipe updated!', 'success');
    if (params.get('deleted')) showToast('Recipe deleted.', 'info');
    // Clean URL without reload
    if (params.get('added') || params.get('edited') || params.get('deleted')) {
        window.history.replaceState({}, '', window.location.pathname);
    }
});

// ---- SEARCH DEBOUNCE (homepage live search) ----
(function () {
    const searchInput = document.getElementById('searchInput') || document.querySelector('.hero-search input');
    if (!searchInput) return;

    let debounceTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const val = this.value.trim();
        debounceTimer = setTimeout(() => {
            if (val.length === 0 || val.length >= 2) {
                // Submit the search form
                const form = this.closest('form');
                if (form) form.submit();
            }
        }, 600);
    });
})();

// ---- CONFIRM DELETE ----
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });
});

// ---- ANIMATE STAT CARDS (dashboard) ----
document.addEventListener('DOMContentLoaded', function () {
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(el => {
        const target = parseInt(el.textContent, 10);
        if (isNaN(target) || target === 0) return;
        let current  = 0;
        const step   = Math.max(1, Math.floor(target / 20));
        const timer  = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(timer);
        }, 40);
    });
});

// ---- ANIMATE CATEGORY BARS (dashboard) ----
document.addEventListener('DOMContentLoaded', function () {
    const bars = document.querySelectorAll('.cat-bar-fill');
    if (!bars.length) return;
    // They already have width set inline — trigger CSS transition
    setTimeout(() => {
        bars.forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => { bar.style.width = w; });
            });
        });
    }, 100);
});

// ---- DRAG UPLOAD HIGHLIGHT ----
document.addEventListener('DOMContentLoaded', function () {
    const uploadArea = document.getElementById('uploadArea');
    if (!uploadArea) return;
    uploadArea.addEventListener('dragover', e => {
        e.preventDefault();
        uploadArea.style.borderColor = 'var(--clr-primary)';
        uploadArea.style.background  = 'rgba(108,99,255,0.05)';
    });
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '';
        uploadArea.style.background  = '';
    });
    uploadArea.addEventListener('drop', e => {
        e.preventDefault();
        uploadArea.style.borderColor = '';
        uploadArea.style.background  = '';
        const file  = e.dataTransfer.files[0];
        const input = document.getElementById('imageInput');
        if (!file || !input) return;
        const dt   = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        previewImage(input);
    });
});
