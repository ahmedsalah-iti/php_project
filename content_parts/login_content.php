<!-- content_parts/login_content.php -->
<div class="login-container">
    <h2>Login</h2>
    <form id="loginForm">
        <div class="form-row">
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" class="form-control" id="email" placeholder="Enter email" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" id="password" placeholder="Enter password" required>
            </div>
        </div>

        <button type="submit" class="btn-login">Login</button>
        <div class="register-link">
            Don’t have an account? <a href="./index.php?action=register">Register</a>
        </div>
    </form>
</div>