<?php
// app/views/salida/salidaVerVista.php
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

    #salidasTable tbody tr {
        background-color: white !important;
    }

    #salidasTable tbody tr:hover {
        background-color: #f9fafb !important;
    }

    .dataTables_paginate .paginate_button.current {
        background-color: #4f46e5 !important;
        color: white !important;
        border-color: #4f46e5 !important;
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

    .dark #salidasTable tbody tr {
        background-color: #1f2937 !important;
        color: #d1d5db !important;
    }

    .dark #salidasTable tbody tr:hover {
        background-color: #374151 !important;
    }

    .dark .dataTables_paginate .paginate_button.current {
        background-color: #6366f1 !important;
        color: white !important;
        border-color: #6366f1 !important;
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
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-sign-out-alt text-indigo-600 dark:text-indigo-500 mr-2"></i> Historial de Salidas /
                    Despachos
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Registro unificado de repuestos y consumibles
                    entregados.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                <?php if (!empty($data['salidas'])): ?>
                    <button onclick="exportarExcel()"
                        class="px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-file-excel"></i> <span>Exportar Excel</span>
                    </button>
                <?php endif; ?>
                <?php if ($esAdmin): ?>
                    <a href="<?= BASE_URL ?>salidaCrear"
                        class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-dolly"></i> <span>Nueva Asignación</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($data['error'])): ?>
            <div
                class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Error</p>
                <p class="text-sm mt-1"><?= htmlspecialchars($data['error']) ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($data['exito'])): ?>
            <div
                class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold"><i class="fas fa-check-circle mr-2"></i> Éxito</p>
                <p class="text-sm mt-1"><?= htmlspecialchars($data['exito']) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($data['salidas'])): ?>
            <div class="overflow-x-auto">
                <table id="salidasTable" class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead
                        class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                        <tr>
                            <th class="py-3 px-4">Fecha</th>
                            <th class="py-3 px-4">Artículo / Detalle</th>
                            <th class="py-3 px-4 text-center">Cant.</th>
                            <th class="py-3 px-4">Entregado a</th>
                            <th class="py-3 px-4">Registrado por</th>
                            <?php if ($esAdmin): ?>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($data['salidas'] as $s): ?>
                            <tr class="transition-colors border-b dark:border-gray-700">
                                <!-- Fecha -->
                                <td class="py-4 px-4 font-medium whitespace-nowrap">
                                    <?= date('d/m/Y h:i A', strtotime($s['fecha_movimiento'])) ?>
                                </td>

                                <!-- Artículo Inteligente -->
                                <td class="py-4 px-4">
                                    <?php if (!empty($s['id_repuesto'])): ?>
                                        <span class="font-bold text-blue-600 dark:text-blue-400"><i
                                                class="fas fa-tools mr-1"></i><?= htmlspecialchars($s['nombre_repuesto']) ?></span>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            <span>Cod: <?= htmlspecialchars($s['codigo_referencia'] ?: 'S/C') ?></span> •
                                            <span
                                                class="font-bold uppercase text-gray-500 dark:text-gray-300"><?= htmlspecialchars($s['condicion']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="font-bold text-purple-600 dark:text-purple-400"><i
                                                class="fas fa-soap mr-1"></i><?= htmlspecialchars($s['nombre_producto']) ?></span>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            <span>Cod Interno: <?= htmlspecialchars($s['codigo_interno'] ?: 'S/C') ?></span> •
                                            <span class="italic text-purple-400">Consumible</span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Cantidad -->
                                <td class="py-4 px-4 text-center text-red-600 dark:text-red-400 font-bold text-lg">
                                    -<?= htmlspecialchars($s['cantidad']) ?>
                                </td>

                                <!-- Técnico / Destino -->
                                <td class="py-4 px-4 font-bold text-indigo-700 dark:text-indigo-400">
                                    <?= htmlspecialchars($s['tecnico_nombre'] ?? 'Desconocido') ?>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-normal mt-1 leading-tight">
                                        <?= htmlspecialchars($s['observacion']) ?>
                                    </div>
                                </td>

                                <!-- Registrado por -->
                                <td class="py-4 px-4 text-xs">
                                    <?= htmlspecialchars($s['admin_nombre']) ?>
                                </td>

                                <!-- Acciones -->
                                <?php if ($esAdmin): ?>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex space-x-2">
                                            <a href="<?= BASE_URL ?>salidaEditar/<?= $s['id_movimiento'] ?>"
                                                class="p-2 w-8 h-8 inline-flex items-center justify-center bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-full hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition-colors border border-yellow-200 dark:border-yellow-800"
                                                title="Editar Asignación">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <button onclick="abrirModalEliminar(<?= $s['id_movimiento'] ?>)"
                                                class="p-2 w-8 h-8 inline-flex items-center justify-center bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors border border-red-200 dark:border-red-800"
                                                title="Anular Asignación y Devolver Stock">
                                                <i class="fas fa-undo-alt text-xs"></i>
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
            <div
                class="text-center p-10 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                <i class="fas fa-dolly text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Aún no se han registrado asignaciones o despachos a
                    técnicos.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Eliminar / Anular -->
<div id="modalEliminar" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">
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
                        <i class="fas fa-undo text-red-600 dark:text-red-500"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            Anular Asignación Definitivamente
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                ¿Estás seguro de anular esta salida? El registro se eliminará y <span
                                    class="font-bold text-green-500">el stock regresará a tu almacén local</span> (sea
                                de repuesto o producto).
                            </p>
                            <p
                                class="text-xs text-yellow-600 dark:text-yellow-500 mt-2 bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded border border-yellow-200 dark:border-yellow-800">
                                <b>Nota:</b> Si esta asignación fue para un motorizado, debes notificar o ajustar
                                manualmente el inventario en la aplicación externa.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t dark:border-gray-700">
                <a id="btnConfirmarEliminar" href="#"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                    Sí, Devolver al Almacén
                </a>
                <button type="button" onclick="cerrarModalEliminar()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    const listaSalidas = <?= json_encode($data['salidas'] ?? []) ?>;

    function exportarExcel() {
        if (listaSalidas.length === 0) return alert("No hay datos");
        const datosExcel = listaSalidas.map(s => {
            let esRepuesto = (s.id_repuesto != null);
            return {
                "Fecha": s.fecha_movimiento,
                "Tipo de Artículo": esRepuesto ? "REPUESTO" : "CONSUMIBLE",
                "Código": esRepuesto ? s.codigo_referencia : s.codigo_interno,
                "Nombre Artículo": esRepuesto ? s.nombre_repuesto : s.nombre_producto,
                "Condición": esRepuesto ? s.condicion.toUpperCase() : "N/A",
                "Cantidad Asignada": parseInt(s.cantidad),
                "Técnico Destino": s.tecnico_nombre || 'Desconocido',
                "Observación": s.observacion,
                "Registrado Por": s.admin_nombre
            };
        });
        const ws = XLSX.utils.json_to_sheet(datosExcel);
        const wb = XLSX.utils.book_new();
        ws['!cols'] = [{ wch: 20 }, { wch: 15 }, { wch: 20 }, { wch: 35 }, { wch: 15 }, { wch: 15 }, { wch: 25 }, { wch: 30 }, { wch: 20 }];
        XLSX.utils.book_append_sheet(wb, ws, "Salidas");
        XLSX.writeFile(wb, `Historial_Salidas_Unificado_${new Date().toISOString().slice(0, 10)}.xlsx`);
    }

    $(document).ready(function () {
        $('#salidasTable').DataTable({
            responsive: true,
            order: [[0, "desc"]],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
            dom: '<"flex flex-wrap justify-between items-center mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>'
        });
    });

    function abrirModalEliminar(id) {
        document.getElementById('btnConfirmarEliminar').href = "<?= BASE_URL ?>salidaEliminar/" + id;
        document.getElementById('modalEliminar').classList.remove('hidden');
    }
    function cerrarModalEliminar() {
        document.getElementById('modalEliminar').classList.add('hidden');
    }
</script>