<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
$h = $data['herramienta'];
?>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 max-w-3xl mx-auto">
        
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-edit text-amber-500 mr-2"></i> Editar Herramienta #<?= htmlspecialchars($h['id_herramienta']) ?>
                </h1>
            </div>
            <a href="<?= BASE_URL ?>herramientaVer" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">¡Error!</span> <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>herramientaEditar/<?= $h['id_herramienta'] ?>" method="POST" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre_herramienta" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre de la Herramienta <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre_herramienta" id="nombre_herramienta" required value="<?= htmlspecialchars($h['nombre_herramienta']) ?>"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="codigo_referencia" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Código de Referencia</label>
                    <input type="text" name="codigo_referencia" id="codigo_referencia" value="<?= htmlspecialchars($h['codigo_referencia'] ?? '') ?>"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Debe ser único si se usa.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="marca" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Marca</label>
                    <input type="text" name="marca" id="marca" value="<?= htmlspecialchars($h['marca'] ?? '') ?>"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="modelo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Modelo</label>
                    <input type="text" name="modelo" id="modelo" value="<?= htmlspecialchars($h['modelo'] ?? '') ?>"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="id_bodega" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ubicación (Bodega)</label>
                    <select name="id_bodega" id="id_bodega" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Sin asignar --</option>
                        <?php foreach ($data['bodegas'] as $b): ?>
                            <option value="<?= $b['id_bodega'] ?>" <?= $h['id_bodega'] == $b['id_bodega'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($b['nombre_bodega']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="condicion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Condición</label>
                    <select name="condicion" id="condicion" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="nueva" <?= $h['condicion'] == 'nueva' ? 'selected' : '' ?>>Nueva</option>
                        <option value="usada" <?= $h['condicion'] == 'usada' ? 'selected' : '' ?>>Usada</option>
                        <option value="reparacion" <?= $h['condicion'] == 'reparacion' ? 'selected' : '' ?>>En Reparación</option>
                        <option value="dañada" <?= $h['condicion'] == 'dañada' ? 'selected' : '' ?>>Dañada</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="stock" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock (unidades en almacén)</label>
                <input type="number" name="stock" id="stock" min="0" value="<?= (int)$h['stock'] ?>"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label for="descripcion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripción (Opcional)</label>
                <textarea name="descripcion" id="descripcion" rows="3"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"><?= htmlspecialchars($h['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-amber-500 text-white font-bold rounded-lg shadow-md hover:bg-amber-600 transition-transform transform hover:scale-105">
                    <i class="fas fa-sync-alt mr-2"></i> Actualizar Herramienta
                </button>
            </div>
        </form>
    </div>
</div>
