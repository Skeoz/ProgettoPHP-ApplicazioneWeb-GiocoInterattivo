// assets/js/game.js - Logica completa del gioco BUG HUNTER

/* ===== SEZIONE 1: CONFIGURAZIONE ===== */
// Durata della partita in secondi
const GAME_DURATION = 120;
// Intervallo di spawn dei bug in millisecondi
const SPAWN_RATE = 550;

/* ===== SEZIONE 2: VARIABILI DI STATO ===== */
// Punteggio attuale del giocatore
let score = 0;
// Tempo rimanente in secondi
let timeLeft = GAME_DURATION;
// Flag: il gioco è in corso
let isPlaying = false;
// Flag: il gioco è in pausa
let isPaused = false; 
// Flag: la musica è attiva
let isMusicActive = true; 
// Contatore di click errati consecutivi (per overheat)
let consecutiveMisses = 0; 

// Timer & Loop - Identificatori degli intervalli
let gameInterval, spawnInterval, updateInterval, idleTimer, bonusTimer;

// Hold to Start - Variables per il meccanismo di pressione prolungata
let holdInterval;
// Tempo richiesto per premere e tenere (in ms)
const HOLD_DURATION = 1500;
// Numero di segmenti della rotella di caricamento
const TOTAL_SEGMENTS = 8;

// Bonus 2x Moltiplicatore - Logica aggiornata per la pausa
// Flag: il moltiplicatore 2X è attivo
let isMultiplierActive = false;
// Timestamp di quando è iniziato il timer corrente
let bonusStartTime = 0;
// Tempo rimanente del bonus (in ms)
let bonusRemaining = 0;

/* ===== SEZIONE 3: SISTEMA AUDIO ===== */
// Oggetto con i percorsi dei file audio
const paths = {
    music: 'assets/sounds/music.mp3',
    bonus: 'assets/sounds/bonus.mp3',
    overheat: 'assets/sounds/overheat.mp3',
    laser: 'assets/sounds/laser.mp3',
    damage: 'assets/sounds/danno.mp3',
    gold: 'assets/sounds/bug_dorato.mp3',
    shield: 'assets/sounds/rompi_scudo.mp3',
    slow: 'assets/sounds/youre_too_slow.mp3',
    good: 'assets/sounds/youre_pretty_good.mp3',
    job: 'assets/sounds/good_job.mp3',
    amazing: 'assets/sounds/youre_amazing.mp3',
    end: 'assets/sounds/fine_partita.mp3'
};

// Musica di background: loop continuo con volume basso
let bgMusic = new Audio(paths.music);
bgMusic.loop = true;   
bgMusic.volume = 0.12; 

// Audio bonus: suona durante il moltiplicatore 2X
let bonusAudio = new Audio(paths.bonus);
bonusAudio.volume = 0.3; 

// Audio announcer: per gli annunci narrativi del gioco
let announcerAudio = new Audio(); 

/* ===== SEZIONE 4: SELEZIONE ELEMENTI DOM ===== */
// Contenitore principale dove vengono renderizzati i bug
const gameArea = document.getElementById('game-area');
// Elemento che mostra il punteggio corrente
const scoreEl = document.getElementById('score');
// Elemento che mostra il timer
const timerEl = document.getElementById('timer');
// Overlay iniziale con dashboard
const overlay = document.getElementById('overlay');
// Menu di pausa
const pauseMenu = document.getElementById('pause-menu');
// Bottone per il controllo della musica
const musicBtn = document.getElementById('btn-music');
// Elemento per i messaggi narrativi
const messageEl = document.getElementById('message');
// Bottone per iniziare il gioco (hold to start)
const btnInit = document.getElementById('btn-init');
// Contenitore della rotella di caricamento
const wheelContainer = document.getElementById('progress-wheel');
// Singoli segmenti della rotella
const segments = document.querySelectorAll('.bar');

