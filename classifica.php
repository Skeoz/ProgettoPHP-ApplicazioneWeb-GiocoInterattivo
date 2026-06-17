<?php
/* ===== SEZIONE 1: Inclusione File e Recupero Dati Top Players ===== */
// Carica la connessione PDO al database
require_once 'includes/connection.php';
// Carica il file con funzioni database helper (getTopPlayers, getUserData, etc.)
require_once 'includes/select.php';
// Avvia la sessione per controllare l'utente loggato
session_start();

// Titolo della pagina nel tag <title>
$pageTitle = "Classifica - GameSAW";
// Chiama la funzione helper che recupera i top 10 giocatori dal database
// Escludi utenti bannati, ordina per punteggio DESC
$top_players = getTopPlayers($pdo);

// Include il document head con meta, CSS e gestione tema
include 'includes/header.php';
// Include la barra di navigazione
include 'includes/navbar.php';
?>

<!-- ===== SEZIONE 2: Hero Section con Titolo e Sottotitolo ===== -->
<div class="hero-section hero-sm">
    <!-- Titolo principale della classifica -->
    <h1>🏆 Classifica Top 10</h1>
    <!-- Sottotitolo descrittivo -->
    <p>I migliori Bug Hunter di sempre</p>
</div>

<!-- ===== SEZIONE 3: Contenitore Principale e Tabella Ranking ===== -->
<div class="container">
    <!-- Tabella che mostra i top 10 giocatori con posizione, nome, punteggio e data -->
    <table>
        <thead>
            <tr>
                <th width="10%">Pos</th>
                <th>Giocatore</th>
                <th>Punteggio</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <!-- Inizializza contatore di posizione per la classifica -->
            <?php $rank = 1; ?>
            <!-- Itera su ogni giocatore nei top 10 -->
            <?php foreach ($top_players as $row): ?>
                <?php 
                    // Determina la classe CSS per il badge di posizione (colori speciali per top 3)
                    $rankClass = "rank-other"; 
                    if($rank == 1) $rankClass = "rank-1";  // Oro
                    if($rank == 2) $rankClass = "rank-2";  // Argento
                    if($rank == 3) $rankClass = "rank-3";  // Bronzo
                ?>
                <tr>
                    <!-- Posizione in classifica con badge colorato -->
                    <td>
                        <span class="rank-badge <?= $rankClass ?>">
                            <?= $rank++ ?>
                        </span>
                    </td>
                    <!-- Nome giocatore: nome + prima lettera cognome + punto (es: "Andrea P.") -->
                    <td class="player-name">
                        <?= htmlspecialchars($row['nome'] . ' ' . $row['cognome'][0] . '.') ?>
                    </td>
                    <!-- Punteggio totale raggiunto -->
                    <td>
                        <span class="score-val"><?= $row['punteggio'] ?></span>
                    </td>
                    <!-- Data della partita nel formato GG/MM/AAAA -->
                    <td>
                        <span class="date-val">
                            <?= date("d/m/Y", strtotime($row['data_partita'])) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <!-- Se non ci sono punteggi, mostra messaggio vuoto -->
            <?php if (count($top_players) == 0): ?>
                <tr><td colspan="4" class="text-center p-30">Ancora nessuna partita giocata.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- ===== SEZIONE 4: Bottone per Giocare e Salire in Classifica ===== -->
    <div class="text-center mt-30">
        <!-- Link al game per permettere ai giocatori di aggiungere il loro punteggio -->
        <a href="game.php" class="btn-hero">🎮 SCALA LA CLASSIFICA</a>
    </div>

    <!-- ===== SEZIONE 5: Link per Tornare alla Home ===== -->
    <div class="back-footer">
        <!-- Bottone per ritornare alla pagina principale -->
        <a href="index.php" class="btn-back">⬅ Torna alla Home</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>