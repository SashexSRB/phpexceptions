<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($data['title']); ?></title>
    <link rel="stylesheet" href="/style/style.css">
    <link rel="icon" type="image/png" href="/style/icon.png">
</head>
<body>
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