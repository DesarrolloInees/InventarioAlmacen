<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

$registros = $data['registros'] ?? [];
$kpis      = $data['kpis'] ?? [];
$grupos    = $data['grupos'] ?? [];
$bodegas   = $data['bodegas'] ?? [];
$filtros   = $data['filtros'] ?? [];
$mensaje   = $data['mensaje'] ?? '';
$esAdmin   = in_array((int) ($_SESSION['nivel_acceso'] ?? 0), [1, 2], true);

if (!function_exists('rbFormatoCantidad')) {
    function rbFormatoCantidad($valor)
    {
        $texto = number_format((float) $valor, 2, ',', '.');
        $texto = rtrim(rtrim($texto, '0'), ',');
        return $texto === '' ? '0' : $texto;
    }
}

if (!function_exists('rbFecha')) {
    function rbFecha($valor)
    {
        return !empty($valor) ? date('d/m/Y H:i', strtotime($valor)) : '—';
    }
}

$mensajes = [
    'creado'              => ['tipo' => 'ok',    'texto' => 'Registro creado correctamente.'],
    'actualizado'         => ['tipo' => 'ok',    'texto' => 'Registro actualizado correctamente.'],
    'anulado'             => ['tipo' => 'warn',  'texto' => 'Registro anulado correctamente. Permanece visible en el listado con estado ANULADO.'],
    'ya_anulado'          => ['tipo' => 'error', 'texto' => 'El registro ya se encontraba anulado.'],
    'anulado_no_editable' => ['tipo' => 'error', 'texto' => 'Un registro anulado no puede editarse.'],
    'noencontrado'        => ['tipo' => 'error', 'texto' => 'No se encontró el registro solicitado.'],
];
$aviso = $mensajes[$mensaje] ?? null;
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.dataTables.min.css">
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

    #tablaRegistros thead th:first-child,
    #tablaRegistros tbody tr:not(.dtrg-group) td:first-child {
        display: none;
    }

    #tablaRegistros tbody tr {
        background-color: white !important;
    }

    #tablaRegistros tbody tr:hover {
        background-color: #f9fafb !important;
    }

    #tablaRegistros tr.dtrg-group th {
        background-color: #fff7ed !important;
        color: #9a3412 !important;
        border-top: 2px solid #fdba74 !important;
        border-bottom: 1px solid #fed7aa !important;
        padding: 0.65rem 1rem !important;
        font-size: 0.78rem;
        letter-spacing: 0.01em;
    }

    .dataTables_paginate .paginate_button.current {
        background-color: #f97316 !important;
        color: white !important;
        border-color: #f97316 !important;
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

    .dark #tablaRegistros tbody tr {
        background-color: #1f2937 !important;
        color: #d1d5db !important;
    }

    .dark #tablaRegistros tbody tr:hover {
        background-color: #374151 !important;
    }

    .dark #tablaRegistros tr.dtrg-group th {
        background-color: #374151 !important;
        color: #fdba74 !important;
        border-top: 2px solid #9a3412 !important;
        border-bottom: 1px solid #4b5563 !important;
    }

    .dark .dataTables_paginate .paginate_button.current {
        background-color: #ea580c !important;
        color: white !important;
        border-color: #ea580c !important;
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

    .rb-filtro {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        color: #111827;
        font-size: 0.875rem;
        border-radius: 0.5rem;
        padding: 0.55rem 0.7rem;
        width: 100%;
        outline: none;
    }

    .rb-filtro:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);
    }

    .dark .rb-filtro {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }
