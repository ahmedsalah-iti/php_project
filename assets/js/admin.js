// assets/js/admin.js
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    let user = JSON.parse(localStorage.getItem('user'));

    if (!token || !user || user.role !== 'admin') {
        window.location.href = './index.php?action=login';
    }

    redirectIfNotLoggedIn();

    const addMemberBtn = document.getElementById('addMemberBtn');
    const addMemberForm = document.getElementById('addMemberForm');
    const membersList = document.getElementById('membersList');
    const registerForm = document.getElementById('registerForm');
    const membersTable = document.getElementById('membersTable');
    const backToUsersBtn = document.getElementById('backToUsersBtn');

    let roomsData = [];

    // Fetch Rooms
    async function fetchRooms() {
        disableButtons(true);
        try {
            const response = await fetch('./api/get_rooms', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await response.json();
            if (data.status === 'success') {
                roomsData = data.data || [];
                populateRoomDropdown();
            } else {
                showNotification('notifications-container', data.message || 'Failed to fetch rooms', 'danger');
            }
        } catch (error) {
            console.error('Fetch Rooms Error:', error);
            showNotification('notifications-container', 'Failed to fetch rooms', 'danger');
        } finally {
            disableButtons(false);
        }
    }

    function populateRoomDropdown() {
        const roomSelect = registerForm.querySelector('select[name="room_id"]');
        roomSelect.innerHTML = '<option value="">Select Room</option>';
        roomsData.forEach(room => {
            const option = document.createElement('option');
            option.value = room.id;
            option.textContent = room.name;
            roomSelect.appendChild(option);
        });
    }

    // Fetch Members
    async function fetchMembers() {
        disableButtons(true);
        try {
            const response = await fetch('./api/admin/get_members', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await response.json();
            if (data.status === 'success') {
                membersList.innerHTML = (data.all_members_data || []).map(member => `
                    <tr data-member-id="${member.id}">
                        <td>
                            <img src="${member.profile_img || './uploads/empty.jpg'}" alt="Profile" class="profile-img-upload" data-member-id="${member.id}">
                            <input type="file" class="hidden-input" data-member-id="${member.id}" accept="image/*" style="display: none;">
                        </td>
                        <td><input type="text" value="${member.first_name || ''}" data-field="first_name" data-member-id="${member.id}"></td>
                        <td><input type="text" value="${member.last_name || ''}" data-field="last_name" data-member-id="${member.id}"></td>
                        <td><input type="text" value="${member.phone || ''}" data-field="phone" data-member-id="${member.id}"></td>
                        <td><input type="email" value="${member.email || ''}" data-field="email" data-member-id="${member.id}"></td>
                        <td>
                            <select data-field="role" data-member-id="${member.id}">
                                <option value="admin" ${member.role === 'admin' ? 'selected' : ''}>Admin</option>
                                <option value="customer" ${member.role === 'customer' ? 'selected' : ''}>Customer</option>
                            </select>
                        </td>
                        <td>
                            <select data-field="room_id" data-member-id="${member.id}">
                                ${roomsData.map(room => `<option value="${room.id}" ${room.id === member.room_id ? 'selected' : ''}>${room.name}</option>`).join('')}
                            </select>
                        </td>
                        <td class="action-buttons">
                            <button class="btn-action btn-update" onclick="updateMember(${member.id})">Update</button>
                            <button class="btn-action btn-delete" onclick="deleteMember(${member.id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
                addImageUploadListeners();
            } else {
                if (data.message === "invalid access token / unauthorized") logout();
                showNotification('notifications-container', data.message || 'Failed to fetch members', 'danger');
            }
        } catch (error) {
            console.error('Fetch Members Error:', error);
            showNotification('notifications-container', 'Failed to fetch members', 'danger');
        } finally {
            disableButtons(false);
        }
    }

    // Image Upload Listeners
    function addImageUploadListeners() {
        membersList.addEventListener('click', (e) => {
            if (e.target.classList.contains('profile-img-upload')) {
                const memberId = e.target.dataset.memberId;
                const fileInput = document.querySelector(`input.hidden-input[data-member-id="${memberId}"]`);
                if (fileInput) fileInput.click();
            }
        });

        document.querySelectorAll('.hidden-input').forEach(input => {
            input.addEventListener('change', async (e) => {
                disableButtons(true);
                const memberId = e.target.dataset.memberId;
                if (e.target.files.length > 0) {
                    try {
                        const uploadResult = await uploadProfileImage(memberId, e.target.files[0]);
                        if (uploadResult.status === 'success') {
                            showNotification('notifications-container', uploadResult.message, 'success');
                            const imgElement = document.querySelector(`.profile-img-upload[data-member-id="${memberId}"]`);
                            if (imgElement) imgElement.src = uploadResult.data.profile_img + '?t=' + new Date().getTime();
                        } else {
                            showNotification('notifications-container', uploadResult.message, 'danger');
                        }
                    } catch (error) {
                        console.error('Image Upload Error:', error);
                        showNotification('notifications-container', 'Image upload failed', 'danger');
                    } finally {
                        disableButtons(false);
                    }
                }
            });
        });
    }

    async function uploadProfileImage(userId, file) {
        const formData = new FormData();
        formData.append('profile_img', file);
        formData.append('id', userId);

        const response = await fetch('./api/admin/change_img', {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}` },
            body: formData,
        });
        return await response.json();
    }

    // Update Member
    window.updateMember = async function(memberId) {
        disableButtons(true);
        const memberRow = document.querySelector(`tr[data-member-id="${memberId}"]`);
        if (!memberRow) {
            showNotification('notifications-container', 'Member not found', 'danger');
            disableButtons(false);
            return;
        }

        const updatedData = {
            id: memberId,
            first_name: memberRow.querySelector('input[data-field="first_name"]').value,
            last_name: memberRow.querySelector('input[data-field="last_name"]').value,
            phone: memberRow.querySelector('input[data-field="phone"]').value,
            email: memberRow.querySelector('input[data-field="email"]').value,
            role: memberRow.querySelector('select[data-field="role"]').value,
            room_id: memberRow.querySelector('select[data-field="room_id"]').value,
        };

        try {
            const response = await fetch('./api/admin/update_member', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify(updatedData),
            });
            const result = await response.json();
            if (result.status === 'success') {
                showNotification('notifications-container', 'Member updated successfully', 'success');
            } else {
                if (result.message === "invalid access token / unauthorized") logout();
                showNotification('notifications-container', result.message, 'danger');
            }
        } catch (error) {
            console.error('Update Member Error:', error);
            showNotification('notifications-container', 'Error updating member', 'danger');
        } finally {
            disableButtons(false);
        }
    };

    // Delete Member
    window.deleteMember = async function(memberId) {
        disableButtons(true);
        if (!confirm('Are you sure you want to delete this member?')) {
            disableButtons(false);
            return;
        }

        try {
            const response = await fetch('./api/admin/delete_member', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({ id: memberId }),
            });
            const result = await response.json();
            if (result.status === 'success') {
                showNotification('notifications-container', 'Member deleted successfully', 'success');
                fetchMembers();
            } else {
                if (result.message === "invalid access token / unauthorized") logout();
                showNotification('notifications-container', result.message, 'danger');
            }
        } catch (error) {
            console.error('Delete Member Error:', error);
            showNotification('notifications-container', 'Error deleting member', 'danger');
        } finally {
            disableButtons(false);
        }
    };

    // Event Listeners
    addMemberBtn.addEventListener('click', () => {
        disableButtons(true);
        addMemberForm.style.display = 'block';
        membersTable.style.display = 'none';
        disableButtons(false);
    });

    backToUsersBtn.addEventListener('click', () => {
        disableButtons(true);
        addMemberForm.style.display = 'none';
        membersTable.style.display = 'block';
        disableButtons(false);
    });

    registerForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        disableButtons(true);
        const formData = new FormData(registerForm);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('./api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify(data),
            });
            const result = await response.json();
            if (result.status === 'success') {
                showNotification('notifications-container', result.message, 'success');
                addMemberForm.style.display = 'none';
                membersTable.style.display = 'block';
                fetchMembers();
                registerForm.reset();
            } else {
                if (result.message === "invalid access token / unauthorized") logout();
                showNotification('notifications-container', result.message, 'danger');
            }
        } catch (error) {
            console.error('Register Member Error:', error);
            showNotification('notifications-container', 'Error registering member', 'danger');
        } finally {
            disableButtons(false);
        }
    });

    // Navbar Interactions
    setupNavbarInteractions();
    document.getElementById('change-profile-btn').addEventListener('click', (e) => {
        e.preventDefault();
        disableButtons(true);
        document.getElementById('profile-upload').click();
    });

    document.getElementById('profile-upload').addEventListener('change', async () => {
        const file = document.getElementById('profile-upload').files[0];
        if (file) {
            try {
                const formData = new FormData();
                formData.append('profile_img', file);
                const response = await fetch('./api/upload', {
                    method: 'POST',
                    headers: { 'AUTHORIZATION': token },
                    body: formData
                });
                const data = await response.json();
                if (data.status === 'success') {
                    showNotification('notifications-container', data.message, 'success');
                    const newSrc = data.url || URL.createObjectURL(file);
                    document.getElementById('profile-img-nav').src = newSrc;
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
            }
        }
    });

    // Initial Load
    checkAuth(token, (newUser) => {
        user = newUser;
        localStorage.setItem('user', JSON.stringify(user));
        updateUserUI(user);
        fetchRooms().then(fetchMembers);
    }, logout);
    setInterval(() => checkAuth(token, (newUser) => {
        user = newUser;
        localStorage.setItem('user', JSON.stringify(user));
        updateUserUI(user);
    }, logout), 60000);
});