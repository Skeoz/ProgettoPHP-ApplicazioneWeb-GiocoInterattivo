# Bug Clicker - Sviluppo di Applicazioni Web

Un videogioco web-based in stile "clicker" sviluppato come progetto per il corso di Sviluppo di Applicazioni Web (UniGe). Il progetto non è solo un'applicazione interattiva lato client, ma un sistema full-stack completo, dotato di un proprio backend, gestione utenti e un forte focus sulla sicurezza del dato.

🎮 **Live Demo:** [Gioca a Bug Clicker](https://saw.dibris.unige.it/~s5415544/)

## 🚀 Funzionalità Principali

* **Core Gameplay Interattivo**: Logica di gioco sviluppata nativamente in JavaScript per gestire i click, il loop di gioco e il tracciamento del punteggio in tempo reale sul browser.
* **Sistema di Autenticazione**: Form completi e sicuri di Registrazione e Login per la gestione delle sessioni utente.
* **Database e Classifiche**: Salvataggio persistente dei record e dei progressi dei giocatori su database relazionale, con gestione dinamica dei punteggi.
* **Architettura Client-Server**: Comunicazione solida tra l'interfaccia utente (HTML/JS) e la logica di business (PHP).

## 🛡️ Sicurezza Implementata

La sicurezza è stata un pilastro dello sviluppo backend. L'infrastruttura in PHP è stata blindata contro le vulnerabilità web più critiche:
* **Prevenzione SQL Injection (SQLi)**: Interazione con il database gestita rigorosamente tramite *Prepared Statements* (utilizzo dei *placeholder* `?`). Nessun input utente viene mai concatenato direttamente nelle stringhe SQL.
* **Prevenzione Cross-Site Scripting (XSS)**: Hardening degli output. Ogni dato proveniente dall'esterno viene filtrato e ogni output generato dinamicamente viene severamente sanitizzato prima del rendering HTML, bloccando alla radice l'iniezione di script malevoli.

## 🛠️ Stack Tecnologico

* **Frontend**: HTML5, CSS3, JavaScript
* **Backend**: PHP
* **Database**: PostgreSQL
* **Ambiente di Sviluppo**: XAMPP
* **Hosting / Deployment**: Server DIBRIS (Università degli Studi di Genova)

## 👤 Autori
* **Andrea** (Matricola: 5415544)
* **Jeffrey Germano** (Matricola: 5669424)
