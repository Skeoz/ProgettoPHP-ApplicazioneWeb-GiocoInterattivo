<?php
/* ===== SEZIONE 1: Inclusione File e Impostazione Risposta JSON ===== */
// api_save_score.php - API endpoint per salvare i punteggi dal game.js
require_once 'includes/connection.php';
session_start();

// HEADER: Specifica che la risposta è JSON
// Questo header viene inviato per dire al browser che il contenuto è JSON, non HTML
header('Content-Type: application/json');

/* ===== SEZIONE 2: Controllo Sessione Utente ===== */
// Verifica che l'utente sia loggato prima di salvare qualsiasi punteggio
// Se non loggato, restituisce errore JSON
if (!isset($_SESSION['user_id'])) {
    // Risposta JSON con errore (formato compatibile con fetch del game.js)
    echo json_encode(['success' => false, 'message' => 'Devi essere loggato per salvare il punteggio']);
    exit;
}

/* ===== SEZIONE 3: Decodifica Dati JSON in Input ===== */
// Legge il raw input (corpo della richiesta POST) che contiene JSON
$input = file_get_contents("php://input");
// Converte la stringa JSON in array associativo PHP (true = associativo)
$data = json_decode($input, true);

/* ===== SEZIONE 4: Validazione e Salvataggio Punteggio ===== */
// Verifica che il campo 'score' sia presente nei dati ricevuti
if (isset($data['score'])) {
    // Cast a INT per proteggere da valori non numerici
    $punteggio = (int)$data['score'];
    // Recupera l'ID utente dalla sessione (loggato)
    $user_id = $_SESSION['user_id'];

    try {
        /* ===== SEZIONE 5: Inserimento nel Database ===== */
        // INSERT query: aggiunge un nuovo record alla tabella punteggi
        // Campi: id_utente (chi ha fatto il punteggio), punteggio (valore)
        // Il timestamp viene aggiunto automaticamente dal database (DEFAULT CURRENT_TIMESTAMP)
        $sql = "INSERT INTO punteggi (id_utente, punteggio) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $punteggio]);
        
        // RISPOSTA: Successo con messaggio
        echo json_encode(['success' => true, 'message' => 'Punteggio salvato!']);
    } catch (PDOException $e) {
        // GESTIONE ERRORE: Se il database fallisce, restituisce messaggio di errore
        echo json_encode(['success' => false, 'message' => 'Errore DB: ' . $e->getMessage()]);
    }
} else {
    // ERRORE: Se 'score' non è presente nei dati
    echo json_encode(['success' => false, 'message' => 'Nessun punteggio ricevuto']);
}
?>