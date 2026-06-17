<?php
/* ===== SEZIONE 1: Inclusione File e Protezione Accesso ===== */
// delete_account.php - Script per l'auto-cancellazione dell'account utente
require_once 'includes/connection.php';
session_start();

// PROTEZIONE ASSOLUTA: Verifica che l'utente sia loggato
// Se non è loggato, non può cancellare nulla
// Viene reindirizzato alla pagina di login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Recupera l'ID dell'utente corrente dalla sessione
$user_id = $_SESSION['user_id'];

/* ===== SEZIONE 2: Cancellazione dal Database con CASCADE ===== */
// Grazie al vincolo "ON DELETE CASCADE" definito nel database,
// questa singola riga di DELETE elimina automaticamente:
// - L'utente dalla tabella 'utenti'
// - I suoi punteggi dalla tabella 'punteggi' (child table)
// - (In futuro) Badge, medaglie, commenti e qualsiasi dato correlato
try {
    // Query DELETE con prepared statement (protezione SQL Injection)
    $sql = "DELETE FROM utenti WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    // Esegui con il parametro user_id
    $stmt->execute([$user_id]);

    /* ===== SEZIONE 3: Pulizia della Sessione (Logout Forzato) ===== */
    // Una volta cancellato dal database, l'utente deve essere immediatamente
    // scollegato dalla sessione (logout forzato per sicurezza)
    
    // Pulisce l'array della sessione
    $_SESSION = [];
    
    // Verifica se il PHP usa i cookie di sessione (configurazione standard)
    if (ini_get("session.use_cookies")) {
        // Recupera i parametri del cookie di sessione corrente
        $params = session_get_cookie_params();
        // Cancella il cookie impostando data di scadenza nel passato
        // Questo forza il browser a eliminare il cookie
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    // Distrugge la sessione server-side completamente
    session_destroy();

    /* ===== SEZIONE 4: Reindirizzamento a Pagina di Conferma ===== */
    // L'utente viene mandato alla pagina di registrazione con parametro status
    // La pagina register.php può mostrare un messaggio di conferma cancellazione
    header("Location: register.php?status=deleted");
    exit;

} catch (PDOException $e) {
    // GESTIONE ERRORE: Se il database genera un'eccezione
    // Stampa il messaggio di errore per debug (in produzione usare logging)
    die("Errore durante la cancellazione: " . $e->getMessage());
}
?>