/* ===== SEZIONE 5: FUNZIONI AUSILIARIE ===== */
// Genera un numero casuale tra min e max (inclusi)
function random(min, max) { return Math.floor(Math.random() * (max - min + 1) + min); }

// Riproduce un effetto sonoro
function playSFX(path) {
    let sfx = new Audio(path);
    sfx.volume = 0.3; 
    sfx.play().catch(e => {});
}

// Riproduce un annuncio narrativo
function playAnnouncer(path) {
    // Ferma l'audio precedente
    announcerAudio.pause();      
    announcerAudio.currentTime = 0;
    // Carica il nuovo file
    announcerAudio.src = path;
    announcerAudio.volume = 1.0; 
    announcerAudio.play().catch(e => {});
}


/* ===== SEZIONE 6: LOGICA CORE (TIMERS E LOOP) ===== */
// Avvia i tre loop principali del gioco: countdown, spawn e aggiornamento
function startTimers() {
    // Loop 1: Countdown del timer ogni secondo
    gameInterval = setInterval(() => {
        timeLeft--;
        timerEl.innerText = timeLeft;
        // Timer rosso quando rimangono 10 secondi
        if (timeLeft <= 10) timerEl.style.color = 'red';
        // Fine della partita quando il timer raggiunge 0
        if (timeLeft <= 0) endGame();
    }, 1000);

    // Loop 2: Spawn dei bug ogni SPAWN_RATE ms
    spawnInterval = setInterval(spawnBug, SPAWN_RATE);

    // Loop 3: Aggiornamento posizioni e stati dei bug ogni 30ms
    updateInterval = setInterval(() => {
        // Salta l'aggiornamento se il gioco è in pausa
        if (isPaused) return; 
        
        const allBugs = document.querySelectorAll('.bug');
        allBugs.forEach(bug => {
            // Non aggiornare bug in fase di morte
            if (bug.classList.contains('dying')) return;

            // Worm: muove orizzontalmente
            if (bug.isWorm) {
                let currentLeft = parseFloat(bug.style.left);
                let newLeft = currentLeft + bug.speedX;
                bug.style.left = newLeft + 'px';
                // Rimuovi il worm quando esce dallo schermo
                if (newLeft > window.innerWidth + 200 || newLeft < -200) {
                    bug.remove();
                }
            } else {
                // Bug normale: decresce la durata di vita
                if (bug.lifeTime > 0) {
                    bug.lifeTime -= 30; 
                    // Rimuovi il bug quando la durata termina
                    if (bug.lifeTime <= 0) {
                        bug.remove(); 
                    }
                }
            }
        });
    }, 30);

    // Reset del timer di inattività
    resetIdleTimer();
}

// Avvia il gioco: inizializza variabili e timer
function startGame() {
    // Reset di tutte le variabili di stato
    score = 0;
    timeLeft = GAME_DURATION;
    consecutiveMisses = 0;
    isPlaying = true;
    isPaused = false;
    isMultiplierActive = false;
    bonusRemaining = 0;
    
    // Aggiorna l'UI
    scoreEl.innerText = score;
    timerEl.innerText = timeLeft;
    timerEl.style.color = '#33ff33';
    
    // Nascondi overlay e pausa menu
    overlay.style.display = 'none';
    pauseMenu.style.display = 'none';
    document.body.classList.remove('paused-game');
    gameArea.innerHTML = '';
    
    // Reset audio: ferma e riavvia da zero
    bgMusic.currentTime = 0;
    bonusAudio.currentTime = 0;
    bonusAudio.pause();
    
    // Avvia la musica di background se abilitata
    if (isMusicActive) bgMusic.play().catch(e => {});
    
    // Pulisci i vecchi timer
    clearInterval(gameInterval);
    clearInterval(spawnInterval);
    clearInterval(updateInterval);
    
    // Avvia i nuovi timer
    startTimers();
    spawnBug();
}

