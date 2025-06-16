<?php
session_start();
$isLoggedIn = !empty($_SESSION['logged_in']);

$db = new SQLite3('db/bookmarks.sqlite');
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Bookmark Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Moje záložky</h1>

    <input type="text" id="search" placeholder="Hledat záložky..." autocomplete="off" style="width: 100%; padding: 8px; margin-bottom: 20px;">

    <?php if ($isLoggedIn): ?>
        <form id="add-form">
            <input type="text" name="title" placeholder="Název" required>
            <input type="url" name="url" placeholder="URL" required>
            <button type="submit">Přidat</button>
        </form>

        <p><a href="logout.php">Odhlásit se</a></p>
    <?php else: ?>
        <p><a href="login.php">Přihlásit se pro přidání záložek</a></p>
    <?php endif; ?>

    <div id="categories"></div>

    <script src="js/app.js"></script>
</body>
</html>
