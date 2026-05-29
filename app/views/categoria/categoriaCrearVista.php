<?php
// app/views/categoria/categoriaCrearVista.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
?>

<div class="w-full max-w-3xl mx-auto px-4 md:px-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 transition-all">

        <div class="mb-8 border-b border-gray-100 dark:border-gray-700 pb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center">
                    <i class="fas fa-tags text-orange-500 mr-3"></i> Nueva Categoría
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Crea agrupaciones como "SDM 100", "Aseo",
                    "Herramientas".</p>
            </div>
        </div>

        <?php if (!empty($errores)): ?>
            <div
                class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-8 rounded-xl animate-pulse">
                <ul class="list-disc list-inside text-sm font-bold">
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>categoriaCrear" method="POST" class="space-y-6">

            <div>
                <label for="nombre_categoria"
                    class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">
                    Nombre de la Categoría <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-tag text-gray-400"></i>
                    </div>
                    <input type="text" id="nombre_categoria" name="nombre_categoria" required placeholder="Ej: SDM 100"
                        value="<?= htmlspecialchars($datosPrevios['nombre_categoria'] ?? '') ?>"
                        class="pl-12 w-full p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all outline-none uppercase font-bold">
                </div>
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">
                    Descripción (Opcional)
                </label>
                <textarea id="descripcion" name="descripcion" rows="3"
                    placeholder="Ej: Repuestos exclusivos para las máquinas dispensadoras SDM 100"
                    class="w-full p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all outline-none"><?= htmlspecialchars($datosPrevios['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>categoriaVer"
                    class="px-8 py-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all text-center">
                    Cancelar
                </a>

                <button type="submit"
                    class="px-10 py-4 bg-orange-500 text-white font-extrabold rounded-2xl shadow-lg hover:bg-orange-600 transform hover:-translate-y-1 transition-all">
                    <i class="fas fa-save mr-2"></i> Guardar Categoría
                </button>
            </div>
        </form>
    </div>
</div>