<?php
/* ===== SEZIONE 1: Inclusione File e Inizializzazione Sessione ===== */
// Carica la connessione PDO al database dal file includes/connection.php
require_once 'includes/connection.php';
// Avvia la sessione PHP per gestire utenti loggati
session_start();

// Titolo della pagina utilizzato nel tag <title> del document head
$pageTitle = "Info Progetto - GameSAW";
// Include il tag <head> con meta, CSS e gestione intelligente del tema
include 'includes/header.php';
// Include la barra di navigazione con logo e link autenticazione
include 'includes/navbar.php';
?>

<!-- ===== SEZIONE 2: Hero Section - Intestazione della Pagina ===== -->
<div class="hero-section hero-sm">
    <!-- Titolo principale con emoji per rendere attraente la pagina -->
    <h1>ℹ️ Il Progetto GameSAW</h1>
    <!-- Sottotitolo descrittivo del contenuto -->
    <p>Sviluppo Applicazioni Web: Dietro le quinte del codice</p>
</div>

<!-- ===== SEZIONE 3: Contenitore Principale con Card di Obiettivo e Developer ===== -->
<div class="container">
    <!-- Container flessibile con gap tra elementi e wrapping su mobile -->
    <div class="d-flex gap-20 flex-wrap mb-40">
        
        <!-- Card principale descrive lo scopo didattico del progetto -->
        <div class="card-panel about-main">
            <h2 class="section-title">🎯 L'Obiettivo</h2>
            <p class="section-text">
                <!-- Descrizione del progetto: simulazione sistema sotto attacco bug -->
                GameSAW è un progetto didattico nato per esplorare l'interazione tra <strong>Client</strong> e <strong>Server</strong>. 
                L'obiettivo è simulare un sistema informatico sotto attacco da parte di bug informatici. 
                L'utente deve "pulire" il sistema cliccando sui nemici prima che il tempo scada.
            </p>
        </div>

        <!-- Card laterale elenca gli sviluppatori del progetto con badge iniziali -->
        <div class="card-panel about-side">
            <h3 class="section-title">👨‍💻 Developers</h3>
            
            <!-- Lista dei tre sviluppatori con badge colorate -->
            <ul class="dev-list">
                <li class="dev-item">
                    <span class="badge-initials badge-gray">AP</span> 
                    Andrea Peri
                </li>
                <li class="dev-item">
                    <span class="badge-initials badge-gray">JG</span> 
                    Jeffrey Germano
                </li>
                <li class="dev-item">
                    <span class="badge-initials badge-blue">AI</span> 
                    Gemini (Supporto Tecnico)
                </li>
            </ul>
        </div>

    </div>

    <!-- ===== SEZIONE 4: Grid delle Tecnologie Utilizzate ===== -->
    <h3 class="text-center mb-20">🛠️ Stack Tecnologico</h3>
    <!-- Griglia flessibile con tre card di tecnologie principali -->
    <div class="features-grid">
        
        <!-- Card Front-End: HTML5, CSS3, JavaScript vanilla -->
        <div class="feature-card card-orange">
            <span class="feature-icon">🎨</span>
            <h3>Front-End</h3>
            <p>
                <!-- Tecnologie lato client: marcatura, stile e dinamica -->
                <b>HTML5 & CSS3:</b> Layout moderno e responsive.<br>
                <b>JavaScript (Vanilla):</b> Logica di gioco, spawn nemici, animazioni e gestione eventi.
            </p>
        </div>

        <!-- Card Back-End: PHP e struttura modulare con includes -->
        <div class="feature-card card-purple">
            <span class="feature-icon">⚙️</span>
            <h3>Back-End</h3>
            <p>
                <!-- Tecnologie lato server: gestione utenti e API -->
                <b>PHP 8:</b> Gestione sessioni, autenticazione utenti e API per il salvataggio punteggi.<br>
                <b>Includes:</b> Struttura modulare (Header, Navbar, DB).
            </p>
        </div>

        <!-- Card Database: MySQL e PDO per connessioni sicure -->
        <div class="feature-card card-teal">
            <span class="feature-icon">🗄️</span>
            <h3>Database</h3>
            <p>
                <!-- Persistenza dati e sicurezza SQL -->
                <b>MySQL:</b> Archiviazione utenti e storico punteggi.<br>
                <b>PDO:</b> Connessione sicura al database per prevenire SQL Injection.
            </p>
        </div>

    </div>

    <!-- ===== SEZIONE 5: Tabella Guida ai 6 Tipi di Nemici ===== -->
    <div class="mt-40">
        <h3 class="text-center mb-20">📖 Guida ai Nemici</h3>
        
        <!-- Tabella con icone, descrizioni, effetti e punti per ogni nemico -->
        <table>
            <thead>
                <tr>
                    <th>Icona</th>
                    <th>Nome</th>
                    <th>Effetto</th>
                    <th>Punti</th>
                </tr>
            </thead>
            <tbody>
                <!-- 🐛 Bug Standard: nemico base facile -->
                <tr>
                    <td class="icon-lg">🐛</td>
                    <td><b>Standard Bug</b></td>
                    <td>Nemico comune. Facile da eliminare.</td>
                    <td><span class="status-badge status-ok">+10</span></td>
                </tr>
                <!-- 🐍 Worm: nemico veloce che attraversa lo schermo -->
                <tr>
                    <td class="icon-lg">🐍</td>
                    <td><b>Worm</b></td>
                    <td>Veloce e sfuggente. Attraversa lo schermo.</td>
                    <td><span class="status-badge status-ok">+30</span></td>
                </tr>
                <!-- 🛡️ Tank: nemico corazzato che richiede 2 click -->
                <tr>
                    <td class="icon-lg">🛡️</td>
                    <td><b>Tank</b></td>
                    <td>Corazzato. Richiede 2 click per essere distrutto.</td>
                    <td><span class="status-badge status-ok">+40</span></td>
                </tr>
                <!-- 🪲 Golden Bug: nemico raro che scompare velocemente -->
                <tr>
                    <td class="icon-lg">🪲</td>
                    <td><b>Golden Bug</b></td>
                    <td>Raro. Appare e scompare velocemente.</td>
                    <td><span class="status-badge rank-1">+50</span></td>
                </tr>
                <!-- 🐛 Virus: trappola negativa che toglie punti se cliccato -->
                <tr class="bg-danger-light">
                    <td class="icon-lg">🐛</td>
                    <td><b>Virus (Trappola)</b></td>
                    <td>Si mimetizza! Attenzione al colore.</td>
                    <td><span class="status-badge status-err">-20</span></td>
                </tr>
                <!-- 💎 Bonus 2X: power-up speciale che raddoppia i punti per 15 secondi -->
                <tr>
                    <td class="icon-lg">💎</td>
                    <td><b>Bonus 2X</b></td>
                    <td>Raddoppia i punti per 15s.</td>
                    <td><span class="status-badge status-admin">+ PUNTI</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ===== SEZIONE 6: Link per Tornare alla Home ===== -->
    <div class="back-footer">
        <!-- Bottone per ritornare alla pagina principale -->
        <a href="index.php" class="btn-back">⬅ Torna alla Home</a>
    </div>

</div>

<?php include 'includes/footer.php'; ?>