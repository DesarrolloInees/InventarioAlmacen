<?php
// app/views/proveedor/proveedorCrearVista.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
?>

<div class="w-full max-w-4xl mx-auto px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700">
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white"><i
                    class="fas fa-building text-blue-600 dark:text-blue-500 mr-2"></i> Crear Proveedor</h1>
        </div>

        <?php if (!empty($errores)): ?>
            <div
                class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-6 rounded">
                <p class="font-bold">Error:</p>
                <ul class="list-disc list-inside ml-6 text-sm">
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>proveedorCrear" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nombre / Razón Social
                        <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre_proveedor" required
                        value="<?= htmlspecialchars($datosPrevios['nombre_proveedor'] ?? '') ?>"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">NIT / Documento</label>
                    <input type="text" name="nit_documento"
                        value="<?= htmlspecialchars($datosPrevios['nit_documento'] ?? '') ?>"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($datosPrevios['telefono'] ?? '') ?>"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($datosPrevios['email'] ?? '') ?>"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                    <select name="estado"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500">
                        <option value="activo" <?= (($datosPrevios['estado'] ?? '') === 'activo') ? 'selected' : '' ?>>
                            Activo</option>
                        <option value="inactivo" <?= (($datosPrevios['estado'] ?? '') === 'inactivo') ? 'selected' : '' ?>>
                            Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>proveedorVer"
                    class="px-6 py-3 bg-white dark:bg-gray-800 border text-gray-700 dark:text-gray-300 rounded-lg">Cancelar</a>
                <button type="submit"
                    class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Guardar
                    Proveedor</button>
            </div>
        </form>
    </div>
</div>