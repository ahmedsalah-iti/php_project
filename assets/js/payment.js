document.addEventListener('DOMContentLoaded', () => {
    redirectIfNotLoggedIn();
    const token = localStorage.getItem('token');
    let user = JSON.parse(localStorage.getItem('user'));
    const paymentsContainer = document.querySelector('.orders-container');
    const paymentsGrid = document.querySelector('.orders-grid');
    const paymentDetailsContainer = document.getElementById('payment-details-container');
    const statusFilter = document.getElementById('status-filter');
    const urlParams = new URLSearchParams(window.location.search);
    const paymentId = urlParams.get('payment_id');
    let allPayments = []; // Flat array of payment objects

    // Fetch Payments
    async function fetchPayments() {
        disableButtons(true);
        try {
            if (paymentId) {
                // Fetch a single payment by payment_id
                const response = await fetch('./api/get_payment', {
                    method: 'POST',
                    headers: { 'Authorization': token, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ payment_id: parseInt(paymentId) })
                });
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();
                if (data.status === 'success' && data.data) {
                    // Assuming POST returns a single payment object (based on previous example)
                    renderPaymentDetails(data.data);
                } else {
                    handleError(data.message || 'Failed to fetch payment details.');
                }
            } else {
                // Fetch all payments
                const response = await fetch('./api/get_payment', {
                    method: 'GET',
                    headers: { 'Authorization': token }
                });
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();
                if (data.status === 'success' && data.data) {
                    // Flatten the grouped payments into a single array
                    allPayments = Object.values(data.data).flat();
                    allPayments.sort((a, b) => (b.date || '9999-12-31') > (a.date || '9999-12-31') ? 1 : -1);
                    applyFilter();
                } else {
                    handleError(data.message || 'Failed to fetch payments.');
                }
            }
        } catch (error) {
            console.error('Error fetching payments:', error);
            handleError('Error fetching payments.');
        } finally {
            disableButtons(false);
        }
    }

    // Apply Status Filter
    function applyFilter() {
        const filterValue = statusFilter.value;
        const filteredPayments = allPayments.filter(payment => 
            filterValue === 'all' || payment.status === filterValue
        );
        renderPaymentsList(filteredPayments);
    }

    // Format Date Relative to Now
    function formatRelativeDate(dateStr) {
        if (!dateStr) return 'Not processed yet';
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

    // Render Payments List
    function renderPaymentsList(payments) {
        paymentsGrid.innerHTML = '';
        if (payments.length === 0) {
            paymentsGrid.innerHTML = '<p class="empty"><i class="fas fa-wallet"></i> No payments match your filter.</p>';
            return;
        }
        payments.forEach(payment => {
            const paymentCard = document.createElement('div');
            paymentCard.className = `order-card status-${payment.status}`;
            paymentCard.innerHTML = `
                <h3><i class="fas fa-wallet"></i> Payment #${payment.id}</h3>
                <p><i class="fas fa-info-circle"></i> Status: <span class="status-${payment.status}">${payment.status}</span></p>
                <p><i class="fas fa-receipt"></i> Order: #${payment.order_id}</p>
                <p><i class="fas fa-credit-card"></i> Method: ${payment.method}</p>
                <p><i class="fas fa-clock"></i> Date: ${formatRelativeDate(payment.date)}</p>
            `;
            paymentCard.addEventListener('click', () => window.location.assign(`./payments?payment_id=${payment.id}`));
            paymentsGrid.appendChild(paymentCard);
        });
    }

    // Render Payment Details
    function renderPaymentDetails(payment) {
        paymentsContainer.style.display = 'none';
        paymentDetailsContainer.style.display = 'block';
        paymentDetailsContainer.innerHTML = `
            <h2><i class="fas fa-wallet"></i> Payment #${payment.id}</h2>
            <p><i class="fas fa-info-circle"></i> Status: <span class="status-${payment.status}">${payment.status}</span></p>
            <p><i class="fas fa-receipt"></i> Order ID: ${payment.order_id}</p>
            <p><i class="fas fa-credit-card"></i> Method: ${payment.method || 'Not specified'}</p>
            <p><i class="fas fa-clock"></i> Date: ${formatRelativeDate(payment.date)}</p>
            <button class="btn-back"><i class="fas fa-arrow-left"></i> Back to Payments</button>
        `;
        paymentDetailsContainer.querySelector('.btn-back').addEventListener('click', goBack);
    }

    // Go Back to Payments List
    function goBack() {
        window.location.assign('./payments');
    }

    // Handle Errors
    function handleError(message) {
        showNotification('notifications-container', message, 'danger', 2000);
        if (paymentId) {
            paymentDetailsContainer.innerHTML = `<p class="error"><i class="fas fa-exclamation-triangle"></i> ${message}</p>`;
        } else {
            paymentsGrid.innerHTML = `<p class="error"><i class="fas fa-exclamation-triangle"></i> ${message}</p>`;
        }
    }

    // Filter Event Listener
    statusFilter.addEventListener('change', applyFilter);

    // Navbar Interactions
    setupNavbarInteractions(['my-payments-btn'], { 'my-payments-btn': 'My Payments feature already on this page!' });
    document.getElementById('change-profile-btn')?.addEventListener('click', (e) => {
        e.preventDefault();
        showNotification('notifications-container', 'Change Profile Picture feature coming soon!', 'info', 2000);
    });

    // Initial Load
    checkAuth(token, (newUser) => {
        user = newUser;
        localStorage.setItem('user', JSON.stringify(user));
        updateUserUI(user);
    }, logout);
    setInterval(() => checkAuth(token, (newUser) => {
        user = newUser;
        localStorage.setItem('user', JSON.stringify(user));
        updateUserUI(user);
    }, logout), 60000);
    fetchPayments();
});