<!-- app/views/reportes/reporteInventarioGenerar.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        @page { size: A4 landscape; margin: 10mm; }
        .avoid-break { page-break-inside: avoid; break-inside: avoid; }
        .card-wrapper { display: block; width: 100%; box-sizing: border-box; margin-bottom: 1.5rem; }
        .card { background-color: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb; padding: 24px; }
        table.reporte-tabla tr { page-break-inside: avoid; break-inside: avoid; }
    </style>
</head>

<body class="p-4 text-slate-800">

    <!-- PORTADA / ENCABEZADO -->
    <div class="card-wrapper avoid-break">
        <div class="card flex justify-between items-center border-t-8 border-blue-600 relative overflow-hidden">
            <div>
                <h1 class="text-4xl font-black text-blue-700 tracking-tight">REPORTE DE <span class="text-slate-800">INVENTARIO</span></h1>
                <p class="text-slate-500 mt-2 font-medium">Condición filtrada: <span class="text-blue-600 font-bold"><?= $etiquetaCondicion ?></span></p>
                <div class="mt-3 inline-block bg-blue-50 border border-blue-100 px-4 py-1.5 rounded-md text-sm font-bold text-blue-700">
                    Corte al: <?= $fechaReporte ?>
                </div>
            </div>
            <?php if (!empty($logoBase64)): ?>
                <img src="<?= $logoBase64 ?>" class="h-16 object-contain" alt="Logo">
            <?php endif; ?>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 gap-4 mb-6 avoid-break">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-2xl">📦</div>
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400">Total Referencias Distintas</p>
                <p class="text-2xl font-black text-indigo-700"><?= number_format($kpis['total_referencias']) ?></p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl">🔢</div>
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400">Unidades Totales en Stock</p>
                <p class="text-2xl font-black text-emerald-700"><?= number_format($kpis['stock_total']) ?></p>
            </div>
        </div>
    </div>

    <!-- TABLA DE DATOS -->
    <?php if (!empty($inventario)): ?>
        <div class="card-wrapper">
            <div class="card p-0 overflow-hidden">
                <table class="reporte-tabla w-full text-xs text-left">
                    <thead class="bg-slate-800 text-white font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Código / Ref</th>
                            <th class="py-3 px-4">Nombre del Repuesto</th>
                            <th class="py-3 px-4 text-center">Condición</th>
                            <th class="py-3 px-4 text-center">Stock Actual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($inventario as $item): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 px-4 font-mono text-blue-600 font-bold"><?= !empty($item['codigo_referencia']) ? htmlspecialchars($item['codigo_referencia']) : 'N/A' ?></td>
                                <td class="py-2 px-4 font-medium text-slate-700"><?= htmlspecialchars($item['nombre_repuesto']) ?></td>
                                <td class="py-2 px-4 text-center text-slate-600 uppercase text-[10px] font-bold"><?= htmlspecialchars($item['condicion']) ?></td>
                                <td class="py-2 px-4 text-center font-black text-lg <?= $item['cantidad_en_stock'] > 0 ? 'text-slate-800' : 'text-rose-600' ?>">
                                    <?= $item['cantidad_en_stock'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="card-wrapper avoid-break">
            <div class="card text-center text-slate-400 font-bold py-10">
                No se encontraron repuestos con la condición "<?= strtolower($etiquetaCondicion) ?>".
            </div>
        </div>
    <?php endif; ?>

</body>
</html>