<?php
$db = new SQLite3('db/bookmarks.sqlite');

function normalize($string) {
    $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
    return strtolower($string);
}

$query = trim($_GET['q'] ?? '');
$offset = intval($_GET['offset'] ?? 0);
$limit = ($query === '') ? intval($_GET['limit'] ?? 10) : null;

$bookmarks = [];
$results = $db->query("SELECT * FROM bookmarks ORDER BY id DESC");
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $bookmarks[] = $row;
}

if ($query === '') {
    // Bez dotazu: zobraz jen omezen� po�et
    $paged = array_slice($bookmarks, $offset, $limit);
    header('Content-Type: application/json');
    echo json_encode($paged);
    exit;
}

// S dotazem: filtruj fulltextov� v PHP
$words = preg_split('/\s+/', normalize($query));
$filtered = array_filter($bookmarks, function ($item) use ($words) {
    $normalizedTitle = normalize($item['title']);
    foreach ($words as $word) {
        if (strpos($normalizedTitle, $word) === false) {
            return false;
        }
    }
    return true;
});

// Vr�tit v�echny odpov�daj�c� v�sledky
header('Content-Type: application/json');
echo json_encode(array_values($filtered));
