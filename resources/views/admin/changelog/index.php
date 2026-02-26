<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Changelog Cronologia Rilasci</h1>
    <p class="text-gray-600 mt-1">Tutte le novità introdotte nelle versioni di PHPlastic.</p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-8">

        <div class="prose prose-indigo max-w-none">
            <!-- Il contenuto HTML generato dal parser Markdown custom verrà stampato qui -->
            <?= $changelogHtml ?>
        </div>

    </div>
</div>

<style>
    /* Qualche fix rapido se la regex del parser crea ul annidati in maniera strana */
    .changelog-content ul {
        list-style-type: none;
        padding-left: 0;
    }

    .changelog-content li span {
        display: inline-block;
        max-width: 95%;
    }
</style>