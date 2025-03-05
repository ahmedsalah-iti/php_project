<!-- content_parts/admin_content.php -->
<div class="admin-container">
    <h1 class="admin-title"><i class="fas fa-tools"></i> Admin Panel</h1>

    <!-- Add Member Form -->
    <div id="addMemberForm" class="form-container">
        <h2 class="text-xl font-semibold">Add New Member</h2>
        <form id="registerForm" class="row g-3">
            <div class="col-md-6">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Room</label>
                <select name="room_id" class="form-control" required></select>
            </div>
            <div class="col-12 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn-primary"><i class="fas fa-plus mr-2"></i> Register Member</button>
                <button type="button" id="backToUsersBtn" class="btn-primary" style="background: linear-gradient(90deg, #6c757d, #adb5bd);"><i class="fas fa-users mr-2"></i> Users</button>
            </div>
        </form>
    </div>

    <!-- Members Table -->
    <div id="membersTable" class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="text-xl font-semibold">Members List</h2>
            <button id="addMemberBtn" class="btn-primary"><i class="fas fa-plus mr-2"></i> Add Member</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Room</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="membersList"></tbody>
        </table>
    </div>
</div>
<input type="file" id="profile-upload" style="display: none;" accept="image/*">