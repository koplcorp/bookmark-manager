<?php

ini_set('session.cookie_lifetime', 86400);
session_start();

if (!empty($_SESSION['logged_in'])) {
    setcookie(session_name(), session_id(), time() + 86400, "/");
}

if (empty($_SESSION['logged_in'])) {
    die('Přístup zamítnut.');
}

$db = new SQLite3('db/bookmarks.sqlite');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, 'r');
    
    if ($handle !== false) {
        // Přeskoč hlavičku (pokud existuje)
        fgetcsv($handle, 1000, ';');

        $importCount = 0;

        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            if (count($data) >= 2) {
                $title = trim($data[0]);
                $url = trim($data[1]);
                if ($title && $url) {
                    $host = parse_url($url, PHP_URL_HOST);
                    $favicon = "https://www.google.com/s2/favicons?domain={$host}";

                    $stmt = $db->prepare("INSERT INTO bookmarks (title, url, favicon) VALUES (?, ?, ?)");
                    $stmt->bindValue(1, $title, SQLITE3_TEXT);
                    $stmt->bindValue(2, $url, SQLITE3_TEXT);
                    $stmt->bindValue(3, $favicon, SQLITE3_TEXT);
                    $stmt->execute();
                    $importCount++;
                }
            }
        }

        fclose($handle);
        $message = "✅ Naimportováno $importCount záložek.";
    } else {
        $message = "❌ Nepodařilo se načíst soubor.";
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Import záložek (CSV)</title>
    <style>
        body { font-family: sans-serif; margin: 30px; }
        input[type="file"] { margin-bottom: 10px; }
        .msg { margin-top: 20px; font-weight: bold; color: green; }
    </style>
</head>
<body>
    <h1>Import záložek z CSV souboru</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>CSV soubor (oddělovač <code>;</code>, bez hlavičky nebo s hlavičkou):</label><br>
        <input type="file" name="csv_file" accept=".csv" required><br>
        <button type="submit">Importovat</button>
    </form>

    <?php if ($message): ?>
        <p class="msg"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
</body>
</html>
