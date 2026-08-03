<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>

<style>
    /* Usamos los mismos estilos del DataTables de la vista anterior */
    .dataTables_length select, .dataTables_filter input { background-color: white !important; color: #374151 !important; border: 1px solid #d1d5db !important; border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin: 0 0.5rem; outline: none; }
    .dataTables_length label, .dataTables_filter label { color: #4b5563 !important; font-weight: 500; display: flex; align-items: center; }
    #historialTable tbody tr { background-color: white !important; }
    #historialTable tbody tr:hover { background-color: #f9fafb !important; }
    .dataTables_paginate .paginate_button.current { background-color: #3b82f6 !important; color: white !important; border-color: #3b82f6 !important; }
    
    .dark .dataTables_length select, .dark .dataTables_filter input { background-color: #374151 !important; color: #f3f4f6 !important; border: 1px solid #4b5563 !important; }
    .dark .dataTables_length label, .dark .dataTables_filter label, .dark .dataTables_info, .dark .dataTables_paginate .paginate_button { color: #9ca3af !important; }
    .dark #historialTable tbody tr { background-color: #1f2937 !important; color: #d1d5db !important; }
    .dark #historialTable tbody tr:hover { background-color: #374151 !important; }
    .dark .dataTables_paginate .paginate_button.current { background-color: #2563eb !important; color: white !important; border-color: #2563eb !important; }
    
    .dataTables_wrapper > div:first-child, .dataTables_wrapper > div:last-of-type { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin: 1.5rem 0; }
</style>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-history text-blue-500 mr-2"></i> Historial de Movimientos
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Bitácora de traslados, ingresos y salidas de máquinas.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                <?php if (!empty($data['movimientos'])): ?>
                    <button onclick="exportarExcel()" class="px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-file-excel"></i> <span>Exportar Excel</span>
                    </button>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>maquinaInventarioVer" class="px-5 py-2.5 bg-gray-500 text-white font-bold rounded-lg shadow-md hover:bg-gray-600 transition-transform transform hover:scale-105 flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i> <span>Volver a Máquinas</span>
                </a>
            </div>
        </div>

        <?php if (!empty($data['movimientos'])): ?>
            <div class="overflow-x-auto">
                <table id="historialTable" class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                        <tr>
                            <th class="py-3 px-4">Fecha</th>
                            <th class="py-3 px-4">Máquina</th>
                            <th class="py-3 px-4 text-center">Movimiento</th>
                            <th class="py-3 px-4">Trayecto (Origen <i class="fas fa-arrow-right text-[10px]"></i> Destino)</th>
                            <th class="py-3 px-4">Usuario</th>
                            <th class="py-3 px-4">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($data['movimientos'] as $m): ?>
                            <tr class="transition-colors border-b dark:border-gray-700">
                                
                                <td class="py-4 px-4 whitespace-nowrap text-sm">
                                    <?= date('d/m/Y H:i', strtotime($m['fecha_movimiento'])) ?>
                                </td>

                                <td class="py-4 px-4">
                                    <span class="font-bold text-gray-900 dark:text-white block"><?= htmlspecialchars($m['numero_serie']) ?></span>
                                    <span class="text-xs text-gray-500"><?= htmlspecialchars($m['nombreTipoMaquina']) ?></span>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <?php
                                    $estiloTipo = match ($m['tipo_movimiento']) {
                                        'INGRESO' => 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:border-green-800',
                                        'TRASLADO' => 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:border-blue-800',
                                        'SALIDA_REMISION' => 'bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-900/30 dark:border-purple-800',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200'
                                    };
                                    
                                    $iconoTipo = match ($m['tipo_movimiento']) {
                                        'INGRESO' => '<i class="fas fa-sign-in-alt mr-1"></i>',
                                        'TRASLADO' => '<i class="fas fa-exchange-alt mr-1"></i>',
                                        'SALIDA_REMISION' => '<i class="fas fa-sign-out-alt mr-1"></i>',
                                        default => ''
                                    };
                                    ?>
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full border uppercase tracking-wider <?= $estiloTipo ?>">
                                        <?= $iconoTipo . htmlspecialchars($m['tipo_movimiento']) ?>
                                    </span>
                                </td>

                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-2 text-sm">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">
                                            <?= $m['bodega_origen'] ? htmlspecialchars($m['bodega_origen']) : '<span class="text-gray-400 italic">Externa</span>' ?>
                                        </span>
                                        <i class="fas fa-long-arrow-alt-right text-gray-400"></i>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">
                                            <?= $m['bodega_destino'] ? htmlspecialchars($m['bodega_destino']) : '<span class="text-gray-400 italic">Externa</span>' ?>
                                        </span>
                                    </div>
                                </td>

                                <td class="py-4 px-4 text-sm">
                                    <i class="fas fa-user-circle text-gray-400 mr-1"></i> <?= htmlspecialchars($m['usuario_registra']) ?>
                                </td>

                                <td class="py-4 px-4 text-sm text-gray-500 dark:text-gray-400 italic max-w-xs truncate" title="<?= htmlspecialchars($m['observacion']) ?>">
                                    <?= !empty($m['observacion']) ? htmlspecialchars($m['observacion']) : 'Sin observaciones' ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center p-10 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                <i class="fas fa-history text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay movimientos registrados todavía.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    const listaMovimientos = <?= json_encode($data['movimientos'] ?? []) ?>;

    function exportarExcel() {
        if (listaMovimientos.length === 0) return alert("No hay datos para exportar");
        const datosExcel = listaMovimientos.map(m => ({
            "Fecha": m.fecha_movimiento,
            "Serial Máquina": m.numero_serie,
            "Tipo de Máquina": m.nombreTipoMaquina,
            "Tipo Movimiento": m.tipo_movimiento,
            "Bodega Origen": m.bodega_origen || 'Externa',
            "Bodega Destino": m.bodega_destino || 'Externa',
            "Registrado Por": m.usuario_registra,
            "Observación": m.observacion || 'N/A'
        }));
        const ws = XLSX.utils.json_to_sheet(datosExcel);
        const wb = XLSX.utils.book_new();
        ws['!cols'] = [{wch:18}, {wch:20}, {wch:25}, {wch:15}, {wch:25}, {wch:25}, {wch:20}, {wch:40}];
        XLSX.utils.book_append_sheet(wb, ws, "Historial Movimientos");
        XLSX.writeFile(wb, `Historial_Movimientos_${new Date().toISOString().slice(0,10)}.xlsx`);
    }

    $(document).ready(function () {
        $('#historialTable').DataTable({
            responsive: true,
            order: [[ 0, "desc" ]], // Ordenar por la columna de fecha (0) de más nuevo a más viejo
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
            dom: '<"flex flex-wrap justify-between items-center mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>'
        });
    });
</script>