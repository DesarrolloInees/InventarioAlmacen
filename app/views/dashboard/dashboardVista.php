<?php if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado."); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="w-full px-4 md:px-6 pb-10 space-y-8">

    <!-- ENCABEZADO EJECUTIVO DEL DASHBOARD -->
    <div class="bg-gradient-to-br from-indigo-900 via-blue-800 to-blue-900 rounded-3xl p-6 md:p-8 shadow-xl border border-blue-700 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-extrabold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                Panel de Control & Gestión
            </div>
            <h1 class="text-3xl md:text-4xl font-black tracking-tight">
                Bienvenido, <span class="bg-gradient-to-r from-blue-300 to-emerald-300 bg-clip-text text-transparent"><?= htmlspecialchars($_SESSION['usuario_name'] ?? $_SESSION['nombre'] ?? 'Usuario') ?></span> 👋
            </h1>
            <p class="text-slate-300 text-sm md:text-base font-light">
                Resumen analítico en tiempo real del inventario, movimientos y requerimientos del sistema.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- EXPORTAR PDF -->
            <a href="<?= BASE_URL ?>dashboard?accion=exportarPDF"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition transform hover:scale-105">
                <i class="fas fa-file-pdf text-sm"></i> Exportar Reporte PDF
            </a>

            <!-- FECHA ACTUAL -->
            <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800/80 border border-slate-700 text-slate-200 text-xs font-bold rounded-xl shadow-inner">
                <i class="far fa-calendar-alt text-blue-400"></i>
                <?= date('d M, Y') ?>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- 1. METRICAS CLAVE / METRICS CARDS GRID (4 METRICAS)          -->
    <!-- ============================================================ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- METRICA 1: STOCK TOTAL -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stock Total</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white">
                        <?= number_format($data['metricas']['total_unidades'] ?? 0) ?>
                    </h3>
                    <p class="text-[11px] text-emerald-500 font-semibold flex items-center gap-1">
                        <i class="fas fa-cubes"></i> Unidades registradas
                    </p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>

        <!-- METRICA 2: STOCK BAJO / CRITICO -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 hover:border-red-500 transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stock Crítico</p>
                    <h3 class="text-3xl font-black text-red-600 dark:text-red-400">
                        <?= $data['metricas']['items_bajo_stock'] ?? 0 ?>
                    </h3>
                    <p class="text-[11px] text-red-500 font-semibold flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> Reponer urgente
                    </p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <!-- METRICA 3: SOLICITUDES PENDIENTES -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 hover:border-amber-500 transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Por Aprobar</p>
                    <h3 class="text-3xl font-black text-amber-500">
                        <?= $data['metricas']['solicitudes_pendientes'] ?? 0 ?>
                    </h3>
                    <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold flex items-center gap-1">
                        <i class="fas fa-clock"></i> Pendientes SuperAdmin
                    </p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>

        <!-- METRICA 4: CATALOGO GLOBAL -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 hover:border-purple-500 transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Catálogo Activo</p>
                    <h3 class="text-3xl font-black text-purple-600 dark:text-purple-400">
                        <?= $data['metricas']['total_catalogo'] ?? 0 ?>
                    </h3>
                    <p class="text-[11px] text-purple-500 font-semibold flex items-center gap-1">
                        <i class="fas fa-tags"></i> Referencias de productos
                    </p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================================ -->
    <!-- 2. GRAFICO DE MOVIMIENTOS Y PANEL DE STOCK CRITICO           -->
    <!-- ============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- GRAFICO DE MOVIMIENTOS (2/3 ANCHO) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-500"></i> Flujo de Inventario
                    </h3>
                    <p class="text-xs text-slate-400">Entradas vs Salidas registradas en los últimos 7 días</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                    Última semana
                </span>
            </div>

            <div class="relative w-full h-[420px] md:h-[460px] flex-1">
                <canvas id="movimientosChart"></canvas>
            </div>
        </div>

        <!-- PANEL DE ALERTAS DE STOCK CRITICO (1/3 ANCHO) -->
        <div class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i> Stock Crítico
                    </h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                        Top Alertas
                    </span>
                </div>

                <?php if (!empty($data['alertas'])): ?>
                    <div class="space-y-4">
                        <?php foreach ($data['alertas'] as $alerta): 
                            $disponible = (int)$alerta['cantidad_total'];
                            $minimo = (int)($alerta['stock_minimo'] ?? 5);
                            $porcentaje = $minimo > 0 ? min(100, round(($disponible / $minimo) * 100)) : 20;
                        ?>
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate max-w-[180px]">
                                        <?= htmlspecialchars($alerta['nombre_articulo']) ?>
                                    </span>
                                    <span class="px-2.5 py-0.5 bg-red-500 text-white text-xs font-black rounded-full">
                                        <?= $disponible ?> und
                                    </span>
                                </div>

                                <!-- BARRA DE PROGRESO DE CRITICIDAD -->
                                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                    <div class="bg-red-500 h-2 rounded-full transition-all duration-500" style="width: <?= max(10, $porcentaje) ?>%"></div>
                                </div>
                                <div class="flex justify-between text-[10px] text-slate-400 font-medium">
                                    <span>Categoría: <?= htmlspecialchars($alerta['codigo']) ?></span>
                                    <span>Stock Mínimo: <?= $minimo ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-12 text-center text-slate-400">
                        <i class="fas fa-check-circle text-5xl text-emerald-400 mb-3"></i>
                        <p class="text-base font-bold text-slate-700 dark:text-slate-300">¡Inventario Saludable!</p>
                        <p class="text-xs text-slate-400 mt-1">No hay ítems por debajo del stock mínimo.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="pt-6 border-t border-slate-100 dark:border-slate-700 mt-4">
                <a href="<?= BASE_URL ?>inventario"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-bold text-xs transition">
                    <i class="fas fa-warehouse text-blue-500"></i> Ver Inventario Completo
                </a>
            </div>
        </div>

    </div>

    <!-- ============================================================ -->
    <!-- 3. FEED DE ULTIMAS SOLICITUDES REGISTRADAS                   -->
    <!-- ============================================================ -->
    <div class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-emerald-500"></i> Últimas Solicitudes Registradas
                </h3>
                <p class="text-xs text-slate-400">Monitoreo reciente de requerimientos de servicio y repuestos</p>
            </div>
            <a href="<?= BASE_URL ?>solicitudes"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                <span>Ver todas las solicitudes</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <?php if (!empty($data['solicitudes_recientes'])): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-700 text-slate-400 text-xs font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Asunto</th>
                            <th class="py-3 px-4">Solicitante</th>
                            <th class="py-3 px-4 text-center">Prioridad</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4 text-right">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <?php foreach ($data['solicitudes_recientes'] as $sol): 
                            $prio = strtolower($sol['prioridad'] ?? 'media');
                            $est = strtolower($sol['estado'] ?? 'pendiente');

                            $prioBadge = match($prio) {
                                'urgente' => 'text-red-600 dark:text-red-400 font-extrabold',
                                'alta'    => 'text-orange-600 dark:text-orange-400 font-bold',
                                'media'   => 'text-amber-600 dark:text-amber-400 font-medium',
                                default   => 'text-slate-500',
                            };

                            $estBadge = match($est) {
                                'aprobada', 'atendida' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'rechazada' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                            };
                        ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                                <td class="py-3.5 px-4 font-bold text-blue-600 dark:text-blue-400">
                                    <a href="<?= BASE_URL ?>solicitudes&id=<?= $sol['solicitud_id'] ?>">
                                        #<?= $sol['solicitud_id'] ?>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-800 dark:text-slate-200">
                                    <?= htmlspecialchars($sol['asunto']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                    <?= htmlspecialchars($sol['nombre_solicitante']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-center <?= $prioBadge ?> uppercase text-xs">
                                    <?= ucfirst($prio) ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase <?= $estBadge ?>">
                                        <?= ucfirst($est) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right text-xs text-slate-400">
                                    <?= date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-6 text-slate-400 text-sm">
                No hay solicitudes recientes registradas.
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- ============================================================ -->
<!-- CONFIGURACION Y RENDERIZADO DE CHART.JS EN DASHBOARD         -->
<!-- ============================================================ -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initMovimientosChart();
    });

    // También ejecutamos por si DOMContentLoaded ya ocurrió
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(initMovimientosChart, 100);
    }

    function initMovimientosChart() {
        const canvas = document.getElementById('movimientosChart');
        if (!canvas) return;

        if (typeof Chart === 'undefined') {
            console.warn('Chart.js no está cargado aún. Reintentando en 200ms...');
            setTimeout(initMovimientosChart, 200);
            return;
        }

        // Evitar duplicar la gráfica en reconexiones o llamados múltiples
        if (window.myMovimientosChartInstance) {
            window.myMovimientosChartInstance.destroy();
        }

        const ctx = canvas.getContext('2d');
        const rawData = <?= json_encode($data['grafico'] ?? []) ?>;

        const labels = rawData.map(item => {
            if (!item.fecha) return '';
            const parts = item.fecha.split('-');
            if (parts.length === 3) {
                return parts[2] + '/' + parts[1]; // Formato dd/mm
            }
            return item.fecha;
        });

        const entradas = rawData.map(item => parseInt(item.entradas) || 0);
        const salidas = rawData.map(item => parseInt(item.salidas) || 0);

        // Gradientes elegantes para el área bajo las curvas
        const gradientEntradas = ctx.createLinearGradient(0, 0, 0, 300);
        gradientEntradas.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        gradientEntradas.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        const gradientSalidas = ctx.createLinearGradient(0, 0, 0, 300);
        gradientSalidas.addColorStop(0, 'rgba(239, 68, 68, 0.4)');
        gradientSalidas.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        window.myMovimientosChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length > 0 ? labels : ['Sin datos'],
                datasets: [
                    {
                        label: 'Entradas (Compras/Repuestos)',
                        data: entradas.length > 0 ? entradas : [0],
                        borderColor: '#10b981',
                        backgroundColor: gradientEntradas,
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Salidas (Despachos)',
                        data: salidas.length > 0 ? salidas : [0],
                        borderColor: '#ef4444',
                        backgroundColor: gradientSalidas,
                        borderWidth: 3,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#ffffff',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#9ca3af',
                            font: { weight: 'bold', size: 12 },
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { weight: 'bold' },
                        padding: 12,
                        cornerRadius: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(156, 163, 175, 0.1)' },
                        ticks: { color: '#9ca3af', font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af', font: { size: 11 } }
                    }
                }
            }
        });
    }
</script>
