<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

// Ruta física real a la imagen del logo
$rutaLogo = __DIR__ . '/../../logos/logoInees.jpg';

$logoBase64 = '';
if (file_exists($rutaLogo)) {
    $tipoImagen = pathinfo($rutaLogo, PATHINFO_EXTENSION);
    $datosImagen = file_get_contents($rutaLogo);
    $logoBase64 = 'data:image/' . $tipoImagen . ';base64,' . base64_encode($datosImagen);
}
?>

<!-- CSS de DataTables con Tailwind -->
<link href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css" rel="stylesheet">

<style>
    .dataTables_wrapper {
        padding: 10px 0;
    }

    div.dt-buttons {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    /* Estilos por defecto (Modo Claro) para la cabecera */
    table.dataTable thead th {
        background-color: #f3f4f6 !important;
        color: #374151 !important;
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    /* Modo Oscuro para DataTables */
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

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700">

        <!-- Cabecera -->
        <div
            class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-list text-orange-500 mr-2"></i> Historial de Movimientos
                </h1>
                <p class="text-gray-500 text-sm mt-1">Exporta las entradas y salidas a Excel o PDF según las fechas que
                    necesites.</p>
            </div>
            <div class="flex space-x-2">
                <a href="<?= BASE_URL ?>entradaSalidaCrear"
                    class="px-4 py-2 bg-orange-500 text-white font-bold rounded-lg shadow-md hover:bg-orange-600 transition-colors">
                    <i class="fas fa-plus mr-1"></i> Nuevos Registros
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

        <!-- Tabla con columnas individuales -->
        <div class="overflow-x-auto">
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
                        <th scope="col" class="px-4 py-3">N° Cotización</th>
                        <th scope="col" class="px-4 py-3">Registrado Por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['movimientos'])): ?>
                        <?php foreach ($data['movimientos'] as $mov):
                            $colorTipo = ($mov['tipo_movimiento'] === 'ENTRADA') ? 'text-green-600 font-bold' : 'text-red-600 font-bold';
                            $nombreRepuesto = $mov['nombre_repuesto'] ? ($mov['codigo_referencia'] . ' - ' . $mov['nombre_repuesto']) : 'MANUAL';

                            // --- LÓGICA DE PARSEADO DE LA OBSERVACIÓN ---
                            // Inicializamos variables por defecto
                            $novedad = 'N/A';
                            $destino = 'N/A';
                            $remision = 'N/A';
                            $cotizacion = 'N/A';

                            $obs = $mov['observacion'] ?? '';

                            // Si contiene repuesto manual, lo limpiamos para extraer la novedad limpia
                            if (strpos($obs, 'Repuesto Manual:') !== false) {
                                $partesManual = explode(' | ', $obs, 2);
                                if (count($partesManual) > 1) {
                                    $obs = $partesManual[1];
                                }
                            }

                            // Separamos por '|'
                            $partes = explode(' | ', $obs);
                            foreach ($partes as $parte) {
                                if (strpos($parte, 'Novedad:') !== false) {
                                    $novedad = trim(str_replace('Novedad:', '', $parte));
                                } elseif (strpos($parte, 'Destino:') !== false) {
                                    $destino = trim(str_replace('Destino:', '', $parte));
                                } elseif (strpos($parte, 'Remisión:') !== false) {
                                    $remision = trim(str_replace('Remisión:', '', $parte));
                                } elseif (strpos($parte, 'Cotización:') !== false) {
                                    $cotizacion = trim(str_replace('Cotización:', '', $parte));
                                }
                            }
                            ?>
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?= date('Y-m-d', strtotime($mov['fecha_movimiento'])) ?>
                                </td>
                                <td class="px-4 py-3 <?= $colorTipo ?>"><?= $mov['tipo_movimiento'] ?></td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($nombreRepuesto) ?>
                                </td>
                                <td class="px-4 py-3 font-bold text-center"><?= $mov['cantidad'] ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($novedad) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($destino) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($remision) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($cotizacion) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($mov['nombre_usuario'] ?? 'Sistema') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    $(document).ready(function () {

        // Filtro por rangos de fecha
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
                    title: 'Reporte Inventario Entradas y Salidas'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf mr-1"></i> Exportar PDF Filtrado',
                    className: 'bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors shadow',
                    title: 'Reporte Inventario',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function (doc) {
                        var logoBase64 = "<?= $logoBase64 ?>";

                        doc.pageMargins = [20, 70, 20, 30];

                        doc['header'] = function (currentPage, pageCount) {
                            var headerColumns = [];

                            if (logoBase64 !== '') {
                                headerColumns.push({
                                    image: logoBase64,
                                    width: 100
                                });
                            }

                            headerColumns.push({
                                alignment: 'right',
                                text: [
                                    { text: 'INEES - Electrónica IT Seguridad\n', fontSize: 11, bold: true, color: '#1e3a8a' },
                                    { text: 'Reporte Oficial de Entradas y Salidas\n', fontSize: 8, color: '#4b5563' },
                                    { text: 'Fecha: ' + new Date().toLocaleDateString('es-CO'), fontSize: 7, italic: true }
                                ]
                            });

                            return {
                                margin: [20, 15, 20, 0],
                                columns: headerColumns
                            };
                        };

                        doc['footer'] = function (currentPage, pageCount) {
                            return {
                                margin: [20, 5, 20, 0],
                                columns: [
                                    {
                                        text: 'InventarioAlmacen - Documento Interno',
                                        fontSize: 7,
                                        color: '#9ca3af'
                                    },
                                    {
                                        alignment: 'right',
                                        text: 'Página ' + currentPage.toString() + ' de ' + pageCount.toString(),
                                        fontSize: 7,
                                        bold: true,
                                        color: '#4b5563'
                                    }
                                ]
                            };
                        };

                        // Configuración de estilos y anchos de las 9 columnas en el PDF
                        doc.defaultStyle.fontSize = 7;
                        doc.styles.tableHeader.fontSize = 8;
                        doc.styles.tableHeader.fillColor = '#1e3a8a';
                        doc.styles.tableHeader.color = '#ffffff';
                        doc.styles.tableHeader.alignment = 'center';

                        // Ancho distribuido para las 9 columnas (Suma 100%)
                        doc.content[1].table.widths = ['9%', '8%', '18%', '5%', '22%', '12%', '9%', '9%', '8%'];
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