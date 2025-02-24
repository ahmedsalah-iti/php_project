// login.js
document.addEventListener('DOMContentLoaded', () => {
    redirectIfLoggedIn();

    const loginButton = document.querySelector('#loginForm .btn-login');

    document.getElementById('loginForm').addEventListener('submit', async (event) => {
        event.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        disableButtons(true);
        loginButton.textContent = 'Logging in...';

        const data = { email, pass: password, type: 'email' };

        try {
            const response = await fetch('./login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const result = await response.json();
            if (result.status === 'success') {
                showNotification('notifications-container', result.message, 'success');
                localStorage.setItem('token', result.data.token);
                localStorage.setItem('user', JSON.stringify(result.data));
                setTimeout(() => window.location.href = './index.php?action=dashboard', 2000);
            } else {
                showNotification('notifications-container', result.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('notifications-container', `An error occurred: ${error.message}`, 'danger');
        } finally {
            disableButtons(false);
            loginButton.textContent = 'Login';
        }
    });
});