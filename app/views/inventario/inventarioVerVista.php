<?php
// app/views/inventario/inventarioVerVista.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

$esAdmin = (($_SESSION['nivel_acceso'] ?? 0) == 1 || ($_SESSION['nivel_acceso'] ?? 0) == 2);
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>

<style>
    .dataTables_length select,
    .dataTables_filter input {
        background-color: white !important;
        color: #374151 !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        margin: 0 0.5rem;
        outline: none;
    }

    .dataTables_length label,
    .dataTables_filter label {
        color: #4b5563 !important;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    #invTable tbody tr {
        background-color: white !important;
    }

    #invTable tbody tr:hover {
        background-color: #f9fafb !important;
    }

    .dataTables_paginate .paginate_button.current {
        background-color: #0ea5e9 !important;
        color: white !important;
        border-color: #0ea5e9 !important;
    }

    .dark .dataTables_length select,
    .dark .dataTables_filter input {
        background-color: #374151 !important;
        color: #f3f4f6 !important;
        border: 1px solid #4b5563 !important;
    }

    .dark .dataTables_length label,
    .dark .dataTables_filter label,
    .dark .dataTables_info,
    .dark .dataTables_paginate .paginate_button {
        color: #9ca3af !important;
    }

    .dark #invTable tbody tr {
        background-color: #1f2937 !important;
        color: #d1d5db !important;
    }

    .dark #invTable tbody tr:hover {
        background-color: #374151 !important;
    }

    .dark .dataTables_paginate .paginate_button.current {
        background-color: #0284c7 !important;
        color: white !important;
        border-color: #0284c7 !important;
    }

    .dataTables_wrapper>div:first-child,
    .dataTables_wrapper>div:last-of-type {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin: 1.5rem 0;
    }
</style>

