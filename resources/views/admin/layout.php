<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'Admin | PHPlastic') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6', // blue-500
                        secondary: '#1e40af', // blue-800
                        sidebar: '#1f2937', // gray-800
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-800 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-sidebar text-white p-5 flex flex-col justify-between hidden md:flex">
        <div>
            <div class="font-bold text-2xl mb-8 tracking-wide">PHPlastic</div>

            <nav class="space-y-3">
                <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>admin" class="flex items-center space-x-2 bg-gray-700 py-2 px-3 rounded hover:bg-gray-600 transition">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>admin/users" class="flex items-center space-x-2 py-2 px-3 rounded hover:bg-gray-600 transition text-gray-400">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Utenti</span>
                </a>

                <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>admin/audit" class="flex items-center space-x-2 py-2 px-3 rounded hover:bg-gray-600 transition text-gray-400">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Audit Log</span>
                </a>

                <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>admin/settings" class="flex items-center space-x-2 py-2 px-3 rounded hover:bg-gray-600 transition text-gray-400">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Impostazioni</span>
                </a>

                <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>admin/changelog" class="flex items-center space-x-2 py-2 px-3 rounded hover:bg-gray-600 transition text-gray-400 mt-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Changelog</span>
                </a>

                <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>admin/tokens" class="flex items-center space-x-2 py-2 px-3 rounded border border-purple-500/30 bg-purple-900/20 hover:bg-purple-800/40 transition text-gray-200 mt-4 shadow-sm">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span><span class="text-purple-300 font-semibold tracking-wide">AI Tokens</span></span>
                </a>

                <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>admin/wizard" class="flex items-center space-x-2 py-2 px-3 rounded hover:bg-gray-700 transition text-yellow-500 mt-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    <span>Wizard</span>
                </a>
            </nav>
        </div>

        <div class="text-sm text-gray-400 border-t border-gray-700 pt-4 flex justify-between items-center">
            <span>v0.1-MVP</span>
            <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>logout" class="text-red-400 hover:text-red-300 transition">Logout</a>
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        <!-- Topbar Mobile (Simplified) -->
        <header class="bg-white shadow py-3 px-6 flex justify-between items-center md:hidden">
            <div class="font-bold text-xl text-primary">PHPlastic</div>
            <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>logout" class="text-red-500 hover:underline">Logout</a>
        </header>

        <!-- Dynamic Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            <?php if (isset($content)) {
                require_once __DIR__ . '/../' . str_replace('.', '/', $content) . '.php';
            } ?>
        </main>
    </div>

</body>

</html>