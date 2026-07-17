<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
$esAdmin = (($_SESSION['nivel_acceso'] ?? 0) == 1 || ($_SESSION['nivel_acceso'] ?? 0) == 2);
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>

<style>
    /* Estilos DataTables idénticos a los tuyos (Omitidos para no hacer spam de CSS, pega aquí los del archivo anterior) */
    .dataTables_length select, .dataTables_filter input { background-color: white !important; color: #374151 !important; border: 1px solid #d1d5db !important; border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin: 0 0.5rem; outline: none; }
    .dataTables_length label, .dataTables_filter label { color: #4b5563 !important; font-weight: 500; display: flex; align-items: center; }
    #maquinaTable tbody tr { background-color: white !important; }
    #maquinaTable tbody tr:hover { background-color: #f9fafb !important; }
    .dataTables_paginate .paginate_button.current { background-color: #f97316 !important; color: white !important; border-color: #f97316 !important; }
    .dark .dataTables_length select, .dark .dataTables_filter input { background-color: #374151 !important; color: #f3f4f6 !important; border: 1px solid #4b5563 !important; }
    .dark .dataTables_length label, .dark .dataTables_filter label, .dark .dataTables_info, .dark .dataTables_paginate .paginate_button { color: #9ca3af !important; }
    .dark #maquinaTable tbody tr { background-color: #1f2937 !important; color: #d1d5db !important; }
    .dark #maquinaTable tbody tr:hover { background-color: #374151 !important; }
    .dark .dataTables_paginate .paginate_button.current { background-color: #ea580c !important; color: white !important; border-color: #ea580c !important; }
    .dataTables_wrapper>div:first-child, .dataTables_wrapper>div:last-of-type { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin: 1.5rem 0; }
</style>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-server text-orange-500 mr-2"></i> Inventario de Máquinas
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Control de seriales, ubicaciones y movimientos.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                <?php if (!empty($data['maquinas'])): ?>
                    <button onclick="exportarExcel()" class="px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-file-excel"></i> <span>Excel</span>
                    </button>
                <?php endif; ?>
                <?php if ($esAdmin): ?>
                    <a href="<?= BASE_URL ?>maquinaInventarioCrear" class="px-5 py-2.5 bg-orange-500 text-white font-bold rounded-lg shadow-md hover:bg-orange-600 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-plus-circle"></i> <span>Ingresar Máquina</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($data['maquinas'])): ?>
            <div class="overflow-x-auto">
                <table id="maquinaTable" class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                        <tr>
                            <th class="py-3 px-4">Serial</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4">Ubicación</th>
                            <th class="py-3 px-4 text-center">Condición</th>
                            <th class="py-3 px-4">Remisión</th>
                            <?php if ($esAdmin): ?>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($data['maquinas'] as $m): ?>
                            <tr class="transition-colors border-b dark:border-gray-700">
                                
                                <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                    <i class="fas fa-barcode text-gray-400 mr-1"></i> <?= htmlspecialchars($m['numero_serie']) ?>
                                </td>

                                <td class="py-4 px-4 text-gray-700 dark:text-gray-300">
                                    <?= htmlspecialchars($m['tipo_maquina']) ?>
                                </td>

                                <td class="py-4 px-4">
                                    <?php if ($m['nombre_bodega']): ?>
                                        <span class="font-semibold text-blue-600 dark:text-blue-400"><i class="fas fa-warehouse text-xs mr-1"></i> <?= htmlspecialchars($m['nombre_bodega']) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Sin asignar / Externa</span>
                                    <?php endif; ?>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <?php 
                                        $colorCond = match($m['condicion']) {
                                            'nueva' => 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:border-green-800',
                                            'usada' => 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:border-yellow-800',
                                            'reparacion' => 'bg-orange-100 text-orange-700 border-orange-200 dark:bg-orange-900/30 dark:border-orange-800',
                                            'dañada' => 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:border-red-800',
                                            default => 'bg-gray-100 text-gray-700 border-gray-200'
                                        };
                                    ?>
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full border uppercase tracking-wider <?= $colorCond ?>">
                                        <?= htmlspecialchars($m['condicion']) ?>
                                    </span>
                                </td>

                                <td class="py-4 px-4">
                                    <?php if (!empty($m['estado_remision'])): ?>
                                        <span class="block font-medium text-purple-600 dark:text-purple-400"><?= htmlspecialchars($m['estado_remision']) ?></span>
                                        <span class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">Flujo independiente<br>(Sin O.S.)</span>
                                    <?php else: ?>
                                        <span class="text-gray-300 dark:text-gray-600">-</span>
                                    <?php endif; ?>
                                </td>

                                <?php if ($esAdmin): ?>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <div class="flex justify-center items-center space-x-2">
                                            <a href="<?= BASE_URL ?>maquinaInventarioEditar/<?= htmlspecialchars($m['id_maquina']) ?>"
                                                class="p-2 w-8 h-8 flex items-center justify-center bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-500 rounded-full hover:bg-yellow-200 border border-yellow-200 dark:border-yellow-800" title="Editar">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>maquinaMovimiento/<?= htmlspecialchars($m['id_maquina']) ?>"
                                                class="p-2 w-8 h-8 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-500 rounded-full hover:bg-blue-200 border border-blue-200 dark:border-blue-800" title="Registrar Traslado/Salida">
                                                <i class="fas fa-exchange-alt text-xs"></i>
                                            </a>
                                            <button onclick="abrirModalEliminar(<?= $m['id_maquina'] ?>)"
                                                class="p-2 w-8 h-8 flex items-center justify-center bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full hover:bg-red-200 border border-red-200 dark:border-red-800" title="Eliminar">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center p-10 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                <i class="fas fa-microchip text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay máquinas registradas en el inventario.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="modalEliminar" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="cerrarModalEliminar()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border dark:border-gray-700">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-500"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Eliminar Máquina</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Esta acción borrará la máquina permanentemente. Si tiene historial de movimientos, podrían borrarse también (dependiendo de la base de datos). ¿Continuar?</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t dark:border-gray-700">
                <a id="btnConfirmarEliminar" href="#" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Sí, Eliminar</a>
                <button type="button" onclick="cerrarModalEliminar()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    const listaMaquinas = <?= json_encode($data['maquinas'] ?? []) ?>;

    function exportarExcel() {
        if (listaMaquinas.length === 0) return alert("No hay datos");
        const datosExcel = listaMaquinas.map(m => ({
            "Serial": m.numero_serie,
            "Tipo Máquina": m.tipo_maquina,
            "Ubicación": m.nombre_bodega || 'Sin asignar / Externa',
            "Condición": m.condicion.toUpperCase(),
            "Estado Remisión": m.estado_remision || 'N/A',
            "Fecha Registro": m.fecha_registro
        }));
        const ws = XLSX.utils.json_to_sheet(datosExcel);
        const wb = XLSX.utils.book_new();
        ws['!cols'] = [{ wch: 20 }, { wch: 25 }, { wch: 25 }, { wch: 15 }, { wch: 20 }, { wch: 20 }];
        XLSX.utils.book_append_sheet(wb, ws, "Inventario Maquinas");
        XLSX.writeFile(wb, `InventarioMaquinas_${new Date().toISOString().slice(0, 10)}.xlsx`);
    }

    $(document).ready(function () {
        $('#maquinaTable').DataTable({
            responsive: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
            dom: '<"flex flex-wrap justify-between items-center mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>'
        });
    });

    function abrirModalEliminar(id) {
        document.getElementById('btnConfirmarEliminar').href = "<?= BASE_URL ?>maquinaInventarioEliminar/" + id;
        document.getElementById('modalEliminar').classList.remove('hidden');
    }

    function cerrarModalEliminar() {
        document.getElementById('modalEliminar').classList.add('hidden');
    }
</script>