<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
?>

<!-- Estilos de Select2 y DataTables/Tailwind -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* Estilos personalizados para adaptar Select2 a Tailwind CSS y Modo Oscuro */
    .select2-container .select2-selection--single {
        height: 42px !important;
        background-color: #f9fafb !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #111827 !important;
        padding-left: 0.75rem !important;
        font-size: 0.875rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
    }
    .select2-dropdown {
        background-color: #ffffff !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        z-index: 9999 !important;
    }
    .select2-results__option {
        padding: 8px 12px !important;
        font-size: 0.875rem !important;
        color: #374151 !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #f97316 !important; /* Naranja Tailwind */
        color: #ffffff !important;
    }
    .select2-search__field {
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        padding: 6px !important;
        outline: none !important;
    }

    /* ESTILOS PARA MODO OSCURO (DARK MODE) */
    .dark .select2-container .select2-selection--single {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f9fafb !important;
    }
    .dark .select2-dropdown {
        background-color: #1f2937 !important;
        border-color: #4b5563 !important;
    }
    .dark .select2-results__option {
        color: #d1d5db !important;
    }
    .dark .select2-search__field {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
        color: #ffffff !important;
    }
    .dark .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: #374151 !important;
        color: #ffffff !important;
    }
</style>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 max-w-4xl mx-auto transition-colors">
        
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-tools text-orange-500 mr-2"></i> Estructurar Nueva Fórmula
            </h1>
            <a href="<?= BASE_URL ?>repuestoFormulaVer" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-900/40 dark:text-red-400 border border-red-200 dark:border-red-800">
                <i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>repuestoFormulaCrear" method="POST" class="space-y-6">
            <div>
                <label for="id_repuesto_padre" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Repuesto Compuesto Final (Padre) <span class="text-red-500">*</span></label>
                <select name="id_repuesto_padre" id="id_repuesto_padre" required class="select2-busqueda w-full">
                    <option value="">-- Seleccione el repuesto a armar --</option>
                    <?php foreach ($data['repuestos'] as $r): ?>
                        <option value="<?= $r['id_repuesto'] ?>"><?= htmlspecialchars($r['nombre_repuesto']) ?> [<?= htmlspecialchars($r['codigo_referencia']) ?>]</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-md font-bold text-gray-800 dark:text-gray-200">Insumos / Componentes de la Pieza</h3>
                    <button type="button" onclick="agregarFilaInsumo()" class="px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-md hover:bg-green-700 transition-transform transform hover:scale-105">
                        <i class="fas fa-plus mr-1"></i> Añadir Insumo
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="p-3">Componente (Repuesto Hijo)</th>
                                <th class="p-3 text-center" style="width: 150px;">Cantidad Req.</th>
                                <th class="p-3 text-center" style="width: 80px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="wrapperInsumos">
                            <tr class="border-b dark:border-gray-700 target-fila">
                                <td class="p-2">
                                    <select name="componentes[]" required class="select2-busqueda select2-hijo w-full">
                                        <option value="">-- Seleccione pieza componente --</option>
                                        <?php foreach ($data['repuestos'] as $r): ?>
                                            <option value="<?= $r['id_repuesto'] ?>"><?= htmlspecialchars($r['nombre_repuesto']) ?> [<?= htmlspecialchars($r['codigo_referencia']) ?>]</option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input type="number" name="cantidades[]" min="1" value="1" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-center">
                                </td>
                                <td class="p-2 text-center">
                                    <button type="button" onclick="removerFila(this)" class="text-red-500 hover:text-red-700 p-2"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-bold rounded-lg shadow-md hover:bg-orange-600 transition-transform transform hover:scale-105">
                    <i class="fas fa-save mr-2"></i> Guardar Repuesto Compuesto
                </button>
            </div>
            
        </form>
    </div>
</div>

<!-- Scripts requeridos: JQuery y Select2 -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicializar Select2 en los elementos existentes
        inicializarSelect2($('.select2-busqueda'));
    });

    function inicializarSelect2(elemento) {
        elemento.select2({
            placeholder: "-- Seleccione una opción --",
            allowClear: true,
            width: '100%'
        });
    }

    function agregarFilaInsumo() {
        const wrapper = document.getElementById('wrapperInsumos');
        const primeraFila = document.querySelector('.target-fila');
        
        // Destruimos temporalmente el Select2 de la primera fila para clonar limpio el HTML nativo
        $(primeraFila).find('.select2-busqueda').select2('destroy');

        const nuevaFila = primeraFila.cloneNode(true);

        // Volvemos a activar Select2 en la primera fila
        inicializarSelect2($(primeraFila).find('.select2-busqueda'));

        // Resetear valores de la fila clonada
        $(nuevaFila).find('select').val("").trigger('change');
        $(nuevaFila).find('input[type="number"]').val("1");

        // Añadimos la nueva fila al contenedor
        wrapper.appendChild(nuevaFila);

        // Inicializamos Select2 en la nueva fila recién insertada
        inicializarSelect2($(nuevaFila).find('.select2-busqueda'));
    }

    function removerFila(btn) {
        const filas = document.querySelectorAll('.target-fila');
        if (filas.length > 1) {
            btn.closest('tr').remove();
        } else {
            alert("La receta debe tener por lo menos un componente básico.");
        }
    }
</script>