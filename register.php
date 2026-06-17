<?php
// Inclusione del file di connessione al database
require_once 'includes/connection.php';
// Impostazione del titolo della pagina
$pageTitle = "Registrati - GameSAW";
// Variabile per i messaggi di feedback all'utente
$messaggio = "";

// Visualizzazione del messaggio di conferma dopo eliminazione account
if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
    $messaggio = "😢 Account eliminato.";
}

// Elaborazione del form quando viene inviato via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupero e pulizia dei dati dal form
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validazione: verifica che tutti i campi siano compilati
    if (empty($nome) || empty($cognome) || empty($email) || empty($password)) {
        $messaggio = "❌ Tutti i campi sono obbligatori!";
    // Verifica che le password coincidano
    } elseif ($password !== $confirm_password) {
        $messaggio = "❌ Le password non coincidono!";
    } else {
        // Hashing della password utilizzando PASSWORD_DEFAULT (attualmente bcrypt)
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            // Query preparata per inserire il nuovo utente nel database
            $stmt = $pdo->prepare("INSERT INTO utenti (nome, cognome, email, password) VALUES (?, ?, ?, ?)");
            // Esecuzione della query con parametri legati
            $stmt->execute([$nome, $cognome, $email, $password_hash]);
            // Reindirizzamento alla pagina di login con messaggio di successo
            header("Location: login.php?status=registered");
            exit;
        } catch (PDOException $e) {
            // Gestione degli errori del database
            // Codice 23000 è l'errore di violazione di vincolo unique (email già registrata)
            if ($e->getCode() == 23000) {
                $messaggio = "⚠️ Email già registrata.";
            } else {
                $messaggio = "❌ Errore generico.";
            }
        }
    }
}

// Inclusione dell'header e della barra di navigazione
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Contenitore principale della pagina di registrazione -->
<div class="auth-container">
    <!-- Titolo della pagina -->
    <h2>📝 Crea Account</h2>
    
    <!-- Visualizzazione dei messaggi di errore o feedback -->
    <?php if (!empty($messaggio)): ?>
        <div class="alert-error"><?= $messaggio ?></div>
    <?php endif; ?>

    <!-- Form di registrazione -->
    <form action="register.php" method="POST">
        <!-- Campo Nome -->
        <input type="text" name="nome" placeholder="Nome" required>
        <!-- Campo Cognome -->
        <input type="text" name="cognome" placeholder="Cognome" required>
        <!-- Campo Email -->
        <input type="email" name="email" placeholder="Email" required>
        <!-- Campo Password -->
        <input type="password" name="password" placeholder="Password" required>
        <!-- Campo Conferma Password -->
        <input type="password" name="confirm_password" placeholder="Conferma Password" required>
        
        <!-- Bottone di invio per la registrazione -->
        <button type="submit" class="btn-success">Registrati</button>
    </form>
    
    <!-- Link per gli utenti che hanno già un account -->
    <a href="login.php" class="auth-link">Hai già un account? <b>Accedi</b></a>

    <!-- Link per tornare alla home -->
    <div class="back-footer">
        <a href="index.php" class="btn-back">⬅ Torna alla Home</a>
    </div>
</div>

<!-- Inclusione del footer -->
<?php include 'includes/footer.php'; ?>