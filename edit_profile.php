<?php
// Inclusioni dei file di configurazione e della funzione di database
require_once 'includes/connection.php';
require_once 'includes/select.php'; 
// Inizializzazione della sessione
session_start();

// Impostazione del titolo della pagina
$pageTitle = "Modifica Profilo";

// Verifica se l'utente è autenticato: se non ha una sessione attiva, reindirizza al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Recupero dell'ID utente dalla sessione
$user_id = $_SESSION['user_id'];
// Variabili per gestire i messaggi di feedback all'utente
$messaggio = "";
$tipo_messaggio = ""; 

// Elaborazione del form quando viene inviato via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupero e pulizia dei dati dal form
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $email = trim($_POST['email']);

    // Validazione: verifica che nessun campo sia vuoto
    if (empty($nome) || empty($cognome) || empty($email)) {
        $messaggio = "❌ Tutti i campi sono obbligatori.";
        $tipo_messaggio = "alert-error";
    } else {
        // Query preparata per aggiornare i dati dell'utente nel database
        $sql = "UPDATE utenti SET nome = ?, cognome = ?, email = ? WHERE id = ?";
        try {
            // Preparazione e esecuzione della query con parametri legati (protezione SQL injection)
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $cognome, $email, $user_id]);
            // Aggiornamento del nome in sessione per riflettere i cambiamenti istantaneamente
            $_SESSION['nome'] = $nome;
            $messaggio = "✅ Profilo aggiornato con successo!";
            $tipo_messaggio = "alert-success";
        } catch (PDOException $e) {
            // Gestione degli errori del database
            $messaggio = "❌ Errore aggiornamento.";
            $tipo_messaggio = "alert-error";
        }
    }
}

// Recupero dei dati attuali dell'utente dal database
$stmt = $pdo->prepare("SELECT * FROM utenti WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Inclusione dell'header e della barra di navigazione della pagina
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Contenitore principale della pagina di modifica profilo -->
<div class="auth-container">
    <!-- Titolo della sezione -->
    <h2>✏️ Modifica Dati</h2>

    <!-- Visualizzazione del messaggio di feedback (successo o errore) -->
    <?php if (!empty($messaggio)): ?>
        <div class="<?= $tipo_messaggio ?>">
            <?= $messaggio ?>
        </div>
    <?php endif; ?>

    <!-- Form per la modifica dei dati profilo -->
    <form action="edit_profile.php" method="POST">
        <!-- Campo Nome -->
        <div style="text-align: left; font-weight: bold; font-size: 0.9em; color:#555;">Nome</div>
        <input type="text" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>

        <!-- Campo Cognome -->
        <div style="text-align: left; font-weight: bold; font-size: 0.9em; color:#555;">Cognome</div>
        <input type="text" name="cognome" value="<?= htmlspecialchars($user['cognome']) ?>" required>

        <!-- Campo Email -->
        <div style="text-align: left; font-weight: bold; font-size: 0.9em; color:#555;">Email</div>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <!-- Bottone di submit per salvare le modifiche -->
        <button type="submit" class="btn-primary">Salva Modifiche</button>
    </form>

    <!-- Sezione footer con link di ritorno al profilo -->
    <div class="back-footer">
        <a href="profilo.php" class="btn-back">⬅ Torna al Profilo</a>
    </div>
</div>

<!-- Inclusione del footer della pagina -->
<?php include 'includes/footer.php'; ?>