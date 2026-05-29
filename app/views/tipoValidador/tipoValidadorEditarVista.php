<?php if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado."); ?>

<div class="w-full max-w-3xl mx-auto px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700">
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-edit text-yellow-500 dark:text-yellow-400 mr-2"></i> Editar Validador
            </h1>
        </div>

        <?php if (!empty($errores)): ?>
            <div
                class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-6 rounded">
                <p class="font-bold">¡Error!</p>
                <ul class="list-disc list-inside ml-6 text-sm">
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li> <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <input type="hidden" name="idTipoValidador" value="<?= htmlspecialchars($idTipoValidador) ?>">

            <div>
                <label for="nombreTipoValidador"
                    class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                            class="fas fa-credit-card text-gray-400"></i></div>
                    <input type="text" id="nombreTipoValidador" name="nombreTipoValidador" required
                        value="<?= htmlspecialchars($datosValidador['nombreTipoValidador'] ?? '') ?>"
                        class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                </div>
            </div>

            <div>
                <label for="estado" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                <div class="relative w-full md:w-1/2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                            class="fas fa-toggle-on text-gray-400"></i></div>
                    <select id="estado" name="estado"
                        class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg appearance-none">
                        <?php $est = $datosValidador['estado'] ?? 'activo'; ?>
                        <option value="activo" <?= strtolower($est) === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= strtolower($est) === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>tipoValidadorVer"
                    class="px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</a>
                <button type="submit"
                    class="px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg transform hover:-translate-y-1">Guardar
                    Cambios</button>
            </div>
        </form>
    </div>
</div>