<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

// Capturamos el nivel del usuario para permisos
$nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
$esAdmin = ($nivelUsuario == 1 || $nivelUsuario == 2);
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>

<style>
    /* Personalización Modo Claro */
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

    #maquinasTable tbody tr {
        background-color: white !important;
    }

    #maquinasTable tbody tr:hover {
        background-color: #f9fafb !important;
    }

    .dataTables_paginate .paginate_button.current,
    .dataTables_paginate .paginate_button:hover {
        background-color: #2563eb !important;
        color: white !important;
        border-color: #2563eb !important;
    }

    /* Personalización Modo Oscuro */
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

    .dark #maquinasTable tbody tr {
        background-color: #1f2937 !important;
        color: #d1d5db !important;
    }

    .dark #maquinasTable tbody tr:hover {
        background-color: #374151 !important;
    }

    .dark .dataTables_paginate .paginate_button.current {
        background-color: #3b82f6 !important;
        color: white !important;
        border-color: #3b82f6 !important;
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
                    <i class="fas fa-server text-blue-600 dark:text-blue-500 mr-2"></i> Tipos de Máquina
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gestiona los modelos y terminales en el sistema.</p>
            </div>

            <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                <?php if (!empty($data['maquinas'])): ?>
                    <button onclick="exportarExcel()"
                        class="px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-file-excel"></i> <span>Excel</span>
                    </button>
                <?php endif; ?>

                <?php if ($esAdmin): ?>
                    <a href="<?= BASE_URL ?>tipoMaquinaCrear"
                        class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-plus-circle"></i> <span>Nueva Máquina</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($data['maquinas'])): ?>
            <div class="overflow-x-auto">
                <table id="maquinasTable" class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead
                        class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Tipo de Máquina</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <?php if ($esAdmin): ?>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($data['maquinas'] as $m): ?>
                            <tr class="transition-colors border-b dark:border-gray-700">
                                <td class="py-4 px-4 font-bold text-gray-600 dark:text-gray-400">
                                    #<?= htmlspecialchars($m['idTipoMaquina']) ?></td>
                                <td class="py-4 px-4 font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($m['nombreTipoMaquina']) ?></td>
                                <td class="py-4 px-4 text-center">
                                    <?php if (strtolower($m['estado']) === 'activo'): ?>
                                        <span
                                            class="px-2 py-1 text-[10px] font-bold text-green-700 bg-green-100 dark:bg-green-900/30 dark:text-green-400 rounded-full border border-green-200 dark:border-green-800 uppercase tracking-wider">Activo</span>
                                    <?php else: ?>
                                        <span
                                            class="px-2 py-1 text-[10px] font-bold text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-400 rounded-full border border-red-200 dark:border-red-800 uppercase tracking-wider">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($esAdmin): ?>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <div class="flex justify-center items-center space-x-2">
                                            <a href="<?= BASE_URL ?>tipoMaquinaEditar/<?= htmlspecialchars($m['idTipoMaquina']) ?>"
                                                class="p-2 w-8 h-8 flex items-center justify-center bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-500 rounded-full hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition-colors border border-yellow-200 dark:border-yellow-800"
                                                title="Editar">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
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
                <i class="fas fa-server text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay tipos de máquina registrados.</p>
            </div>
        <?php endif; ?>
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
            "ID": m.idTipoMaquina,
            "Nombre Máquina": m.nombreTipoMaquina,
            "Estado": m.estado.charAt(0).toUpperCase() + m.estado.slice(1) // Capitaliza 'activo' o 'inactivo'
        }));
        const ws = XLSX.utils.json_to_sheet(datosExcel);
        const wb = XLSX.utils.book_new();
        ws['!cols'] = [{ wch: 10 }, { wch: 40 }, { wch: 15 }];
        XLSX.utils.book_append_sheet(wb, ws, "TiposMaquina");
        XLSX.writeFile(wb, `Tipos_Maquina_${new Date().toISOString().slice(0, 10)}.xlsx`);
    }

    $(document).ready(function () {
        $('#maquinasTable').DataTable({
            responsive: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
            dom: '<"flex flex-wrap justify-between items-center mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>'
        });
    });
</script>