</style>
<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-clipboard-list text-orange-500 mr-2"></i> Registro de Entradas / Salidas de Bodega
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Libro manual de novedades. No afecta el stock ni los movimientos de inventario.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php if ($esAdmin): ?>
                    <a href="<?= BASE_URL ?>registroBodegaCrear"
                        class="px-5 py-2.5 bg-orange-500 text-white font-bold rounded-lg shadow-md hover:bg-orange-600 transition-transform transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-plus-circle"></i><span>Nuevo Registro</span>
                    </a>
                <?php endif; ?>
                <button onclick="exportarExcel()"
                    class="px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                    <i class="fas fa-file-excel"></i><span>Exportar Excel</span>
                </button>
                <button onclick="exportarPdf()"
                    class="px-5 py-2.5 bg-red-600 text-white font-bold rounded-lg shadow-md hover:bg-red-700 transition-transform transform hover:scale-105 flex items-center space-x-2">
                    <i class="fas fa-file-pdf"></i><span>PDF</span>
                </button>
            </div>
        </div>

        <?php if ($aviso): ?>
            <?php
            $clasesAviso = $aviso['tipo'] === 'ok'
                ? 'bg-green-50 text-green-800 dark:bg-gray-700 dark:text-green-400'
                : ($aviso['tipo'] === 'warn'
                    ? 'bg-yellow-50 text-yellow-800 dark:bg-gray-700 dark:text-yellow-300'
                    : 'bg-red-50 text-red-800 dark:bg-gray-700 dark:text-red-400');
            ?>
            <div class="mb-5 p-4 text-sm rounded-lg <?= $clasesAviso ?>" role="alert">
                <?= htmlspecialchars($aviso['texto']) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="rounded-xl p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500">
                <p class="text-[11px] font-bold uppercase text-green-700 dark:text-green-400">Entradas</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300"><?= rbFormatoCantidad($kpis['total_entradas'] ?? 0) ?></p>
                <p class="text-[11px] text-green-600/80 dark:text-green-400/80">Cantidad acumulada</p>
            </div>
            <div class="rounded-xl p-4 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500">
                <p class="text-[11px] font-bold uppercase text-orange-700 dark:text-orange-400">Salidas</p>
                <p class="text-2xl font-bold text-orange-700 dark:text-orange-300"><?= rbFormatoCantidad($kpis['total_salidas'] ?? 0) ?></p>
                <p class="text-[11px] text-orange-600/80 dark:text-orange-400/80">Cantidad acumulada</p>
            </div>
            <div class="rounded-xl p-4 bg-sky-50 dark:bg-sky-900/20 border-l-4 border-sky-500">
                <p class="text-[11px] font-bold uppercase text-sky-700 dark:text-sky-400">Ítems activos</p>
                <p class="text-2xl font-bold text-sky-700 dark:text-sky-300"><?= (int) ($kpis['total_items'] ?? 0) ?></p>
                <p class="text-[11px] text-sky-600/80 dark:text-sky-400/80">Filas de registro</p>
            </div>
            <div class="rounded-xl p-4 bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500">
                <p class="text-[11px] font-bold uppercase text-indigo-700 dark:text-indigo-400">Remisiones</p>
                <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-300"><?= (int) ($kpis['total_remisiones'] ?? 0) ?></p>
                <p class="text-[11px] text-indigo-600/80 dark:text-indigo-400/80">Documentos distintos</p>
            </div>
            <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-700/40 border-l-4 border-gray-500">
                <p class="text-[11px] font-bold uppercase text-gray-700 dark:text-gray-300">Anulados</p>
                <p class="text-2xl font-bold text-gray-700 dark:text-gray-200"><?= (int) ($kpis['total_anulados'] ?? 0) ?></p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">No suman en totales</p>
            </div>
        </div>
        <form method="GET" action="<?= BASE_URL ?>registroBodegaVer"
            class="mb-6 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <div>
                    <label for="f_tipo" class="block mb-1 text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Tipo</label>
                    <select name="tipo" id="f_tipo" class="rb-filtro">
                        <option value="">Todos</option>
                        <option value="ENTRADA" <?= ($filtros['tipo'] ?? '') === 'ENTRADA' ? 'selected' : '' ?>>Entradas</option>
                        <option value="SALIDA" <?= ($filtros['tipo'] ?? '') === 'SALIDA' ? 'selected' : '' ?>>Salidas</option>
                    </select>
                </div>
                <div>
                    <label for="f_desde" class="block mb-1 text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Desde</label>
                    <input type="date" name="fecha_desde" id="f_desde" class="rb-filtro"
                        value="<?= htmlspecialchars($filtros['fecha_desde'] ?? '') ?>">
                </div>
                <div>
                    <label for="f_hasta" class="block mb-1 text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Hasta</label>
                    <input type="date" name="fecha_hasta" id="f_hasta" class="rb-filtro"
                        value="<?= htmlspecialchars($filtros['fecha_hasta'] ?? '') ?>">
                </div>
                <div>
                    <label for="f_bodega" class="block mb-1 text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Bodega</label>
                    <select name="id_bodega" id="f_bodega" class="rb-filtro">
                        <option value="">Todas</option>
                        <?php foreach ($bodegas as $b): ?>
                            <option value="<?= (int) $b['id_bodega'] ?>" <?= (string) ($filtros['id_bodega'] ?? '') === (string) $b['id_bodega'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($b['nombre_bodega']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="f_remision" class="block mb-1 text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Remisión</label>
                    <input type="text" name="remision" id="f_remision" class="rb-filtro" placeholder="N° remisión"
                        value="<?= htmlspecialchars($filtros['remision'] ?? '') ?>">
                </div>
                <div>
                    <label for="f_q" class="block mb-1 text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Buscar</label>
                    <input type="text" name="q" id="f_q" class="rb-filtro" placeholder="Artículo, código, destino..."
                        value="<?= htmlspecialchars($filtros['q'] ?? '') ?>">
                </div>
            </div>
            <div class="mt-3 flex flex-wrap justify-end gap-2">
                <a href="<?= BASE_URL ?>registroBodegaVer"
                    class="px-4 py-2 text-sm font-bold rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-eraser mr-1"></i> Limpiar
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-bold rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition-colors">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table id="tablaRegistros" class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Grupo</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Artículo</th>
                        <th class="px-4 py-3 text-right">Cantidad</th>
                        <th class="px-4 py-3">Bodega</th>
                        <th class="px-4 py-3">Destino / Procedencia</th>
                        <th class="px-4 py-3">Documento</th>
                        <th class="px-4 py-3">Novedad</th>
                        <th class="px-4 py-3">Registra</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $r): ?>
                        <?php
                        $esAnulado = ($r['estado'] ?? '') === 'ANULADO';
                        $esEntrada = ($r['tipo_movimiento'] ?? '') === 'ENTRADA';
                        ?>
                        <tr class="<?= $esAnulado ? 'opacity-60' : '' ?> border-b dark:border-gray-700">
                            <td class="px-4 py-3 text-xs text-gray-400"><?= htmlspecialchars($r['grupo_key'] ?? '') ?></td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?php if ($esEntrada): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                        <i class="fas fa-arrow-down mr-1"></i> ENTRADA
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300">
                                        <i class="fas fa-arrow-up mr-1"></i> SALIDA
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap" data-order="<?= htmlspecialchars($r['fecha_movimiento'] ?? '') ?>">
                                <?= rbFecha($r['fecha_movimiento'] ?? '') ?>
                            </td>
                            <td class="px-4 py-3 min-w-[180px]">
                                <span class="font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($r['nombre_articulo'] ?? '') ?></span>
                                <?php if (!empty($r['codigo_referencia'])): ?>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">Cód: <?= htmlspecialchars($r['codigo_referencia']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                <?= rbFormatoCantidad($r['cantidad'] ?? 0) ?>
                            </td>
                            <td class="px-4 py-3"><?= htmlspecialchars($r['nombre_bodega'] ?? '—') ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($r['destino'] ?? '—') ?></td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="block font-medium"><?= htmlspecialchars(!empty($r['remision']) ? $r['remision'] : 'RB-' . str_pad((string) $r['id_registro'], 6, '0', STR_PAD_LEFT)) ?></span>
                                <?php if (!empty($r['cotizacion'])): ?>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">COT: <?= htmlspecialchars($r['cotizacion']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 max-w-[220px]">
                                <div class="truncate" title="<?= htmlspecialchars($r['novedad'] ?? '') ?>">
                                    <?= htmlspecialchars($r['novedad'] ?? '—') ?>
                                </div>
                                <?php if (!empty($r['observacion'])): ?>
                                    <span class="text-xs text-sky-600 dark:text-sky-400 cursor-help" title="<?= htmlspecialchars($r['observacion']) ?>">
                                        <i class="fas fa-circle-info mr-1"></i>Ver observación
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap"><?= htmlspecialchars($r['usuario_registra'] ?? 'N/A') ?></td>
                            <td class="px-4 py-3">
                                <?php if ($esAnulado): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200">
                                        ANULADO
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                        ACTIVO
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <?php if ($esAdmin && !$esAnulado): ?>
                                    <a href="<?= BASE_URL ?>registroBodegaEditar/<?= (int) $r['id_registro'] ?>"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 hover:bg-sky-200 dark:bg-sky-900/40 dark:text-sky-300 transition-colors"
                                        title="Editar registro">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>registroBodegaEliminar/<?= (int) $r['id_registro'] ?>"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/40 dark:text-red-300 transition-colors ml-1"
                                        title="Anular registro">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.min.js"></script>

<script>
    const registrosRB = <?= json_encode($registros, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const gruposRB = <?= json_encode($grupos, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const kpisRB = <?= json_encode($kpis, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    function rbEscaparHtml(texto) {
        return String(texto).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function rbNumero(valor) {
        return new Intl.NumberFormat('es-CO', { maximumFractionDigits: 2 }).format(Number(valor) || 0);
    }

    function rbNumeroDocumento(registro) {
        if (registro.remision && String(registro.remision).trim() !== '') {
            return registro.remision;
        }
        return 'RB-' + String(registro.id_registro).padStart(6, '0');
    }

    function rbFechaExport(valor) {
        if (!valor) return '';
        const partes = String(valor).split(' ');
        const fecha = (partes[0] || '').split('-');
        const hora = (partes[1] || '').slice(0, 5);
        if (fecha.length !== 3) return valor;
        return fecha[2] + '/' + fecha[1] + '/' + fecha[0] + (hora ? ' ' + hora : '');
    }

    $(document).ready(function () {
        const tabla = document.getElementById('tablaRegistros');
        if (!tabla) return;

        $('#tablaRegistros').DataTable({
            order: [[2, 'desc']],
            orderFixed: { pre: [[0, 'asc']] },
            pageLength: 25,
            columnDefs: [
                { targets: 0, visible: false, searchable: false },
                { targets: 11, orderable: false, searchable: false }
            ],
            rowGroup: {
                dataSrc: 0,
                startRender: function (rows, group) {
                    const info = gruposRB[group] || { label: group, items: rows.count(), total: 0, anulados: 0 };
                    let html = '<div class="flex flex-wrap items-center gap-x-4 gap-y-1">'
                        + '<span class="font-bold"><i class="fas fa-file-invoice mr-2"></i>' + rbEscaparHtml(info.label) + '</span>'
                        + '<span class="text-[11px] font-semibold bg-white/80 dark:bg-gray-900/50 px-2 py-0.5 rounded-full">' + info.items + ' ítem(s)</span>'
                        + '<span class="text-[11px] font-semibold bg-white/80 dark:bg-gray-900/50 px-2 py-0.5 rounded-full">Total: ' + rbNumero(info.total) + '</span>';

                    if (Number(info.anulados) > 0) {
                        html += '<span class="text-[11px] font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 px-2 py-0.5 rounded-full">'
                            + info.anulados + ' anulado(s)</span>';
                    }

                    html += '</div>';
                    return $('<tr></tr>').append($('<th></th>').attr('colspan', 12).html(html));
                }
            },
            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros en total)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados para los filtros aplicados",
                emptyTable: "Aún no hay registros de entradas / salidas de bodega",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                }
            },
            dom: '<"flex flex-wrap justify-between items-center mb-4"lf>rt<"flex flex-wrap justify-between items-center mt-4"ip>'
        });
    });
    function exportarPdf() {
        const params = new URLSearchParams(window.location.search);
        params.set('accion', 'generarPdf');
        window.open('<?= BASE_URL ?>registroBodegaVer?' + params.toString(), '_blank');
    }

    function exportarExcel() {
        if (!registrosRB.length) {
            alert('No hay datos para exportar con los filtros actuales.');
            return;
        }

        const filas = registrosRB.map(function (r) {
            return {
                'Tipo': r.tipo_movimiento,
                'Fecha': rbFechaExport(r.fecha_movimiento),
                'Documento': rbNumeroDocumento(r),
                'Cotización': r.cotizacion || '',
                'Artículo': r.nombre_articulo,
                'Código / Serial': r.codigo_referencia || '',
                'Cantidad': Number(r.cantidad) || 0,
                'Bodega': r.nombre_bodega || '',
                'Destino / Procedencia': r.destino || '',
                'Novedad': r.novedad || '',
                'Observación': r.observacion || '',
                'Registra': r.usuario_registra || '',
                'Estado': r.estado
            };
        });

        const hojaRegistro = XLSX.utils.json_to_sheet(filas);
        hojaRegistro['!cols'] = [
            { wch: 10 }, { wch: 17 }, { wch: 18 }, { wch: 14 }, { wch: 34 },
            { wch: 18 }, { wch: 10 }, { wch: 24 }, { wch: 30 }, { wch: 32 },
            { wch: 42 }, { wch: 24 }, { wch: 11 }
        ];

        const resumen = [
            ['RESUMEN DEL REGISTRO DE BODEGA', ''],
            ['Generado', new Date().toLocaleString('es-CO')],
            ['Entradas (cantidad)', Number(kpisRB.total_entradas) || 0],
            ['Salidas (cantidad)', Number(kpisRB.total_salidas) || 0],
            ['Registros activos', Number(kpisRB.total_items) || 0],
            ['Remisiones distintas', Number(kpisRB.total_remisiones) || 0],
            ['Registros anulados', Number(kpisRB.total_anulados) || 0],
            ['Nota', 'Este módulo es un libro manual y no modifica el stock del sistema.']
        ];
        const hojaResumen = XLSX.utils.aoa_to_sheet(resumen);
        hojaResumen['!cols'] = [{ wch: 32 }, { wch: 70 }];

        const libro = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(libro, hojaResumen, 'Resumen');
        XLSX.utils.book_append_sheet(libro, hojaRegistro, 'Registro');

        const hoy = new Date().toISOString().slice(0, 10);
        XLSX.writeFile(libro, 'RegistroBodega_' + hoy + '.xlsx');
    }

</script>




