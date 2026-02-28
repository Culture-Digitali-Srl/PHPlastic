<h2 class="text-2xl font-bold text-white mb-2 text-center">What do you want to build?</h2>
<p class="text-gray-400 text-sm text-center mb-8">Pick a template to get started quickly, or go custom for full control.</p>

<form method="POST" action="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>setup/save-type" id="type-form">
    <input type="hidden" name="project_type" id="project-type" value="">
    <input type="hidden" name="custom_prompt" id="custom-prompt" value="">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

        <!-- Website -->
        <button type="button" onclick="selectType('website')" class="project-card group relative p-5 rounded-xl border border-gray-700 hover:border-purple-500 bg-gray-800/50 hover:bg-purple-900/20 transition-all duration-300 text-left cursor-pointer">
            <div class="flex items-start space-x-4">
                <span class="text-3xl">🌐</span>
                <div>
                    <h3 class="font-bold text-white group-hover:text-purple-300 transition">Showcase Website</h3>
                    <p class="text-gray-500 text-xs mt-1">Portfolio, landing pages, contact forms</p>
                </div>
            </div>
            <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-600 group-hover:border-purple-500 flex items-center justify-center transition check-circle">
            </div>
        </button>

        <!-- E-Commerce -->
        <button type="button" onclick="selectType('ecommerce')" class="project-card group relative p-5 rounded-xl border border-gray-700 hover:border-purple-500 bg-gray-800/50 hover:bg-purple-900/20 transition-all duration-300 text-left cursor-pointer">
            <div class="flex items-start space-x-4">
                <span class="text-3xl">🛒</span>
                <div>
                    <h3 class="font-bold text-white group-hover:text-purple-300 transition">E-Commerce</h3>
                    <p class="text-gray-500 text-xs mt-1">Product catalog, cart, checkout & orders</p>
                </div>
            </div>
            <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-600 group-hover:border-purple-500 flex items-center justify-center transition check-circle">
            </div>
        </button>

        <!-- Dashboard -->
        <button type="button" onclick="selectType('dashboard')" class="project-card group relative p-5 rounded-xl border border-gray-700 hover:border-purple-500 bg-gray-800/50 hover:bg-purple-900/20 transition-all duration-300 text-left cursor-pointer">
            <div class="flex items-start space-x-4">
                <span class="text-3xl">📊</span>
                <div>
                    <h3 class="font-bold text-white group-hover:text-purple-300 transition">Management Dashboard</h3>
                    <p class="text-gray-500 text-xs mt-1">Clients, projects, tasks & CRUD panels</p>
                </div>
            </div>
            <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-600 group-hover:border-purple-500 flex items-center justify-center transition check-circle">
            </div>
        </button>

        <!-- REST API -->
        <button type="button" onclick="selectType('api')" class="project-card group relative p-5 rounded-xl border border-gray-700 hover:border-purple-500 bg-gray-800/50 hover:bg-purple-900/20 transition-all duration-300 text-left cursor-pointer">
            <div class="flex items-start space-x-4">
                <span class="text-3xl">🔌</span>
                <div>
                    <h3 class="font-bold text-white group-hover:text-purple-300 transition">REST API</h3>
                    <p class="text-gray-500 text-xs mt-1">JSON endpoints, modular architecture</p>
                </div>
            </div>
            <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-600 group-hover:border-purple-500 flex items-center justify-center transition check-circle">
            </div>
        </button>

    </div>

    <!-- Custom -->
    <div class="mb-6">
        <button type="button" onclick="selectType('custom')" class="project-card w-full group relative p-5 rounded-xl border border-gray-700 hover:border-purple-500 bg-gray-800/50 hover:bg-purple-900/20 transition-all duration-300 text-left cursor-pointer">
            <div class="flex items-start space-x-4">
                <span class="text-3xl">✏️</span>
                <div class="flex-1">
                    <h3 class="font-bold text-white group-hover:text-purple-300 transition">Custom Project</h3>
                    <p class="text-gray-500 text-xs mt-1">Describe exactly what you need</p>
                </div>
            </div>
            <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-600 group-hover:border-purple-500 flex items-center justify-center transition check-circle">
            </div>
        </button>
        <textarea id="custom-textarea" class="hidden w-full mt-3 h-24 p-3 bg-gray-800 border border-gray-700 rounded-xl text-gray-300 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="Describe your project in detail..."></textarea>
    </div>

    <button type="submit" id="btn-next" disabled class="w-full bg-purple-600 hover:bg-purple-700 disabled:bg-gray-700 disabled:text-gray-500 disabled:cursor-not-allowed text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-purple-500/25 transition-all duration-300 flex items-center justify-center space-x-2">
        <span>Continue</span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
        </svg>
    </button>
</form>

<script>
    let selectedType = '';

    function selectType(type) {
        selectedType = type;
        document.getElementById('project-type').value = type;
        document.getElementById('btn-next').disabled = false;

        // Visual selection
        document.querySelectorAll('.project-card').forEach(card => {
            card.classList.remove('border-purple-500', 'bg-purple-900/30');
            card.querySelector('.check-circle').innerHTML = '';
        });

        const cards = document.querySelectorAll('.project-card');
        const idx = {
            'website': 0,
            'ecommerce': 1,
            'dashboard': 2,
            'api': 3,
            'custom': 4
        };
        const card = cards[idx[type]];
        card.classList.add('border-purple-500', 'bg-purple-900/30');
        card.querySelector('.check-circle').innerHTML = '<div class="w-3 h-3 rounded-full bg-purple-500"></div>';

        // Show/hide custom textarea
        const ta = document.getElementById('custom-textarea');
        if (type === 'custom') {
            ta.classList.remove('hidden');
            ta.focus();
        } else {
            ta.classList.add('hidden');
        }
    }

    document.getElementById('type-form').addEventListener('submit', (e) => {
        if (selectedType === 'custom') {
            document.getElementById('custom-prompt').value = document.getElementById('custom-textarea').value;
        }
    });
</script>