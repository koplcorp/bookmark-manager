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

// ✅ Získej JSON data
$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id'] ?? 0);

if ($id > 0) {
    $stmt = $db->prepare('DELETE FROM bookmarks WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Neplatné ID']);
}
