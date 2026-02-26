# Changelog

Tutte le modifiche di rilievo apportate a PHPlastic verranno documentate in questo file.
Il formato è basato su [Keep a Changelog](https://keepachangelog.com/it-IT/1.0.0/), e questo progetto aderisce al [Semantic Versioning](https://semver.org/lang/it/).

## [Unreleased]

## [v0.5.0] - 2026-02-26
### Aggiunto
- **Visual Branding:** Sviluppato logo formale "PHPlastic" ed Hero Banner con Nano Banana.
- **Home Page Pubblica:** Creato controller e view separate (fuori Auth) con asset grafici, abilitando la pubblicazione di siti vetrina/e-commerce.
- **Duttilità AI Wizard:** Rimodulato il prompt core di sistema per accettare la generazione di app e componenti non amministrative.
- **Changelog In-App:** Implementato parsing nativo Markdown-to-HTML per visibilità delle *release notes* direttamente nella dashboard backend.
- Centralizzazione della Versione Software nel core config (`Config::get('app.version')`).
- Gestore Fallback globale `Router@setNotFoundHandler` per Errori 404 eleganti.

## [v0.4.0] - 2026-02-26
### Aggiunto
- **Groq AI Driver:** Velocità estrema nell'inferenza dei comandi per il code scaffolding.
- **AI Patcher System:** `PolicyGate` (per prelevare file e bloccare sovrascritture fatali) e `PatchManager` per scrittura diretta I/O.
- AI Cost & Token tracking system aggiornato e scalato al nuovo driver.
- Aggiunto tasto *Applica Modifiche su Disco* in Wizard.
- Aggiunta configurazione dinamica per selezione tra i migliori e più rapidi modelli di LLM.
- **Factory Reset:** Introdotto trigger di drop/rollback/migrate istantaneo del DB in zona Impostazioni.

## [v0.2.0] - 2026-02-25
### Aggiunto
- **RBAC System:** Gestione Ruoli (Admin, Manager, Operator, Viewer) e Permessi (`Gate::allows`).
- **Audit System:** Tracciamento immutabile delle azioni utente nel database tramite `AuditLogger`.
- **Global Settings:** Gestione delle configurazioni runtime salvate nel DB invece che nel `.env`.
- Viste Tailwind per la pagina di amministrazione di Utenti, Audit Log e Impostazioni.
- AuthMiddleware aggiornato per supportare la verifica dei permessi passati come parametri al decoratore.

## [v0.1.0] - 2026-02-25
### Aggiunto
- Struttura base del progetto MVC minimalista (PHP 8.2+).
- `Core\Router` con supporto regex e Middlewares.
- `Core\View` pattern base (`e()` per anti-XSS).
- `Core\Config` loader basico (lettura `.env` e file PHP array).
- `Core\Security` (CSRF Token, Secure session params).
- Configurazione Singleton di connessione tramite PDO per DB SQLite (`app.sqlite`).
- **Modulo Auth:** Login con hash Argon2id e Rate Limiting basilare, disconnessione automatica per inattività.
- Admin Skeleton UI costruita con Tailwind CSS CDN.
- Tooling CLI (`tools/cli.php`) per lancio comandi (migrate, create-admin).
