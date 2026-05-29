<?php
// app/views/tipoMaquina/tipoMaquinaCrearVista.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
?>

<div class="w-full max-w-3xl mx-auto px-4 md:px-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-server text-blue-600 dark:text-blue-500 mr-2"></i> Crear Tipo de Máquina
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 ml-1 text-sm">Registra un nuevo modelo o terminal en el
                    sistema.</p>
            </div>
        </div>

        <?php if (!empty($errores)): ?>
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-6 rounded shadow-sm animate-pulse"
                role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2 text-lg"></i>
                    <p class="font-bold">¡Atención!</p>
                </div>
                <ul class="list-disc list-inside ml-6 mt-1 text-sm">
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>tipoMaquinaCrear" method="POST" class="space-y-6">

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
                        placeholder="Ej: SDM-100, SDM-500"
                        value="<?= htmlspecialchars($datosPrevios['nombreTipoMaquina'] ?? '') ?>"
                        class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <p class="text-xs text-gray-400 mt-1">Nombre descriptivo del equipo o terminal.</p>
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
                        class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none cursor-pointer">
                        <?php $estadoPrevio = $datosPrevios['estado'] ?? 'activo'; ?>
                        <option value="activo" <?= $estadoPrevio === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= $estadoPrevio === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>tipoMaquinaVer"
                    class="px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 dark:focus:ring-gray-700">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>

                <button type="submit"
                    class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-lg hover:bg-blue-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-all duration-300 transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i> Guardar Máquina
                </button>
            </div>
        </form>
    </div>
</div>