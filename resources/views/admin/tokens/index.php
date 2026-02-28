<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Token & Cost Dashboard</h1>
        <p class="text-gray-600 mt-1">Monitor AI request consumption and estimated costs.</p>
    </div>
    <a href="<?= \App\Core\View::e(\App\Core\Config::get('app.base_folder')) ?>admin/ai-demo"
        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded shadow transition font-semibold">
        + Generate Test Token
    </a>
</div>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded shadow-sm">
        <p><?= \App\Core\View::e($_SESSION['flash_message']) ?></p>
        <?php unset($_SESSION['flash_message']); ?>
    </div>
<?php endif; ?>

<!-- Metriche Top -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-purple-500">
        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-2">Total AI Requests</h3>
        <p class="text-3xl font-bold text-gray-800"><?= number_format($stats['total_requests'] ?? 0) ?></p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500">
        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-2">In Tokens</h3>
        <p class="text-3xl font-bold text-gray-800"><?= number_format($stats['total_in'] ?? 0) ?></p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-2">Out Tokens</h3>
        <p class="text-3xl font-bold text-gray-800"><?= number_format($stats['total_out'] ?? 0) ?></p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-red-500">
        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-2">Estimated Spend ($)</h3>
        <p class="text-3xl font-bold text-gray-800">$<?= number_format($stats['total_cost'] ?? 0, 4) ?></p>
    </div>
</div>

<!-- Tabella Eventi -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider / Model</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Input / Output</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Latency</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Feature</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($events as $ev): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= date('d/m/Y H:i:s', strtotime($ev['created_at'])) ?>
                        <?php if ($ev['user_name']): ?>
                            <div class="text-xs text-indigo-400 mt-1">User: <?= \App\Core\View::e($ev['user_name']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-semibold text-gray-800"><?= \App\Core\View::e($ev['provider']) ?></div>
                        <div class="text-xs text-gray-500"><?= \App\Core\View::e($ev['model']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-blue-600 font-medium"><?= number_format($ev['in_tokens']) ?></span> /
                        <span class="text-green-600 font-medium"><?= number_format($ev['out_tokens']) ?></span>
                        <div class="text-xs text-gray-500 mt-1">Tot: <?= number_format($ev['total_tokens']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                        <?= number_format($ev['latency_ms']) ?> ms
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= \App\Core\View::e($ev['feature'] ?? '-') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($ev['status'] === 'success'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Success</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Error</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($events)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center space-y-3">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                            <span class="text-lg">No AI events recorded in database.</span>
                            <span class="text-sm">Use the button above to simulate an event.</span>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>