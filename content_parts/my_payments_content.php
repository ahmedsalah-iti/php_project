<main>
    <div class="orders-container">
        <h1>My Payments</h1>
        <div class="filter-container">
            <select id="status-filter" class="form-select">
                <option value="all">All</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="failed">Failed</option>
            </select>
        </div>
        <div class="orders-grid"></div>
    </div>
    <div id="payment-details-container" style="display: none;"></div>
    <div id="notifications-container" class="notifications-container"></div>
</main>