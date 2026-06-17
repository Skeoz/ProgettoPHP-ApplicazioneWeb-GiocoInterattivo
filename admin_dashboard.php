<?php
/* ===== SEZIONE 1: Inclusione File e Protezione Accesso Admin ===== */
// Carica la connessione PDO al database
require_once 'includes/connection.php';
// Avvia la sessione per controllare lo stato dell'utente
session_start();

// PROTEZIONE: Verifica che l'utente sia loggato E sia un admin
// Se non è admin, viene reindirizzato automaticamente alla home
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'admin') {
    // Reindirizza gli utenti non autorizzati
    header("Location: index.php");
    exit;
}

// Titolo della pagina per il tag <title>
$pageTitle = "Dashboard Admin - GameSAW";

/* ===== SEZIONE 2: Recupero Dati Utenti dal Database ===== */
// Query che seleziona TUTTI gli utenti ordinati per data registrazione (più recenti prima)
$stmt = $pdo->query("SELECT * FROM utenti ORDER BY data_registrazione DESC");
// Recupera il risultato come array associativo
$utenti = $stmt->fetchAll();

// Include il document head con meta e CSS
include 'includes/header.php';
// Include la barra di navigazione
include 'includes/navbar.php';
?>

<!-- ===== SEZIONE 3: Intestazione Dashboard e Messaggio di Successo ===== -->
<div class="container">
    <!-- Header con titolo e sottotitolo -->
    <div class="d-flex justify-between align-center mb-20">
        <h1 class="text-dark-h1">👑 Pannello Admin</h1>
        <span class="text-muted">Gestione Utenti</span>
    </div>
    
    <!-- Mostra messaggio di conferma se presente (es. dopo ban toggle) -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-success">
            <!-- Pulisce il testo dal $_GET per evitare XSS -->
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <!-- ===== SEZIONE 4: Tabella di Gestione Utenti ===== -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Utente</th>
                <th>Email</th>
                <th>Stato</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <!-- Itera su tutti gli utenti dal database -->
            <?php foreach ($utenti as $u): ?>
                <!-- Se l'utente è bannato, applica classe CSS per evidenziarla -->
                <tr class="<?= $u['bannato'] == 1 ? 'row-banned' : '' ?>">
                    <!-- Colonna ID: identificativo univoco dell'utente -->
                    <td><b>#<?= $u['id'] ?></b></td>
                    <!-- Colonna Nome: nome + cognome dell'utente (escapa HTML per sicurezza) -->
                    <td class="player-name"><?= htmlspecialchars($u['nome'] . ' ' . $u['cognome']) ?></td>
                    <!-- Colonna Email: indirizzo email registrato -->
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <!-- Colonna Stato: badge che mostra ruolo (ADMIN) o status (ATTIVO/BLOCCATO) -->
                    <td>
                        <?php if ($u['ruolo'] == 'admin'): ?>
                            <span class="status-badge status-admin">ADMIN</span>
                        <?php elseif ($u['bannato'] == 1): ?>
                            <span class="status-badge status-err">BLOCCATO</span>
                        <?php else: ?>
                            <span class="status-badge status-ok">ATTIVO</span>
                        <?php endif; ?>
                    </td>
                    <!-- Colonna Azioni: permette di bloccare/sbloccare utenti (non se stessi) -->
                    <td>
                        <!-- Non permette all'admin di modificare il proprio status -->
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            
                            <!-- Se utente è ATTIVO, mostra bottone BLOCCA -->
                            <?php if ($u['bannato'] == 0): ?>
                                <a href="admin_toggle_ban.php?id=<?= $u['id'] ?>" 
                                   class="btn-outline-red"
                                   onclick="return confirm('Bloccare questo utente?');">
                                   🚫 Blocca
                                </a>
                            <!-- Se utente è BLOCCATO, mostra bottone SBLOCCA -->
                            <?php else: ?>
                                <a href="admin_toggle_ban.php?id=<?= $u['id'] ?>" 
                                   class="btn-outline-green"
                                   onclick="return confirm('Riattivare questo utente?');">
                                   ✅ Sblocca
                                </a>
                            <?php endif; ?>

                        <!-- Se è l'admin stesso, mostra solo un indicatore -->
                        <?php else: ?>
                            <span class="text-self">(Tu)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ===== SEZIONE 5: Link per Tornare al Profilo ===== -->
    <div class="text-center mt-30">
        <!-- Link per ritornare alla pagina profilo -->
        <a href="profilo.php" class="auth-link">🔙 Torna al Profilo</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>