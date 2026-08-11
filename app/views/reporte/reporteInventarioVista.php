<?php if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado."); ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<!-- Reciclando tus estilos de DataTables de la vista anterior para mantener el diseño -->
<style>
    .dataTables_length select, .dataTables_filter input {
        background-color: white !important; color: #374151 !important; border: 1px solid #d1d5db !important;
        border-radius: 0.5rem; padding: 0.5rem 0.75rem; margin: 0 0.5rem; outline: none;
    }
    .dataTables_length label, .dataTables_filter label {
        color: #4b5563 !important; font-weight: 500; display: flex; align-items: center;
    }
    #inventarioTable tbody tr { background-color: white !important; }
    #inventarioTable tbody tr:hover { background-color: #f9fafb !important; }
    .dataTables_paginate .paginate_button.current, .dataTables_paginate .paginate_button:hover {
        background-color: #2563eb !important; color: white !important; border-color: #2563eb !important;
    }
    /* MODO OSCURO */
    .dark .dataTables_length select, .dark .dataTables_filter input {
        background-color: #374151 !important; color: #f3f4f6 !important; border: 1px solid #4b5563 !important;
    }
    .dark .dataTables_length label, .dark .dataTables_filter label, .dark .dataTables_info, .dark .dataTables_paginate .paginate_button {
        color: #9ca3af !important;
    }
    .dark #inventarioTable tbody tr { background-color: #1f2937 !important; }
    .dark #inventarioTable tbody tr:hover { background-color: #374151 !important; }
    .dark .dataTables_paginate .paginate_button.current {
        background-color: #3b82f6 !important; color: white !important; border-color: #3b82f6 !important;
    }
    .dataTables_wrapper>div:first-child, .dataTables_wrapper>div:last-of-type {
        display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin: 1.5rem 0;
    }
</style>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-500 mr-2"></i> Reporte de Inventario
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Filtra repuestos por condición y visualiza su stock disponible.</p>
            </div>

            <div class="mt-4 sm:mt-0 flex flex-wrap gap-3 items-center">
                <!-- Filtro de Condición -->
                <form id="formFiltro" action="<?= BASE_URL ?>reporteInventario" method="GET" class="flex items-center">
                    <label for="condicion" class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Condición:</label>
                    <select name="condicion" id="condicion" onchange="document.getElementById('formFiltro').submit();"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="recuperado" <?= $data['condicion_actual'] == 'recuperado' ? 'selected' : '' ?>>Recuperados</option>
                        <option value="nuevo" <?= $data['condicion_actual'] == 'nuevo' ? 'selected' : '' ?>>Nuevos</option>
                        <option value="por revisar" <?= $data['condicion_actual'] == 'por revisar' ? 'selected' : '' ?>>Por Revisar</option>
                        <option value="todos" <?= $data['condicion_actual'] == 'todos' ? 'selected' : '' ?>>Todos los Estados</option>
                    </select>
                </form>

                <!-- Botón PDF (Preparado para cuando hagamos la lógica de Node) -->
                <button onclick="generarPDF()" id="btnGenerarPdf"
                    class="px-5 py-2.5 bg-red-600 text-white font-bold rounded-lg shadow-md hover:bg-red-700 transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                    <i class="fas fa-file-pdf"></i>
                    <span>Exportar PDF</span>
                </button>
            </div>
        </div>

        <?php if (!empty($data['inventario'])): ?>
            <div class="overflow-x-auto">
                <table id="inventarioTable" class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                        <tr>
                            <th class="py-3 px-4">Ref.</th>
                            <th class="py-3 px-4">Repuesto</th>
                            <th class="py-3 px-4 text-center">Condición</th>
                            <th class="py-3 px-4 text-center">Stock Actual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($data['inventario'] as $item): ?>
                            <tr class="transition-colors border-b dark:border-gray-700">
                                <td class="py-4 px-4 font-mono text-blue-600 dark:text-blue-400 text-xs font-bold">
                                    <?= !empty($item['codigo_referencia']) ? htmlspecialchars($item['codigo_referencia']) : '<span class="text-gray-300 dark:text-gray-500 italic">N/A</span>' ?>
                                </td>
                                <td class="py-4 px-4 font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($item['nombre_repuesto']) ?>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <?php 
                                        $colorClase = '';
                                        if($item['condicion'] == 'nuevo') $colorClase = 'text-green-700 bg-green-100 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800';
                                        if($item['condicion'] == 'recuperado') $colorClase = 'text-yellow-700 bg-yellow-100 dark:bg-yellow-900/30 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800';
                                        if($item['condicion'] == 'por revisar') $colorClase = 'text-orange-700 bg-orange-100 dark:bg-orange-900/30 dark:text-orange-400 border-orange-200 dark:border-orange-800';
                                    ?>
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full border uppercase tracking-wider <?= $colorClase ?>">
                                        <?= htmlspecialchars($item['condicion']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="text-lg font-bold <?= $item['cantidad_en_stock'] > 0 ? 'text-gray-800 dark:text-white' : 'text-red-500' ?>">
                                        <?= $item['cantidad_en_stock'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center p-10 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                <i class="fas fa-box-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay repuestos con la condición seleccionada.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function () {
        $('#inventarioTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            dom: '<"flex flex-wrap justify-between items-center mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>'
        });
    });

    // Función vacía esperando la integración con Node.js
    function generarPDF() {
    const condicionActual = document.getElementById('condicion').value;
    
    // Le pasamos accion=generarPdf para que index.php ejecute directamente la función del controlador
    const urlPdf = "<?= BASE_URL ?>reporteInventario?accion=generarPdf&condicion=" + condicionActual;
    window.open(urlPdf, '_blank');
}

</script>