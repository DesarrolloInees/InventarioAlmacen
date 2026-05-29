<?php
// app/views/proveedor/proveedorEditarVista.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
?>

<div class="w-full max-w-4xl mx-auto px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700">
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white"><i
                    class="fas fa-edit text-yellow-500 mr-2"></i> Editar Proveedor</h1>
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

        <form action="" method="POST" class="space-y-6">
            <input type="hidden" name="id_proveedor" value="<?= htmlspecialchars($idProv) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nombre / Razón Social
                        <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre_proveedor" required
                        value="<?= htmlspecialchars($datosProv['nombre_proveedor'] ?? '') ?>"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">NIT / Documento</label>
                    <input type="text" name="nit_documento"
                        value="<?= htmlspecialchars($datosProv['nit_documento'] ?? '') ?>"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($datosProv['telefono'] ?? '') ?>"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($datosProv['email'] ?? '') ?>"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                    <select name="estado"
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-yellow-500">
                        <option value="activo" <?= (strtolower($datosProv['estado'] ?? '') === 'activo') ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= (strtolower($datosProv['estado'] ?? '') === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>proveedorVer"
                    class="px-6 py-3 bg-white dark:bg-gray-800 border text-gray-700 dark:text-gray-300 rounded-lg">Cancelar</a>
                <button type="submit"
                    class="px-8 py-3 bg-yellow-500 text-white font-bold rounded-lg hover:bg-yellow-600">Guardar
                    Cambios</button>
            </div>
        </form>
    </div>
</div>