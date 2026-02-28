<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'PHPlastic Setup') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7c3aed',
                        secondary: '#5b21b6',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen flex flex-col items-center justify-center font-sans antialiased text-gray-200 p-4">

    <!-- Step Progress Bar -->
    <div class="w-full max-w-2xl mb-8">
        <div class="flex items-center justify-between relative">
            <!-- Connection line -->
            <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-700 z-0"></div>
            <div class="absolute top-5 left-0 h-0.5 bg-purple-500 z-0 transition-all duration-500" style="width: <?= (($currentStep ?? 1) - 1) * 33.33 ?>%"></div>

            <?php
            $steps = [
                1 => ['icon' => '🎯', 'label' => 'Project'],
                2 => ['icon' => '🔑', 'label' => 'API Key'],
                3 => ['icon' => '👤', 'label' => 'Account'],
                4 => ['icon' => '🚀', 'label' => 'Build'],
            ];
            foreach ($steps as $num => $step):
                $isActive = ($currentStep ?? 1) === $num;
                $isCompleted = ($currentStep ?? 1) > $num;
            ?>
                <div class="flex flex-col items-center z-10">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold transition-all duration-300 <?= $isCompleted ? 'bg-purple-600 text-white shadow-lg shadow-purple-500/30' : ($isActive ? 'bg-purple-500 text-white ring-4 ring-purple-500/30 shadow-lg' : 'bg-gray-800 text-gray-500 border border-gray-700') ?>">
                        <?= $isCompleted ? '✓' : $step['icon'] ?>
                    </div>
                    <span class="text-xs mt-2 font-medium <?= $isActive || $isCompleted ? 'text-purple-300' : 'text-gray-600' ?>"><?= $step['label'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main Card -->
    <div class="w-full max-w-2xl">
        <div class="bg-gray-900/80 backdrop-blur-xl border border-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <!-- Logo Header -->
            <div class="text-center pt-8 pb-4 px-8">
                <div class="flex items-center justify-center space-x-3 mb-2">
                    <img src="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>assets/img/logo.png" alt="PHPlastic" class="h-12 w-auto rounded-lg" onerror="this.style.display='none'">
                    <span class="font-extrabold text-3xl bg-clip-text text-transparent bg-gradient-to-r from-purple-400 to-pink-400">PHPlastic</span>
                </div>
                <p class="text-gray-500 text-sm">AI-Powered Framework Setup</p>
            </div>

            <!-- Dynamic Content -->
            <div class="px-8 pb-8">
                <?php if (isset($content)) {
                    require_once __DIR__ . '/../' . str_replace('.', '/', $content) . '.php';
                } ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <p class="text-gray-600 text-xs mt-6">&copy; <?= date('Y') ?> PHPlastic Framework. Open Source MIT License.</p>

</body>

</html>