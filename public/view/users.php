<h1>List of Registered Users</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Balance</th>
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