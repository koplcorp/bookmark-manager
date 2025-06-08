<?php
// init_db.php
$db = new SQLite3('db/bookmarks.sqlite');

// Tabulka záložek
$db->exec("CREATE TABLE IF NOT EXISTS bookmarks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    url TEXT NOT NULL,
    favicon TEXT,
    category_id INTEGER,
    FOREIGN KEY (category_id) REFERENCES categories(id)
)");

echo "Databáze byla vytvořena.\n";
