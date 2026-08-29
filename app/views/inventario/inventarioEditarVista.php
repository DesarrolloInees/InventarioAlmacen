<?php if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado."); ?>

<div class="w-full px-4 md:px-8 pb-8">

    <div class="mb-8">
        <a href="<?= BASE_URL ?>inventario"
            class="inline-flex items-center gap-2 text-sm text-blue-500 hover:text-blue-600 mb-3">
            <i class="fas fa-arrow-left"></i> Volver al inventario
        </a>
        <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center">
            <i class="fas fa-edit text-blue-500 mr-3"></i> Editar Inventario
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">
            <?= htmlspecialchars($item['nombre_producto']) ?>
            <?php if (!empty($item['categoria'])): ?>
                — <span class="text-gray-400">Categoría: <?= htmlspecialchars($item['categoria']) ?></span>
            <?php endif; ?>
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 max-w-2xl">

        <form method="POST" action="<?= BASE_URL ?>inventario">
            <input type="hidden" name="accion" value="actualizar_inventario">
            <input type="hidden" name="inventario_id" value="<?= htmlspecialchars($item['inventario_id']) ?>">

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Cantidad disponible (<?= htmlspecialchars($item['unidad_medida'] ?? 'unidades') ?>)
                </label>
                <input type="number" name="cantidad_disponible" min="0"
                    value="<?= htmlspecialchars($item['cantidad_disponible']) ?>"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 text-sm"
                    required>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ubicación
                </label>
                <input type="text" name="ubicacion"
                    value="<?= htmlspecialchars($item['ubicacion'] ?? '') ?>"
                    placeholder="Ej: Bodega principal"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 text-sm">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Estado
                </label>
                <select name="estado"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 text-sm">
                    <?php
                    $estados = [
                        'disponible' => 'Disponible',
                        'stock_bajo' => 'Stock bajo',
                        'agotado'    => 'Agotado',
                        'inactivo'   => 'Inactivo',
                    ];
                    foreach ($estados as $valor => $etiqueta):
                    ?>
                        <option value="<?= $valor ?>" <?= $item['estado'] === $valor ? 'selected' : '' ?>>
                            <?= $etiqueta ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium transition">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
                <a href="<?= BASE_URL ?>inventario"
                    class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-5 py-2.5 rounded-xl font-medium transition">
                    Cancelar
                </a>
            </div>
        </form>

    </div>

</div>