<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? \App\Core\Config::get('app.name', 'PHPlastic')) ?></title>
    <!-- Tailwind CSS (CDN for simple prototype, can be replaced via NPM if needed) -->
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

<body class="bg-gray-100 text-gray-800 font-sans antialiased">

    <div class="min-h-screen flex flex-col">
        <!-- Main Content (Injects Here) -->
        <main class="flex-grow">
            <?php if (isset($content)) {
                require_once __DIR__ . '/' . str_replace('.', '/', $content) . '.php';
            } ?>
        </main>
    </div>

</body>

</html>