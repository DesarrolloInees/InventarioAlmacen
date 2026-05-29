<?php if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado."); ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        background-color: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 1rem !important;
        height: 56px !important;
        display: flex;
        align-items: center;
        padding-left: 2.5rem;
        outline: none !important;
    }

    .select2-container--default .select2-selection--single:focus {
        border-color: #eab308 !important;
        box-shadow: 0 0 0 2px rgba(234, 179, 8, 0.5) !important;
    }

    .select2-selection__arrow {
        height: 54px !important;
        right: 15px !important;
    }

    .dark .select2-container--default .select2-selection--single {
        background-color: #111827 !important;
        border-color: #4b5563 !important;
    }

    .dark .select2-selection__rendered {
        color: #f3f4f6 !important;
    }

    .dark .select2-dropdown {
        background-color: #1f2937 !important;
        border-color: #4b5563 !important;
        color: white !important;
    }
</style>

<div class="w-full max-w-4xl mx-auto px-4 md:px-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 transition-all">

        <div class="mb-8 border-b border-gray-100 dark:border-gray-700 pb-6 flex justify-between items-center">
            <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center">
                <i class="fas fa-edit text-yellow-500 mr-3"></i> Editar Repuesto
            </h1>
        </div>

        <?php if (!empty($errores)): ?>
            <div
                class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-8 rounded-xl">
                <ul class="list-disc list-inside text-sm font-bold">
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <input type="hidden" name="id_repuesto" value="<?= htmlspecialchars($idRepuesto) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Nombre del
                        Repuesto <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10"><i
                                class="fas fa-cog text-gray-400"></i></div>
                        <input type="text" name="nombre_repuesto" required
                            value="<?= htmlspecialchars($datosRepuesto['nombre_repuesto'] ?? '') ?>"
                            class="pl-12 w-full p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-yellow-500 outline-none">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Categoría <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10"><i
                                class="fas fa-tags text-gray-400"></i></div>
                        <select name="id_categoria" id="id_categoria" class="w-full select2-buscador" required>
                            <option value="">-- Buscar categoría --</option>
                            <?php foreach ($categoriasActivas as $cat): ?>
                                <?php $sel = (($datosRepuesto['id_categoria'] ?? '') == $cat['id_categoria']) ? 'selected' : ''; ?>
                                <option value="<?= $cat['id_categoria'] ?>" <?= $sel ?>>
                                    <?= htmlspecialchars($cat['nombre_categoria']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Código /
                        SKU</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10"><i
                                class="fas fa-barcode text-gray-400"></i></div>
                        <input type="text" name="codigo_referencia"
                            value="<?= htmlspecialchars($datosRepuesto['codigo_referencia'] ?? '') ?>"
                            class="pl-12 w-full p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-yellow-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Condición <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10"><i
                                class="fas fa-star-half-alt text-gray-400"></i></div>
                        <select name="condicion" id="condicion" class="w-full select2-buscador" required>
                            <option value="nuevo" <?= (($datosRepuesto['condicion'] ?? '') === 'nuevo') ? 'selected' : '' ?>>NUEVO</option>
                            <option value="recuperado" <?= (($datosRepuesto['condicion'] ?? '') === 'recuperado') ? 'selected' : '' ?>>RECUPERADO</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Valor
                        Venta</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10"><i
                                class="fas fa-dollar-sign text-gray-400"></i></div>
                        <input type="number" step="0.01" name="valor_venta"
                            value="<?= htmlspecialchars($datosRepuesto['valor_venta'] ?? '0.00') ?>"
                            class="pl-12 w-full p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-2xl focus:ring-2 focus:ring-yellow-500 outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Estado en
                        Sistema</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10"><i
                                class="fas fa-power-off text-gray-400"></i></div>
                        <select name="estado" id="estado" class="w-full select2-buscador" required>
                            <option value="1" <?= (($datosRepuesto['estado'] ?? 1) == 1) ? 'selected' : '' ?>>ACTIVO
                            </option>
                            <option value="0" <?= (($datosRepuesto['estado'] ?? 1) == 0) ? 'selected' : '' ?>>INACTIVO
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>repuestoVer"
                    class="px-8 py-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-200 transition-all text-center">Cancelar</a>
                <button type="submit"
                    class="px-10 py-4 bg-yellow-500 text-white font-extrabold rounded-2xl shadow-lg hover:bg-yellow-600 transform hover:-translate-y-1 transition-all"><i
                        class="fas fa-save mr-2"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2-buscador').select2({ width: '100%', language: { noResults: () => "Sin resultados" } });
    });
</script>