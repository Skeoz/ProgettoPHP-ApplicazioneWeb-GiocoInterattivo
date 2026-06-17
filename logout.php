<?php
// File di logout: gestisce il termine della sessione dell'utente

// Avvio della sessione corrente
session_start();

// Svuotamento di tutte le variabili di sessione
$_SESSION = [];

// Cancellazione del cookie di sessione (pulizia profonda della sessione)
// Questo viene fatto solo se il server utilizza i cookie per mantenere le sessioni
if (ini_get("session.use_cookies")) {
    // Recupero i parametri del cookie di sessione
    $params = session_get_cookie_params();
    // Impostazione di un cookie vuoto con data di scadenza nel passato
    // In questo modo il browser eliminerà il cookie
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distruzione completa della sessione
session_destroy();

// Reindirizzamento alla pagina di login
header("Location: login.php");
exit;
?>