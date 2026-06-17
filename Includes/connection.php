<?php
// Configurazione della connessione al Database MySQL

// Credenziali del database
$host = '127.0.0.1'; // Indirizzo del server (localhost o 127.0.0.1)
$db   = 'gamesaw_db'; // Nome del database
$user = 'root';       // Utente MySQL di XAMPP (default: root)
$pass = '';           // Password MySQL di XAMPP (default: vuota)
$charset = 'utf8mb4'; // Set di caratteri per supportare emoji e caratteri speciali

// Stringa di connessione DSN (Data Source Name) per PDO
// Formato: driver:host=valore;dbname=valore;charset=valore
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opzioni di configurazione PDO per una connessione robusta e sicura
$options = [
    // ERRMODE_EXCEPTION: Lancia eccezioni in caso di errori SQL
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // FETCH_ASSOC: Restituisce i risultati come array associativi (nome colonna => valore)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // false: Usa statement preparati reali del database (più sicuro contro SQL injection)
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Tentativo di stabilire la connessione al database
    // PDO( stringa_connessione, utente, password, opzioni )
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Se la connessione fallisce, lancia un'eccezione con il messaggio di errore
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
