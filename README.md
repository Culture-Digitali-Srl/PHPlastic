<p align="center">
  <img src="public/assets/img/logo.png" width="150" alt="PHPlastic Logo">
</p>

# PHPlastic
> The **Modular Engine** (v0.6) – Build your Web App in Seconds with AI

PHPlastic è un framework PHP/SQLite minimalista ma potentissimo, in grado di **scrivere sé stesso**. Dotato di un *AI Bootstrap Wizard* integrato alimentato da LLM (Groq / OpenRouter), PHPlastic ti permette di tradurre un prompt testuale ("Voglio un E-commerce", "Crea una To-Do list") direttamente in:
1. File e viste interattive (`HTML/Tailwind`).
2. Tabella del Database **SQLite** (`DDL Auto-migrations`).
3. Moduli REST API Isolati (`Dynamic Routing`).

Senza complicate configurazioni o ORM pesanti, hai il pieno controllo MVC nativo.

## 🚀 Installazione

1. Clona questa repository.
2. Assicurati di avere **PHP 8.2+** e avere l'estensione **PDO SQLite** attivata nel tuo `php.ini`.
3. Avvia il server di sviluppo integrato di PHP puntando alla cartella `public`:
   ```bash
   php -S localhost:8000 -t public
   ```
4. Apri `http://localhost:8000` nel browser.

## 🔑 Primo Accesso (Super Admin)

Al primo avvio, naviga verso `/login`:
- **Email:** `admin@phplastic.local`
- **Password:** `password`

## 🧠 Come usare l'AI Wizard

Una volta loggato, visita il pannello di controllo e clicca su **"Bootstrap Wizard"**. Inserisci un prompt dettagliato di ciò che vuoi costruire. L'AI genererà un'anteprima dei file e delle query SQL.
1. Seleziona il modello AI dalle impostazioni (`Impostazioni > Sincronizza modelli`).
2. Per abilitare l'AI, inserisci la tua API Key di **Groq** nel file `.env` copiando il `.env.example` (se non presente, crealo definendo `GROQ_API_KEY=tua_chiave`).
3. Dalla pagina del Wizard, scrivi (esempio): *"Crea un modulo API per una gestione di Libri. Ho bisogno della tabella sqlite 'books' e di un REST Controller in app/Modules/Books/BooksController.php"*.
4. Clicca su **Applica Patch**, e il framework inietterà in tempo reale le rotte, i controller e genererà il database SQLite per te!

## 🧩 Moduli Dinamici (v0.6)

A partire dalla versione 0.6, PHPlastic supporta l'auto-discovery delle rotte e i Moduli (o Plugin). Metti i tuoi controller all'interno di `app/Modules/{MioModulo}/` e affiancaci un file `routes.php`. Il framework li caricherà in automatico all'avvio senza dover toccare il file di routing centrale!

*Buon Coding (Assistito)!*