<div class="w-full px-4 md:px-6 space-y-6">

    <!-- Tarjetas de Resumen (KPIs) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Total Unidades Físicas</p>
                <h3 class="text-3xl font-black text-gray-800 dark:text-white mt-1">
                    <?= number_format($data['totales']['unidades']) ?>
                </h3>
            </div>
            <div
                class="h-14 w-14 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 text-2xl">
                <i class="fas fa-boxes"></i>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Valor Estimado del Almacén</p>
                <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">$
                    <?= number_format($data['totales']['dinero'], 2) ?>
                </h3>
            </div>
            <div
                class="h-14 w-14 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-2xl">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Artículos con Stock Crítico (≤ 5)</p>
                <h3 class="text-3xl font-black text-red-600 dark:text-red-400 mt-1">
                    <?= number_format($data['totales']['bajo_stock']) ?>
                </h3>
            </div>
            <div
                class="h-14 w-14 rounded-xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center text-red-600 dark:text-red-400 text-2xl">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- Tabla Principal -->
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-warehouse text-sky-500 mr-2"></i> Existencias en Almacén
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Visión global de máquinas, repuestos y consumibles.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                <?php if (!empty($data['inventario'])): ?>
                    <button onclick="exportarExcel()"
                        class="px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-file-excel"></i> <span>Exportar Excel</span>
                    </button>
                <?php endif; ?>
                <?php if ($esAdmin): ?>
                    <a href="<?= BASE_URL ?>compraCrear"
                        class="px-5 py-2.5 bg-sky-600 text-white font-bold rounded-lg shadow-md hover:bg-sky-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-plus"></i> <span>Ingresar Mercancía</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($data['inventario'])): ?>
            <div class="overflow-x-auto">
                <table id="invTable" class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead
                        class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                        <tr>
                            <th class="py-3 px-4">Cod. Referencia</th>
                            <th class="py-3 px-4">Artículo</th>
                            <th class="py-3 px-4 text-center">Tipo</th>
                            <th class="py-3 px-4 text-center">Stock Actual</th>
                            <th class="py-3 px-4 text-right">Valor Unitario</th>
                            <th class="py-3 px-4 text-right">Total Dinero</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($data['inventario'] as $i): ?>
                            <?php
                            $esRepuesto = !empty($i['id_repuesto']);
                            $codigo = $esRepuesto ? $i['codigo_referencia'] : $i['codigo_interno'];
                            $nombre = $esRepuesto ? $i['nombre_repuesto'] : $i['nombre_producto'];
                            $valorUnitario = $esRepuesto ? $i['valor_repuesto'] : $i['valor_producto'];
                            $totalFila = $i['cantidad_total'] * $valorUnitario;
                            ?>
                            <tr class="transition-colors border-b dark:border-gray-700">
                                <td class="py-4 px-4 font-mono font-bold text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    <?= htmlspecialchars($codigo ?: 'S/C') ?>
                                </td>

                                <td class="py-4 px-4">
                                    <span
                                        class="font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($nombre) ?></span>
                                    <?php if ($esRepuesto): ?>
                                        <div class="text-[10px] text-gray-400 mt-1 font-bold uppercase">
                                            <?= htmlspecialchars($i['condicion']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <?php if ($esRepuesto): ?>
                                        <span
                                            class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-full border border-blue-200 dark:border-blue-800">
                                            <i class="fas fa-tools mr-1"></i> Repuesto
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-xs font-bold rounded-full border border-purple-200 dark:border-purple-800">
                                            <i class="fas fa-soap mr-1"></i> Consumible
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <?php if ($i['cantidad_total'] <= 5): ?>
                                        <span
                                            class="text-red-600 dark:text-red-400 font-black text-lg animate-pulse"><?= $i['cantidad_total'] ?></span>
                                        <div class="text-[10px] text-red-500 font-bold uppercase mt-1">Bajo Stock</div>
                                    <?php else: ?>
                                        <span
                                            class="text-emerald-600 dark:text-emerald-400 font-black text-lg"><?= $i['cantidad_total'] ?></span>
                                    <?php endif; ?>
                                </td>

                                <td class="py-4 px-4 text-right font-mono text-gray-500 dark:text-gray-400">
                                    $ <?= number_format($valorUnitario, 2) ?>
                                </td>

                                <td class="py-4 px-4 text-right font-mono font-bold text-gray-900 dark:text-white">
                                    $ <?= number_format($totalFila, 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div
                class="text-center p-10 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                <i class="fas fa-box-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 font-medium">El almacén está vacío. Registra compras para empezar
                    a poblar el inventario.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    const listaInv = <?= json_encode($data['inventario'] ?? []) ?>;

    function exportarExcel() {
        if (listaInv.length === 0) return alert("No hay datos");
        const datosExcel = listaInv.map(i => {
            let esRepuesto = (i.id_repuesto != null);
            let valUnit = esRepuesto ? parseFloat(i.valor_repuesto) : parseFloat(i.valor_producto);
            return {
                "Código": esRepuesto ? i.codigo_referencia : i.codigo_interno,
                "Nombre": esRepuesto ? i.nombre_repuesto : i.nombre_producto,
                "Categoría": esRepuesto ? 'REPUESTO MÁQUINA' : 'CONSUMIBLE',
                "Condición": esRepuesto ? i.condicion.toUpperCase() : 'N/A',
                "Stock": parseInt(i.cantidad_total),
                "Valor Unitario": valUnit,
                "Total Invertido": valUnit * parseInt(i.cantidad_total)
            };
        });
        const ws = XLSX.utils.json_to_sheet(datosExcel);
        const wb = XLSX.utils.book_new();
        ws['!cols'] = [{ wch: 20 }, { wch: 40 }, { wch: 20 }, { wch: 15 }, { wch: 10 }, { wch: 15 }, { wch: 15 }];
        XLSX.utils.book_append_sheet(wb, ws, "Inventario_General");
        XLSX.writeFile(wb, `Inventario_Unificado_${new Date().toISOString().slice(0, 10)}.xlsx`);
    }

    $(document).ready(function () {
        $('#invTable').DataTable({
            responsive: true,
            order: [[3, "desc"]], // Ordenar por stock descendente
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
            dom: '<"flex flex-wrap justify-between items-center mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>'
        });
    });
</script>