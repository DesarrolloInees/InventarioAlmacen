<?php
// app/views/compra/compraCrearVista.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
?>

<!-- Estilos para adaptar Select2 a Tailwind y Modo Oscuro -->
<style>
    /* Estilos generales (altura y bordes para que coincida con Tailwind) */
    .select2-container .select2-selection--single {
        height: 48px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.5rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }

    /* --- MODO OSCURO --- */
    /* 1. Caja principal (cerrada) */
    .dark .select2-container .select2-selection--single {
        background-color: #111827 !important; /* bg-gray-900 */
        border-color: #374151 !important; /* border-gray-700 */
    }
    .dark .select2-container .select2-selection__rendered {
        color: #ffffff !important;
    }

    /* 2. Contenedor del menú desplegable */
    .dark .select2-dropdown {
        background-color: #111827 !important; /* bg-gray-900 */
        border-color: #374151 !important; /* border-gray-700 */
    }

    /* 3. Caja de búsqueda dentro del menú */
    .dark .select2-search--dropdown .select2-search__field {
        background-color: #1f2937 !important; /* bg-gray-800 */
        border: 1px solid #4b5563 !important; /* border-gray-600 */
        color: #ffffff !important;
        border-radius: 0.375rem !important;
        outline: none !important;
    }

    /* 4. Opciones de la lista */
    .dark .select2-results__option {
        background-color: #111827 !important;
        color: #d1d5db !important; /* text-gray-300 */
    }

    /* 5. Opción seleccionada previamente */
    .dark .select2-results__option[aria-selected="true"] {
        background-color: #1f2937 !important; /* bg-gray-800 */
        color: #ffffff !important;
    }

    /* 6. Opción resaltada (al pasar el ratón por encima o navegar con teclado) */
    .dark .select2-results__option--highlighted[aria-selected] {
        background-color: #374151 !important; /* bg-gray-700 */
        color: #ffffff !important;
    }
</style>

