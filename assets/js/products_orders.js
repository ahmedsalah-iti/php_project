// products_orders.js
document.addEventListener('DOMContentLoaded', () => {
    redirectIfNotLoggedIn();
    const token = localStorage.getItem('token');
    let user = JSON.parse(localStorage.getItem('user'));
    let orderItems = {};
    let allProducts = []; // Store fetched products for filtering and adding to order

    const productsGrid = document.querySelector('.products-grid');
    const orderSummary = document.getElementById('order-summary');
    const orderNotes = document.getElementById('order-notes');
    const myRoomRadio = document.getElementById('my-room');
    const chooseRoomRadio = document.getElementById('choose-room');
    const roomSelectDropdown = document.getElementById('room-select-dropdown');
    const categoryFilter = document.createElement('select');

    // Setup category filter
    categoryFilter.id = 'category-filter';
    categoryFilter.innerHTML = '<option value="">All Categories</option>';
    productsGrid.before(categoryFilter);

    // Handle Room Selection
    function handleRoomSelection() {
        if (myRoomRadio.checked) {
            roomSelectDropdown.style.display = 'none';
            showNotification('notifications-container', `Switched to ${myRoomRadio.nextElementSibling.textContent}`, 'success', 2000);
        } else if (chooseRoomRadio.checked) {
            roomSelectDropdown.style.display = 'block';
            fetchRooms();
        }
    }

    myRoomRadio.addEventListener('change', handleRoomSelection);
    chooseRoomRadio.addEventListener('change', handleRoomSelection);

    // Fetch Rooms
    async function fetchRooms() {
        disableButtons(true);
        try {
            const response = await fetch('./getRooms.php', { 
                method: 'GET',
                headers: { 'Authorization': token }
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();
            if (data.status === 'success' && Array.isArray(data.data)) {
                roomSelectDropdown.innerHTML = '<option value="">Select a room</option>';
                data.data.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = room.name;
                    roomSelectDropdown.appendChild(option);
                });
            } else {
                showNotification('notifications-container', 'Failed to fetch rooms.', 'danger');
            }
        } catch (error) {
            console.error('Error fetching rooms:', error);
            showNotification('notifications-container', 'Error fetching rooms.', 'danger');
        } finally {
            disableButtons(false);
        }
    }

    roomSelectDropdown.addEventListener('change', (e) => {
        const selectedRoomName = e.target.selectedOptions[0].text;
        if (e.target.value) {
            chooseRoomRadio.nextElementSibling.textContent = `Choose Another Room (${selectedRoomName})`;
            showNotification('notifications-container', `Room changed to ${selectedRoomName}`, 'success', 2000);
        }
    });

    // Fetch Categories
    async function fetchCategories() {
        try {
            const response = await fetch('./getCategories.php', {
                method: 'GET',
                headers: { 'Authorization': token }
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();
            if (data.status === 'success' && Array.isArray(data.all_categories_data)) {
                data.all_categories_data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    categoryFilter.appendChild(option);
                });
            } else {
                showNotification('notifications-container', 'Failed to fetch categories.', 'danger');
            }
        } catch (error) {
            console.error('Error fetching categories:', error);
            showNotification('notifications-container', 'Error fetching categories.', 'danger');
        }
    }

    // Fetch Products
    async function fetchProducts() {
        disableButtons(true);
        try {
            const response = await fetch('./getProducts.php', {
                method: 'GET',
                headers: { 'Authorization': token }
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();
            if (data.status === 'success' && Array.isArray(data.all_products_data)) {
                allProducts = data.all_products_data; // Store all products
                renderProducts(allProducts); // Render initially with all products
            } else {
                showNotification('notifications-container', data.message || 'Failed to fetch products.', 'danger');
            }
        } catch (error) {
            console.error('Error fetching products:', error);
            showNotification('notifications-container', 'Error fetching products.', 'danger');
        } finally {
            disableButtons(false);
        }
    }

    function renderProducts(products) {
        productsGrid.innerHTML = '';
        products.forEach(product => {
            const card = document.createElement('div');
            card.className = 'product-card';
            const imageSrc = product.product_img ? product.product_img : './uploads/default_product.jpg';
            card.innerHTML = `
                <img src="${imageSrc}" alt="${product.name}">
                <h3>${product.name}</h3>
                <p class="price">${parseFloat(product.price).toFixed(2)} L.E</p>
                <button class="btn-add" onclick="addToOrder(${product.id})"><i class="fas fa-plus"></i></button>
            `;
            productsGrid.appendChild(card);
        });
    }

    // Category Filter Event
    categoryFilter.addEventListener('change', (e) => {
        const categoryId = e.target.value;
        const filteredProducts = categoryId 
            ? allProducts.filter(product => product.category_id === parseInt(categoryId))
            : allProducts;
        renderProducts(filteredProducts);
    });

    // Add to Order
    window.addToOrder = function(productId) {
        disableButtons(true);
        try {
            const product = allProducts.find(p => p.id === productId);
            if (product) {
                orderItems[product.id] = orderItems[product.id] || { ...product, quantity: 0 };
                orderItems[product.id].quantity += 1;
                updateOrderSummary();
                showNotification('notifications-container', `${product.name} added to your order!`, 'success', 2000);
            }
        } catch (error) {
            console.error('Error adding to order:', error);
            showNotification('notifications-container', 'Error adding item.', 'danger');
        } finally {
            disableButtons(false);
        }
    };

    // Update Order Summary
    function updateOrderSummary() {
        if (Object.keys(orderItems).length === 0) {
            orderSummary.innerHTML = '<p class="empty">No items in your order yet.</p>';
            return;
        }
        orderSummary.innerHTML = '';
        let total = 0;
        for (const id in orderItems) {
            const item = orderItems[id];
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            const itemDiv = document.createElement('div');
            itemDiv.className = 'order-item';
            itemDiv.innerHTML = `
                <span class="item-details">${item.name} - ${parseFloat(item.price).toFixed(2)} L.E × ${item.quantity} = ${itemTotal.toFixed(2)} L.E</span>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="decreaseQuantity(${item.id})"><i class="fas fa-minus"></i></button>
                    <span>${item.quantity}</span>
                    <button class="quantity-btn" onclick="increaseQuantity(${item.id})"><i class="fas fa-plus"></i></button>
                    <button class="remove-btn" onclick="removeItem(${item.id})"><i class="fas fa-times"></i></button>
                </div>
            `;
            orderSummary.appendChild(itemDiv);
        }
        const totalDiv = document.createElement('div');
        totalDiv.className = 'order-total';
        totalDiv.textContent = `Total: ${total.toFixed(2)} L.E`;
        orderSummary.appendChild(totalDiv);
    }

    window.increaseQuantity = function(productId) {
        if (orderItems[productId]) {
            orderItems[productId].quantity += 1;
            updateOrderSummary();
            showNotification('notifications-container', 'Quantity increased!', 'success', 2000);
        }
    };

    window.decreaseQuantity = function(productId) {
        if (orderItems[productId]) {
            orderItems[productId].quantity -= 1;
            if (orderItems[productId].quantity <= 0) delete orderItems[productId];
            updateOrderSummary();
            showNotification('notifications-container', 'Quantity decreased!', 'success', 2000);
        }
    };

    window.removeItem = function(productId) {
        if (orderItems[productId]) {
            delete orderItems[productId];
            updateOrderSummary();
            showNotification('notifications-container', 'Item removed from order.', 'success', 2000);
        }
    };

    // Place Order (Placeholder)
    window.placeOrder = async function() {
        disableButtons(true);
        try {
            if (Object.keys(orderItems).length === 0) {
                showNotification('notifications-container', 'No items in your order.', 'danger');
                return;
            }
            let roomId = myRoomRadio.checked ? user.room_id || '' : roomSelectDropdown.value;
            if (!roomId) {
                showNotification('notifications-container', 'Please select a room.', 'danger');
                return;
            }
            const notes = orderNotes.value;
            const orderData = { items: orderItems, room_id: roomId, notes, user_id: user.id };
            const response = await new Promise(resolve => setTimeout(() => resolve({
                status: 'success',
                message: 'Order placed successfully!'
            }), 1000));
            if (response.status === 'success') {
                showNotification('notifications-container', response.message, 'success', 2000);
                orderItems = {};
                orderNotes.value = '';
                updateOrderSummary();
            } else {
                showNotification('notifications-container', 'Failed to place order.', 'danger');
            }
        } catch (error) {
            console.error('Error placing order:', error);
            showNotification('notifications-container', 'Error placing order.', 'danger');
        } finally {
            disableButtons(false);
        }
    };

    // Navbar Interactions
    setupNavbarInteractions(['make-new-order-btn'], { 'make-new-order-btn': 'Make New Order feature already on this page!' });
    document.getElementById('change-profile-btn').addEventListener('click', (e) => {
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
    fetchCategories();
    fetchProducts();
});