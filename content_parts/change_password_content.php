<!-- content_parts/change_password_content.php -->
<div class="change-password-container">
    <h2>Change Password</h2>
    <form id="changePasswordForm">
        <div class="form-group">
            <i class="fas fa-lock"></i>
            <input type="password" class="form-control" id="current_pass" placeholder="Enter current password" required>
        </div>

        <div class="form-group">
            <i class="fas fa-lock"></i>
            <input type="password" class="form-control" id="new_pass" placeholder="Enter new password" required>
        </div>

        <div class="form-group">
            <i class="fas fa-lock"></i>
            <input type="password" class="form-control" id="confirm_new_pass" placeholder="Confirm new password" required>
        </div>
        <div class="button-container">
            <button type="submit" class="btn-change-password">Change Password</button>
            <button type="button" class="btn-cancel" onclick="window.location.href='./index.php?action=dashboard'">Cancel</button>
        </div>
    </form>
</div>