<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($data['title']); ?></title>
    <link rel="stylesheet" href="/style/style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
            <li><a href="/users">Users</a></li>
        </ul>
    </nav>

    <?php
    // Determine which view to load based on the route
    $view = basename($_SERVER['REQUEST_URI'], '.php');
    $view = $view === '' ? 'home' : $view;
    $viewFile = __DIR__ . '/' . $view . '.php';

    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        echo '<p>Error: View not found</p>';
    }
    ?>
</body>
</html>