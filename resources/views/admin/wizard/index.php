<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Bootstrap Wizard <span class="text-sm font-normal bg-purple-100 text-purple-800 py-1 px-2 rounded-full ml-2"><?= \App\Core\View::e(\App\Core\Config::get('app.version', 'v0.5.0')) ?> Active</span></h1>
        <p class="text-gray-600 mt-1">Inizializza il tuo gestionale, app o e-commerce tramite un semplice prompt descrittivo.</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
    <div class="p-8">
        <div class="max-w-3xl mx-auto flex flex-col items-center text-center">

            <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-4">Cosa vuoi costruire oggi?</h2>

            <p class="text-gray-600 mb-8 max-w-xl">
                Inserisci una descrizione del dominio (es. "Gestione prenotazioni albergo" oppure "Carrello E-Commerce" o "Minisito per Avvocato") e l'AI preparerà l'intero schema DB e le viste MVC per te.
            </p>

            <div class="w-full relative">
                <textarea
                    id="ai-prompt"
                    class="w-full h-32 p-4 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition shadow-inner text-gray-700 placeholder-gray-400"
                    placeholder="Es: Voglio una vetrina per vendere t-shirt. Tabelle necessarie: prodotti (nome, prezzo, immagine). Viste pubbliche: catalog/index."></textarea>

                <div class="absolute bottom-3 right-3 flex items-center space-x-2 text-gray-400 text-sm">
                    <span>Modello: </span>
                    <select disabled class="bg-transparent border-none font-semibold text-purple-600 focus:ring-0 cursor-not-allowed">
                        <option><?= \App\Core\View::e(\App\Core\Config::get('ai_default_model', 'llama3-8b-8192 (Default)')) ?></option>
                    </select>
                </div>
            </div>

            <button id="btn-generate" class="mt-8 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 flex items-center justify-center space-x-2">
                <span id="btn-text">Genera Architettura</span>
                <svg id="btn-spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
            <p class="mt-4 text-xs text-green-600 font-mono hidden" id="status-msg">
                In attesa di risposta...
            </p>

            <div id="results-area" class="w-full mt-10 text-left border-t pt-8 hidden">
                <h3 class="text-xl font-bold mb-4 text-gray-800">File Proposti dall'AI</h3>
                <div id="file-list" class="bg-gray-50 rounded p-4 mb-6 border border-gray-200 max-h-64 overflow-y-auto font-mono text-sm shadow-inner text-left">
                </div>

                <div class="flex justify-end gap-4">
                    <button id="btn-cancel" class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100 transition font-medium">Annulla</button>
                    <button id="btn-apply" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow transition flex items-center space-x-2 font-medium">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Applica Patch (Scrivi su Disco)</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnGenerate = document.getElementById('btn-generate');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const promptInput = document.getElementById('ai-prompt');
        const statusMsg = document.getElementById('status-msg');
        const resultsArea = document.getElementById('results-area');
        const fileList = document.getElementById('file-list');
        const btnApply = document.getElementById('btn-apply');
        const btnCancel = document.getElementById('btn-cancel');

        const baseFolder = "<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>";

        btnGenerate.addEventListener('click', async () => {
            if (!promptInput.value.trim()) return alert("Inserisci un prompt!");

            btnText.textContent = "Analisi in corso tramite Groq...";
            btnSpinner.classList.remove('hidden');
            btnGenerate.disabled = true;
            statusMsg.classList.remove('hidden');
            statusMsg.textContent = "Attendere la risposta dell'LLM (pochi secondi su Groq)...";
            resultsArea.classList.add('hidden');
            fileList.innerHTML = '';

            try {
                const res = await fetch(baseFolder + 'admin/wizard/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        prompt: promptInput.value.trim()
                    })
                });

                const data = await res.json();

                if (data.error) {
                    alert("Errore: " + data.error);
                    statusMsg.textContent = "Fallito: " + data.error;
                    statusMsg.classList.replace('text-green-600', 'text-red-600');
                } else if (data.success) {
                    if ((!data.files || data.files.length === 0) && (!data.sql || data.sql.length === 0)) {
                        alert("L'AI non ha restituito file validi o query SQL.");
                    } else {
                        statusMsg.classList.replace('text-red-600', 'text-green-600');

                        if (data.files && data.files.length > 0) {
                            data.files.forEach(f => {
                                const div = document.createElement('div');
                                div.className = "mb-2 text-green-700 bg-green-50 px-2 py-1 rounded border border-green-200";
                                div.textContent = "+ " + f.file;
                                fileList.appendChild(div);
                            });
                        }

                        if (data.sql && data.sql.length > 0) {
                            data.sql.forEach(q => {
                                const div = document.createElement('div');
                                div.className = "mb-2 text-blue-700 bg-blue-50 px-2 py-1 rounded border border-blue-200";
                                // Mostra un abstract della query in frontend
                                div.textContent = "⚙️ [SQL] " + q.substring(0, 100) + (q.length > 100 ? "..." : "");
                                fileList.appendChild(div);
                            });
                        }

                        if (data.rejected_paths && data.rejected_paths.length > 0) {
                            data.rejected_paths.forEach(p => {
                                const div = document.createElement('div');
                                div.className = "mb-2 text-red-700 bg-red-50 px-2 py-1 rounded line-through opacity-75";
                                div.textContent = "- " + p + " (Bloccato forzatamente da PolicyGate)";
                                fileList.appendChild(div);
                            });
                        }

                        resultsArea.classList.remove('hidden');
                        const fileCount = data.files ? data.files.length : 0;
                        const sqlCount = data.sql ? data.sql.length : 0;
                        statusMsg.textContent = `Generazione completata! L'AI propone ${fileCount} file e ${sqlCount} query SQL. Rivedi e Applica.`;
                    }
                } else {
                    alert("Risposta non valida dal server");
                    statusMsg.textContent = "Risultato incoerente dal server.";
                }
            } catch (e) {
                alert("Errore di rete: " + e.message);
                statusMsg.textContent = "Rete Fallita.";
            } finally {
                btnText.textContent = "Rigenera Architettura";
                btnSpinner.classList.add('hidden');
                btnGenerate.disabled = false;
            }
        });

        btnCancel.addEventListener('click', () => {
            resultsArea.classList.add('hidden');
            statusMsg.classList.add('hidden');
        });

        btnApply.addEventListener('click', async () => {
            const originalText = btnApply.innerHTML;
            btnApply.innerHTML = "Scrittura in corso...";
            btnApply.disabled = true;
            btnCancel.disabled = true;

            try {
                const res = await fetch(baseFolder + 'admin/wizard/apply', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await res.json();

                if (data.error) {
                    alert("Errore salvataggio: " + data.error);
                } else {
                    let msg = "Esito Patching:\n";
                    if (data.success) {
                        msg += "SUCCESSO TOTALE!\n\n";
                    } else {
                        msg += "SUCCESSO PARZIALE / ERRORI RILEVATI.\n\n";
                    }

                    if (data.applied) msg += "Files scritti con successo: " + data.applied.length + "\n";
                    if (data.errors && data.errors.length) msg += "Errori: \n" + data.errors.join("\n");

                    alert(msg);
                    if (data.applied && data.applied.length > 0) {
                        window.location.reload();
                    }
                }
            } catch (e) {
                alert("Errore di applicazione: " + e.message);
            } finally {
                btnApply.innerHTML = originalText;
                btnApply.disabled = false;
                btnCancel.disabled = false;
            }
        });
    });
</script>