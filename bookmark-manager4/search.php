<?php
$db = new SQLite3('db/bookmarks.sqlite');

$query = trim($_GET['q'] ?? '');
if ($query === '') {
    // Pokud nen� dotaz, vyber v�echny z�lo�ky
    $results = $db->query("SELECT * FROM bookmarks");
} else {
    // Rozd�l�me dotaz na slova
    $words = preg_split('/\s+/', $query);

    // Sestav�me SQL podm�nku (title LIKE '%word1%' AND title LIKE '%word2%' ...)
    $whereClauses = [];
    foreach ($words as $word) {
        $word = SQLite3::escapeString($word);
        $whereClauses[] = "title LIKE '%$word%'";
    }
    $where = implode(' AND ', $whereClauses);

    $sql = "SELECT * FROM bookmarks WHERE $where";
    $results = $db->query($sql);
}

$bookmarks = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $bookmarks[] = $row;
}

header('Content-Type: application/json');
echo json_encode($bookmarks);
