<?php
// app/views/tipoMaquina/tipoMaquinaEditarVista.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
?>

<div class="w-full max-w-3xl mx-auto px-4 md:px-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-edit text-yellow-500 dark:text-yellow-400 mr-2"></i> Editar Máquina
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-1">Modifica los detalles del modelo o terminal
                #<?= htmlspecialchars($idTipoMaquina) ?></p>
        </div>

        <?php if (!empty($errores)): ?>
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-6 rounded shadow-sm"
                role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
                    <p class="font-bold">¡Error al guardar!</p>
                </div>
                <ul class="list-disc list-inside ml-6 mt-1 text-sm">
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">

            <input type="hidden" name="idTipoMaquina" value="<?= htmlspecialchars($idTipoMaquina) ?>">

            <!-- Fila 1: Nombre de la máquina -->
            <div>
                <label for="nombreTipoMaquina" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Nombre del Tipo de Máquina <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-microchip text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <input type="text" id="nombreTipoMaquina" name="nombreTipoMaquina" required
                        value="<?= htmlspecialchars($datosMaquina['nombreTipoMaquina'] ?? '') ?>"
                        class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 focus:border-yellow-500 dark:focus:border-yellow-400 transition-all">
                </div>
            </div>

            <!-- Fila 2: Estado -->
            <div>
                <label for="estado" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                    Estado
                </label>
                <div class="relative w-full md:w-1/2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-toggle-on text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <select id="estado" name="estado"
                        class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 focus:border-yellow-500 dark:focus:border-yellow-400 transition-all appearance-none cursor-pointer">
                        <?php $estadoPrevio = $datosMaquina['estado'] ?? 'activo'; ?>
                        <option value="activo" <?= strtolower($estadoPrevio) === 'activo' ? 'selected' : '' ?>>Activo
                        </option>
                        <option value="inactivo" <?= strtolower($estadoPrevio) === 'inactivo' ? 'selected' : '' ?>>Inactivo
                        </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>tipoMaquinaVer"
                    class="px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 dark:focus:ring-gray-700">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>

                <button type="submit"
                    class="px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg shadow-md transition-all transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-offset-gray-800">
                    <i class="fas fa-save mr-2"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>