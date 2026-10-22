<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

$agrupados = $data['agrupados'] ?? [];
$grupos = $data['grupos'] ?? [];
$kpis = $data['kpis'] ?? [];
$filtrosTexto = $data['filtros_texto'] ?? 'Todos los registros';
$logo = $data['logo'] ?? '';
$generado = $data['generado'] ?? date('d/m/Y H:i');
$usuario = $data['usuario'] ?? 'Sistema';

if (!function_exists('pdfCantidad')) {
    function pdfCantidad($valor)
    {
        $texto = number_format((float) $valor, 2, ',', '.');
        $texto = rtrim(rtrim($texto, '0'), ',');
        return $texto === '' ? '0' : $texto;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Entradas / Salidas de Bodega</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
            color: #1f2937;
            font-size: 9px;
            margin: 0;
            padding: 0;
        }
        .cabecera {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #f97316;
            padding-bottom: 7px;
            margin-bottom: 8px;
        }
        .marca { display: flex; align-items: center; gap: 10px; }
        .marca img { height: 38px; width: auto; }
        .marca h1 { font-size: 15px; margin: 0; color: #111827; text-transform: uppercase; letter-spacing: .4px; }
        .marca p { margin: 2px 0 0 0; font-size: 8.5px; color: #6b7280; }
        .meta { text-align: right; font-size: 8px; color: #4b5563; line-height: 1.5; }
        .meta .tipo { font-size: 9px; font-weight: bold; color: #ea580c; text-transform: uppercase; letter-spacing: .5px; }

        .filtros {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #f97316;
            padding: 4px 8px;
            font-size: 8.5px;
            color: #475569;
            margin-bottom: 7px;
        }

        .kpis { display: flex; gap: 6px; margin-bottom: 8px; }
        .kpi { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-left: 4px solid #9ca3af; padding: 4px 7px; }
        .kpi.verde { border-left-color: #16a34a; }
        .kpi.naranja { border-left-color: #f97316; }
        .kpi.azul { border-left-color: #0284c7; }
        .kpi.indigo { border-left-color: #4f46e5; }
        .kpi .rotulo { font-size: 6.8px; font-weight: bold; text-transform: uppercase; color: #6b7280; letter-spacing: .3px; }
        .kpi .valor { font-size: 13px; font-weight: bold; color: #111827; line-height: 1.15; }

        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }
        thead th {
            background: #1f2937;
            color: #ffffff;
            font-size: 7.2px;
            text-transform: uppercase;
            letter-spacing: .3px;
            padding: 5px 4px;
            border: 1px solid #374151;
            text-align: left;
        }
        tbody td {
            border: 1px solid #e5e7eb;
            padding: 4px;
            vertical-align: top;
            font-size: 8px;
            line-height: 1.3;
        }
        tbody tr { page-break-inside: avoid; }
        tr.grupo td {
            background: #fff7ed;
            border-top: 2px solid #fdba74;
            border-bottom: 1px solid #fed7aa;
            color: #9a3412;
            font-weight: bold;
            font-size: 8.2px;
            padding: 5px 6px;
        }
        tr.anulado td { background: #f9fafb; color: #6b7280; }
        .mini { font-size: 7px; color: #6b7280; }
        .derecha { text-align: right; }
        .centro { text-align: center; }
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 8px;
            font-size: 6.8px;
            font-weight: bold;
            border: 1px solid #d1d5db;
            background: #f3f4f6;
            color: #374151;
        }
        .badge.entrada { background: #dcfce7; color: #166534; border-color: #86efac; }
        .badge.salida { background: #ffedd5; color: #9a3412; border-color: #fdba74; }
        .badge.anulado { background: #e5e7eb; color: #4b5563; }
        .nota { margin-top: 7px; font-size: 7px; color: #6b7280; }
        .sin-datos { text-align: center; padding: 18px; color: #6b7280; font-size: 9px; }
    </style>
</head>
<body>
    <div class="cabecera">
        <div class="marca">
            <?php if ($logo !== ''): ?>
                <img src="<?= $logo ?>" alt="Logo">
            <?php endif; ?>
            <div>
                <h1>Registro de Entradas / Salidas de Bodega</h1>
                <p>Libro manual de novedades &middot; No modifica el stock del sistema</p>
            </div>
        </div>
        <div class="meta">
            <div class="tipo">Reporte de bodega</div>
            <div><strong>Generado:</strong> <?= htmlspecialchars($generado) ?></div>
            <div><strong>Usuario:</strong> <?= htmlspecialchars($usuario) ?></div>
            <div><strong>Total filas:</strong> <?= count($data['registros'] ?? []) ?></div>
        </div>
    </div>

    <div class="filtros"><strong>Filtros aplicados:</strong> <?= htmlspecialchars($filtrosTexto) ?></div>

    <div class="kpis">
        <div class="kpi verde">
            <div class="rotulo">Entradas</div>
            <div class="valor"><?= pdfCantidad($kpis['total_entradas'] ?? 0) ?></div>
        </div>
        <div class="kpi naranja">
            <div class="rotulo">Salidas</div>
            <div class="valor"><?= pdfCantidad($kpis['total_salidas'] ?? 0) ?></div>
        </div>
        <div class="kpi azul">
            <div class="rotulo">Ítems activos</div>
            <div class="valor"><?= (int) ($kpis['total_items'] ?? 0) ?></div>
        </div>
        <div class="kpi indigo">
            <div class="rotulo">Remisiones</div>
            <div class="valor"><?= (int) ($kpis['total_remisiones'] ?? 0) ?></div>
        </div>
        <div class="kpi">
            <div class="rotulo">Anulados</div>
            <div class="valor"><?= (int) ($kpis['total_anulados'] ?? 0) ?></div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:5%">Tipo</th>
                <th style="width:9%">Fecha</th>
                <th style="width:17%">Artículo</th>
                <th style="width:6%" class="derecha">Cantidad</th>
                <th style="width:10%">Bodega</th>
                <th style="width:12%">Destino / Procedencia</th>
                <th style="width:9%">Documento</th>
                <th style="width:14%">Novedad</th>
                <th style="width:9%">Registra</th>
                <th style="width:4%" class="centro">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($agrupados)): ?>
                <tr>
                    <td colspan="10" class="sin-datos">No hay registros para los filtros seleccionados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($agrupados as $clave => $filas): ?>
                    <?php $g = $grupos[$clave] ?? null; ?>
                    <tr class="grupo">
                        <td colspan="10">
                            <?= htmlspecialchars($g['label'] ?? $clave) ?>
                            <span style="font-weight:normal;">
                                &nbsp;&middot;&nbsp; <?= (int) ($g['items'] ?? count($filas)) ?> ítem(s)
                                &middot; Total: <?= pdfCantidad($g['total'] ?? 0) ?>
                                <?php if (!empty($g['anulados'])): ?>
                                    &middot; <?= (int) $g['anulados'] ?> anulado(s)
                                <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                    <?php foreach ($filas as $r): ?>
                        <?php
                        $anulado = ($r['estado'] ?? '') === 'ANULADO';
                        $entrada = ($r['tipo_movimiento'] ?? '') === 'ENTRADA';
                        $documento = !empty($r['remision'])
                            ? $r['remision']
                            : 'RB-' . str_pad((string) $r['id_registro'], 6, '0', STR_PAD_LEFT);
                        ?>
                        <tr class="<?= $anulado ? 'anulado' : '' ?>">
                            <td>
                                <span class="badge <?= $entrada ? 'entrada' : 'salida' ?>"><?= $entrada ? 'ENTRADA' : 'SALIDA' ?></span>
                            </td>
                            <td><?= !empty($r['fecha_movimiento']) ? date('d/m/Y H:i', strtotime($r['fecha_movimiento'])) : '—' ?></td>
                            <td>
                                <strong><?= htmlspecialchars($r['nombre_articulo'] ?? '') ?></strong>
                                <?php if (!empty($r['codigo_referencia'])): ?>
                                    <br><span class="mini">Cód: <?= htmlspecialchars($r['codigo_referencia']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($r['observacion'])): ?>
                                    <br><span class="mini">Obs: <?= htmlspecialchars($r['observacion']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="derecha"><strong><?= pdfCantidad($r['cantidad'] ?? 0) ?></strong></td>
                            <td><?= htmlspecialchars($r['nombre_bodega'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($r['destino'] ?? '—') ?></td>
                            <td>
                                <?= htmlspecialchars($documento) ?>
                                <?php if (!empty($r['cotizacion'])): ?>
                                    <br><span class="mini">COT: <?= htmlspecialchars($r['cotizacion']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($r['novedad'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($r['usuario_registra'] ?? 'N/A') ?></td>
                            <td class="centro">
                                <span class="badge <?= $anulado ? 'anulado' : '' ?>"><?= $anulado ? 'ANULADO' : 'ACTIVO' ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="nota">Este reporte corresponde al libro manual de novedades de bodega y no altera el stock ni los movimientos de inventario del sistema.</div>


</body>
</html>
