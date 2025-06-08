<?php
$db = new SQLite3('db/bookmarks.sqlite');

$categories = $db->query("SELECT * FROM categories");

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
    
    <input type="text" id="search" placeholder="Hledat záložky..." style="width: 100%; padding: 8px; margin-bottom: 20px;">

    
    <form id="add-form">
        <input type="text" name="title" placeholder="Název" required>
        <input type="url" name="url" placeholder="URL" required>
        <select name="category_id">
            <?php while ($row = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Přidat</button>
    </form>

    <div id="categories"></div>

    <script src="js/app.js"></script>
</body>
</html>
