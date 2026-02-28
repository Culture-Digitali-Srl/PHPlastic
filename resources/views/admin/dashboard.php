<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Welcome, <?= \App\Core\View::e($userName ?? 'Admin') ?>!</h1>
    <p class="text-gray-600 mt-2">This is the main dashboard of your "secure-by-default" app.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <!-- Widget 1 -->
    <div class="bg-white rounded-lg shadow p-5 border border-gray-100">
        <div class="flex items-center justify-between hover:scale-105 transition-transform">
            <div>
                <p class="text-sm font-medium text-gray-500 truncate">Active Users</p>
                <p class="text-3xl font-semibold text-gray-800 mt-1">1</p>
            </div>
            <div class="p-3 bg-blue-100 rounded-full text-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V10a2 2 0 00-2-2h-3l-4-4-4 4H4a2 2 0 00-2 2v10h5m4 0v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Widget 2 -->
    <div class="bg-white rounded-lg shadow p-5 border border-gray-100">
        <div class="flex items-center justify-between hover:scale-105 transition-transform">
            <div>
                <p class="text-sm font-medium text-gray-500 truncate">Audit Events</p>
                <p class="text-3xl font-semibold text-gray-800 mt-1">0</p>
            </div>
            <div class="p-3 bg-green-100 rounded-full text-green-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Widget 3 -->
    <div class="bg-white rounded-lg shadow p-5 border border-purple-500/20 bg-gradient-to-br from-white to-purple-50">
        <div class="flex items-center justify-between hover:scale-105 transition-transform">
            <div>
                <p class="text-sm font-medium text-gray-500 truncate">AI Tokens Used</p>
                <p class="text-3xl font-semibold text-purple-700 mt-1">0</p>
            </div>
            <div class="p-3 bg-purple-100 rounded-full text-purple-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>
    </div>

</div>

<!-- Recent Activities -->
<div class="bg-white rounded-lg shadow border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-medium text-gray-800">Recent Activities</h3>
    </div>
    <div class="p-6">
        <p class="text-gray-500 text-sm italic">No recent events. The audit log system is functional (v0.6).</p>
    </div>
</div>