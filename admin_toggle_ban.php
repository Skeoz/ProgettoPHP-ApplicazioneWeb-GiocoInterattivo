<?php
/* ===== SEZIONE 1: Inclusione File e Protezione Accesso Admin ===== */
// admin_toggle_ban.php - Script per bloccare/sbloccare utenti (inverte il campo bannato)
require_once 'includes/connection.php';
session_start();

// PROTEZIONE: Verifica accesso admin
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'admin') {
    die("ACCESSO NEGATO.");
}

/* ===== SEZIONE 2: Verifica Parametro e Protezione Self-Toggle ===== */
// Controlla se è stato passato un ID da toggleare
if (isset($_GET['id'])) {
    $id_utente = $_GET['id'];
    
    // PROTEZIONE: Non permette all'admin di bloccare se stesso
    // Questo evita di bloccarsi fuori dal sistema
    if ($id_utente == $_SESSION['user_id']) {
        die("Non puoi modificare il tuo stesso stato.");
    }
    
    try {
        /* ===== SEZIONE 3: Toggle del Campo Bannato ===== */
        // UPDATE query che inverte il valore di bannato:
        // Se bannato = 0 (attivo) → diventa 1 (bloccato)
        // Se bannato = 1 (bloccato) → diventa 0 (attivo)
        // Funzione NOT su campo BOOLEAN/TINYINT inverte automaticamente
        $sql = "UPDATE utenti SET bannato = NOT bannato WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_utente]);
        
        // REDIRECT: Torna alla dashboard con messaggio di conferma
        header("Location: admin_dashboard.php?msg=Stato utente aggiornato correttamente.");
        exit;
    } catch (PDOException $e) {
        // Se il database genera errore, mostralo
        die("Errore Database: " . $e->getMessage());
    }
} else {
    // Se non c'è ID, reindirizza senza fare nulla
    header("Location: admin_dashboard.php");
    exit;
}
?>