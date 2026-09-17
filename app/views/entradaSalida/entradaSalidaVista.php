<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
?>

<!-- Carga de Tailwind CSS CDN con fallback de JS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    brand: {
                        50: '#fff7ed',
                        500: '#f97316',
                        600: '#ea580c',
                    }
                }
            }
        }
    }
</script>

<!-- FontAwesome para los iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- CSS DataTables Tailwind -->
<link href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css" rel="stylesheet">

<style>
    /* Corrección para que el modal no aparezca de golpe si Tailwind se tarda en compilar */
    #modalEditarFecha.hidden {
        display: none !important;
    }

    /* Fuerza el salto de línea en seriales/textos largos sin espacio */
    table.dataTable td {
        max-width: 250px;
        white-space: normal !important;
        word-break: break-all;
    }

    .dataTables_wrapper {
        padding: 10px 0;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
    }

    div.dt-buttons {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    table.dataTable thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    .dark table.dataTable thead th {
        background-color: #1f2937 !important;
        color: #d1d5db !important;
        border-bottom-color: #374151 !important;
    }

    .dark .dt-search input,
    .dark .dt-length select {
        background-color: #1f2937 !important;
        border: 1px solid #4b5563 !important;
        color: #ffffff !important;
    }

    .dark .dt-info {
        color: #9ca3af !important;
    }
</style>

<div class="w-full px-4 md:px-6 py-6">

    <!-- TARJETAS DE KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div
            class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm border-l-4 border-l-emerald-500 transition-colors">
            <p class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">Total Entradas</p>
            <p class="text-3xl font-black text-gray-800 dark:text-white mt-1">
                <?= number_format($kpis['total_entradas'] ?? 0) ?>
            </p>
        </div>
        <div
            class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm border-l-4 border-l-red-500 transition-colors">
            <p class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">Total Salidas</p>
            <p class="text-3xl font-black text-gray-800 dark:text-white mt-1">
                <?= number_format($kpis['total_salidas'] ?? 0) ?>
            </p>
        </div>
        <div
            class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm border-l-4 border-l-blue-500 transition-colors">
            <p class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">Balance Neto</p>
            <p
                class="text-3xl font-black <?= ($kpis['balance_neto'] ?? 0) >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' ?> mt-1">
                <?= (($kpis['balance_neto'] ?? 0) >= 0 ? '+' : '') . number_format($kpis['balance_neto'] ?? 0) ?>
            </p>
        </div>
        <div
            class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm border-l-4 border-l-amber-500 transition-colors">
            <p class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">Total Movimientos</p>
            <p class="text-3xl font-black text-gray-800 dark:text-white mt-1">
                <?= number_format($kpis['movimientos_totales'] ?? 0) ?>
            </p>
        </div>
    </div>

    <!-- CONTENEDOR PRINCIPAL TABLA -->
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <!-- Cabecera -->
        <div
            class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                    <i class="fas fa-list text-orange-500 mr-2"></i> Historial de Movimientos
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Exporta las entradas y salidas a Excel o PDF, y
                    corrige fechas si es necesario.</p>
            </div>
            <div class="flex space-x-2">
                <a href="<?= BASE_URL ?>SalidaCrear"
                    class="px-4 py-2 bg-orange-500 text-white font-bold rounded-lg shadow-md hover:bg-orange-600 transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i> Nuevos Registros
                </a>
            </div>
        </div>

        <!-- Filtro de Fechas -->
        <div
            class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-transparent dark:border-gray-700">
            <div>
                <label class="block mb-1 text-sm font-bold text-gray-700 dark:text-gray-300">Desde Fecha:</label>
                <input type="date" id="filtro_desde"
                    class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
            <div>
                <label class="block mb-1 text-sm font-bold text-gray-700 dark:text-gray-300">Hasta Fecha:</label>
                <input type="date" id="filtro_hasta"
                    class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
        </div>

        <!-- Tabla -->
        <div class="w-full max-w-full overflow-x-auto">
            <table id="tabla_movimientos" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead>
                    <tr>
                        <th scope="col" class="px-4 py-3">Fecha</th>
                        <th scope="col" class="px-4 py-3">Tipo</th>
                        <th scope="col" class="px-4 py-3">Repuesto / Artículo</th>
                        <th scope="col" class="px-4 py-3 text-center">Cant.</th>
                        <th scope="col" class="px-4 py-3">Novedad</th>
                        <th scope="col" class="px-4 py-3">Destino</th>
                        <th scope="col" class="px-4 py-3">N° Remisión</th>
                        <th scope="col" class="px-4 py-3">N° Cotización/OC</th>
                        <th scope="col" class="px-4 py-3">Registrado Por</th>
                        <th scope="col" class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $todosLosMovimientos = array_merge($entradas ?? [], $salidas ?? []);
                    if (!empty($todosLosMovimientos)):
                        foreach ($todosLosMovimientos as $mov):
                            $esEntrada = ($mov['tipo'] ?? '') === 'ENTRADA';
                            $esRecuperado = (($mov['origen_entrada'] ?? null) === 'RECUPERADO');
                            $colorTipo = $esEntrada ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-red-600 dark:text-red-400 font-bold';
                            $fechaRaw = str_replace('/', '-', $mov['fecha']);
                            $fechaISO = date('Y-m-d', strtotime($fechaRaw));
                            $idMov = $mov['id_movimiento'] ?? 0;
                            $articulo = htmlspecialchars($mov['articulo'] ?? '', ENT_QUOTES);
                            $cantidad = (int) ($mov['cantidad'] ?? 0);
                            $etiquetaTipo = $esRecuperado ? 'RECUPERADO' : ($mov['tipo'] ?? '');
                            $articuloFull = $mov['articulo'] ?? '';
                            if ($esRecuperado && !empty($mov['tecnico_origen']) && $mov['tecnico_origen'] !== 'N/A') {
                                $articuloFull .= ' | Tec: ' . $mov['tecnico_origen'];
                            }
                            ?>
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap"><?= $fechaISO ?></td>
                                <td class="px-4 py-3 <?= $colorTipo ?>"><?= htmlspecialchars($etiquetaTipo) ?><?php if ($esRecuperado): ?> <span class="ml-1 px-2 py-0.5 text-[10px] rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 font-bold">REC</span><?php endif; ?></td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($articuloFull) ?>
                                </td>
                                <td class="px-4 py-3 font-bold text-center"><?= $esEntrada ? '+' : '-' ?><?= $cantidad ?></td>
                                <td class="px-4 py-3 max-w-xs break-all"><?= htmlspecialchars($mov['novedad'] ?? 'N/A') ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($mov['destino'] ?? 'N/A') ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($mov['remision'] ?? 'N/A') ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($mov['cotizacion'] ?? 'N/A') ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($mov['usuario'] ?? 'Sistema') ?></td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        onclick="abrirModalEditar(<?= $idMov ?>, '<?= $fechaISO ?>', '<?= $articulo ?>', <?= $cantidad ?>)"
                                        class="text-orange-500 hover:text-orange-700 dark:hover:text-orange-400 p-2 rounded-lg hover:bg-orange-50 dark:hover:bg-gray-700 transition-colors"
                                        title="Editar Fecha">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL DE EDICIÓN -->
<div id="modalEditarFecha"
    class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div
            class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Editar Fecha de Movimiento</h3>
            <button onclick="cerrarModal()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>entradaSalida?accion=actualizarFecha" method="POST" class="p-6">
            <input type="hidden" id="modal_id_movimiento" name="id_movimiento">

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Artículo (No
                    editable)</label>
                <input type="text" id="modal_articulo" readonly
                    class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 rounded-lg cursor-not-allowed">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Cantidad (No
                    editable)</label>
                <input type="number" id="modal_cantidad" readonly
                    class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 rounded-lg cursor-not-allowed">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nueva Fecha de
                    Registro</label>
                <input type="date" id="modal_fecha" name="nueva_fecha" required
                    class="w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="cerrarModal()"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white font-bold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>

