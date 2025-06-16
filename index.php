<?php
ini_set('session.cookie_lifetime', 86400);
session_start();

if (!empty($_SESSION['logged_in'])) {
    // Prodloužení platnosti session cookie o 1 den při každém načtení stránky
    setcookie(session_name(), session_id(), time() + 86400, "/");
}

$isLoggedIn = !empty($_SESSION['logged_in']);

$db = new SQLite3('db/bookmarks.sqlite');

$countResult = $db->querySingle("SELECT COUNT(*) FROM bookmarks");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Bookmark Manager - Moje záložky</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <h1>Bookmark Manager - Moje záložky (Celkem <?= $countResult ?>) </h1>


    <input type="text" id="search" placeholder="Hledat záložky..." autocomplete="off" style="width: 100%; padding: 8px; margin-bottom: 20px;">
    
    <p id="bookmark-count" style="margin-top: -10px; color: #777;"></p>


    <?php if ($isLoggedIn): ?>
        <form id="add-form">
            <input type="text" id="add_bookmark" name="title" placeholder="Název" required>
            <input type="url" id="add_bookmark" name="url" placeholder="URL" autocomplete="off" required>
            <button type="submit">➕ Přidat</button>

        </form>
    <?php endif; ?>

    <div id="categories"></div>



<p style="text-align: center; margin-top: 20px;">
    <button id="load-more" style="
        padding: 10px 20px;
        border-radius: 8px;
        background-color: #007bff;
        color: white;
        border: none;
        font-size: 16px;
        cursor: pointer;
    ">
        ⬇ Načíst další
    </button>
</p>


    <hr>

    <?php if ($isLoggedIn): ?>
        <p>
            <a href="export.php" target="_blank">📥 Exportovat záložky do CSV</a><br>
            <a href="import.php" target="_blank">📤 Importovat záložky z CSV</a><br>
            <a href="logout.php">🚪 Odhlásit se</a>
        </p>



<?php else: ?>
    <div id="login-container">
        <form id="login-form">
            <input type="text" name="username" placeholder="Uživatelské jméno" required><br><br>
            <input type="password" name="password" placeholder="Heslo" required><br><br>
            <button type="submit">🔐 Přihlásit se</button>
        </form>
        <p id="login-error" style="color:red;"></p>
    </div>
<?php endif; ?>





    <script>
        const isLoggedIn = <?= json_encode($isLoggedIn) ?>;
    </script>
    <script src="js/app.js"></script>
    
    
    
    
    
    
    
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('login-form');
    const loginError = document.getElementById('login-error');

    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            loginError.textContent = '';

            const formData = new FormData(loginForm);

            const response = await fetch('login.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // reloadne stránku, aby se ukázaly záložky
                location.reload();
            } else {
                loginError.textContent = result.error || 'Chyba při přihlášení.';
            }
        });
    }
});
</script>
    
    
    
<script>
// Po načtení stránky nastaví kurzor do vyhledávacího pole - START
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.focus();
    }
});
// Po načtení stránky nastaví kurzor do vyhledávacího pole - END

</script>    
    
    
    
</body>
</html>