// Attiva/disattiva la pausa
function togglePause() {
    if (!isPlaying) return;
    isPaused = !isPaused;
    
    if (isPaused) {
        // === INIZIO PAUSA ===
        // Ferma tutti i timer
        clearInterval(gameInterval); 
        clearInterval(spawnInterval); 
        clearInterval(updateInterval);
        clearTimeout(idleTimer); 
        
        // Pausa il timer del bonus e calcola il tempo rimanente
        if (isMultiplierActive) {
            clearTimeout(bonusTimer);
            // Sottrai il tempo trascorso da quando il timer è stato avviato
            bonusRemaining -= (Date.now() - bonusStartTime);
        }
        
        // Ferma la musica
        bgMusic.pause(); 
        bonusAudio.pause();
        
        // Mostra il menu di pausa
        pauseMenu.style.display = 'flex';
        document.body.classList.add('paused-game'); 
    } else {
        // === FINE PAUSA (RIPRENDI) ===
        pauseMenu.style.display = 'none';
        document.body.classList.remove('paused-game');
        
        // Riavvia la musica se abilitata
        if (isMusicActive) {
            if (isMultiplierActive) bonusAudio.play();
            else bgMusic.play();
        }
        
        // Riprendi il timer bonus se era attivo
        if (isMultiplierActive && bonusRemaining > 0) {
            resumeBonusTimer();
        } else if (isMultiplierActive && bonusRemaining <= 0) {
            // Il bonus è scaduto durante la pausa
            deactivateBonus();
        }
        
        // Riavvia i timer
        startTimers();
    }
}

/* ===== SEZIONE 7: CONTROLLO DELLA MUSICA ===== */
// Toggle on/off della musica di background
document.getElementById('btn-music').onclick = function() {
    // Cambia lo stato della musica
    isMusicActive = !isMusicActive;
    if (isMusicActive) {
        // Musica attivata
        musicBtn.innerText = "[ MUSICA: ON ]";
        musicBtn.classList.remove('btn-off');
        musicBtn.classList.add('btn-cyan');
        // Riavvia la musica se il gioco è in corso e non in pausa
        if (isPlaying && !isPaused) {
            if (isMultiplierActive) bonusAudio.play().catch(e => {}); 
            else bgMusic.play().catch(e => {});
        }
    } else {
        // Musica disattivata
        musicBtn.innerText = "[ MUSICA: OFF ]";
        musicBtn.classList.remove('btn-cyan');
        musicBtn.classList.add('btn-off');
        // Ferma la musica
        bgMusic.pause(); 
        bonusAudio.pause();
    }
};

