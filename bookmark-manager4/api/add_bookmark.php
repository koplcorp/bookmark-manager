<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'Přístup odepřen']);
    exit;
}

$db = new SQLite3('../db/bookmarks.sqlite');

$title = $_POST['title'];
$url = $_POST['url'];

// Automatické získání favicon
$host = parse_url($url, PHP_URL_HOST);
$favicon = "https://www.google.com/s2/favicons?domain={$host}";

$stmt = $db->prepare("INSERT INTO bookmarks (title, url, favicon) VALUES (?, ?, ?)");
$stmt->bindValue(1, $title, SQLITE3_TEXT);
$stmt->bindValue(2, $url, SQLITE3_TEXT);
$stmt->bindValue(3, $favicon, SQLITE3_TEXT);

$stmt->execute();

echo json_encode(["success" => true]);
