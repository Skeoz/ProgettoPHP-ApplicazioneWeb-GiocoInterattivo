<?php
// includes/header.php
// File di intestazione: contiene meta tag, link a CSS e apertura del documento HTML

// Verifica se la sessione è già stata avviata; se no, la inizia
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <!-- Meta tag essenziali -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Titolo della pagina: impostabile da ogni file che include questo header -->
    <title><?= isset($pageTitle) ? $pageTitle : 'GameSAW - Bug Hunter' ?></title>
    
    <!-- Link ai font di Google (Press Start 2P per lo stile arcade, Roboto per il moderno) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <?php 
    // LOGICA INTELLIGENTE PER IL CARICAMENTO DEI CSS
    // Verifica se la pagina corrente è una pagina di gioco ($isGamePage = true)
    // Se sì, carica lo stile arcade (style.css - sfondo nero, pixel art)
    // Altrimenti, carica lo stile moderno (base.css - sfondo bianco, card design)
    
    if (isset($isGamePage) && $isGamePage === true) {
        // Stile gioco: tema arcade con sfondo scuro
        echo '<link rel="stylesheet" href="assets/css/style.css?v=3.0">';
    } else {
        // Stile sito: tema moderno con card e layout responsive
        echo '<link rel="stylesheet" href="assets/css/base.css?v=3.0">';
    }
    ?>
</head>
<!-- Inizio del corpo HTML della pagina -->
<body>