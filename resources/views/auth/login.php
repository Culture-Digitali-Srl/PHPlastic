<div class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login - <?= \App\Core\View::e(\App\Core\Config::get('app.name', 'PHPlastic')) ?></h2>

        <?php if (!empty($error)): ?>
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?= \App\Core\View::e($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>login">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\View::e($csrf_token) ?>">

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" id="email" name="email" required autofocus
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" id="password" name="password" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition-colors duration-200">
                    Sign In
                </button>
            </div>
        </form>
        <p class="text-center text-gray-500 text-xs mt-6">
            &copy; <?= date('Y') ?> Culture Digitali. Secure-by-default.
        </p>
    </div>
</div>