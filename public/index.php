<?php

require_once("./class/DatabaseConnection.php");
require_once("./class/User.php");

$users = [];
$message = '';
$error = '';

try {
    $db = new DatabaseConnection($host, $user, $pass, $db);
    $dbConnection = $db->getConnection();

    $message = '';
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW);
        $username = trim(strip_tags($username));

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        if ($username && $email) {
            $user = new User($username, $email, $dbConnection);
            $user->saveToDatabase();
            $message = "User created successfully!";
        } else {
            $error = "Invalid input data";
        }
    }

    $users = User::getAllUsers($dbConnection);

} catch (Exception $e) {
    $error = "MySQL Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MySQL User Management</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .error { color: red; }
        .success { color: green; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>MySQL User Management</h1>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($message): ?>
        <p class="success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2>Add New User</h2>
    <form method="POST">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <button type="submit">Add User</button>
    </form>

    <h2>Users List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Attributes</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user->getId()); ?></td>
                <td><?php echo htmlspecialchars($user->getUsername()); ?></td>
                <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                <td>
                    <?php
                    foreach ($user as $key => $value) {
                        echo htmlspecialchars("$key: $value") . "<br>";
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>