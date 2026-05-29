<?php
// app/views/salida/salidaEditarVista.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

// Detectamos si es motorizado para mostrar la alerta
$esMotorizado = strpos(strtolower($datosSalida['observacion']), 'motorizado') !== false;
?>

<div class="w-full max-w-4xl mx-auto px-4 md:px-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-edit text-yellow-500 dark:text-yellow-400 mr-2"></i> Editar Asignación / Salida
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-1 text-sm">Ajusta la cantidad entregada. Los inventarios
                se sincronizarán automáticamente.</p>
        </div>

        <?php if (!empty($errores)): ?>
            <div
                class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-6 rounded shadow-sm">
                <div class="flex items-center"><i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
                    <p class="font-bold">¡Error al guardar!</p>
                </div>
                <ul class="list-disc list-inside ml-6 mt-1 text-sm">
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($esMotorizado): ?>
            <div
                class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-400 p-4 mb-6 rounded text-sm">
                <i class="fas fa-motorcycle mr-2"></i> <b>Atención:</b> Esta es una asignación a un motorizado. Al cambiar
                la cantidad, el sistema intentará ajustar el inventario en la aplicación externa también.
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">

            <input type="hidden" name="id_movimiento" value="<?= htmlspecialchars($idMovimiento) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Técnico (Solo Lectura) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Técnico Destino (No modificable)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-user-wrench text-gray-400"></i></div>
                        <input type="text" readonly disabled
                            value="<?= htmlspecialchars($datosSalida['tecnico_nombre'] ?? 'Desconocido') ?>"
                            class="pl-10 mt-1 block w-full px-3 py-3 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 rounded-lg cursor-not-allowed">
                    </div>
                </div>

                <!-- Repuesto (Solo Lectura) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Repuesto (No modificable)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-box text-gray-400"></i></div>
                        <input type="text" readonly disabled
                            value="[<?= htmlspecialchars($datosSalida['codigo_referencia'] ?? '') ?>] <?= htmlspecialchars($datosSalida['nombre_repuesto']) ?>"
                            class="pl-10 mt-1 block w-full px-3 py-3 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 rounded-lg cursor-not-allowed truncate">
                    </div>
                </div>

                <!-- Cantidad Editable -->
                <div class="md:col-span-2">
                    <label for="cantidad" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Cantidad Entregada <span class="text-red-500">*</span>
                    </label>
                    <div class="relative w-full md:w-1/2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-cubes text-gray-400"></i></div>
                        <input type="number" id="cantidad" name="cantidad" min="1" required
                            value="<?= htmlspecialchars($datosSalida['cantidad'] ?? '') ?>"
                            class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500 focus:border-yellow-500 text-lg font-bold">
                    </div>
                </div>

                <!-- Observación Editable -->
                <div class="md:col-span-2">
                    <label for="observacion" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Observaciones / Notas
                    </label>
                    <textarea id="observacion" name="observacion" rows="3"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500 focus:border-yellow-500"><?= htmlspecialchars($datosSalida['observacion'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>salidaVer"
                    class="px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </a>

                <button type="submit"
                    class="px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg shadow-md transform hover:-translate-y-1">
                    <i class="fas fa-sync-alt mr-2"></i> Recalcular y Guardar
                </button>
            </div>
        </form>
    </div>
</div>