<script>
    var BASE_URL_JS = "<?= BASE_URL ?>";

    function abrirModalEditar(idMovimiento, fechaISO, articulo, cantidad) {
        document.getElementById('modal_id_movimiento').value = idMovimiento;
        document.getElementById('modal_fecha').value = fechaISO;
        document.getElementById('modal_articulo').value = articulo;
        document.getElementById('modal_cantidad').value = cantidad;

        document.getElementById('modalEditarFecha').classList.remove('hidden');
    }

    function cerrarModal() {
        document.getElementById('modalEditarFecha').classList.add('hidden');
    }

    $(document).ready(function () {
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var min = $('#filtro_desde').val();
                var max = $('#filtro_hasta').val();
                var dateCol = data[0];

                if (
                    (min === "" && max === "") ||
                    (min === "" && dateCol <= max) ||
                    (min <= dateCol && max === "") ||
                    (min <= dateCol && dateCol <= max)
                ) {
                    return true;
                }
                return false;
            }
        );

        var table = $('#tabla_movimientos').DataTable({
            responsive: true,
            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros en total)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                }
            },
            layout: {
                topStart: 'buttons',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel mr-1"></i> Exportar Excel Filtrado',
                    className: 'bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition-colors shadow',
                    title: '',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    },
                    action: function (e, dt, node, config) {
                        var data = dt.rows({ search: 'applied' }).data().toArray();
                        var salidasRows = [];
                        var entradasRows = [];
                        var salidasDates = new Set();
                        var entradasDates = new Set();

                        data.forEach(function (row) {
                            var cleanRow = row.map(cell => $('<div>').html(cell).text().trim());
                            var tipoMovimiento = cleanRow[1];
                            var fecha = cleanRow[0];
                            cleanRow.splice(1, 1);
                            cleanRow.pop();

                            if (tipoMovimiento === 'SALIDA') {
                                salidasRows.push(cleanRow);
                                salidasDates.add(fecha);
                            } else if (tipoMovimiento === 'ENTRADA') {
                                entradasRows.push(cleanRow);
                                entradasDates.add(fecha);
                            }
                        });

                        var wb = XLSX.utils.book_new();

                        if (salidasRows.length > 0) {
                            var headersSalidas = ["Fecha", "Repuesto / Artículo", "Cant.", "Novedad", "Destino", "N° Remisión", "N° Cotización", "Registrado Por"];
                            var colsSalidas = [{ wch: 12 }, { wch: 35 }, { wch: 8 }, { wch: 25 }, { wch: 20 }, { wch: 15 }, { wch: 15 }, { wch: 20 }];

                            if (salidasDates.size === 1) {
                                headersSalidas.splice(0, 1);
                                colsSalidas.shift();
                                salidasRows.forEach(r => r.splice(0, 1));
                            }
                            var wsSalidas = XLSX.utils.aoa_to_sheet([headersSalidas].concat(salidasRows));
                            wsSalidas['!cols'] = colsSalidas;
                            XLSX.utils.book_append_sheet(wb, wsSalidas, "Salidas");
                        }

                        if (entradasRows.length > 0) {
                            var headersEntradas = ["Fecha", "Repuesto / Artículo", "Cant.", "Novedad", "Destino", "N° Remisión", "Orden de Compra", "Registrado Por"];
                            var colsEntradas = [{ wch: 12 }, { wch: 35 }, { wch: 8 }, { wch: 25 }, { wch: 20 }, { wch: 15 }, { wch: 15 }, { wch: 20 }];

                            if (entradasDates.size === 1) {
                                headersEntradas.splice(0, 1);
                                colsEntradas.shift();
                                entradasRows.forEach(r => r.splice(0, 1));
                            }
                            var wsEntradas = XLSX.utils.aoa_to_sheet([headersEntradas].concat(entradasRows));
                            wsEntradas['!cols'] = colsEntradas;
                            XLSX.utils.book_append_sheet(wb, wsEntradas, "Entradas");
                        }

                        if (salidasRows.length === 0 && entradasRows.length === 0) {
                            alert("No hay datos para exportar.");
                            return;
                        }

                        XLSX.writeFile(wb, "Reporte_Inventario_" + new Date().toISOString().slice(0, 10) + ".xlsx");
                    }
                },
                {
                    text: '<i class="fas fa-file-pdf mr-1"></i> Exportar PDF Filtrado',
                    className: 'bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors shadow',
                    action: function (e, dt, node, config) {
                        var desde = $('#filtro_desde').val();
                        var hasta = $('#filtro_hasta').val();

                        // Usamos la ruta amigable registrada en BD 'entradaSalida' e indicamos la accion por GET
                        var url = BASE_URL_JS + "entradaSalida?accion=generarPdf";

                        if (desde) url += "&fecha_desde=" + encodeURIComponent(desde);
                        if (hasta) url += "&fecha_hasta=" + encodeURIComponent(hasta);

                        window.open(url, '_blank');
                    }
                }
            ],
            order: [[0, "desc"]]
        });

        $('#filtro_desde, #filtro_hasta').on('change', function () {
            table.draw();
        });
    });
</script>