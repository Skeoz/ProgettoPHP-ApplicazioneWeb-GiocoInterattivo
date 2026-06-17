<?php
// Inclusioni e inizializzazione della sessione
require_once 'includes/connection.php';
session_start();

// Impostazioni della pagina: titolo e flag per identificare questa come pagina di gioco
$pageTitle = "BUG HUNTER - GameSAW";
$isGamePage = true; 
include 'includes/header.php';
?>

<!-- Contenitore principale del gioco -->
<div class="game-wrapper">
    
    <!-- Effetto scanlines per lo stile retrò -->
    <div class="scanlines"></div>

    <!-- Strato UI sovrapposto: statistiche di gioco in tempo reale -->
    <div id="ui-layer">
        <!-- Sezione sinistra: Punteggio del giocatore -->
        <div class="ui-left">
            <div class="stat-box">PUNTI: <span id="score">0</span></div>
        </div>

        <!-- Sezione centrale: Timer del gioco -->
        <div class="ui-center">
            <div class="stat-box">TEMPO: <span id="timer">120</span></div>
        </div>

        <!-- Sezione destra: Hint per il controllo pausa -->
        <div class="ui-right">
            <div class="stat-box-hint">PREMI 'ESC' PER PAUSA</div>
        </div>
    </div>

    <!-- Menu di pausa: visualizzato quando il gioco viene messo in pausa -->
    <div id="pause-menu" style="display: none;">
        <h1 class="pause-title">SISTEMA IN ATTESA</h1>
        
        <!-- Pulsanti di controllo pausa: riprendere, gestire musica, abbandonare -->
        <div class="dashboard dashboard-pause">
            <button id="btn-resume" class="btn-orange">[ RIPRENDI ]</button>
            <button id="btn-music" class="btn-cyan">[ MUSICA: ON ]</button>
            <button id="btn-quit" class="btn-red">[ ABBANDONA ]</button>
        </div>
    </div>

    <!-- Overlay iniziale: schermata di benvenuto e istruzioni -->
    <div id="overlay">
        <h1>BUG HUNTER</h1>
        
        <!-- Dashboard principale con pannelli informativi -->
        <div class="dashboard">
            <!-- Pannello sinistro: Legenda dei nemici e loro punti -->
            <div class="panel panel-left">
                <h3 class="panel-title-text">DATI SULLE MINACCE</h3>
                
                <!-- Legenda con tutti i tipi di nemici e bonus -->
                <div class="legend">
                    <p class="leg-std">🐛 STD BUG (+10)</p>
                    <p class="leg-worm">🐍 WORM (+30)</p>
                    <p class="leg-tank">🛡️ TANK (+40)</p>
                    <p class="leg-gold">🪲 GOLD (+50)</p>
                    <p class="leg-virus">🐛 VIRUS (-20)</p> 
                    <p class="leg-bonus">💎 BONUS (2X)</p>
                </div>
            </div>

            <!-- Pannello destro: Messaggio iniziale e informazioni giocatore -->
            <div class="panel panel-right">
                <!-- Messaggio narrativo di inizio gioco -->
                <p id="message" class="mt-0">SISTEMA INFETTO.<br>AVVIARE PROCEDURA DI BONIFICA.</p>

                <!-- Informazioni del giocatore: mostra nome se loggato, altrimenti modalità ospite -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <p class="player-name">OPERATORE: <?= htmlspecialchars($_SESSION['nome']) ?></p>
                <?php else: ?>
                    <p class="warning">⚠️ MODALITÀ OSPITE ⚠️</p>
                <?php endif; ?>

                <!-- Area di inizio gioco: bottone hold-to-start e spinner di caricamento -->
                <div class="start-area">
                    <!-- Bottone che richiede di essere tenuto premuto per iniziare -->
                    <button id="btn-init" class="btn-start btn-start-hold">
                        [ TIENI PREMUTO PER INIZIARE ]
                    </button>
                    
                    <!-- Spinner rotante visualizzato durante il caricamento -->
                    <div id="progress-wheel" class="loader-wheel">
                        <div class="bar s1"></div>
                        <div class="bar s2"></div>
                        <div class="bar s3"></div>
                        <div class="bar s4"></div>
                        <div class="bar s5"></div>
                        <div class="bar s6"></div>
                        <div class="bar s7"></div>
                        <div class="bar s8"></div>
                    </div>
                </div>

                <!-- Spaziatore visuale -->
                <div class="spacer-15"></div>
                
                <!-- Bottone per tornare alla home page -->
                <a href="index.php" class="link-no-decor w-100">
                    <button class="btn-exit-sys">[ TERMINA SESSIONE ]</button>
                </a>
            </div>
        </div>
    </div>

    <!-- Contenitore principale dove vengono renderizzati gli elementi del gioco (nemici, etc) -->
    <div id="game-area"></div>

    <!-- Script principale del gioco con logica di gameplay -->
    <script src="assets/js/game.js?v=FINAL_V3"></script>
</div>

<!-- Inclusione del footer della pagina -->
<?php include 'includes/footer.php'; ?>