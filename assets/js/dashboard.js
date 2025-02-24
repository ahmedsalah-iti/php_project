// dashboard.js
document.addEventListener('DOMContentLoaded', () => {
    redirectIfNotLoggedIn();
    const token = localStorage.getItem('token');
    let user = JSON.parse(localStorage.getItem('user'));

    const profileImgContainer = document.getElementById('profile-img-container');
    const profileImg = document.getElementById('profile-img');
    const profileUpload = document.getElementById('profile-upload');

    // Profile Picture Upload
    profileImgContainer.addEventListener('click', () => {
        disableButtons(true);
        profileUpload.click();
    });

    document.getElementById('change-profile-btn').addEventListener('click', (e) => {
        e.preventDefault();
        disableButtons(true);
        profileUpload.click();
    });

    profileUpload.addEventListener('change', async () => {
        const file = profileUpload.files[0];
        if (file) {
            try {
                const formData = new FormData();
                formData.append('profile_img', file);
                const response = await fetch('./upload.php', {
                    method: 'POST',
                    headers: { 'AUTHORIZATION': token },
                    body: formData
                });
                const data = await response.json();
                if (data.status === 'success') {
                    showNotification('notifications-container', data.message, 'success');
                    const newSrc = data.url || URL.createObjectURL(file);
                    profileImg.src = newSrc;
                    document.getElementById('profile-img-nav').src = newSrc;
                    profileImg.classList.add('loaded');
                    if (data.data?.profile_img) {
                        user.profile_img = data.data.profile_img;
                        localStorage.setItem('user', JSON.stringify(user));
                    }
                } else {
                    showNotification('notifications-container', data.message, 'danger');
                }
            } catch (error) {
                console.error('Upload Error:', error);
                showNotification('notifications-container', 'Error uploading profile picture', 'danger');
            } finally {
                disableButtons(false);
                profileUpload.value = '';
            }
        }
    });

    // Navbar Interactions
    setupNavbarInteractions();

    // Initial Load
    checkAuth(token, (newUser) => {
        user = newUser;
        localStorage.setItem('user', JSON.stringify(user));
        updateUserUI(user, { 
            extraFields: { 
                'user-email': user.email, 
                'user-phone': user.phone, 
                'user-room': user.room_name, 
                'user-role': user.role 
            }, 
            profileImgId: 'profile-img' 
        });
    }, logout);
    setInterval(() => checkAuth(token, (newUser) => {
        user = newUser;
        localStorage.setItem('user', JSON.stringify(user));
        updateUserUI(user, { 
            extraFields: { 
                'user-email': user.email, 
                'user-phone': user.phone, 
                'user-room': user.room_name, 
                'user-role': user.role 
            }, 
            profileImgId: 'profile-img' 
        });
    }, logout), 60000);
});