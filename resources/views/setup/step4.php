<?php
$typeLabels = ['website' => '🌐 Showcase Website', 'ecommerce' => '🛒 E-Commerce', 'dashboard' => '📊 Management Dashboard', 'api' => '🔌 REST API', 'custom' => '✏️ Custom'];
$typeLabel = $typeLabels[$projectType ?? 'custom'] ?? 'Custom';
?>

<h2 class="text-2xl font-bold text-white mb-2 text-center">Build Your App</h2>
<p class="text-gray-400 text-sm text-center mb-2">Project type: <span class="text-purple-300 font-semibold"><?= $typeLabel ?></span></p>
<p class="text-gray-500 text-xs text-center mb-6">Review the AI prompt below, then generate your architecture.</p>

<div class="mb-6">
    <label class="block text-gray-300 text-sm font-semibold mb-2">AI Prompt <span class="text-gray-600 font-normal">(editable)</span></label>
    <textarea id="ai-prompt"
        class="w-full h-32 bg-gray-800 border border-gray-700 rounded-xl py-3 px-4 text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
        placeholder="Describe what you want..."><?= \App\Core\View::e($prompt ?? '') ?></textarea>
</div>

<button type="button" id="btn-generate" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-purple-500/25 transition-all duration-300 flex items-center justify-center space-x-2 mb-4">
    <span id="btn-text">🚀 Generate Architecture</span>
    <svg id="btn-spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
</button>

<p class="text-xs font-mono hidden text-center mb-4" id="status-msg">Waiting for response...</p>

<!-- Results -->
<div id="results-area" class="hidden mt-6 border-t border-gray-800 pt-6">
    <h3 class="text-lg font-bold mb-3 text-white">Files Proposed by AI</h3>
    <div id="file-list" class="bg-gray-800/50 rounded-xl p-4 mb-4 border border-gray-700 max-h-48 overflow-y-auto font-mono text-xs shadow-inner">
    </div>

    <div class="flex gap-3">
        <button id="btn-cancel" type="button" class="flex-1 border border-gray-700 text-gray-400 hover:text-white hover:border-gray-500 font-semibold py-2.5 px-4 rounded-xl transition text-center text-sm">Cancel</button>
        <button id="btn-apply" type="button" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-xl shadow transition flex items-center justify-center space-x-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Apply Patch & Launch</span>
        </button>
    </div>
</div>

<!-- Skip Link -->
<div class="text-center mt-4">
    <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>setup/complete" class="text-gray-600 hover:text-gray-400 text-xs underline">Skip — go to Dashboard</a>
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
            if (!promptInput.value.trim()) return alert("Please enter a prompt!");

            btnText.textContent = "Analyzing via AI...";
            btnSpinner.classList.remove('hidden');
            btnGenerate.disabled = true;
            statusMsg.classList.remove('hidden');
            statusMsg.textContent = "Waiting for LLM response...";
            statusMsg.className = "text-xs font-mono text-center mb-4 text-purple-400";
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
                    alert("Error: " + data.error);
                    statusMsg.textContent = "Failed: " + data.error;
                    statusMsg.className = "text-xs font-mono text-center mb-4 text-red-400";
                } else if (data.success) {
                    if ((!data.files || data.files.length === 0) && (!data.sql || data.sql.length === 0)) {
                        alert("AI returned no valid files or SQL queries.");
                    } else {
                        statusMsg.className = "text-xs font-mono text-center mb-4 text-green-400";

                        if (data.files && data.files.length > 0) {
                            data.files.forEach(f => {
                                const div = document.createElement('div');
                                div.className = "mb-1 text-green-400";
                                div.textContent = "+ " + f.file;
                                fileList.appendChild(div);
                            });
                        }
                        if (data.sql && data.sql.length > 0) {
                            data.sql.forEach(q => {
                                const div = document.createElement('div');
                                div.className = "mb-1 text-blue-400";
                                div.textContent = "⚙️ [SQL] " + q.substring(0, 80) + (q.length > 80 ? "..." : "");
                                fileList.appendChild(div);
                            });
                        }

                        resultsArea.classList.remove('hidden');
                        const fc = data.files ? data.files.length : 0;
                        const sc = data.sql ? data.sql.length : 0;
                        statusMsg.textContent = `✓ AI proposed ${fc} files and ${sc} SQL queries. Review and Apply.`;
                    }
                }
            } catch (e) {
                alert("Network error: " + e.message);
                statusMsg.textContent = "Network failed.";
                statusMsg.className = "text-xs font-mono text-center mb-4 text-red-400";
            } finally {
                btnText.textContent = "🚀 Regenerate Architecture";
                btnSpinner.classList.add('hidden');
                btnGenerate.disabled = false;
            }
        });

        btnCancel.addEventListener('click', () => {
            resultsArea.classList.add('hidden');
            statusMsg.classList.add('hidden');
        });

        btnApply.addEventListener('click', async () => {
            const orgText = btnApply.innerHTML;
            btnApply.innerHTML = "Writing...";
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
                    alert("Error: " + data.error);
                } else {
                    let msg = "Patching Result:\n";
                    if (data.success) msg += "SUCCESS!\n\n";
                    else msg += "PARTIAL SUCCESS.\n\n";
                    if (data.applied) msg += "Files written: " + data.applied.length + "\n";
                    if (data.errors && data.errors.length) msg += "Errors:\n" + data.errors.join("\n");
                    alert(msg);

                    // Redirect to dashboard on success
                    window.location.href = baseFolder + 'admin';
                }
            } catch (e) {
                alert("Error: " + e.message);
            } finally {
                btnApply.innerHTML = orgText;
                btnApply.disabled = false;
                btnCancel.disabled = false;
            }
        });
    });
</script>