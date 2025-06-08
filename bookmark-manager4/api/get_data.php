<?php
$db = new SQLite3('../db/bookmarks.sqlite');

$data = [];
$res = $db->query("SELECT c.id as category_id, c.name, b.id as bookmark_id, b.title, b.url, b.favicon
                   FROM categories c
                   LEFT JOIN bookmarks b ON c.id = b.category_id
                   ORDER BY c.id, b.id");

while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $data[] = $row;
}

echo json_encode($data);
