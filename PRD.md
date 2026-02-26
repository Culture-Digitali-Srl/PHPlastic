PROMPT COMPLETO PER AGENTE AI (VIBECODING)
Titolo progetto

PHPlastic By Culture Digitali (nome working) — AI-native PHP starter kit per micro-app e prototipazione rapida, secure-by-default, modulare, e “malleabile” tramite AI (patch controllate), con wizard di bootstrap e dashboard token/costi.

Contesto e problema da risolvere

Ogni volta che sviluppiamo una micro-app/gestionale in PHP “vibe coding”, sprechiamo tempo e token perché ripetiamo sempre le stesse fondamenta:

gestione utenti, login, ruoli/permessi

dashboard e statistiche base

audit log

impostazioni, notifiche

integrazione AI (provider, prompt, costi)

sicurezza (CSRF, sessioni, rate limit, headers)

database di base (SQLite) e predisposizione a MySQL/Postgres

Obiettivo: un progetto open source su GitHub che chiunque possa clonare e usare subito su shared hosting, ma che sia pronto a crescere in modo pulito.

Manifesto (principi non negoziabili)

Boring is beautiful: niente magie, niente complicazioni inutili.

Secure-by-default: sicurezza attiva di default, non opzionale.

SQLite-first, DB-agnostic: SQLite out-of-the-box; migrazione semplice a MySQL/Postgres.

AI-native, ma sotto controllo: l’AI propone modifiche come patch, non “scrive a caso”.

Modulare e malleabile: ogni feature è un modulo attivabile, estendibile, sostituibile.

Deploy facile: PHP 8.2+, Composer, nessun requisito enterprise.

Osservabilità: log, audit, token usage, error handling, metrics minime.

DX & UX: onboarding wizard + admin panel usabile, copy chiaro, empty states.

Target utenti

piccole aziende (10 dipendenti) e consulenti che vogliono micro-gestionali

maker e dev che vogliono prototipi rapidi

team che non vuole “il framework del momento”, ma vuole solidità e velocità

Requisiti funzionali (MVP v0.1–v0.3)
1) Core app (obbligatorio)

Routing semplice (no framework pesante), middleware support.

Template engine semplice (PHP views) + layout base.

Config .env + loader config.

Error handling: dev vs prod, logging su file.

2) Auth + Users (obbligatorio)

Registrazione (opzionale), login, logout, reset password.

Password hashing Argon2id.

Session hardening: cookie HttpOnly, SameSite, Secure se HTTPS.

Email templates (anche mock) + provider interface per invio mail.

3) RBAC (ruoli/permessi) (obbligatorio)

Ruoli: admin, manager, operator, viewer (default).

Permessi per modulo/azione (policy).

UI admin per gestire ruoli e assegnazioni.

4) Admin Panel (obbligatorio)

Dashboard admin con:

utenti (CRUD)

ruoli/permessi

audit log

token dashboard (vedi sotto)

impostazioni globali (env safe: solo alcune chiavi editabili)

UI: Tailwind via CDN (MVP), componenti semplici.

5) Audit log (obbligatorio)

Traccia:

chi ha fatto cosa

su quale risorsa (type/id)

quando

ip/user-agent (se disponibile)

esito (success/failure)
UI con filtri (utente, modulo, data range).

6) AI Provider Abstraction (obbligatorio)

Implementare un layer unico AIClient con driver:

OpenRouter (obbligatorio)

OpenAI (opzionale)

“Local/Custom endpoint” (opzionale)

Funzioni minime:

chat(messages, model, temperature, max_tokens, metadata)

embeddings(texts, model, metadata) (opzionale in MVP, ma predisporre)

timeouts, retry, rate limit

logging completo dell’evento token

7) Token & Cost Dashboard (obbligatorio)

Persistenza eventi token con:

provider, model

input_tokens, output_tokens, total_tokens

costo stimato (se pricing configurato) o unità astratte

feature tag (es. wizard, ai_patch, chat_assistant)

user_id / project_id (se presente)

timestamp, latency_ms, status

UI:

Oggi / 7 / 30 giorni

breakdown per modello, feature, utente

budget mensile e burn rate

alert al superamento soglie (80%, 100%)

8) Bootstrap Wizard (v0.3)

Una pagina “Welcome” che guida l’utente e genera scaffold:

tipo progetto (gestionale/CRM/portale/tracker)

entità principali (nome entità + campi + tipi)

ruoli base

KPI da tracciare

provider AI + modello + budget mensile

Output wizard:

genera un modulo CRUD per almeno 1 entità

genera menu + pagina dashboard collegata

Funzione “killer”: AI patching controllato (v0.4)
Obiettivo

Permettere all’AI di modificare il progetto senza distruggerlo e senza aprire falle.

Regola fondamentale

L’AI produce solo patch (unified diff). Il sistema valida e applica.

Flusso

UI “AI Dev Panel”: descrivi modifica desiderata.

Backend invia contesto minimo + regole + file list ammessi.

AI risponde con:

unified diff

lista file toccati

breve rationale

Policy gate:

blocca file “core security” a meno di unlock manuale

