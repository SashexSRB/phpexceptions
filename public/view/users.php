<h1>PayBro Payment System</h1>

<?php if ($data['error']): ?>
    <p class="error"><?php echo htmlspecialchars($data['error']); ?></p>
<?php endif; ?>
<?php if ($data['message']): ?>
    <p class="success"><?php echo htmlspecialchars($data['message']); ?></p>
<?php endif; ?>

<h2>Add New User</h2>
<form method="POST" action="/users">
    <label>Username: <input type="text" name="username" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <input type="hidden" name="money" value="0"><br>
    <button type="submit">Add User</button>
</form>

<h2>Users List</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Money Amount</th>
    </tr>
    <?php foreach ($data['users'] as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user->getId()); ?></td>
            <td><?php echo htmlspecialchars($user->getUsername()); ?></td>
            <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
            <td><?php echo htmlspecialchars($user->getMoney()); ?></td>
        </tr>
    <?php endforeach; ?>
</table>