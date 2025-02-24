<!-- content_parts/register_content.php -->
<div class="register-container">
    <h2>Register</h2>
    <form id="registerForm">
        <div class="form-row">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" id="first_name" placeholder="Enter first name" required>
            </div>
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" id="last_name" placeholder="Enter last name" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" class="form-control" id="email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <i class="fas fa-phone"></i>
                <input type="tel" class="form-control" id="phone" placeholder="Enter phone number" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" id="password" placeholder="Enter password" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" id="confirm_password" placeholder="Confirm password" required>
            </div>
        </div>

        <div class="form-group room-select">
            <select class="form-control" id="room" required>
                <option value="">Select a room</option>
            </select>
        </div>
        <br>
        <button type="submit" class="btn-register">Register</button>
        <div class="login-link">
            Already have an account? <a href="./index.php?action=login">Login</a>
        </div>
    </form>
</div>