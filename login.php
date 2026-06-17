<?php
// Inclusione del file di connessione al database
require_once 'includes/connection.php';
// Inizializzazione della sessione
session_start(); 

// Impostazione del titolo della pagina
$pageTitle = "Accedi - GameSAW";
// Variabile per i messaggi di feedback all'utente
$messaggio = "";

// Reindirizzamento automatico se l'utente è già loggato
if (isset($_SESSION['user_id'])) {
    header("Location: profilo.php");
    exit;
}

// Elaborazione del form quando viene inviato via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupero e pulizia dei dati dal form
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validazione: verifica che tutti i campi siano compilati
    if (empty($email) || empty($password)) {
        $messaggio = "❌ Inserisci tutti i campi.";
    } else {
        // Query per cercare l'utente nel database tramite email
        $stmt = $pdo->prepare("SELECT * FROM utenti WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verifica dei dati dell'utente
        if ($user) {
            // Controllo se l'account è bannato
            if ($user['bannato'] == 1) {
                $messaggio = "🚫 Accesso negato: Account bloccato.";
            // Verifica della password tramite password_verify (hash sicuro)
            } elseif (password_verify($password, $user['password'])) {
                // Creazione della sessione con i dati dell'utente
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nome'] = $user['nome'];
                $_SESSION['ruolo'] = $user['ruolo'];
                // Reindirizzamento al profilo
                header("Location: profilo.php");
                exit;
            } else {
                $messaggio = "❌ Password errata.";
            }
        } else {
            $messaggio = "❌ Utente non trovato.";
        }
    }
}

// Inclusione dell'header e della barra di navigazione
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Contenitore principale della pagina di login -->
<div class="auth-container">
    <!-- Titolo della pagina -->
    <h2>🔐 Accedi</h2>

    <!-- Visualizzazione del messaggio di errore se presente -->
    <?php if (!empty($messaggio)): ?>
        <div class="alert-error"><?= $messaggio ?></div>
    <?php endif; ?>
    
    <!-- Visualizzazione del messaggio di successo dopo la registrazione -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'registered'): ?>
        <div class="alert-success">✅ Account creato! Accedi qui.</div>
    <?php endif; ?>

    <!-- Form di login -->
    <form action="login.php" method="POST">
        <!-- Campo email -->
        <input type="email" name="email" placeholder="Email" required>
        <!-- Campo password -->
        <input type="password" name="password" placeholder="Password" required>
        <!-- Bottone di invio -->
        <button type="submit" class="btn-primary">Entra</button>
    </form>

    <!-- Link per la registrazione di nuovi utenti -->
    <a href="register.php" class="auth-link">Non hai un account? <b>Registrati</b></a>

    <!-- Link per tornare alla home -->
    <div class="back-footer">
        <a href="index.php" class="btn-back">⬅ Torna alla Home</a>
    </div>
</div>

<!-- Inclusione del footer -->
<?php include 'includes/footer.php'; ?>