<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

$registro = $data['registro'] ?? [];
$id = (int) ($data['id_registro'] ?? 0);
$esAnulado = (($registro['estado'] ?? '') === 'ANULADO');
$cantidadFmt = rtrim(rtrim(number_format((float) ($registro['cantidad'] ?? 0), 2, ',', '.'), '0'), ',');
$documento = !empty($registro['remision'])
    ? $registro['remision']
    : 'RB-' . str_pad((string) ($registro['id_registro'] ?? 0), 6, '0', STR_PAD_LEFT);
?>

<div class="w-full px-4 md:px-6">
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-ban text-red-500 mr-2"></i> Anular Registro de Bodega
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                El registro no se borra: queda marcado como ANULADO y sigue visible para auditoría.
            </p>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-700 dark:text-red-400" role="alert">
                <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <?php if ($esAnulado): ?>
            <div class="mb-4 p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-700 dark:text-yellow-300" role="alert">
                <i class="fas fa-circle-exclamation mr-1"></i>
                Este registro ya está ANULADO. No se puede volver a anular.
            </div>
        <?php else: ?>
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-700 dark:text-red-400" role="alert">
                <i class="fas fa-triangle-exclamation mr-1"></i>
                ¿Confirmas la anulación de este registro? Esta acción no modifica el stock del sistema.
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <tbody>
                    <tr class="border-b dark:border-gray-700">
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold w-1/3">Tipo</th>
                        <td class="px-4 py-2.5">
                            <?php if (($registro['tipo_movimiento'] ?? '') === 'ENTRADA'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">ENTRADA</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300">SALIDA</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold">Fecha</th>
                        <td class="px-4 py-2.5"><?= !empty($registro['fecha_movimiento']) ? date('d/m/Y H:i', strtotime($registro['fecha_movimiento'])) : '—' ?></td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold">Artículo</th>
                        <td class="px-4 py-2.5 font-semibold text-gray-900 dark:text-white">
                            <?= htmlspecialchars($registro['nombre_articulo'] ?? '') ?>
                            <?php if (!empty($registro['codigo_referencia'])): ?>
                                <span class="block text-xs font-normal text-gray-500 dark:text-gray-400">Código: <?= htmlspecialchars($registro['codigo_referencia']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold">Cantidad</th>
                        <td class="px-4 py-2.5 font-bold"><?= $cantidadFmt === '' ? '0' : $cantidadFmt ?></td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold">Bodega</th>
                        <td class="px-4 py-2.5"><?= htmlspecialchars($registro['nombre_bodega'] ?? '—') ?></td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold">Destino / Procedencia</th>
                        <td class="px-4 py-2.5"><?= htmlspecialchars($registro['destino'] ?? '—') ?></td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold">Documento</th>
                        <td class="px-4 py-2.5">
                            <?= htmlspecialchars($documento) ?>
                            <?php if (!empty($registro['cotizacion'])): ?>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">COT: <?= htmlspecialchars($registro['cotizacion']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold">Novedad</th>
                        <td class="px-4 py-2.5"><?= htmlspecialchars($registro['novedad'] ?? '—') ?></td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold">Observación</th>
                        <td class="px-4 py-2.5 whitespace-pre-line"><?= htmlspecialchars($registro['observacion'] ?? '—') ?></td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold">Registra</th>
                        <td class="px-4 py-2.5"><?= htmlspecialchars($registro['usuario_registra'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 font-semibold">Estado actual</th>
                        <td class="px-4 py-2.5">
                            <?php if ($esAnulado): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200">ANULADO</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">ACTIVO</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
        <?php if (!$esAnulado): ?>
            <form action="<?= BASE_URL ?>registroBodegaEliminar/<?= $id ?>" method="POST"
                class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <input type="hidden" name="id_registro" value="<?= $id ?>">
                <input type="hidden" name="confirmar" value="1">

                <a href="<?= BASE_URL ?>registroBodegaVer"
                    class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white font-bold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-center">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-red-600 text-white font-bold rounded-lg shadow-md hover:bg-red-700 transition-transform transform hover:scale-105">
                    <i class="fas fa-ban mr-2"></i> Sí, anular registro
                </button>
            </form>
        <?php else: ?>
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="<?= BASE_URL ?>registroBodegaVer"
                    class="px-6 py-2.5 bg-gray-600 text-white font-bold rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Volver al listado
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>
