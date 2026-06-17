<?php
// includes/select.php
// File contenente le funzioni ausiliarie per il recupero dati dal database

// Funzione per ottenere la Classifica dei Top 10 giocatori
// Restituisce: Array di utenti ordinati per punteggio decrescente
function getTopPlayers($pdo) {
    try {
        // Query: recupera nome, cognome e punteggio dei giocatori non bannati
        // Ordina per punteggio decrescente e limita a 10 risultati
        $sql = "SELECT u.nome, u.cognome, p.punteggio, p.data_partita 
                FROM punteggi p
                JOIN utenti u ON p.id_utente = u.id
                WHERE u.bannato = 0 
                ORDER BY p.punteggio DESC 
                LIMIT 10";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return []; // In caso di errore, ritorna lista vuota
    }
}

// Funzione per ottenere i dati completi di un singolo utente
// Parametri: $pdo (connessione DB), $user_id (ID dell'utente)
// Restituisce: Array associativo con tutti i dati dell'utente
function getUserData($pdo, $user_id) {
    try {
        // Query preparata per evitare SQL injection
        $stmt = $pdo->prepare("SELECT * FROM utenti WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Funzione per ottenere il punteggio migliore di un utente
// Parametri: $pdo (connessione DB), $user_id (ID dell'utente)
// Restituisce: Numero intero con il miglior punteggio (0 se non ha punteggi)
function getUserBestScore($pdo, $user_id) {
    try {
        // Query che calcola il MAX dei punteggi per un utente specifico
        $stmt = $pdo->prepare("SELECT MAX(punteggio) as best_score FROM punteggi WHERE id_utente = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        // Se non ci sono punteggi, ritorna 0
        return $result['best_score'] ? $result['best_score'] : 0;
    } catch (PDOException $e) {
        return 0;
    }
}

// Funzione per calcolare le medaglie sbloccate in base al punteggio
// Parametri: $score (punteggio dell'utente)
// Restituisce: Array di medaglie sbloccate con icon, name, desc e color
function getEarnedMedals($score) {
    $medals = [];

    // Logica delle soglie di punteggio per sbloccare le medaglie
    // Bronzo: 500 punti
    if ($score >= 500) {
        $medals[] = ['icon' => '🥉', 'name' => 'Bronzo', 'desc' => '500 punti raggiunti!', 'color' => '#cd7f32'];
    }
    // Argento: 1500 punti
    if ($score >= 1500) {
        $medals[] = ['icon' => '🥈', 'name' => 'Argento', 'desc' => '1500 punti raggiunti!', 'color' => '#c0c0c0'];
    }
    // Oro: 3000 punti
    if ($score >= 3000) {
        $medals[] = ['icon' => '🥇', 'name' => 'Oro', 'desc' => '3000 punti raggiunti!', 'color' => '#ffd700'];
    }
    // Diamante: 5000 punti
    if ($score >= 5000) {
        $medals[] = ['icon' => '💎', 'name' => 'Diamante', 'desc' => '5000 punti raggiunti!', 'color' => '#00ffff'];
    }

    return $medals;
}
?>