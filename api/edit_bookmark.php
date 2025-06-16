<?php
ini_set('session.cookie_lifetime', 86400);
session_start();

if (!empty($_SESSION['logged_in'])) {
    setcookie(session_name(), session_id(), time() + 86400, "/");
}

if (empty($_SESSION['logged_in'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Přístup odepřen']);
    exit;
}

$db = new SQLite3('../db/bookmarks.sqlite');

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id'] ?? 0);
$title = trim($data['title'] ?? '');
$url = trim($data['url'] ?? '');

if ($id && $title && $url) {
    $host = parse_url($url, PHP_URL_HOST);
    $favicon = "https://www.google.com/s2/favicons?domain={$host}";

    $stmt = $db->prepare("UPDATE bookmarks SET title = ?, url = ?, favicon = ? WHERE id = ?");
    $stmt->bindValue(1, $title, SQLITE3_TEXT);
    $stmt->bindValue(2, $url, SQLITE3_TEXT);
    $stmt->bindValue(3, $favicon, SQLITE3_TEXT);
    $stmt->bindValue(4, $id, SQLITE3_INTEGER);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Neplatná data']);
}
