<?php
// app/views/recuperacion/recuperacionVerVista.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
$esAdmin = (($_SESSION['nivel_acceso'] ?? 0) == 1 || ($_SESSION['nivel_acceso'] ?? 0) == 2);
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700">
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white"><i
                        class="fas fa-recycle text-amber-500 mr-2"></i>Recuperados por Tecnicos</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Solo entradas RECUPERADO. Las compras no aparecen aqui.
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="<?= BASE_URL ?>recuperacionCrear"
                    class="px-5 py-2.5 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600"><i
                        class="fas fa-plus"></i> Nueva entrada</a>
            </div>
        </div>
        <?php if (isset($data['error'])): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <?= htmlspecialchars($data['error']) ?>
            </div><?php endif; ?>
        <?php if (isset($data['exito'])): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <?= htmlspecialchars($data['exito']) ?>
            </div><?php endif; ?>
        <?php if (!empty($data['recuperados'])): ?>
            <div class="overflow-x-auto">
                <table id="recupTable" class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="py-3 px-4">Fecha</th>
                            <th class="py-3 px-4">Articulo</th>
                            <th class="py-3 px-4 text-center">Cant.</th>
                            <th class="py-3 px-4">Serial</th>
                            <th class="py-3 px-4">Tecnico</th>
                            <th class="py-3 px-4">Novedad</th>
                            <th class="py-3 px-4">Registro</th><?php if ($esAdmin): ?>
                                <th class="py-3 px-4 text-center">Acciones</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['recuperados'] as $c): ?>
                            <tr class="border-b dark:border-gray-700">
                                <td class="py-3 px-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($c['fecha_movimiento'])) ?>
                                </td>
                                <td class="py-3 px-4"><?php if (!empty($c['id_repuesto'])): ?><span
                                            class="font-bold text-blue-600"><?= htmlspecialchars($c['nombre_repuesto']) ?></span>
                                        <div class="text-[11px] text-gray-400">Cod:
                                            <?= htmlspecialchars($c['codigo_referencia'] ?? 'S/C') ?>
                                        </div>
                                    <?php elseif (!empty($c['id_producto'])): ?><span
                                            class="font-bold text-purple-600"><?= htmlspecialchars($c['nombre_producto']) ?></span><?php else: ?><span
                                            class="font-bold text-amber-600"><?= htmlspecialchars($c['repuesto_manual'] ?? 'MANUAL') ?></span>
                                        <div class="text-[11px] text-gray-400">Sin catalogo (solo historial)</div><?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-green-600">
                                    +<?= htmlspecialchars($c['cantidad']) ?></td>
                                <td class="py-3 px-4 font-mono text-xs">
                                    <?= htmlspecialchars($c['serial_recuperado'] ?? 'S/S') ?>
                                </td>
                                <td class="py-3 px-4"><?= htmlspecialchars($c['tecnico_origen'] ?? 'N/A') ?></td>
                                <td class="py-3 px-4 text-xs"><?= htmlspecialchars($c['novedad'] ?: '—') ?></td>
                                <td class="py-3 px-4 text-xs text-gray-400"><?= htmlspecialchars($c['usuario_nombre']) ?></td>
                                <?php if ($esAdmin): ?>
                                    <td class="py-3 px-4 text-center"><a
                                            href="<?= BASE_URL ?>recuperacionVer?accion=eliminar&id=<?= $c['id_movimiento'] ?>"
                                            onclick="return confirm('Anular esta entrada y restar del stock?')"
                                            class="p-2 bg-red-100 text-red-600 rounded-full" title="Anular"><i
                                                class="fas fa-trash-alt text-xs"></i></a></td><?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center p-10 border-2 border-dashed rounded-lg text-gray-500">Aun no hay entradas recuperadas.
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>document.addEventListener('DOMContentLoaded', function () { if (window.jQuery && jQuery('#recupTable').length) { jQuery('#recupTable').DataTable({ order: [[0, 'desc']], language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' } }); } });</script>