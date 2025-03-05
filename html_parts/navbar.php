<!-- html_parts/navbar.php -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="./index.php?action=dashboard"><span class="logo">Cafeteria</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars" style="color: #6c757d;"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle"></i> Order
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" id="make-new-order-btn" href="./products">Make New Order</a></li>
                        <li><a class="dropdown-item" id="my-orders-btn" href="#">My Orders</a></li>
                        <li><a class="dropdown-item" id="history-btn" href="#">History</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="wallet-transactions-btn" href="#"><i class="fas fa-wallet"></i> Wallet</a>
                </li>
                <?php
                    if (isset($_GET['action']) && $_GET['action'] === 'admin'):
                ?>
                <li class="nav-item" id="admin-nav" style="display: none;">
                    <a class="nav-link" href="./index.php?action=dashboard"><i class="fas fa-tachometer-alt"></i> Customer Page</a>
                </li>
                <?php
                    else:
                ?>
                 <li class="nav-item" id="admin-nav" style="display: none;">
                    <a class="nav-link" href="./index.php?action=admin"><i class="fas fa-tools"></i> Admin Panel</a>
                </li>
                <?php
                    endif;
                ?>
            </ul>
            <div class="profile-section">
                <span class="balance-display"><i class="fas fa-wallet"></i> <span id="user-balance-nav">Loading...</span></span>
                <img id="profile-img-nav" class="profile-img-nav" src="./uploads/empty.jpg" alt="Profile">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle user-name-nav" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="user-name-nav">Loading...</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" id="change-profile-btn" href="#">Change Profile Picture</a></li>
                        <li><a class="dropdown-item" href="./index.php?action=change_password">Change Password</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><a class="dropdown-item" id="logout-btn" href="#">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>