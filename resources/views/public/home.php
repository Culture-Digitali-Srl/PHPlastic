<!-- Hero Section with Nano Banana Banner -->
<div class="relative bg-gradient-to-br from-blue-900 via-indigo-900 to-purple-900 overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div> <!-- Overlay -->

    <!-- Banner Img as Background or side element -->
    <div class="relative max-w-7xl mx-auto px-6 py-24 md:py-32 flex flex-col md:flex-row items-center">

        <div class="w-full md:w-1/2 z-10 text-center md:text-left mb-12 md:mb-0">
            <h1 class="text-5xl md:text-6xl font-extrabold text-white tracking-tight leading-tight mb-6">
                Build everything.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Much faster.</span>
            </h1>
            <p class="text-lg text-indigo-100 mb-8 max-w-lg mx-auto md:mx-0 leading-relaxed text-shadow">
                Not just admin panels. **PHPlastic** is the PHP mini-framework as flexible as plasticine. Create stunning APIs, agile E-Commerce, or showcase minisites in a snap, guided by AI.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center md:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#features" class="bg-yellow-400 text-yellow-900 font-bold text-lg px-8 py-4 rounded-full shadow-lg hover:bg-yellow-300 hover:shadow-xl transition transform hover:-translate-y-1 w-full sm:w-auto text-center">
                    Learn More
                </a>
                <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>login" class="bg-white/10 backdrop-blur-md border border-white/20 text-white font-semibold text-lg px-8 py-4 rounded-full hover:bg-white/20 transition w-full sm:w-auto text-center">
                    Enter AI Wizard
                </a>
            </div>
        </div>

        <div class="w-full md:w-1/2 z-10 flex justify-center md:justify-end">
            <!-- Nano Banana Banner Image -->
            <div class="relative w-full max-w-xl group">
                <div class="absolute -inset-1 bg-gradient-to-r from-yellow-400 to-purple-500 rounded-2xl blur opacity-30 group-hover:opacity-60 transition duration-1000 group-hover:duration-200"></div>
                <img src="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>assets/img/banner.png" alt="Nano Banana Showcase" class="relative rounded-2xl shadow-2xl transform transition hover:scale-[1.02] duration-500 w-full object-cover">
            </div>
        </div>

    </div>

    <!-- Decorative Wave Sub-Hero -->
    <div class="absolute bottom-0 w-full">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full text-slate-50 h-auto">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 40C840 50 960 70 1080 75C1200 80 1320 70 1380 65L1440 60V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="currentColor"></path>
        </svg>
    </div>
</div>

<!-- Features Section -->
<div id="features" class="max-w-7xl mx-auto px-6 py-20">
    <div class="text-center mb-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Pure Versatility.</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">PHPlastic doesn't force you into a box. You choose the shape it will take, and the AI handles the tedious boilerplate.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

        <!-- Feature 1 -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition">
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6 text-blue-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Portfolio & Blogs</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Create responsive public layouts in moments. Let the routing generate static or dynamic pages connected to a lightweight SQLite database in just a few MBs.</p>
        </div>

        <!-- Feature 2 -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition">
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-6 text-purple-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Micro E-Commerce</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Set up the cart, process payments, and showcase your products. The plugin architecture allows vertical scalability without the overhead of heavy CMSs.</p>
        </div>

        <!-- Feature 3 -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition">
            <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-6 text-emerald-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Management Dashboards</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Born for light Enterprise. Immutable Audit Logs, 4-level RBAC, secure logins with hardened sessions, and advanced CSRF tracking.</p>
        </div>

    </div>
</div>

<!-- AI Integration Banner -->
<div class="bg-indigo-50 border-y border-indigo-100 py-16">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-indigo-900 mb-6">The only framework with an Auto-Generative Architectural Wizard</h2>
        <p class="text-indigo-700 max-w-3xl mx-auto mb-8">Stop writing boilerplate. Describe your business idea: <i>"I need a system to manage warehouse shift schedules"</i>, and let the AI Patcher assemble Controllers, Models, and Views instantly.</p>
        <img src="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>assets/img/logo.png" alt="Tiny Elephant" class="w-16 h-16 mx-auto drop-shadow-md">
    </div>
</div>