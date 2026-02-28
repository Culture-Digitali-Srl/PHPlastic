<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Bootstrap Wizard <span class="text-sm font-normal bg-purple-100 text-purple-800 py-1 px-2 rounded-full ml-2"><?= \App\Core\View::e(\App\Core\Config::get('app.version', 'v0.7.0')) ?> Active</span></h1>
        <p class="text-gray-600 mt-1">Initialize your dashboard, app, or e-commerce with a simple descriptive prompt.</p>
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

            <h2 class="text-2xl font-bold text-gray-900 mb-4">What do you want to build today?</h2>

            <p class="text-gray-600 mb-6 max-w-xl">
                Enter a domain description (e.g., "Hotel booking management" or "E-Commerce Cart" or "Showcase for Lawyer") and the AI will prepare the entire DB schema and MVC views for you.
            </p>

            <!-- Quick-Pick Templates -->
            <div class="flex flex-wrap justify-center gap-3 mb-6 w-full">
                <button type="button" onclick="fillTemplate('website')" class="quick-pick px-4 py-2 text-sm font-medium rounded-full border border-purple-200 text-purple-700 bg-purple-50 hover:bg-purple-100 hover:border-purple-400 transition-all duration-200">🌐 Website</button>
                <button type="button" onclick="fillTemplate('ecommerce')" class="quick-pick px-4 py-2 text-sm font-medium rounded-full border border-purple-200 text-purple-700 bg-purple-50 hover:bg-purple-100 hover:border-purple-400 transition-all duration-200">🛒 E-Commerce</button>
                <button type="button" onclick="fillTemplate('dashboard')" class="quick-pick px-4 py-2 text-sm font-medium rounded-full border border-purple-200 text-purple-700 bg-purple-50 hover:bg-purple-100 hover:border-purple-400 transition-all duration-200">📊 Dashboard</button>
                <button type="button" onclick="fillTemplate('api')" class="quick-pick px-4 py-2 text-sm font-medium rounded-full border border-purple-200 text-purple-700 bg-purple-50 hover:bg-purple-100 hover:border-purple-400 transition-all duration-200">🔌 REST API</button>
            </div>

            <div class="w-full relative">
                <textarea
                    id="ai-prompt"
                    class="w-full h-32 p-4 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition shadow-inner text-gray-700 placeholder-gray-400"
                    placeholder="E.g.: I want a showcase to sell t-shirts. Required tables: products (name, price, image). Public views: catalog/index."></textarea>

                <div class="absolute bottom-3 right-3 flex items-center space-x-2 text-gray-400 text-sm">
                    <span>Model: </span>
                    <select disabled class="bg-transparent border-none font-semibold text-purple-600 focus:ring-0 cursor-not-allowed">
                        <option><?= \App\Core\View::e(\App\Core\Config::get('ai_default_model', 'llama3-8b-8192 (Default)')) ?></option>
                    </select>
                </div>
            </div>

            <button id="btn-generate" class="mt-8 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 flex items-center justify-center space-x-2">
                <span id="btn-text">Generate Architecture</span>
                <svg id="btn-spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
            <p class="mt-4 text-xs text-green-600 font-mono hidden" id="status-msg">
                Waiting for response...
            </p>

            <div id="results-area" class="w-full mt-10 text-left border-t pt-8 hidden">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Files Proposed by AI</h3>
                <div id="file-list" class="bg-gray-50 rounded p-4 mb-6 border border-gray-200 max-h-64 overflow-y-auto font-mono text-sm shadow-inner text-left">
                </div>

                <div class="flex justify-end gap-4">
                    <button id="btn-cancel" class="px-6 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100 transition font-medium">Cancel</button>
                    <button id="btn-apply" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow transition flex items-center space-x-2 font-medium">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Apply Patch (Write to Disk)</span>
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

        // Quick-Pick templates
        window.fillTemplate = function(type) {
            const templates = {
                website: 'I need a modern showcase website with a homepage, an about page, a services page, and a contact form. Create a "pages" table (id, title, slug, content, created_at) and a "contacts" table (id, name, email, message, created_at). Build public views for each page and an admin CRUD to manage pages.',
                ecommerce: 'I need a micro e-commerce with product catalog, shopping cart, and checkout. Create tables: "products" (id, name, description, price, image_url, stock, category, created_at), "orders" (id, customer_name, customer_email, total, status, created_at), "order_items" (id, order_id, product_id, quantity, price). Build public catalog views and admin product management.',
                dashboard: 'I need a management dashboard with CRUD for entities. Create tables: "clients" (id, name, email, phone, company, status, created_at), "projects" (id, client_id, name, description, status, deadline, created_at), "tasks" (id, project_id, title, description, priority, status, assigned_to, created_at). Build admin views with data tables and forms.',
                api: 'I need a RESTful API module with JSON endpoints. Create tables: "items" (id, name, description, category, status, created_at, updated_at). Build a full CRUD REST API controller in app/Modules/Api/ with routes for GET /api/items, GET /api/items/{id}, POST /api/items, PUT /api/items/{id}, DELETE /api/items/{id}. Return JSON responses.'
            };
            promptInput.value = templates[type] || '';
            promptInput.focus();
            // Highlight active button
            document.querySelectorAll('.quick-pick').forEach(b => b.classList.remove('bg-purple-200', 'border-purple-500'));
            event.target.classList.add('bg-purple-200', 'border-purple-500');
        };

        btnGenerate.addEventListener('click', async () => {
            if (!promptInput.value.trim()) return alert("Please enter a prompt!");

            btnText.textContent = "Analyzing via Groq...";
            btnSpinner.classList.remove('hidden');
            btnGenerate.disabled = true;
            statusMsg.classList.remove('hidden');
            statusMsg.textContent = "Waiting for LLM response (this takes a few seconds on Groq)...";
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
                    statusMsg.classList.replace('text-green-600', 'text-red-600');
                } else if (data.success) {
                    if ((!data.files || data.files.length === 0) && (!data.sql || data.sql.length === 0)) {
                        alert("AI returned no valid files or SQL queries.");
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
                                div.textContent = "- " + p + " (Forced block by PolicyGate)";
                                fileList.appendChild(div);
                            });
                        }

                        resultsArea.classList.remove('hidden');
                        const fileCount = data.files ? data.files.length : 0;
                        const sqlCount = data.sql ? data.sql.length : 0;
                        statusMsg.textContent = `Generation completed! AI proposed ${fileCount} files and ${sqlCount} SQL queries. Review and Apply.`;
                    }
                } else {
                    alert("Invalid response from server");
                    statusMsg.textContent = "Inconsistent result from server.";
                }
            } catch (e) {
                alert("Network error: " + e.message);
                statusMsg.textContent = "Network Failed.";
            } finally {
                btnText.textContent = "Regenerate Architecture";
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
            btnApply.innerHTML = "Writing in progress...";
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
                    alert("Save error: " + data.error);
                } else {
                    let msg = "Patching Result:\n";
                    if (data.success) {
                        msg += "TOTAL SUCCESS!\n\n";
                    } else {
                        msg += "PARTIAL SUCCESS / ERRORS DETECTED.\n\n";
                    }

                    if (data.applied) msg += "Files written successfully: " + data.applied.length + "\n";
                    if (data.errors && data.errors.length) msg += "Errors: \n" + data.errors.join("\n");

                    alert(msg);
                    if (data.applied && data.applied.length > 0) {
                        window.location.reload();
                    }
                }
            } catch (e) {
                alert("Application error: " + e.message);
            } finally {
                btnApply.innerHTML = originalText;
                btnApply.disabled = false;
                btnCancel.disabled = false;
            }
        });
    });
</script>