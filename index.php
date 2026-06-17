<?php
// Inclusioni e inizializzazione sessione
require_once 'includes/connection.php';
session_start();

// Impostazione del titolo della pagina e inclusione dei componenti header/navbar
$pageTitle = "Home - GameSAW";
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Sezione hero principale con titolo e call-to-action -->
<header class="hero-section">
    <h1>GameSAW<br>BUG HUNTER</h1>
    <p>Il sistema è infetto. Abbiamo bisogno delle tue abilità di programmatore per eliminare i bug e salvare il database.</p>
    
    <!-- Bottone per iniziare il gioco -->
    <div class="mt-30">
        <a href="game.php" class="btn-hero">🎮 INIZIA LA MISSIONE</a>
    </div>
</header>

<!-- Indicatore dello stato della connessione al database -->
<div class="text-center mb-40">
    <?php if (isset($pdo)): ?>
        <span class="status-badge status-ok">
            ✅ Connessione al Database: OK
        </span>
    <?php else: ?>
        <span class="status-badge status-err">
            ❌ Connessione al Database: ERRORE
        </span>
    <?php endif; ?>
</div>

<!-- Griglia delle feature principali -->
<div class="container">
    <div class="features-grid">
        
        <!-- Card 1: Classifica dei migliori bug hunter -->
        <div class="feature-card card-rank">
            <span class="feature-icon">🏆</span>
            <h3>Albo D'Oro</h3>
            <p>Scopri chi sono i migliori cacciatori di bug del corso. Riuscirai a battere il record?</p>
            <a href="classifica.php" class="btn-small btn-yellow">Vedi Classifica</a>
        </div>

        <!-- Card 2: Profilo utente (condizionato al login) -->
        <div class="feature-card card-profile">
            <span class="feature-icon">👤</span>
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Visualizzato se l'utente è loggato -->
                <h3>Il tuo Profilo</h3>
                <p>Bentornato, <b><?= htmlspecialchars($_SESSION['nome']) ?></b>. Controlla le tue statistiche e aggiorna i dati.</p>
                <a href="profilo.php" class="btn-small btn-blue">Vai al Profilo</a>
            <?php else: ?>
                <!-- Visualizzato se l'utente non è loggato -->
                <h3>Accesso Agenti</h3>
                <p>Identificati per salvare i tuoi punteggi nel database centrale.</p>
                <a href="login.php" class="btn-small btn-blue">Accedi Ora</a>
            <?php endif; ?>
        </div>

        <!-- Card 3: Documentazione del progetto -->
        <div class="feature-card card-code">
            <span class="feature-icon">💻</span>
            <h3>Il progetto</h3>
            <p>Scopri come è stato realizzato questo progetto e le tecnologie utilizzate.</p>
            <a href="about.php" class="btn-small btn-red">Leggi Documentazione</a>
        </div>

    </div>
</div>

<!-- Inclusione del footer -->
<?php include 'includes/footer.php'; ?>