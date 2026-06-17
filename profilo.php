<?php
// Inclusioni dei file di configurazione e funzioni ausiliarie
require_once 'includes/connection.php';
require_once 'includes/select.php';
// Inizializzazione della sessione
session_start();

// Impostazione del titolo della pagina
$pageTitle = "Il mio Profilo";

// Verifica se l'utente è autenticato: se non ha una sessione attiva, reindirizza al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Recupero dell'ID utente dalla sessione
$user_id = $_SESSION['user_id'];
// Recupero dei dati completi dell'utente dal database
$user = getUserData($pdo, $user_id);
// Recupero del miglior punteggio dell'utente
$best_score = getUserBestScore($pdo, $user_id);

// RECUPERO LE MEDAGLIE SBLOCCATE
$my_medals = getEarnedMedals($best_score);

// Controllo se l'utente esiste ed è non bannato; se bannato, forza il logout
if (!$user || $user['bannato'] == 1) {
    header("Location: logout.php");
    exit;
}

// Inclusione dell'header e della barra di navigazione
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Contenitore principale della pagina profilo -->
<div class="container container-narrow">
    
    <!-- Intestazione del profilo con saluto all'utente -->
    <div class="profile-header">
        <div>
            <!-- Saluto personalizzato con il nome dell'utente -->
            <h1 class="text-dark-h1">Ciao, <?= htmlspecialchars($user['nome']) ?> 👋</h1>
            <p class="text-muted-sub">Area Personale</p>
            <div class="mt-20"></div>
        </div>
    </div>

    <!-- Sezione statistiche: miglior punteggio e dati personali -->
    <div class="d-flex gap-20 mb-40 flex-wrap">
        <!-- Card: Miglior punteggio dell'utente -->
        <div class="feature-card card-full stat-card-green">
            <h3 class="m-0">🏆 Miglior Punteggio</h3>
            <span class="score-big"><?= $best_score ?></span>
        </div>
        <!-- Card: Informazioni personali dell'utente -->
        <div class="feature-card card-full stat-card-blue">
            <h3 class="m-0">📂 I tuoi Dati</h3>
            <p><b>Nome:</b> <?= htmlspecialchars($user['nome'] . " " . $user['cognome']) ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
            <p><b>Stato:</b> <span class="status-badge status-ok">Attivo</span></p>
        </div>
    </div>

    <!-- Sezione medaglie sbloccate -->
    <h3 class="section-title">🎖️ Medaglie Sbloccate</h3>
    <div class="medals-wrapper">
        <!-- Visualizzazione del messaggio se non ci sono medaglie -->
        <?php if (empty($my_medals)): ?>
            <div class="medals-empty">
                Non hai ancora guadagnato medaglie. <br>Gioca per superare i 500 punti!
            </div>
        <?php else: ?>
            <!-- Griglia di medaglie sbloccate -->
            <div class="medals-grid">
                <?php foreach ($my_medals as $medal): ?>
                    <?php
                        // Assegnazione della classe bordo in base al colore della medaglia
                        $borderClass = "border-bronze"; 
                        if($medal['color'] == '#c0c0c0') $borderClass = "border-silver";
                        if($medal['color'] == '#ffd700') $borderClass = "border-gold";
                        if($medal['color'] == '#00ffff') $borderClass = "border-diamond";
                    ?>
                    <!-- Card della singola medaglia -->
                    <div class="medal-card <?= $borderClass ?>">
                        <div class="medal-icon"><?= $medal['icon'] ?></div>
                        <div class="medal-name"><?= $medal['name'] ?></div>
                        <div class="medal-desc"><?= $medal['desc'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sezione di gestione account -->
    <div class="card-panel mt-40">
        <h3 class="mb-20">⚙️ Gestione Account</h3>
        
        <!-- Bottone per modificare i dati personali -->
        <a href="edit_profile.php" class="btn-block-lg btn-primary no-decoration">
            ✏️ Modifica Dati Personali
        </a>

        <!-- Bottone admin dashboard: visibile solo agli amministratori -->
        <?php if ($user['ruolo'] === 'admin'): ?>
            <a href="admin_dashboard.php" class="btn-block-lg btn-admin mt-10 no-decoration">
                👑 Admin Dashboard
            </a>
        <?php endif; ?>

        <!-- Sezione di eliminazione account (diritto all'oblio) -->
        <div class="danger-separator mt-30">
            <div>
                <strong>Diritto all'oblio</strong><br>
                <span class="text-muted font-sm">Vuoi cancellare i tuoi dati?</span>
            </div>
            <!-- Bottone per eliminare l'account con conferma -->
            <a href="delete_account.php" class="btn-outline-red no-decoration" onclick="return confirm('Sei sicuro? Questa azione eliminerà DEFINITIVAMENTE il tuo account e i punteggi.');">
                🗑️ Elimina Account
            </a>
        </div>
    </div>

    <!-- Link per tornare alla home -->
    <div class="back-footer">
        <a href="index.php" class="btn-back">⬅ Torna alla Home</a>
    </div>
</div>

<!-- Inclusione del footer -->
<?php include 'includes/footer.php'; ?>