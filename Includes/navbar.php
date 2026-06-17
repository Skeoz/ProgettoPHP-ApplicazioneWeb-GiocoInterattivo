<!-- Barra di navigazione principale del sito -->
<nav class="main-nav">
    <!-- Contenitore della navbar -->
    <div class="nav-container">
        
        <!-- Sezione sinistra: Logo del sito -->
        <div class="nav-left">
            <a href="index.php" class="nav-logo">
                🐛 GameSAW
            </a>
        </div>

        <!-- Sezione destra: Link di navigazione condizionati all'autenticazione -->
        <div class="nav-right">
            <!-- Se l'utente è loggato: mostra nome utente e bottone logout -->
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <!-- Link al profilo utente con icona -->
                <a href="profilo.php" class="user-link">
                    <span class="user-icon">👤</span> 
                    <?= htmlspecialchars($_SESSION['nome']) ?>
                </a>
                
                <!-- Bottone di logout con icona SVG -->
                <a href="logout.php" class="btn-logout" title="Disconnetti" style="display: flex; align-items: center; justify-content: center;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                   <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                   <line x1="12" y1="2" x2="12" y2="12"></line>
                  </svg>
                </a>
            <?php else: ?>

                <!-- Se l'utente non è loggato: mostra bottone di accesso -->
                <a href="login.php" class="btn-login-nav">Accedi</a>

            <?php endif; ?>
        </div>

    </div>
</nav>