/* ===== SEZIONE 8: LOGICA DI SPAWN DEI BUG ===== */
// Crea e spawna un nuovo bug casuale
function spawnBug() {
    // Non spawnare durante la pausa
    if (isPaused) return;

    // Crea l'elemento bug
    const bug = document.createElement('div');
    bug.classList.add('bug');

    // Determina il tipo di bug casualmente
    let rand = Math.random();
    let type = 'normal';
    let symbol = '🐛';
    let points = 10;
    let hp = 1;
    let lifeTime = 3000; 

    // Probabilità di spawn:
    // 30% virus (danno)
    if (rand < 0.30) { 
        type = 'virus'; symbol = '🐛'; points = -20; bug.classList.add('bug-virus');
    } 
    // 36% normale (standard)
    else if (rand < 0.66) { 
        type = 'normal'; symbol = '🐛'; bug.classList.add('bug-normal');
    }
    // 12% worm (mobile, più punti)
    else if (rand < 0.78) { 
        type = 'worm'; symbol = '🐍'; points = 30; bug.classList.add('bug-worm');
    }
    // 10% tank (scudo, 2 hit)
    else if (rand < 0.88) { 
        type = 'tank'; symbol = '🛡️'; points = 40; hp = 2; bug.classList.add('bug-tank');
    }
    // 8% gold (raro, vita breve)
    else if (rand < 0.96) { 
        type = 'gold'; symbol = '🪲'; points = 50; bug.classList.add('bug-gold');
        lifeTime = 1500; 
    }
    // 4% diamond (moltiplicatore 2X)
    else { 
        const existingDiamond = document.querySelector('.bug-multiplier');
        // Evita due moltiplicatori contemporaneamente
        if (isMultiplierActive || existingDiamond) {
            type = 'normal'; symbol = '🐛'; bug.classList.add('bug-normal');
        } else {
            type = 'multiplier'; symbol = '💎'; points = 0; bug.classList.add('bug-multiplier');
            lifeTime = 2000; 
        }
    }

    // Imposta il simbolo del bug
    bug.innerText = symbol;
    bug.isWorm = false;
    bug.lifeTime = lifeTime; 

    // Calcola i limiti dello schermo
    const maxX = window.innerWidth - 80;
    const maxY = window.innerHeight - 80;
    
    // Posizionamento specifico per tipo
    if (type === 'worm') {
        // Worm: entra da un lato e attraversa lo schermo
        bug.isWorm = true;
        let startLeft = random(0, 1) === 0;
        let startX = startLeft ? -150 : window.innerWidth + 150;
        let y = random(100, window.innerHeight - 200);
        
        bug.style.left = startX + 'px';
        bug.style.top = y + 'px';
        // Velocità: positiva da sinistra, negativa da destra
        bug.speedX = startLeft ? 12 : -12; 
    } else {
        // Bug normale: posiziona random con animazione spawn
        bug.classList.add('spawn-anim');
        bug.style.left = random(50, maxX) + 'px';
        bug.style.top = random(50, maxY) + 'px';
    }

    // Gestione del click sul bug
    bug.onmousedown = (e) => { 
        e.stopPropagation();
        // Ignora click se in pausa o bug morente
        if (isPaused || bug.classList.contains('dying')) return;

        // Reset dei miss consecutivi
        consecutiveMisses = 0;
        resetIdleTimer();

        // Gestione tank: 2 hit per distruggere
        if (type === 'tank' && hp > 1) {
            hp--;
            playSFX(paths.shield);
            bug.style.filter = 'none'; bug.innerText = '🕷️';
            showFloatText(e.clientX, e.clientY, "CRACK!", '#ccc');
            return;
        }

        // Gestione moltiplicatore: attiva il bonus
        if (type === 'multiplier') {
            activateBonus();
            bug.remove(); 
            return;
        }

        // Riproduci effetto sonoro appropriato
        if (type === 'virus') playSFX(paths.damage);
        else if (type === 'gold') playSFX(paths.gold);
        else playSFX(paths.laser);

        // Calcola i punti finali (con moltiplicatore se attivo)
        let finalPoints = points;
        if (isMultiplierActive && points > 0) finalPoints *= 2;

        // Aggiorna il punteggio
        let oldScore = score;
        score += finalPoints;
        if (score < 0) score = 0;
        scoreEl.innerText = score;

        // Trigger annunci narrativi ai traguardi
        if (oldScore < 1000 && score >= 1000) playAnnouncer(paths.good);
        if (oldScore < 2000 && score >= 2000) playAnnouncer(paths.job);
        if (oldScore < 3000 && score >= 3000) playAnnouncer(paths.amazing);

        // Mostra il testo fluttuante con i punti
        let text = finalPoints > 0 ? `+${finalPoints}` : finalPoints;
        let color = '#33ff33';
        if (finalPoints < 0) color = 'red';
        if (isMultiplierActive && finalPoints > 0) { text += " (2X)"; color = 'gold'; }
        
        showFloatText(e.clientX, e.clientY, text, color);

        // Animazione di morte del bug
        bug.classList.add('dying');
        bug.isWorm = false;

        // Rimuovi il bug dopo l'animazione
        setTimeout(() => bug.remove(), 400);
        // Spawn un nuovo bug se non è un virus
        if (type !== 'virus') spawnBug(); 
    };

    // Aggiungi il bug al gioco
    gameArea.appendChild(bug);
}

