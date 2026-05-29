<?php
// app/views/compra/compraEditarVista.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

// Lógica para mostrar la etiqueta correcta (Repuesto o Producto)
$esRepuesto = !empty($datosCompra['id_repuesto']);
?>

<div class="w-full max-w-4xl mx-auto px-4 md:px-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-edit text-yellow-500 mr-2"></i> Editar Registro de Compra
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-1 text-sm">
                Solo puedes modificar precios, cantidades y detalles. Para cambiar el artículo, debes anular la compra.
            </p>
        </div>

        <?php if (!empty($errores)): ?>
            <div
                class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-6 rounded shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <input type="hidden" name="id_movimiento" value="<?= htmlspecialchars($idMovimiento) ?>">

            <!-- TARJETA DEL ARTÍCULO (NO EDITABLE) -->
            <div
                class="bg-gray-50 dark:bg-gray-900/50 p-5 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center">
                    <?php if ($esRepuesto): ?>
                        <div
                            class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl mr-4">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-blue-500 uppercase">Repuesto Técnico -
                                <?= htmlspecialchars($datosCompra['condicion']) ?></p>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                <?= htmlspecialchars($datosCompra['nombre_repuesto']) ?></p>
                            <p class="text-xs text-gray-500">Ref: <?= htmlspecialchars($datosCompra['codigo_referencia']) ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div
                            class="h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center text-purple-600 dark:text-purple-400 text-xl mr-4">
                            <i class="fas fa-soap"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-purple-500 uppercase">Producto Consumible</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                <?= htmlspecialchars($datosCompra['nombre_producto']) ?></p>
                            <p class="text-xs text-gray-500">Cod Interno:
                                <?= htmlspecialchars($datosCompra['codigo_interno']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:block text-right">
                    <span
                        class="px-2 py-1 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded border dark:border-gray-600">
                        Ítem Bloqueado <i class="fas fa-lock ml-1"></i>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">

                <!-- Número Factura -->
                <div>
                    <label for="numero_factura" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Número de Factura / Recibo <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-hashtag text-gray-400"></i></div>
                        <input type="text" id="numero_factura" name="numero_factura" required
                            value="<?= htmlspecialchars($datosCompra['numero_factura'] ?? '') ?>"
                            class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500">
                    </div>
                </div>

                <!-- Proveedor -->
                <div>
                    <label for="id_proveedor" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Proveedor <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-building text-gray-400"></i></div>
                        <select id="id_proveedor" name="id_proveedor" required
                            class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500 appearance-none cursor-pointer">
                            <?php foreach ($proveedoresActivos as $prov): ?>
                                <?php $selected = ($datosCompra['id_proveedor'] == $prov['id_proveedor']) ? 'selected' : ''; ?>
                                <option value="<?= $prov['id_proveedor'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($prov['nombre_proveedor']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none"><i
                                class="fas fa-chevron-down text-gray-400 text-xs"></i></div>
                    </div>
                </div>

                <!-- Cantidad Editable -->
                <div>
                    <label for="cantidad" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Cantidad Comprada <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-cubes text-gray-400"></i></div>
                        <input type="number" id="cantidad" name="cantidad" min="1" required
                            value="<?= htmlspecialchars($datosCompra['cantidad'] ?? '') ?>"
                            class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500 font-bold">
                    </div>
                </div>

                <!-- Precio -->
                <div>
                    <label for="precio_compra" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Precio Unitario <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-dollar-sign text-gray-400"></i></div>
                        <input type="number" step="0.01" min="0" id="precio_compra" name="precio_compra" required
                            value="<?= htmlspecialchars($datosCompra['precio_compra'] ?? '') ?>"
                            class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500 font-mono">
                    </div>
                </div>

                <!-- Observación Editable -->
                <div class="md:col-span-2">
                    <label for="observacion" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Observaciones
                    </label>
                    <textarea id="observacion" name="observacion" rows="3"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500"><?= htmlspecialchars($datosCompra['observacion'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>compraVer"
                    class="px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancelar
                </a>

                <button type="submit"
                    class="px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg shadow-md transform hover:-translate-y-1 transition">
                    <i class="fas fa-sync-alt mr-2"></i> Guardar y Recalcular
                </button>
            </div>
        </form>
    </div>
</div>