<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
?>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 max-w-4xl mx-auto">
        
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-tools text-orange-500 mr-2"></i> Estructurar Nueva Fórmula
            </h1>
            <a href="<?= BASE_URL ?>repuestoFormulaVer" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">Volver</a>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-900/40 dark:text-red-400 border border-red-200"><?= htmlspecialchars($data['error']) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>repuestoFormulaCrear" method="POST" class="space-y-6">
            <div>
                <label for="id_repuesto_padre" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Repuesto Compuesto Final (Padre)</label>
                <select name="id_repuesto_padre" id="id_repuesto_padre" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">-- Seleccione el repuesto a armar --</option>
                    <?php foreach ($data['repuestos'] as $r): ?>
                        <option value="<?= $r['id_repuesto'] ?>"><?= htmlspecialchars($r['nombre_repuesto']) ?> [<?= htmlspecialchars($r['codigo_referencia']) ?>]</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-md font-bold text-gray-800 dark:text-gray-200">Insumos / Componentes de la Pieza</h3>
                    <button type="button" onclick="agregarFilaInsumo()" class="px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-md hover:bg-green-700 transition-colors">
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
                                    <select name="componentes[]" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
                    <i class="fas fa-save mr-2"></i> Guardar Receta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function agregarFilaInsumo() {
        const wrapper = document.getElementById('wrapperInsumos');
        const primeraFila = document.querySelector('.target-fila');
        const nuevaFila = primeraFila.cloneNode(true);
        
        // Resetear valores clonados
        nuevaFila.querySelector('select').value = "";
        nuevaFila.querySelector('input').value = "1";
        
        wrapper.appendChild(nuevaFila);
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