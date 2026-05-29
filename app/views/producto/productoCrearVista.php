<?php
// app/views/producto/productoCrearVista.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
?>

<div class="w-full max-w-4xl mx-auto px-4 md:px-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 transition-all">

        <div class="mb-8 border-b border-gray-100 dark:border-gray-700 pb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center">
                    <i class="fas fa-soap text-purple-500 mr-3"></i> Nuevo Producto Consumible
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Registra artículos de gasto administrativo, aseo o
                    papelería.</p>
            </div>
            <div class="hidden sm:block">
                <span
                    class="px-4 py-2 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold rounded-full border border-purple-100 dark:border-purple-800 uppercase">
                    Catálogo de Insumos
                </span>
            </div>
        </div>

        <?php if (!empty($errores)): ?>
            <div
                class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-8 rounded-xl animate-pulse">
                <div class="flex items-center mb-1">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p class="font-bold">Por favor corrige lo siguiente:</p>
                </div>
                <ul class="list-disc list-inside ml-4 text-sm">
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>productoCrear" method="POST" class="space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <div class="md:col-span-2">
                    <label for="nombre_producto"
                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">
                        Nombre Completo del Producto <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-box-open text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <input type="text" id="nombre_producto" name="nombre_producto" required
                            placeholder="Ej: Alcohol Isopropílico 99% 1Lt"
                            value="<?= htmlspecialchars($datosPrevios['nombre_producto'] ?? '') ?>"
                            class="pl-12 w-full p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none">
                    </div>
                </div>

                <div>
                    <label for="codigo_interno"
                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">
                        Código de Referencia Interno
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-barcode text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <input type="text" id="codigo_interno" name="codigo_interno" placeholder="Ej: ASEO-001"
                            value="<?= htmlspecialchars($datosPrevios['codigo_interno'] ?? '') ?>"
                            class="pl-12 w-full p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 ml-1 italic">Puedes usar el escáner de barras aquí si el
                        producto ya tiene uno de fábrica.</p>
                </div>

                <div>
                    <label for="valor_venta" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">
                        Valor Unitario Estimado (Venta/Uso)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-dollar-sign text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <input type="number" step="0.01" min="0" id="valor_venta" name="valor_venta" placeholder="0.00"
                            value="<?= htmlspecialchars($datosPrevios['valor_venta'] ?? '') ?>"
                            class="pl-12 w-full p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none font-mono">
                    </div>
                </div>

            </div>

            <div
                class="pt-8 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-end space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="<?= BASE_URL ?>productoVer"
                    class="px-8 py-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all text-center">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>

                <button type="submit"
                    class="px-10 py-4 bg-purple-600 text-white font-extrabold rounded-2xl shadow-lg shadow-purple-200 dark:shadow-none hover:bg-purple-700 transform hover:-translate-y-1 transition-all">
                    <i class="fas fa-check-circle mr-2"></i> Crear Producto
                </button>
            </div>

        </form>
    </div>
</div>