consenti di default solo /app/Modules/** e /resources/views/**

blocca funzioni pericolose (pattern scan)

Applica patch in “staging” + run checks:

lint (php -l)

test minimi

static analysis light (phpstan level basso o equivalente)

Se ok: apply definitivo + audit log + commit git (se git presente)

Requisiti non funzionali (obbligatori)

PHP 8.2+

Composer

SQLite default in /storage/app.sqlite

DB access via PDO + prepared statements (mai SQL concatenata)

CSRF su tutti i form

Rate limit su login e endpoint AI

Security headers (CSP ragionevole, X-Frame-Options, ecc.)

Logging e audit sempre attivi

Struttura modulare chiara

Documentazione “clone → install → run” in 10 minuti

Struttura repository (obbligatoria)
/app
  /Http
    Controllers/
    Middleware/
  /Core
    Router.php
    View.php
    Auth/
    Security/
    Config/
  /Modules
    Users/
    Roles/
    Audit/
    Tokens/
    Settings/
    Wizard/        (v0.3)
    AIPatching/    (v0.4)
  /Services
  /Data
    Migrations/
    Seeders/
/config
/public
/resources
  /views
  /ui (components)
/storage
  logs/
  cache/
  app.sqlite
/tools
  cli.php (scaffold, migrate, seed)
/tests
/docs
Database schema minima (MVP)

Tabelle:

users (id, email, password_hash, name, status, created_at…)

roles (id, name)

permissions (id, key, description)

role_permissions (role_id, permission_id)

user_roles (user_id, role_id)

audit_events (id, user_id, action, entity_type, entity_id, meta_json, ip, ua, created_at)

token_events (id, user_id, provider, model, in_tokens, out_tokens, total_tokens, cost, feature, status, latency_ms, meta_json, created_at)

settings (key, value, type, updated_at)

migrations (id, name, ran_at)

API / Routes minime (MVP)

/login, /logout, /register (se attivo)

/admin

/admin/users

/admin/roles

/admin/audit

/admin/tokens

/admin/settings

/wizard (v0.3)

/admin/ai-dev (v0.4)

Tooling / CLI (obbligatorio)

php tools/cli.php con comandi:

migrate

seed

make:module Name

make:crud EntityName --fields="name:string,age:int"

user:create-admin

Testing & CI (obbligatorio)

PHPUnit base

test minimo su:

login flow

RBAC check

token event creation

GitHub Actions:

composer install

php -l

phpunit

Documentazione (obbligatorio)

README:

cos’è

install

config provider AI

come creare modulo CRUD

sicurezza (cosa è già coperto)

CONTRIBUTING.md

SECURITY.md

LICENSE (MIT consigliata)

Roadmap (da inserire come milestones GitHub)

v0.1 Core + Auth + SQLite + Admin skeleton

v0.2 RBAC + Audit + Settings

v0.3 AI Provider + Token Dashboard + Wizard

v0.4 AI Patching controllato

v1.0 docs perfette + 3 esempi app (CRM, tracker, mini-gestionale)

Definition of Done (criteri di accettazione)

Repo clonato e avviabile in locale in <10 minuti.

Login funzionante, admin panel funzionante.

Ruoli/permessi: un utente viewer NON vede admin.

Audit log registra azioni admin.

Token dashboard mostra eventi generati da chiamate AI mock o reali.

Wizard genera almeno un modulo CRUD funzionante.

CI verde su GitHub Actions.

Nota sulla sicurezza AI (obbligatoria da implementare)

Nessun endpoint AI deve accettare input non autenticato.

Rate limit su chiamate AI.

Sanitizzazione output AI (mai eseguire codice generato senza patch flow).

Logging + audit su ogni azione AI.

MCP SERVER: sì, puoi farlo (e ha senso)

Sì: questo progetto può avere un MCP server per esporre strumenti (“tools”) a client LLM (IDE, desktop agents, ecc.) in modo standard. MCP è un protocollo aperto con specifica e SDK ufficiali.

Come lo integrerei nel progetto

Due opzioni pulite:

MCP server separato (consigliato)
Un piccolo servizio (Node o Python) che parla MCP e chiama l’app PHP via HTTP interno. Pro: più stabile, “always-on”, più facile gestire streaming/async.

MCP server in PHP (fattibile)
Esistono SDK/implementazioni PHP per creare MCP server.
Pro: tutto in PHP. Contro: devi gestire bene il runtime (CLI long-running) e la sicurezza.

Cosa esporre via MCP (tools utili)

create_module(name, fields) → chiama lo scaffold interno

list_token_stats(range) → legge dashboard token

search_audit(query) → interroga audit log

apply_patch(diff) → usa il tuo “AI patching gate” (con policy e check)

run_migrations() → solo admin e solo in ambienti consentiti

Sicurezza MCP (non scherzare)

MCP è potente e quindi pericoloso se esposto male. Segui le best practice di autenticazione/autorizzazione (in molte implementazioni si parla di server come “resource server” con OAuth e indicatori risorsa).
Inoltre, nella storia di MCP ci sono stati casi reali di vulnerabilità su server tool-based: significa che il “gate” che vuoi tu non è un optional, è il cuore.