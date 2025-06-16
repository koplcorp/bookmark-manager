<?php
ini_set('session.cookie_lifetime', 86400); // 1 den
session_set_cookie_params(86400);
session_start();

if (!empty($_SESSION['logged_in'])) {
    // Prodloužení platnosti session cookie o 1 den při každém požadavku
    setcookie(session_name(), session_id(), time() + 86400, "/");
}

define('USERNAME', 'admin'); 
define('PASSWORD_HASH', '$2y$10$QrPSSA6EmqsAxUD2SMeHHu49UjmSg8molH3HltyHGGGajAdgYjy36');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === USERNAME && password_verify($password, PASSWORD_HASH)) {
        $_SESSION['logged_in'] = true;
        // Cookie je nastavena výše, ale můžeš i tady explicitně obnovit platnost
        setcookie(session_name(), session_id(), time() + 86400, "/");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Neplatné jméno nebo heslo.']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Pouze POST je povolen.']);
