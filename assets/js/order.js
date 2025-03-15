// order.js
document.addEventListener('DOMContentLoaded', () => {
    redirectIfNotLoggedIn();
    const token = localStorage.getItem('token');
    let user = JSON.parse(localStorage.getItem('user'));
    const ordersContainer = document.querySelector('.orders-container');
    const ordersGrid = document.querySelector('.orders-grid');
    const orderDetailsContainer = document.getElementById('order-details-container');
    const statusFilter = document.getElementById('status-filter');
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id');
    let allOrders = [];

    // Fetch Orders
    async function fetchOrders() {
        disableButtons(true);
        try {
            if (orderId) {
                const response = await fetch('./api/get_order', {
                    method: 'POST',
                    headers: { 'Authorization': token, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: parseInt(orderId) })
                });
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();
                if (data.status === 'success' && data.data) {
                    renderOrderDetails(data.data);
                } else {
                    handleError(data.message || 'Failed to fetch order details.');
                }
            } else {
                const response = await fetch('./api/get_order', {
                    method: 'GET',
                    headers: { 'Authorization': token }
                });
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();
                if (data.status === 'success' && Array.isArray(data.data)) {
                    allOrders = data.data.sort((a, b) => new Date(b.date) - new Date(a.date));
                    applyFilter();
                } else {
                    handleError(data.message || 'Failed to fetch orders.');
                }
            }
        } catch (error) {
            console.error('Error fetching orders:', error);
            handleError('Error fetching orders.');
        } finally {
            disableButtons(false);
        }
    }

    // Apply Status Filter
    function applyFilter() {
        const filterValue = statusFilter.value;
        const filteredOrders = allOrders.filter(order => {
            const isExpired = order.status === 'pending' && (new Date() - new Date(order.date)) > 3 * 24 * 60 * 60 * 1000;
            return filterValue === 'all' || (isExpired ? 'expired' : order.status) === filterValue;
        });
        renderOrdersList(filteredOrders);
    }

    // Format Date Relative to Now
    function formatRelativeDate(dateStr) {
        if (!dateStr) return 'Invalid date';
        const germanyOffset = '+01:00';
        const dateInGermany = new Date(dateStr.replace(' ', 'T') + germanyOffset);
        if (isNaN(dateInGermany)) return 'Invalid date';
        const clientDate = new Date(dateInGermany.toLocaleString('en-US', { timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone }));
        if (isNaN(clientDate)) return 'Invalid date';
        const now = new Date();
        const diffMs = now - clientDate;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHr = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHr / 24);
        if (diffSec < 0) return 'Just now';
        if (diffSec < 60) return `${diffSec} second${diffSec !== 1 ? 's' : ''} ago`;
        if (diffMin < 60) return `${diffMin} minute${diffMin !== 1 ? 's' : ''} ago`;
        if (diffHr < 24) return `${diffHr} hour${diffHr !== 1 ? 's' : ''} ago`;
        if (diffDay <= 6) return `${diffDay} day${diffDay !== 1 ? 's' : ''} ago`;
        return clientDate.toLocaleDateString();
    }

    // Render Orders List
    function renderOrdersList(orders) {
        ordersGrid.innerHTML = '';
        if (orders.length === 0) {
            ordersGrid.innerHTML = '<p class="empty"><i class="fas fa-box-open"></i> No orders match your filter.</p>';
            return;
        }
        orders.forEach(order => {
            const isExpired = order.status === 'pending' && (new Date() - new Date(order.date)) > 3 * 24 * 60 * 60 * 1000;
            const effectiveStatus = isExpired ? 'expired' : order.status;
            const orderCard = document.createElement('div');
            orderCard.className = `order-card status-${effectiveStatus}`;
            orderCard.innerHTML = `
                <h3><i class="fas fa-receipt"></i> Order #${order.id}</h3>
                <p><i class="fas fa-info-circle"></i> Status: <span class="status-${effectiveStatus}">${effectiveStatus}</span></p>
                <p><i class="fas fa-clock"></i> Date: ${formatRelativeDate(order.date)}</p>
                <p><i class="fas fa-sticky-note"></i> Note: ${order.note || 'None'}</p>
            `;
            orderCard.addEventListener('click', () => window.location.assign(`./orders?order_id=${order.id}`));
            ordersGrid.appendChild(orderCard);
        });
    }

    // Render Order Details
    function renderOrderDetails(order) {
        const isExpired = order.status === 'pending' && (new Date() - new Date(order.date)) > 3 * 24 * 60 * 60 * 1000;
        const effectiveStatus = isExpired ? 'expired' : order.status;
        ordersContainer.style.display = 'none';
        orderDetailsContainer.style.display = 'block';
        orderDetailsContainer.innerHTML = `
            <h2><i class="fas fa-receipt"></i> Order #${order.id}</h2>
            <p><i class="fas fa-info-circle"></i> Status: <span class="status-${effectiveStatus}">${effectiveStatus}</span></p>
            <p><i class="fas fa-clock"></i> Date: ${formatRelativeDate(order.date)}</p>
            <p><i class="fas fa-door-open"></i> Room ID: ${order.room_id}</p>
            <p><i class="fas fa-sticky-note"></i> Note: ${order.note || 'None'}</p>
            <p><i class="fas fa-coins"></i> Total Price: ${order.total_price.toFixed(2)} L.E</p>
            <h3><i class="fas fa-boxes"></i> Items:</h3>
            <div class="order-items">
                ${order.items.length === 0 ? '<p class="empty"><i class="fas fa-box-open"></i> No items in this order.</p>' : renderOrderItems(order.items)}
            </div>
            ${effectiveStatus === 'pending' && order.total_price > 0 && order.items.length > 0 ? '<button class="btn-pay"><i class="fas fa-credit-card"></i> Pay Now</button>' : ''}
            <button class="btn-back"><i class="fas fa-arrow-left"></i> Back to Orders</button>
        `;

        // Attach event listeners programmatically
        const payButton = orderDetailsContainer.querySelector('.btn-pay');
        if (payButton) {
            payButton.addEventListener('click', () => showPaymentModal(order.id));
        }
        orderDetailsContainer.querySelector('.btn-back').addEventListener('click', goBack);
    }

    // Render Order Items
    function renderOrderItems(items) {
        return items.map(item => `
            <div class="order-item">
                <img src="${item.product_data.product_img || './uploads/default_product.jpg'}" alt="${item.product_data.name}">
                <div class="item-details">
                    <h4><i class="fas fa-tag"></i> ${item.product_data.name}</h4>
                    <p><i class="fas fa-coins"></i> Price: ${parseFloat(item.price_at_purchase).toFixed(2)} L.E</p>
                    <p><i class="fas fa-sort-numeric-up"></i> Quantity: ${item.quantity}</p>
                    <p><i class="fas fa-money-bill-wave"></i> Total: ${(item.price_at_purchase * item.quantity).toFixed(2)} L.E</p>
                </div>
            </div>
        `).join('');
    }

    // Show Payment Modal
    function showPaymentModal(orderId) {
        const modal = document.createElement('div');
        modal.className = 'payment-modal';
        modal.innerHTML = `
            <div class="payment-modal-content">
                <h2>Choose Payment Method</h2>
                <div class="payment-options">
                    <button class="payment-btn" data-method="paypal"><i class="fab fa-paypal"></i> PayPal</button>
                    <button class="payment-btn" data-method="instapay"><i class="fab fa-amazon-pay"></i> InstaPay</button>
                    <button class="payment-btn" data-method="credit"><i class="fas fa-credit-card"></i> Credit Card</button>
                    <button class="payment-btn" data-method="cash"><i class="fas fa-money-bill-wave"></i> Cash</button>
                    <button class="payment-btn" data-method="delivery"><i class="fas fa-truck"></i> Pay on Delivery</button>
                </div>
                <div class="payment-form-container"></div>
                <button class="btn-close-modal"><i class="fas fa-times"></i> Close</button>
            </div>
            <div class="loading-overlay" style="display: none;">
                <div class="spinner"></div>
            </div>
        `;
        document.body.appendChild(modal);

        // Event Listeners for Payment Buttons
        document.querySelectorAll('.payment-btn').forEach(btn => {
            btn.addEventListener('click', () => handlePaymentMethod(btn.dataset.method, orderId, modal));
        });

        // Close Modal
        modal.querySelector('.btn-close-modal').addEventListener('click', () => modal.remove());
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.remove();
        });
    }

    // Handle Payment Method Selection
    async function handlePaymentMethod(method, orderId, modal) {
        const formContainer = modal.querySelector('.payment-form-container');
        formContainer.innerHTML = '';

        if (method === 'paypal' || method === 'instapay') {
            showNotification('notifications-container', `${method.charAt(0).toUpperCase() + method.slice(1)} is not supported at this moment.`, 'info', 2000);
            return;
        }

        if (method === 'credit') {
            formContainer.innerHTML = `
                <form class="credit-card-form">
                    <div class="form-group">
                        <i class="fas fa-credit-card"></i>
                        <input type="text" class="form-control" placeholder="Card Number" maxlength="19" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="text" class="form-control" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-lock"></i>
                            <input type="text" class="form-control" placeholder="CVV" maxlength="4" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" placeholder="Cardholder Name" required>
                    </div>
                    <button type="submit" class="btn-confirm-payment">Confirm Payment</button>
                </form>
            `;
            const form = formContainer.querySelector('.credit-card-form');
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const cardNumber = form.querySelector('input[placeholder="Card Number"]').value.replace(/\s/g, '');
                const expiry = form.querySelector('input[placeholder="MM/YY"]').value;
                const cvv = form.querySelector('input[placeholder="CVV"]').value;
                const name = form.querySelector('input[placeholder="Cardholder Name"]').value;
                payOrder(orderId, { method: 'online', credit_card: { number: cardNumber, expiry, cvv, name } }, modal);
            });
        } else if (method === 'cash') {
            payOrder(orderId, { method: 'cash' }, modal);
        } else if (method === 'delivery') {
            payOrder(orderId, { method: 'delivery' }, modal);
        }
    }

    // Pay Order
    async function payOrder(orderId, payload, modal) {
        const loadingOverlay = modal.querySelector('.loading-overlay');
        const modalContent = modal.querySelector('.payment-modal-content');
        loadingOverlay.style.display = 'flex';
        disableButtons(true, modal);

        try {
            payload.order_id = orderId;
            const response = await fetch('./api/pay', {
                method: 'POST',
                headers: { 'Authorization': token, 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();
            if (data.status === 'success') {
                modalContent.innerHTML = `
                    <div class="payment-result success">
                        <i class="fas fa-check-circle"></i>
                        <h2>Payment Successful!</h2>
                        <p>${data.message || 'Payment processed successfully.'}</p>
                        <p>Redirecting in <span class="countdown">5</span> seconds...</p>
                    </div>
                `;
                let countdown = 5;
                const countdownSpan = modalContent.querySelector('.countdown');
                const interval = setInterval(() => {
                    countdown--;
                    countdownSpan.textContent = countdown;
                    if (countdown <= 0) {
                        clearInterval(interval);
                        window.location.assign(`./payments?payment_id=${data.payment_id}`);
                    }
                }, 1000);
            } else {
                throw new Error(data.message || 'Payment failed.');
            }
        } catch (error) {
            console.error('Error paying order:', error);
            modalContent.innerHTML = `
                <div class="payment-result failure">
                    <i class="fas fa-times-circle"></i>
                    <h2>Payment Failed</h2>
                    <p>${error.message || 'Error processing payment.'}</p>
                    <p>Showing options in <span class="countdown">5</span> seconds...</p>
                </div>
            `;
            let countdown = 5;
            const countdownSpan = modalContent.querySelector('.countdown');
            const interval = setInterval(() => {
                countdown--;
                countdownSpan.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(interval);
                    modalContent.innerHTML = `
                        <h2>Choose Payment Method</h2>
                        <div class="payment-options">
                            <button class="payment-btn" data-method="paypal"><i class="fab fa-paypal"></i> PayPal</button>
                            <button class="payment-btn" data-method="instapay"><i class="fab fa-amazon-pay"></i> InstaPay</button>
                            <button class="payment-btn" data-method="credit"><i class="fas fa-credit-card"></i> Credit Card</button>
                            <button class="payment-btn" data-method="cash"><i class="fas fa-money-bill-wave"></i> Cash</button>
                            <button class="payment-btn" data-method="delivery"><i class="fas fa-truck"></i> Pay on Delivery</button>
                        </div>
                        <div class="payment-form-container"></div>
                        <button class="btn-close-modal"><i class="fas fa-times"></i> Close</button>
                    `;
                    document.querySelectorAll('.payment-btn').forEach(btn => {
                        btn.addEventListener('click', () => handlePaymentMethod(btn.dataset.method, orderId, modal));
                    });
                    modal.querySelector('.btn-close-modal').addEventListener('click', () => modal.remove());
                }
            }, 1000);
        } finally {
            loadingOverlay.style.display = 'none';
            disableButtons(false, modal);
        }
    }

    // Show Notification
    function showNotification(containerId, message, type, duration = 2000) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `notification-item ${type}`;
        notification.innerHTML = `
            <span>${message}</span>
            <button class="notification-close"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(notification);

        setTimeout(() => notification.classList.add('visible'), 10);

        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => notification.remove());

        setTimeout(() => notification.remove(), duration);
    }

    // Handle Errors
    function handleError(message) {
        if (message === 'unauthorized') {
            showNotification('notifications-container', 'You are not authorized. Please log in again.', 'danger', 2000);
            setTimeout(logout, 2000);
        } else if (message === 'invalid json data' || message === 'invalid order id') {
            showNotification('notifications-container', 'Invalid order request.', 'danger', 2000);
        } else if (message === 'you dont have permission to access this order.') {
            showNotification('notifications-container', 'You don’t have permission to view this order.', 'danger', 2000);
        } else {
            showNotification('notifications-container', message, 'danger', 2000);
        }
        if (orderId) {
            orderDetailsContainer.innerHTML = `<p class="error"><i class="fas fa-exclamation-triangle"></i> ${message}</p>`;
        } else {
            ordersGrid.innerHTML = `<p class="error"><i class="fas fa-exclamation-triangle"></i> ${message}</p>`;
        }
    }

    // Go Back to Orders List
    function goBack() {
        window.location.assign('./orders');
    }

    // Disable Buttons Helper
    function disableButtons(state, scope = document) {
        scope.querySelectorAll('button').forEach(btn => btn.disabled = state);
    }

    // Filter Event Listener
    statusFilter.addEventListener('change', applyFilter);

    // Navbar Interactions
    setupNavbarInteractions(['my-orders-btn'], { 'my-orders-btn': 'My Orders feature already on this page!' });
    document.getElementById('change-profile-btn')?.addEventListener('click', (e) => {
        e.preventDefault();
        showNotification('notifications-container', 'Change Profile Picture feature coming soon!', 'info', 2000);
    });

    // Initial Load
    checkAuth(token, (newUser) => {
        user = newUser;
        localStorage.setItem('user', JSON.stringify(user));
        updateUserUI(user, { roomLabelId: 'my-room-label' });
    }, logout);
    setInterval(() => checkAuth(token, (newUser) => {
        user = newUser;
        localStorage.setItem('user', JSON.stringify(user));
        updateUserUI(user, { roomLabelId: 'my-room-label' });
    }, logout), 60000);
    fetchOrders();
});