<div class="w-full max-w-5xl mx-auto px-4 md:px-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-file-invoice-dollar text-green-600 dark:text-green-500 mr-2"></i> Registro de Compra
                Inteligente
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Ingresa facturas al inventario mapeando repuestos
                técnicos o consumibles generales.</p>
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

        <?php if (isset($exito) && $exito): ?>
            <div
                class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 mb-6 rounded shadow-sm">
                <div class="flex items-center"><i class="fas fa-check-circle mr-2 text-lg"></i>
                    <p class="font-bold">¡Compra e Inventario Guardados!</p>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>compraCrear" method="POST" class="space-y-6">

            <div class="bg-gray-50 dark:bg-gray-900/60 p-4 rounded-xl border dark:border-gray-700">
                <label class="block text-sm font-black text-gray-700 dark:text-gray-300 mb-2">
                    ¿Qué tipo de artículo vas a ingresar con esta factura?
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <label
                        class="flex items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition-all bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 select-none"
                        id="label_tipo_repuesto">
                        <input type="radio" name="tipo_item" value="repuesto" class="hidden"
                            onchange="conmutarTipoItem('repuesto')" <?= (!isset($datosPrevios['tipo_item']) || $datosPrevios['tipo_item'] === 'repuesto') ? 'checked' : '' ?>>
                        <div class="text-center">
                            <i class="fas fa-tools text-xl mb-1 text-blue-500"></i>
                            <p class="text-sm font-bold text-gray-800 dark:text-white">Repuesto de Máquina</p>
                            <span class="text-[10px] text-gray-400">(Filtra por Condición: Nuevo / Recuperado)</span>
                        </div>
                    </label>
                    <label
                        class="flex items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition-all bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 select-none"
                        id="label_tipo_producto">
                        <input type="radio" name="tipo_item" value="producto" class="hidden"
                            onchange="conmutarTipoItem('producto')" <?= (isset($datosPrevios['tipo_item']) && $datosPrevios['tipo_item'] === 'producto') ? 'checked' : '' ?>>
                        <div class="text-center">
                            <i class="fas fa-soap text-xl mb-1 text-purple-500"></i>
                            <p class="text-sm font-bold text-gray-800 dark:text-white">Producto Consumible</p>
                            <span class="text-[10px] text-gray-400">(Aseo, oficina, insumos generales)</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div id="wrapper_repuestos" class="md:col-span-2">
                    <label for="id_repuesto"
                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Repuesto Técnico <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <select id="id_repuesto" name="id_repuesto"
                            class="w-full p-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white select2-elemento">
                            <option value="">-- Elige el repuesto del catálogo --</option>
                            <?php foreach ($repuestosActivos as $rep): ?>
                                <?php $selected = (isset($datosPrevios['id_repuesto']) && $datosPrevios['id_repuesto'] == $rep['id_repuesto']) ? 'selected' : ''; ?>
                                <option value="<?= $rep['id_repuesto'] ?>" <?= $selected ?>>
                                    <?= !empty($rep['codigo_referencia']) ? '[' . htmlspecialchars($rep['codigo_referencia']) . '] ' : '' ?>
                                    <?= htmlspecialchars($rep['nombre_repuesto']) ?> —
                                    (<?= strtoupper($rep['condicion']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="wrapper_productos" class="md:col-span-2 hidden">
                    <label for="id_producto"
                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Producto Consumible /
                        Gasto <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select id="id_producto" name="id_producto"
                            class="w-full p-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white select2-elemento">
                            <option value="">-- Elige el producto/insumo --</option>
                            <?php foreach ($productosActivos as $prod): ?>
                                <?php $selected = (isset($datosPrevios['id_producto']) && $datosPrevios['id_producto'] == $prod['id_producto']) ? 'selected' : ''; ?>
                                <option value="<?= $prod['id_producto'] ?>" <?= $selected ?>>
                                    <?= !empty($prod['codigo_interno']) ? '[' . htmlspecialchars($prod['codigo_interno']) . '] ' : '' ?>
                                    <?= htmlspecialchars($prod['nombre_producto']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="numero_factura"
                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Número de Factura o Recibo
                        <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-hashtag text-gray-400"></i></div>
                        <input type="text" id="numero_factura" name="numero_factura" required placeholder="Ej: FE-45920"
                            value="<?= htmlspecialchars($datosPrevios['numero_factura'] ?? '') ?>"
                            class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white focus:ring-green-500">
                    </div>
                </div>

                <div>
                    <label for="id_proveedor"
                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Proveedor Relacionado
                        <span class="text-red-500">*</span></label>
                    <select id="id_proveedor" name="id_proveedor" required
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white focus:ring-green-500 select2-elemento">
                        <option value="">-- Selecciona el proveedor --</option>
                        <?php foreach ($proveedoresActivos as $prov): ?>
                            <?php $selected = (isset($datosPrevios['id_proveedor']) && $datosPrevios['id_proveedor'] == $prov['id_proveedor']) ? 'selected' : ''; ?>
                            <option value="<?= $prov['id_proveedor'] ?>" <?= $selected ?>>
                                <?= htmlspecialchars($prov['nombre_proveedor']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="cantidad" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Cantidad
                        Comprada <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-cubes text-gray-400"></i></div>
                        <input type="number" id="cantidad" name="cantidad" min="1" required
                            value="<?= htmlspecialchars($datosPrevios['cantidad'] ?? '') ?>"
                            class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white focus:ring-green-500">
                    </div>
                </div>

                <div>
                    <label for="precio_compra"
                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Precio Unitario de Compra
                        <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fas fa-dollar-sign text-gray-400"></i></div>
                        <input type="number" step="0.01" min="0" id="precio_compra" name="precio_compra" required
                            placeholder="0.00" value="<?= htmlspecialchars($datosPrevios['precio_compra'] ?? '') ?>"
                            class="pl-10 mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white focus:ring-green-500">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="observacion"
                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Observaciones
                        Internas</label>
                    <textarea id="observacion" name="observacion" rows="2"
                        placeholder="Ej: Viene de la sucursal norte, despacho urgente..."
                        class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white focus:ring-green-500"><?= htmlspecialchars($datosPrevios['observacion'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button type="submit"
                    class="px-8 py-3 bg-green-600 text-white font-bold rounded-lg shadow-lg hover:bg-green-700 transition-all transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i> Procesar Entrada e Inventariar
                </button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function conmutarTipoItem(tipo) {
        const wrpRepuestos = document.getElementById('wrapper_repuestos');
        const wrpProductos = document.getElementById('wrapper_productos');
        const selectRepuesto = document.getElementById('id_repuesto');
        const selectProducto = document.getElementById('id_producto');

        const labelRep = document.getElementById('label_tipo_repuesto');
        const labelProd = document.getElementById('label_tipo_producto');

        if (tipo === 'repuesto') {
            wrpRepuestos.classList.remove('hidden');
            wrpProductos.classList.add('hidden');
            
            selectRepuesto.setAttribute('required', 'required');
            selectProducto.removeAttribute('required');
            
            $('#id_producto').val(null).trigger('change');

            labelRep.className = "flex items-center justify-center p-3 rounded-xl border-2 cursor-pointer transition-all bg-blue-50 dark:bg-blue-900/20 border-blue-500 text-blue-600 shadow-sm";
            labelProd.className = "flex items-center justify-center p-3 rounded-xl border-2 cursor-pointer transition-all bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-400 opacity-60";
        } else {
            wrpProductos.classList.remove('hidden');
            wrpRepuestos.classList.add('hidden');
            
            selectProducto.setAttribute('required', 'required');
            selectRepuesto.removeAttribute('required');
            
            $('#id_repuesto').val(null).trigger('change');

            labelProd.className = "flex items-center justify-center p-3 rounded-xl border-2 cursor-pointer transition-all bg-purple-50 dark:bg-purple-900/20 border-purple-500 text-purple-600 shadow-sm";
            labelRep.className = "flex items-center justify-center p-3 rounded-xl border-2 cursor-pointer transition-all bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-400 opacity-60";
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        $('.select2-elemento').select2({
            width: '100%',
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            }
        });

        const tipoInicial = document.querySelector('input[name="tipo_item"]:checked').value;
        conmutarTipoItem(tipoInicial);
    });
</script>