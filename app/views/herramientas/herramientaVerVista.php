<?php
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

    #herramientaTable tbody tr {
        background-color: white !important;
    }

    #herramientaTable tbody tr:hover {
        background-color: #f9fafb !important;
    }

    .dataTables_paginate .paginate_button.current {
        background-color: #f59e0b !important;
        color: white !important;
        border-color: #f59e0b !important;
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

    .dark #herramientaTable tbody tr {
        background-color: #1f2937 !important;
        color: #d1d5db !important;
    }

    .dark #herramientaTable tbody tr:hover {
        background-color: #374151 !important;
    }

    .dark .dataTables_paginate .paginate_button.current {
        background-color: #d97706 !important;
        color: white !important;
        border-color: #d97706 !important;
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


<div class="w-full px-4 md:px-6">

    <!-- Tarjetas de Resumen (KPIs) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Tipos de Herramientas</p>
                <h3 class="text-3xl font-black text-gray-800 dark:text-white mt-1">
                    <?= number_format($data['totales']['items']) ?>
                </h3>
            </div>
            <div class="h-14 w-14 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-400 text-2xl">
                <i class="fas fa-tools"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Stock Total en Almacén</p>
                <h3 class="text-3xl font-black text-gray-800 dark:text-white mt-1">
                    <?= number_format($data['totales']['stock']) ?>
                </h3>
            </div>
            <div class="h-14 w-14 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400 text-2xl">
                <i class="fas fa-boxes"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Stock Crítico (≤ 5 uds)</p>
                <h3 class="text-3xl font-black text-gray-800 dark:text-white mt-1">
                    <?= number_format($data['totales']['bajo_stock']) ?>
                </h3>
            </div>
            <div class="h-14 w-14 rounded-xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center text-red-600 dark:text-red-400 text-2xl">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    <!-- Encabezado + Acciones -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-tools text-amber-500 mr-2"></i> Inventario de Herramientas
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Control de herramientas que se encuentran en el almacén.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                <?php if (!empty($data['herramientas'])): ?>
                    <button onclick="exportarExcel()"
                        class="px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-file-excel"></i> <span>Excel</span>
                    </button>
                <?php endif; ?>
                <?php if ($esAdmin): ?>
                    <a href="<?= BASE_URL ?>herramientaCrear"
                        class="px-5 py-2.5 bg-amber-500 text-white font-bold rounded-lg shadow-md hover:bg-amber-600 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-plus-circle"></i> <span>Ingresar Herramienta</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($data['herramientas'])): ?>
            <div class="overflow-x-auto">
                <table id="herramientaTable" class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead
                        class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                        <tr>
                            <th class="py-3 px-4">Nombre</th>
                            <th class="py-3 px-4">Marca / Modelo</th>
                            <th class="py-3 px-4">Código Ref.</th>
                            <th class="py-3 px-4 text-center">Stock</th>
                            <th class="py-3 px-4 text-center">Condición</th>
                            <th class="py-3 px-4">Ubicación</th>
                            <?php if ($esAdmin): ?>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">


                        <?php foreach ($data['herramientas'] as $h): ?>
                            <tr class="transition-colors border-b dark:border-gray-700">
                                <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                    <i class="fas fa-hammer text-gray-400 mr-1"></i>
                                    <?= htmlspecialchars($h['nombre_herramienta']) ?>
                                </td>
                                <td class="py-4 px-4 text-gray-700 dark:text-gray-300">
                                    <?php if ($h['marca'] || $h['modelo']): ?>
                                        <?= htmlspecialchars(trim(($h['marca'] ?? '') . ' ' . ($h['modelo'] ?? ''))) ?>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 text-gray-700 dark:text-gray-300">
                                    <?php if ($h['codigo_referencia']): ?>
                                        <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                            <?= htmlspecialchars($h['codigo_referencia']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <?php if ($h['stock'] <= 5): ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800"><?= (int)$h['stock'] ?></span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800"><?= (int)$h['stock'] ?></span>
                                    <?php endif; ?>

                                <td class="py-4 px-4 text-center">
                                    <?php
                                    $colorCond = match ($h['condicion']) {
                                        'nueva' => 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:border-green-800',
                                        'usada' => 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:border-yellow-800',
                                        'reparacion' => 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:border-blue-800',
                                        'dañada' => 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:border-red-800',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:border-gray-600',
                                    };
                                    ?>
                                    <span class="px-3 py-1 text-xs font-bold rounded-full border <?= $colorCond ?>">
                                        <?= htmlspecialchars(ucfirst($h['condicion'])) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <?php if ($h['nombre_bodega']): ?>
                                        <span class="font-semibold text-blue-600 dark:text-blue-400"><i
                                                class="fas fa-warehouse text-xs mr-1"></i>
                                            <?= htmlspecialchars($h['nombre_bodega']) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Sin asignar</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($esAdmin): ?>
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="<?= BASE_URL ?>herramientaEditar/<?= $h['id_herramienta'] ?>"
                                                class="p-2 w-8 h-8 flex items-center justify-center bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-full hover:bg-yellow-200 border border-yellow-200 dark:border-yellow-800"
                                                title="Editar">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <button onclick="abrirModalEliminar(<?= $h['id_herramienta'] ?>, '<?= htmlspecialchars($h['nombre_herramienta'], ENT_QUOTES) ?>')"
                                                class="p-2 w-8 h-8 flex items-center justify-center bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full hover:bg-red-200 border border-red-200 dark:border-red-800"
                                                title="Eliminar">
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
                <i class="fas fa-tools text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay herramientas registradas en el almacén.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div id="modalEliminar" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
            onclick="cerrarModalEliminar()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border dark:border-gray-700">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-500"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Eliminar Herramienta</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">¿Seguro que quieres eliminar <span
                                id="nombreHerramientaModal" class="font-bold text-gray-700 dark:text-gray-300"></span>
                            del inventario? Esta acción la marcará como inactiva.</p>
                    </div>
                </div>
            </div>
            <div
                class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t dark:border-gray-700">
                <a id="btnConfirmarEliminar" href="#"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Sí,
                    Eliminar</a>
                <button type="button" onclick="cerrarModalEliminar()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    const listaHerramientas = <?= json_encode($data['herramientas'] ?? []) ?>;

    function exportarExcel() {
        if (listaHerramientas.length === 0) return alert("No hay datos");
        const datosExcel = listaHerramientas.map(h => ({
            "Nombre": h.nombre_herramienta,
            "Marca": h.marca || 'N/A',
            "Modelo": h.modelo || 'N/A',
            "Código Referencia": h.codigo_referencia || 'N/A',
            "Descripción": h.descripcion || 'N/A',
            "Condición": h.condicion.toUpperCase(),
            "Stock": h.stock,
            "Ubicación": h.nombre_bodega || 'Sin asignar',
            "Fecha Registro": h.fecha_registro
        }));
        const ws = XLSX.utils.json_to_sheet(datosExcel);
        const wb = XLSX.utils.book_new();
        ws['!cols'] = [{ wch: 30 }, { wch: 20 }, { wch: 20 }, { wch: 20 }, { wch: 40 }, { wch: 15 }, { wch: 10 }, { wch: 25 }, { wch: 20 }];
        XLSX.utils.book_append_sheet(wb, ws, "Inventario Herramientas");
        XLSX.writeFile(wb, `InventarioHerramientas_${new Date().toISOString().slice(0, 10)}.xlsx`);
    }

    $(document).ready(function () {
        $('#herramientaTable').DataTable({
            responsive: true,
            order: [[0, "asc"]],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
            dom: '<"flex flex-wrap justify-between items-center mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>'
        });
    });

    function abrirModalEliminar(id, nombre) {
        document.getElementById('btnConfirmarEliminar').href = "<?= BASE_URL ?>herramientaEliminar/" + id;
        document.getElementById('nombreHerramientaModal').textContent = nombre;
        document.getElementById('modalEliminar').classList.remove('hidden');
    }

    function cerrarModalEliminar() {
        document.getElementById('modalEliminar').classList.add('hidden');
    }
</script>

