<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Global Settings</h1>
    <p class="text-gray-600 mt-1">Configure PHPlastic system keys and parameters.</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden max-w-4xl">

    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-medium text-gray-800">Database Parameters</h3>
        <p class="text-sm text-gray-500 mt-1">Add or modify global keys (e.g., API keys, limits). Secret settings reside in the .env file.</p>
    </div>

    <div class="p-6">
        <?php if (empty($settings)): ?>
            <div class="bg-yellow-50 text-yellow-700 p-4 rounded text-sm mb-4">
                No global settings currently in the database.
            </div>
        <?php else: ?>
            <form action="#" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">

                <?php foreach ($settings as $s): ?>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <?= \App\Core\View::e($s['key']) ?>
                            <span class="text-xs font-normal text-gray-500">(<?= \App\Core\View::e($s['type']) ?>)</span>
                        </label>
                        <input type="text" name="settings[<?= \App\Core\View::e($s['key']) ?>]" value="<?= \App\Core\View::e($s['value']) ?>"
                            class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                <?php endforeach; ?>

                <div class="mt-6">
                    <button type="submit" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded transition">
                        Save Settings
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <div class="mt-8 pt-6 border-t border-gray-100">
            <h4 class="text-md font-medium text-gray-800 mb-3">Add New Key</h4>
            <form action="#" method="POST" class="flex items-center space-x-3">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
                <input type="hidden" name="action" value="add_setting">

                <input type="text" name="key" placeholder="key_name" required class="flex-1 shadow-sm appearance-none border rounded py-2 px-3 text-gray-700 text-sm focus:outline-none">
                <input type="text" name="value" placeholder="Value" required class="flex-1 shadow-sm appearance-none border rounded py-2 px-3 text-gray-700 text-sm focus:outline-none">
                <select name="type" class="shadow-sm border rounded py-2 px-3 text-gray-700 text-sm focus:outline-none bg-white">
                    <option value="string">String</option>
                    <option value="boolean">Boolean</option>
                    <option value="integer">Integer</option>
                </select>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded shadow text-sm font-semibold transition">
                    Add
                </button>
            </form>
        </div>

        <!-- SEZIONE MODELLI AI -->
        <div class="mt-10 pt-6 border-t border-gray-100">
            <h4 class="text-lg font-bold text-indigo-700 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                AI Model Configuration (Groq)
            </h4>
            <p class="text-sm text-gray-600 mb-4">Select the default model you prefer for AI integration within the framework.</p>

            <div class="flex items-center space-x-4 mb-4">
                <button type="button" id="btn-fetch-models" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-800 font-semibold py-2 px-4 rounded transition text-sm flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Sync Models from Groq
                </button>
                <span id="models-status" class="text-sm text-gray-500 hidden">Loading models...</span>
            </div>

            <form action="#" method="POST" id="form-save-model" class="hidden flex items-end space-x-3">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
                <input type="hidden" name="action" value="update_settings">

                <div class="flex-1">
                    <label class="block text-gray-700 text-xs font-bold mb-1">Current Selected Model</label>
                    <select name="ai_default_model" id="ai-model-select" class="shadow-sm border rounded py-2 px-3 text-gray-700 w-full focus:outline-none bg-white">
                        <!-- Popolato via JS -->
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow transition text-sm h-10">
                    Set as Default
                </button>
            </form>
        </div>

        <!-- DANGER ZONE: FACTORY RESET -->
        <div class="mt-12 p-6 bg-red-50 border border-red-200 rounded-lg">
            <h4 class="text-lg font-bold text-red-700 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Danger Zone - Factory Reset
            </h4>
            <p class="text-sm text-red-600 mb-4">This option will delete <strong>ALL</strong> data from the database (users, logs, settings) and restart migrations from scratch to reinstall the framework clean. You will be logged out at the end of the process.</p>

            <form action="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>admin/settings/reset" method="POST" onsubmit="return confirm('WARNING! Are you absolutely sure? We will wipe the entire database and re-initialize it. This operation is NOT reversible.');">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow transition">
                    Execute Factory Reset
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnFetch = document.getElementById('btn-fetch-models');
        const statusTxt = document.getElementById('models-status');
        const formSave = document.getElementById('form-save-model');
        const selectModel = document.getElementById('ai-model-select');
        const baseFolder = "<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>";

        // Proviamo a pre-selezionare il current se esistente fra le options del db
        const currentModelDb = "<?= \App\Core\View::e($settings[array_search('ai_default_model', array_column($settings, 'key'))]['value'] ?? 'llama3-8b-8192') ?>";

        btnFetch.addEventListener('click', async () => {
            btnFetch.disabled = true;
            statusTxt.classList.remove('hidden');
            statusTxt.textContent = "Contacting Groq APIs...";

            try {
                const res = await fetch(baseFolder + 'admin/settings/models');
                const data = await res.json();

                if (data.error) {
                    statusTxt.textContent = data.error;
                    statusTxt.classList.replace('text-gray-500', 'text-red-500');
                } else if (data.data && Array.isArray(data.data)) {
                    // Filtriamo i modelli gemma basici o teniamo tutto
                    selectModel.innerHTML = '';
                    data.data.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.id;
                        opt.textContent = m.id + (m.developer ? ` (${m.developer})` : '');
                        if (m.id === currentModelDb) {
                            opt.selected = true;
                        }
                        selectModel.appendChild(opt);
                    });

                    statusTxt.textContent = `Found ${data.data.length} available models.`;
                    statusTxt.classList.replace('text-gray-500', 'text-green-600');
                    formSave.classList.remove('hidden');
                } else {
                    statusTxt.textContent = "Invalid response format.";
                }
            } catch (e) {
                statusTxt.textContent = "Network error in API fetch.";
                statusTxt.classList.replace('text-gray-500', 'text-red-500');
            } finally {
                btnFetch.disabled = false;
            }
        });
    });
</script>