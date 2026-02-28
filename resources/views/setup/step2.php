<h2 class="text-2xl font-bold text-white mb-2 text-center">Configure AI Engine</h2>
<p class="text-gray-400 text-sm text-center mb-8">Enter your Groq API key to enable AI-powered code generation. <a href="https://console.groq.com/keys" target="_blank" class="text-purple-400 hover:text-purple-300 underline">Get a free key →</a></p>

<form method="POST" action="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>setup/save-keys" id="api-form">

    <div class="mb-6">
        <label class="block text-gray-300 text-sm font-semibold mb-2">Groq API Key</label>
        <div class="relative">
            <input type="password" name="groq_api_key" id="groq-key"
                class="w-full bg-gray-800 border border-gray-700 rounded-xl py-3 px-4 pr-28 text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm font-mono"
                placeholder="gsk_xxxxxxxxxxxxxxxxxxxx">
            <button type="button" id="btn-test" onclick="testKey()"
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-purple-600/20 hover:bg-purple-600/40 text-purple-300 text-xs font-semibold py-1.5 px-3 rounded-lg transition">
                Test Key
            </button>
        </div>
        <div id="key-status" class="mt-2 text-sm hidden"></div>
    </div>

    <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 mb-6">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-xs text-gray-400">
                <p class="font-semibold text-gray-300 mb-1">Why do I need this?</p>
                <p>PHPlastic uses AI (via Groq) to auto-generate Controllers, Models, Views, and SQL migrations from natural language prompts. The API key is stored locally in your <code class="text-purple-400">.env</code> file and never shared.</p>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>setup"
            class="flex-1 border border-gray-700 text-gray-400 hover:text-white hover:border-gray-500 font-semibold py-3 px-6 rounded-xl transition text-center">
            ← Back
        </a>
        <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-purple-500/25 transition-all duration-300 flex items-center justify-center space-x-2">
            <span>Continue</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
        </button>
    </div>

    <p class="text-center mt-4">
        <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>setup/step3" class="text-gray-600 hover:text-gray-400 text-xs underline">Skip — I'll add it later</a>
    </p>
</form>

<script>
    const baseFolder = "<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>";

    async function testKey() {
        const key = document.getElementById('groq-key').value.trim();
        const status = document.getElementById('key-status');
        const btn = document.getElementById('btn-test');

        if (!key) {
            status.textContent = 'Please enter a key first.';
            status.className = 'mt-2 text-sm text-yellow-400';
            status.classList.remove('hidden');
            return;
        }

        btn.textContent = 'Testing...';
        btn.disabled = true;
        status.classList.remove('hidden');
        status.textContent = 'Connecting to Groq...';
        status.className = 'mt-2 text-sm text-gray-400';

        try {
            const res = await fetch(baseFolder + 'setup/test-key', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    key
                })
            });
            const data = await res.json();
            if (data.success) {
                status.textContent = '✓ ' + data.message;
                status.className = 'mt-2 text-sm text-green-400 font-semibold';
            } else {
                status.textContent = '✗ ' + data.error;
                status.className = 'mt-2 text-sm text-red-400';
            }
        } catch (e) {
            status.textContent = '✗ Network error: ' + e.message;
            status.className = 'mt-2 text-sm text-red-400';
        } finally {
            btn.textContent = 'Test Key';
            btn.disabled = false;
        }
    }
</script>