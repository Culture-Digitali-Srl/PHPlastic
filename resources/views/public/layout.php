<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'PHPlastic Framework') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6', // blue-500
                        secondary: '#1e40af', // blue-800
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 font-sans antialiased text-gray-800 flex flex-col min-h-screen">

    <!-- Public Header -->
    <header class="bg-white shadow-sm border-b border-gray-100 py-4 px-6 md:px-12 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center space-x-3">
            <img src="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>assets/img/logo.png" alt="PHPlastic Logo" class="h-10 w-auto rounded-lg shadow-sm border border-gray-100">
            <span class="font-extrabold text-2xl bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">PHPlastic</span>
            <span class="ml-2 px-2.py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full hidden sm:inline-block"> <?= \App\Core\View::e($version ?? '') ?></span>
        </div>
        <nav class="hidden md:flex space-x-6 text-sm font-medium text-gray-600">
            <a href="#" class="hover:text-blue-600 transition">Documentation</a>
            <a href="#" class="hover:text-blue-600 transition">Showcase</a>
            <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>login" class="bg-blue-600 text-white px-5 py-2 rounded-full hover:bg-blue-700 hover:shadow-md transition">Admin (Login)</a>
        </nav>
        <div class="md:hidden">
            <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>login" class="text-blue-600 font-bold">Login</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        <?php if (isset($content)) {
            require_once __DIR__ . '/../' . str_replace('.', '/', $content) . '.php';
        } ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800 mt-16">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <img src="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>assets/img/logo.png" alt="Logo" class="h-6 opacity-80 grayscale hover:grayscale-0 transition">
                    <span class="font-bold text-xl text-white">PHPlastic</span>
                </div>
                <p class="text-sm">The versatile, moldable, and indestructible micro-framework. Built with native PHP and powered by Artificial Intelligence.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Resources</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition">GitHub Repo</a></li>
                    <li><a href="#" class="hover:text-white transition">Changelog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Common Usage</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition">Showcase Sites</a></li>
                    <li><a href="#" class="hover:text-white transition">Micro E-Commerce</a></li>
                    <li><a href="#" class="hover:text-white transition">Dashboards & ERPs</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-6xl mx-auto px-6 mt-12 pt-8 border-t border-gray-800 text-center text-sm flex flex-col md:flex-row justify-between items-center">
            <p>&copy; <?= date('Y') ?> PHPlastic Framework. Open Source MIT License.</p>
            <p class="mt-2 md:mt-0 text-xs">Build your future, one patch at a time. 🐘🍌</p>
        </div>
    </footer>

</body>

</html>