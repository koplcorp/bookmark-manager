<?php
ini_set('session.cookie_lifetime', 86400);
session_start();

if (!empty($_SESSION['logged_in'])) {
    setcookie(session_name(), session_id(), time() + 86400, "/");
}

if (empty($_SESSION['logged_in'])) {
    http_response_code(403);
    echo 'Přístup odepřen.';
    exit;
}

$db = new SQLite3('db/bookmarks.sqlite');

// Připravíme CSV hlavičky
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="bookmarks.csv"');

$output = fopen('php://output', 'w');

// Hlavička CSV
fputcsv($output, ['Název', 'URL'], ';');

// Data z databáze
$results = $db->query("SELECT title, url FROM bookmarks ORDER BY id");

while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    fputcsv($output, [$row['title'], $row['url']], ';');
}

fclose($output);
exit;
