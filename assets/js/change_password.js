// change_password.js
document.addEventListener('DOMContentLoaded', () => {
    redirectIfNotLoggedIn();
    const token = localStorage.getItem('token');
    let user = JSON.parse(localStorage.getItem('user'));

    const changePasswordButton = document.querySelector('#changePasswordForm .btn-change-password');

    // Handle Form Submission
    document.getElementById('changePasswordForm').addEventListener('submit', async (event) => {
        event.preventDefault();

        const currentPassword = document.getElementById('current_pass').value;
        const newPassword = document.getElementById('new_pass').value;
        const confirmNewPassword = document.getElementById('confirm_new_pass').value;

        if (newPassword !== confirmNewPassword) {
            showNotification('notifications-container', 'New passwords do not match.', 'danger');
            return;
        }

        disableButtons(true);
        changePasswordButton.textContent = 'Changing...';

        const data = { current_pass: currentPassword, new_pass: newPassword };

        try {
            const response = await fetch('./api/change_pass', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(data)
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const result = await response.json();
            if (result.status === 'success') {
                showNotification('notifications-container', result.message, 'success');
                setTimeout(() => window.location.href = './index.php?action=dashboard', 2000);
            } else {
                showNotification('notifications-container', result.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('notifications-container', `An error occurred: ${error.message}`, 'danger');
        } finally {
            disableButtons(false);
            changePasswordButton.textContent = 'Change Password';
        }
    });

    // Navbar Interactions
    setupNavbarInteractions();
    document.getElementById('change-profile-btn').addEventListener('click', (e) => {
        e.preventDefault();
        showNotification('notifications-container', 'Profile picture change not available here.', 'info');
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
});