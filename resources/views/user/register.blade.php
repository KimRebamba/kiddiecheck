<div>
    <form action="/register" method="POST">
    @csrf <div>
        <label>Name:</label>
        <input type="text" name="name" required>
    </div>

    <div>
        <label>Email:</label>
        <input type="email" name="email" required>
    </div>

    <div>
        <label>Password:</label>
        <input type="password" name="password" required>
    </div>

    <div>
        <label>Role:</label>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="teacher">Teacher</option>
        </select>
    </div>

    <div>
        <label>Home Address:</label>
        <input type="text" name="home_address">
    </div>

    <div>
        <label>Status:</label>
        <select name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <div>
        <label>Profile Path (Image URL/Path):</label>
        <input type="text" name="profile_path">
    </div>

    <button type="submit">Create User</button>
</form>
</div>
