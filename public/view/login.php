<h1><?php echo htmlspecialchars($data['content']); ?></h1>

<?php if ($data['error']): ?>
    <p class="error"><?php echo htmlspecialchars($data['error']); ?></p>
<?php endif; ?>
<?php if ($data['message']): ?>
    <p class="success"><?php echo htmlspecialchars($data['message']); ?></p>
<?php endif; ?>

<h2>Login</h2>
<form method="POST" action="/login">
    <label>Email: <input type="email" name="email" required></label>
    <label>Password: <input type="password" name="password" required></label>
    <button type="submit">Login</button>
    <p>Don't have an account yet? <a href="/register">Register</a></p>
</form>