/* ===== SEZIONE 9: GESTIONE DEI CLICK SBAGLIATI ===== */
// Gestisce i click sbagliati (click nel vuoto)
gameArea.onmousedown = (e) => {
    if (!isPlaying || isPaused) return;
    // Ignora se il click è su un bug (non sul vuoto)
    if (e.target !== gameArea) return;

    // Incrementa i miss consecutivi
    consecutiveMisses++;
    // Dopo 2 miss consecutivi: penalità
    if (consecutiveMisses >= 2) {
        playSFX(paths.overheat);
        score = Math.max(0, score - 5);
        scoreEl.innerText = score;
        showFloatText(e.clientX, e.clientY, "OVERHEAT!", 'red');
        consecutiveMisses = 0;
    }
};

// Mostra un testo fluttuante (punti, crack, etc.)
function showFloatText(x, y, text, color) {
    const el = document.createElement('div');
    el.className = 'float-text';
    el.innerText = text;
    el.style.color = color;
    el.style.left = x + 'px';
    el.style.top = y + 'px';
    gameArea.appendChild(el);
    // Rimuovi l'elemento dopo l'animazione
    setTimeout(() => el.remove(), 1000);
}

// Reset del timer di inattività (idle timer)
// Quando il giocatore è inattivo per 5 secondi, viene riprodotto un annuncio
function resetIdleTimer() {
    clearTimeout(idleTimer);
    if (!isPlaying || isPaused) return;
    idleTimer = setTimeout(() => {
        if (isPlaying && !isPaused) {
            // Annuncio narrativo: "sei troppo lento"
            playAnnouncer(paths.slow);
            // Ricomincia il conteggio
            resetIdleTimer();
        }
    }, 5000);
}

/* ===== SEZIONE 10: SISTEMA DEL BONUS (MOLTIPLICATORE 2X) ===== */
// Attiva il moltiplicatore 2X quando raccogli il diamante
function activateBonus() {
    if (isMultiplierActive) return;
    isMultiplierActive = true;
    // Durata del bonus: 15 secondi
    bonusRemaining = 15000;

    // Ferma la musica normale e inizia quella del bonus
    if (isMusicActive) {
        bgMusic.pause();
        bonusAudio.currentTime = 0;
        bonusAudio.play().catch(e => {});
    }

    // Mostra un'animazione di flash per annunciare il bonus
    const flash = document.createElement('div');
    flash.className = 'bonus-announcement';
    flash.innerText = "SYSTEM OVERDRIVE!";
    document.body.appendChild(flash);
    setTimeout(() => flash.remove(), 2500);

    // Crea il badge 2X SCORE nell'UI
    const badge = document.createElement('div');
    badge.id = 'multiplier-badge';
    badge.className = 'multiplier-badge';
    badge.innerText = "2X SCORE";
    
    // Posiziona il badge nella sezione centrale dell'UI
    const uiCenter = document.querySelector('.ui-center');
    if (uiCenter) {
        uiCenter.appendChild(badge);
    } else {
        document.body.appendChild(badge);
    }

    // Avvia il timer del bonus
    resumeBonusTimer();
}

// Funzione helper per avviare/riprendere il timer del bonus
// Tiene conto del tempo rimanente anche dopo la pausa
function resumeBonusTimer() {
    clearTimeout(bonusTimer);
    // Segna l'ora attuale come inizio del segmento timer
    bonusStartTime = Date.now();
    
    // Imposta il timeout con il tempo residuo (non sempre 15000)
    bonusTimer = setTimeout(deactivateBonus, bonusRemaining);
}

// Disattiva il moltiplicatore 2X
function deactivateBonus() {
    isMultiplierActive = false;
    bonusRemaining = 0;
    // Ferma la musica bonus
    bonusAudio.pause();
    
    // Ritorna alla musica di background
    if (isMusicActive && isPlaying && !isPaused) {
        bgMusic.play().catch(e => {});
    }

    // Rimuovi il badge 2X dalla UI
    const badge = document.getElementById('multiplier-badge');
    if (badge) badge.remove();
}

