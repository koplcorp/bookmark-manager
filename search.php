<?php
$db = new SQLite3('db/bookmarks.sqlite');

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $words = preg_split('/\s+/', $search);
    $fts_query = implode(' AND ', array_map(function($w) use ($db) {
        return '"' . $db->escapeString($w) . '"';
    }, $words));

    $stmt = $db->prepare("
        SELECT b.* FROM bookmarks_fts f
        JOIN bookmarks b ON b.id = f.rowid
        WHERE bookmarks_fts MATCH :query
        ORDER BY b.id DESC
    ");
    $stmt->bindValue(':query', $fts_query);
} else {
    $stmt = $db->prepare("SELECT * FROM bookmarks ORDER BY id DESC");
}

$results = $stmt->execute();

header('Content-Type: application/json');
$out = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $out[] = $row;
}

echo json_encode($out);

// ukonèení PHP tady není potøeba, protože je to celý soubor jen PHP
