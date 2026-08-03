<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Entradas y Salidas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        .page-break {
            page-break-after: always;
            break-after: page;
        }

        .avoid-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .card-wrapper {
            display: block;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 1.5rem;
        }

        .card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            padding: 24px;
        }

        table.reporte-tabla tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    </style>
</head>

<body class="p-4 text-slate-800">

    <!-- PORTADA / ENCABEZADO -->
    <div class="card-wrapper avoid-break">
        <div class="card flex justify-between items-center border-t-8 border-orange-500 relative overflow-hidden">
            <div>
                <h1 class="text-4xl font-black text-orange-600 tracking-tight">HISTORIAL DE <span
                        class="text-slate-800">MOVIMIENTOS</span></h1>
                <p class="text-slate-500 mt-2 font-medium">Detalle oficial de entradas y salidas de inventario.</p>
                <div
                    class="mt-3 inline-block bg-orange-50 border border-orange-100 px-4 py-1.5 rounded-md text-sm font-bold text-orange-700">
                    Corte al: <?= $fechaReporte ?>
                </div>
            </div>
            <?php if (!empty($logoBase64)): ?>
                <img src="<?= $logoBase64 ?>" class="h-16 object-contain" alt="Logo">
            <?php endif; ?>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-5 gap-4 mb-6 avoid-break">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl">📥</div>
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400">Total Entradas</p>
                <p class="text-2xl font-black text-emerald-700"><?= number_format($kpis['total_entradas']) ?></p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 text-2xl">📤</div>
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400">Total Salidas</p>
                <p class="text-2xl font-black text-rose-700"><?= number_format($kpis['total_salidas']) ?></p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl">⚖️</div>
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400">Balance Neto</p>
                <p class="text-2xl font-black <?= $kpis['balance_neto'] >= 0 ? 'text-blue-700' : 'text-rose-700' ?>">
                    <?= ($kpis['balance_neto'] >= 0 ? '+' : '') . number_format($kpis['balance_neto']) ?>
                </p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-2xl">🔖</div>
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400">Referencias Movidas</p>
                <p class="text-2xl font-black text-slate-800"><?= $kpis['referencias_distintas'] ?></p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 text-2xl">🧾</div>
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400">Movimientos Totales</p>
                <p class="text-2xl font-black text-slate-800"><?= $kpis['movimientos_totales'] ?></p>
            </div>
        </div>
    </div>

    <?php
    // Función local para renderizar cada bloque (Salidas / Entradas)
    function renderTablaMovimientos($titulo, $colorTitulo, $colorHeader, $filas, $fechaUnica, $etiquetaUltima)
    {
        if (empty($filas)) {
            return;
        }
        ?>
        <div class="card-wrapper">
            <h3 class="text-2xl font-black <?= $colorTitulo ?> mb-1 px-1"><?= $titulo ?></h3>
            <?php if ($fechaUnica): ?>
                <p class="text-sm text-slate-500 font-medium mb-3 px-1">Fecha: <?= $fechaUnica ?></p>
            <?php else: ?>
                <p class="text-sm text-slate-500 font-medium mb-3 px-1">&nbsp;</p>
            <?php endif; ?>

            <div class="card">
                <table class="reporte-tabla w-full text-xs text-left">
                    <thead class="<?= $colorHeader ?> text-white font-bold uppercase text-[9px]">
                        <tr>
                            <?php if (!$fechaUnica): ?>
                                <th class="py-2 px-3 rounded-tl-lg">Fecha</th>
                            <?php endif; ?>
                            <th class="py-2 px-3 <?= $fechaUnica ? 'rounded-tl-lg' : '' ?>">Repuesto / Artículo</th>
                            <th class="py-2 px-3 text-center">Cant.</th>
                            <th class="py-2 px-3">Novedad</th>
                            <th class="py-2 px-3">Destino</th>
                            <th class="py-2 px-3">N° Remisión</th>
                            <th class="py-2 px-3"><?= $etiquetaUltima ?></th>
                            <th class="py-2 px-3 rounded-tr-lg">Registrado Por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($filas as $fila): ?>
                            <tr class="hover:bg-slate-50">
                                <?php if (!$fechaUnica): ?>
                                    <td class="py-1.5 px-3 whitespace-nowrap"><?= $fila['fecha'] ?></td>
                                <?php endif; ?>
                                <td class="py-1.5 px-3 font-medium text-slate-700"><?= htmlspecialchars($fila['articulo']) ?></td>
                                <td class="py-1.5 px-3 text-center font-black text-slate-800"><?= $fila['cantidad'] ?></td>
                                <td class="py-1.5 px-3 text-slate-600"><?= htmlspecialchars($fila['novedad']) ?></td>
                                <td class="py-1.5 px-3 text-slate-600"><?= htmlspecialchars($fila['destino']) ?></td>
                                <td class="py-1.5 px-3 text-slate-600"><?= htmlspecialchars($fila['remision']) ?></td>
                                <td class="py-1.5 px-3 text-slate-600"><?= htmlspecialchars($fila['cotizacion']) ?></td>
                                <td class="py-1.5 px-3 text-slate-600"><?= htmlspecialchars($fila['usuario']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    ?>

    <!-- SALIDAS -->
    <?php if (!empty($salidas)): ?>
        <?php renderTablaMovimientos('REPORTE DE SALIDAS', 'text-rose-600', 'bg-rose-600', $salidas, $fechaUnicaSalidas, 'N° Cotización'); ?>
    <?php endif; ?>

    <!-- SALTO DE PÁGINA ENTRE SALIDAS Y ENTRADAS -->
    <?php if (!empty($salidas) && !empty($entradas)): ?>
        <div class="page-break"></div>
    <?php endif; ?>

    <!-- ENTRADAS -->
    <?php if (!empty($entradas)): ?>
        <?php renderTablaMovimientos('REPORTE DE ENTRADAS', 'text-emerald-600', 'bg-emerald-600', $entradas, $fechaUnicaEntradas, 'Orden de Compra'); ?>
    <?php endif; ?>

    <!-- SIN DATOS -->
    <?php if (empty($salidas) && empty($entradas)): ?>
        <div class="card-wrapper avoid-break">
            <div class="card text-center text-slate-400 font-bold py-10">
                No se encontraron movimientos para el rango de fechas seleccionado.
            </div>
        </div>
    <?php endif; ?>

</body>

</html>