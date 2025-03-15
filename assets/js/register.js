// register.js
document.addEventListener('DOMContentLoaded', () => {
    redirectIfLoggedIn();

    const registerButton = document.querySelector('#registerForm .btn-register');
    const roomSelect = document.getElementById('room');

    // Fetch Rooms
    async function fetchRooms() {
        disableButtons(true);
        try {
            const response = await fetch('./api/get_rooms', { method: 'GET' });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();
            if (data.status === 'success' && Array.isArray(data.data)) {
                roomSelect.innerHTML = '<option value="">Select a room</option>';
                data.data.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = room.name;
                    roomSelect.appendChild(option);
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

    // Handle Form Submission
    document.getElementById('registerForm').addEventListener('submit', async (event) => {
        event.preventDefault();

        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const phone = document.getElementById('phone').value;
        const roomId = roomSelect.value;

        if (password !== confirmPassword) {
            showNotification('notifications-container', 'Passwords do not match.', 'danger');
            return;
        }

        disableButtons(true);
        registerButton.textContent = 'Registering...';

        const data = { first_name: firstName, last_name: lastName, email, password, phone, room_id: roomId };

        try {
            const response = await fetch('./api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const result = await response.json();
            if (result.status === 'success') {
                showNotification('notifications-container', result.message, 'success');
                if (result.data && result.data.token) {
                    localStorage.setItem('token', result.data.token);
                    localStorage.setItem('user', JSON.stringify(result.data));
                }
                setTimeout(() => window.location.href = './index.php?action=login', 2000);
            } else {
                showNotification('notifications-container', result.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('notifications-container', `An error occurred: ${error.message}`, 'danger');
        } finally {
            disableButtons(false);
            registerButton.textContent = 'Register';
        }
    });

    fetchRooms();
});