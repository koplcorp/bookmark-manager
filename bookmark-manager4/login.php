<?php
session_start();

define('USERNAME', 'admin'); // Změň podle potřeby
// Zahashuj si heslo jednou např. v PHP shellu:
// echo password_hash('tvojeheslo', PASSWORD_DEFAULT);
define('PASSWORD_HASH', '$2y$10$QrPSSA6EmqsAxUD2SMeHHu49UjmSg8molH3HltyHGGGajAdgYjy36'); // nahraď svým hashem zde je generator https://onlinephp.io/password-hash

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === USERNAME && password_verify($password, PASSWORD_HASH)) {
        $_SESSION['logged_in'] = true;
            header('Location: index.php'); // Zpět na hlavní stránku aplikace
        exit;
    } else {
        $error = 'Neplatné uživatelské jméno nebo heslo.';
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <title>Přihlášení</title>
</head>
<body>
    <h1>Přihlášení</h1>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Uživatelské jméno" required><br><br>
        <input type="password" name="password" placeholder="Heslo" required><br><br>
        <button type="submit">Přihlásit se</button>
    </form>
</body>
</html>
