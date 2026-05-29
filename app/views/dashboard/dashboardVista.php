<?php if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado."); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="w-full px-4 md:px-6 space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white">Bienvenido,
                <?= $_SESSION['nombre'] ?? 'Usuario' ?>
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Aquí tienes el resumen del inventario general hoy.</p>
        </div>
        <div
            class="mt-4 md:mt-0 text-sm font-medium px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow border dark:border-gray-700 dark:text-gray-300">
            <i class="far fa-calendar-alt mr-2"></i> <?= date('d M, Y') ?>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Valorización</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">$
                        <?= number_format($data['metricas']['valor_inventario'] ?? 0, 2) ?>
                    </h3>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-xl">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Stock Total</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        <?= number_format($data['metricas']['total_unidades'] ?? 0) ?> Und.
                    </h3>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Stock Bajo</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1"><?= $data['metricas']['items_bajo_stock'] ?? 0 ?>
                        Items</h3>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Catálogo Global</p>
                    <h3 class="text-2xl font-bold text-purple-600 mt-1"><?= $data['metricas']['total_catalogo'] ?? 0 ?>
                        Ref.</h3>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-xl">
                    <i class="fas fa-tags text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- GRÁFICO DE MOVIMIENTOS -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Flujo de Inventario (Últimos 7 días)</h3>
            <div class="relative w-full h-72">
                <canvas id="movimientosChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Artículos por Agotarse</h3>
            <?php if (!empty($data['alertas'])): ?>
                <div class="space-y-4">
                    <?php foreach ($data['alertas'] as $alerta): ?>
                        <div
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl border dark:border-gray-700">
                            <div class="truncate mr-2">
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">
                                    <?= htmlspecialchars($alerta['nombre_articulo']) ?>
                                </p>
                                <p class="text-xs text-gray-500">Cod: <?= htmlspecialchars($alerta['codigo']) ?></p>
                            </div>
                            <span
                                class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-black rounded-full border border-red-200 dark:border-red-800">
                                <?= $alerta['cantidad_total'] ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?= BASE_URL ?>inventarioVer"
                    class="block text-center mt-6 text-sm font-bold text-blue-600 hover:underline">Ver todo el stock</a>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <i class="fas fa-check-circle text-4xl mb-2 text-green-500"></i>
                    <p class="text-sm font-medium">Stock saludable</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('movimientosChart').getContext('2d');
    const labels = <?= json_encode(array_column($data['grafico'], 'fecha')) ?>;
    const entradas = <?= json_encode(array_column($data['grafico'], 'entradas')) ?>;
    const salidas = <?= json_encode(array_column($data['grafico'], 'salidas')) ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Entradas (Compras)',
                    data: entradas,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Salidas (Despachos)',
                    data: salidas,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#9ca3af', font: { weight: 'bold' } } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(156, 163, 175, 0.1)' }, ticks: { color: '#9ca3af' } },
                x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
            }
        }
    });
</script>