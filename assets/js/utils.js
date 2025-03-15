// utils.js
// Shared utility functions

// Notifications
function showNotification(containerId, message, type = 'success', duration = 3000) {
    const notificationsContainer = document.getElementById(containerId);
    const notification = document.createElement('div');
    notification.className = `notification-item ${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button class="notification-close">×</button>
    `;

    notification.classList.add('visible');
    notificationsContainer.insertBefore(notification, notificationsContainer.firstChild);

    const timeout = setTimeout(() => removeNotification(notification), duration);
    notification.querySelector('.notification-close').addEventListener('click', () => {
        clearTimeout(timeout);
        removeNotification(notification);
    });
}

function removeNotification(notification) {
    notification.style.animation = 'fadeOut 0.5s ease-out';
    notification.addEventListener('animationend', () => notification.remove());
}

// Button Disable/Enable Logic
function disableButtons(disable = true, selector = '.btn-primary, .btn-action, .btn-register, .btn-login, .btn-change-password, .btn-place-order, .btn-cancel, .btn-add') {
    document.querySelectorAll(selector).forEach(button => button.disabled = disable);
}

// Check Auth
async function checkAuth(token, callbackSuccess, callbackFailure) {
    disableButtons(true);
    try {
        const response = await fetch('./api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ type: 'token' })
        });
        if (!response.ok) throw new Error('Token check failed');
        const data = await response.json();
        if (data.status === 'success') {
            callbackSuccess(data.data);
        } else {
            callbackFailure();
        }
    } catch (error) {
        console.error('Auth Check Error:', error);
        callbackFailure();
    } finally {
        disableButtons(false);
    }
}

// Logout
function logout() {
    disableButtons(true);
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    showNotification('notifications-container', 'Session expired. Please log in again.', 'danger');
    setTimeout(() => window.location.href = './index.php?action=login', 2000);
}

// Redirect if logged in (for login/register pages)
function redirectIfLoggedIn() {
    const token = localStorage.getItem('token');
    if (token) {
        window.location.href = './index.php?action=dashboard';
    }
}

// Redirect if not logged in (for authenticated pages)
function redirectIfNotLoggedIn() {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));
    if (!token || !user) {
        window.location.href = './index.php?action=login';
    }
}

// Setup Navbar Interactions
function setupNavbarInteractions(exclude = [], customMessages = {}) {
    document.getElementById('logout-btn').addEventListener('click', logout);
    ['wallet-transactions-btn', 'my-orders-btn', 'history-btn'].forEach(id => { // Removed 'make-new-order-btn'
        if (!exclude.includes(id)) {
            document.getElementById(id).addEventListener('click', (e) => {
                e.preventDefault();
                const defaultMessage = `${id.replace('-btn', '').replace(/-/g, ' ')} feature coming soon!`;
                showNotification('notifications-container', customMessages[id] || defaultMessage, 'info');
            });
        }
    });
    document.getElementById('profile-img-nav').addEventListener('click', () => window.location.href = './index.php?action=dashboard');
}

// Update User UI
function updateUserUI(user, options = {}) {
    const fullName = `${user.first_name} ${user.last_name}`;
    document.getElementById('user-name-nav').textContent = fullName;
    document.getElementById('profile-img-nav').src = user.profile_img || './uploads/empty.jpg';
    document.getElementById('user-balance-nav').textContent = `${user.balance || 0} L.E`;
    if (user.role === 'admin') document.getElementById('admin-nav').style.display = 'block';
    
    if (options.extraFields) {
        Object.keys(options.extraFields).forEach(id => {
            const element = document.getElementById(id);
            if (element) element.textContent = options.extraFields[id];
        });
    }
    
    if (options.profileImgId) {
        const profileImg = document.getElementById(options.profileImgId);
        if (profileImg) {
            profileImg.src = user.profile_img || './uploads/empty.jpg';
            if (user.profile_img) profileImg.classList.add('loaded');
        }
    }
    
    if (options.roomLabelId) {
        const roomLabel = document.getElementById(options.roomLabelId);
        if (roomLabel) {
            roomLabel.textContent = user.room_name || user.room_id ? (user.room_name || `Room ${user.room_id}`) : 'My Room';
        }
    }
}