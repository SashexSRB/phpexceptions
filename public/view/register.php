<h1>Register</h1>
<p><?php echo htmlspecialchars($data['content']); ?></p>

<?php if ($data['error']): ?>
    <p class="error"><?php echo htmlspecialchars($data['error']); ?></p>
<?php endif; ?>
<?php if ($data['message']): ?>
    <p class="success"><?php echo htmlspecialchars($data['message']); ?></p>
<?php endif; ?>

<h2>Add New User</h2>
<form method="POST" action="/register">
    <label>Username: <input type="text" name="username" required></label>
    <label>Email: <input type="email" name="email" required></label>
    <label>Password: <input type="password" name="password" required></label>
    <input type="hidden" name="money" value="0"><br>
    <button type="submit">Add User</button>
</form>