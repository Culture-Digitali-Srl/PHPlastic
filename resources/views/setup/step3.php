<h2 class="text-2xl font-bold text-white mb-2 text-center">Create Admin Account</h2>
<p class="text-gray-400 text-sm text-center mb-8">This will be the Super Admin of your application.</p>

<?php if (!empty($_SESSION['setup_error'])): ?>
    <div class="bg-red-900/30 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl mb-6 text-sm">
        <?= \App\Core\View::e($_SESSION['setup_error']) ?>
        <?php unset($_SESSION['setup_error']); ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>setup/create-admin">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\View::e($csrf_token ?? '') ?>">

    <div class="mb-4">
        <label class="block text-gray-300 text-sm font-semibold mb-2">Full Name</label>
        <input type="text" name="name" value="Admin" required
            class="w-full bg-gray-800 border border-gray-700 rounded-xl py-3 px-4 text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
    </div>

    <div class="mb-4">
        <label class="block text-gray-300 text-sm font-semibold mb-2">Email</label>
        <input type="email" name="email" required placeholder="admin@example.com"
            class="w-full bg-gray-800 border border-gray-700 rounded-xl py-3 px-4 text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-300 text-sm font-semibold mb-2">Password</label>
            <input type="password" name="password" required minlength="6"
                class="w-full bg-gray-800 border border-gray-700 rounded-xl py-3 px-4 text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                placeholder="Min. 6 characters">
        </div>
        <div>
            <label class="block text-gray-300 text-sm font-semibold mb-2">Confirm Password</label>
            <input type="password" name="password_confirm" required minlength="6"
                class="w-full bg-gray-800 border border-gray-700 rounded-xl py-3 px-4 text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                placeholder="Repeat password">
        </div>
    </div>

    <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 mb-6">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <div class="text-xs text-gray-400">
                <p class="font-semibold text-gray-300 mb-1">What happens next?</p>
                <p>The database will be automatically initialized, and your admin account will be created. You'll be logged in instantly.</p>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>setup/step2"
            class="flex-1 border border-gray-700 text-gray-400 hover:text-white hover:border-gray-500 font-semibold py-3 px-6 rounded-xl transition text-center">
            ← Back
        </a>
        <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-purple-500/25 transition-all duration-300 flex items-center justify-center space-x-2">
            <span>Create & Continue</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
        </button>
    </div>
</form>