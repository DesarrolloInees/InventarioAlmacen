<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
$b = $data['bodega'];
?>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 max-w-2xl mx-auto">
        
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-edit text-orange-500 mr-2"></i> Editar Bodega #<?= htmlspecialchars($b['id_bodega']) ?>
                </h1>
            </div>
            <a href="<?= BASE_URL ?>bodegaVer" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">¡Error!</span> <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>bodegaEditar/<?= $b['id_bodega'] ?>" method="POST" class="space-y-6">
            
            <div>
                <label for="nombre_bodega" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre de la Bodega <span class="text-red-500">*</span></label>
                <input type="text" name="nombre_bodega" id="nombre_bodega" required value="<?= htmlspecialchars($b['nombre_bodega']) ?>"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label for="ubicacion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion" value="<?= htmlspecialchars($b['ubicacion']) ?>"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label for="estado" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
                <select name="estado" id="estado" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="1" <?= $b['estado'] == 1 ? 'selected' : '' ?>>Activa</option>
                    <option value="0" <?= $b['estado'] == 0 ? 'selected' : '' ?>>Inactiva</option>
                </select>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-bold rounded-lg shadow-md hover:bg-orange-600 transition-transform transform hover:scale-105">
                    <i class="fas fa-sync-alt mr-2"></i> Actualizar Bodega
                </button>
            </div>
        </form>
    </div>
</div>