/* ===== SEZIONE 11: CONTROLLI E INTERAZIONI ===== */
// Aggiorna la rotella di caricamento durante il hold to start
function updateWheel(percent) {
    // Calcola quanti segmenti accendere
    const active = Math.floor((percent / 100) * TOTAL_SEGMENTS);
    segments.forEach((seg, i) => {
        if (i < active) seg.classList.add('lit');
        else seg.classList.remove('lit');
    });
}

// Gestisce l'inizio del hold to start
function startHold(e) {
    // Ignora se il gioco è già in corso o se non è un click sinistro
    if ((e.button !== 0 && e.type !== 'touchstart') || isPlaying) return;
    
    // Mostra la rotella
    wheelContainer.style.display = 'block';
    updateWheel(0);
    
    // Inizia il countdown
    const startTime = Date.now();
    holdInterval = setInterval(() => {
        const elapsed = Date.now() - startTime;
        // Calcola la percentuale di completamento
        const percent = Math.min((elapsed / HOLD_DURATION) * 100, 100);
        updateWheel(percent);
        // Se il tempo è scaduto: avvia il gioco
        if (elapsed >= HOLD_DURATION) {
            clearInterval(holdInterval);
            startGame();
            wheelContainer.style.display = 'none';
        }
    }, 50);
}

// Gestisce il rilascio del hold to start
function stopHold() {
    clearInterval(holdInterval);
    wheelContainer.style.display = 'none';
    updateWheel(0);
}

// Associa gli event listener al bottone start
if(btnInit) {
    // Mouse
    btnInit.addEventListener('mousedown', startHold);
    // Touch (mobile)
    btnInit.addEventListener('touchstart', (e) => { e.preventDefault(); startHold(e); });
    // Stop in entrambi i casi
    btnInit.addEventListener('mouseup', stopHold);
    btnInit.addEventListener('mouseleave', stopHold);
    btnInit.addEventListener('touchend', stopHold);
}

// Bottone resume: riprendi dalla pausa
document.getElementById('btn-resume').onclick = togglePause;
// Tasto ESC: attiva/disattiva pausa
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') togglePause(); });
// Bottone quit: ricarica la pagina (termina il gioco)
document.getElementById('btn-quit').onclick = () => location.reload();


/* ===== SEZIONE 12: FINE PARTITA E SALVATAGGIO ===== */
// Termina la partita quando il timer raggiunge 0
function endGame() {
    isPlaying = false;
    // Ferma tutti i timer e interval
    clearInterval(gameInterval); 
    clearInterval(spawnInterval); 
    clearInterval(updateInterval);
    clearTimeout(idleTimer); 
    clearTimeout(bonusTimer);
    
    // Ferma la musica
    bgMusic.pause(); 
    bonusAudio.pause();
    bgMusic.currentTime = 0; 
    bonusAudio.currentTime = 0;
    // Riproduci il suono di fine partita
    playSFX(paths.end);
    
    // Mostra l'overlay con i risultati
    overlay.style.display = 'flex';
    document.body.classList.remove('paused-game');
    wheelContainer.style.display = 'none';
    
    // Aggiorna il messaggio con il punteggio finale
    messageEl.innerHTML = `SCAN COMPLETATA.<br>MINACCE RIMOSSE: <b>${score}</b>`;
    // Salva il punteggio nel database
    saveScore(score);
}

// Salva il punteggio nel database tramite API
function saveScore(s) {
    fetch('api_save_score.php', {
        method: 'POST', 
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ score: s })
    }).then(r => r.json()).then(d => {
        // Se il salvataggio ha successo, mostra il messaggio
        if(d.success) messageEl.innerHTML += `<br><small style="color:#33ff33">SALVATO SU DATABASE</small>`;
    }).catch(e => console.error(e));
}