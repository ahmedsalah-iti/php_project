<!-- content_parts/products_orders_content.php -->
<div class="products-container">
    <h1><i class="fas fa-coffee"></i> Products</h1>
    <div class="products-grid" id="products-grid">
        <div class="product-card">
            <img src="https://picsum.photos/150/150?random=9" alt="Milkshake">
            <h3>Milkshake</h3>
            <p class="price">18.00 L.E</p>
            <button class="btn-add" onclick="addToOrder(9)"><i class="fas fa-plus"></i></button>
        </div>
    </div>
</div>

<div class="orders-section">
    <h2><i class="fas fa-shopping-cart"></i> Your Order</h2>
    <div id="order-summary">
        <p class="empty">No items in your order yet.</p>
    </div>
    <div class="order-form">
        <div class="form-group room-select">
            <div class="room-radio-group">
                <div class="room-radio">
                    <input type="radio" id="my-room" name="room-option" value="my-room" checked>
                    <label for="my-room" id="my-room-label">My Room</label>
                </div>
                <div class="room-radio">
                    <input type="radio" id="choose-room" name="room-option" value="choose-room">
                    <label for="choose-room">Choose Another Room</label>
                </div>
            </div>
            <select id="room-select-dropdown" required>
                <option value="">Select a room</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="order-notes" placeholder="Add any special notes (optional)">
        </div>
        <button class="btn-place-order" onclick="placeOrder()">Place Order</button>
    </div>
</div>