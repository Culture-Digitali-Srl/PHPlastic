<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Audit Log</h1>
        <p class="text-gray-600 mt-1">Immutable record of system activities.</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date / Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($events as $ev): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= date('d/m/Y H:i:s', strtotime($ev['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= \App\Core\View::e($ev['user_name'] ?? 'System/Guest') ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            <?= \App\Core\View::e($ev['action']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= \App\Core\View::e($ev['entity_type'] ?? '-') ?> #<?= \App\Core\View::e($ev['entity_id'] ?? '') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= \App\Core\View::e($ev['ip'] ?? 'N/A') ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="<?= \App\Core\View::e($ev['meta_json']) ?>">
                        <?= \App\Core\View::e($ev['meta_json'] ?? '-') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($events)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No events found in audit log.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>