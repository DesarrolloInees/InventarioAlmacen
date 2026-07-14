<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
?>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 max-w-4xl mx-auto transition-colors">
        
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-tools text-orange-500 mr-2"></i> Centro de Ensamble de Repuestos
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Generación de repuestos compuestos a partir de existencias individuales.</p>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800/50 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i><span class="font-bold">Atención:</span> <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($data['exito'])): ?>
            <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800/50 dark:text-green-400 border border-green-200 dark:border-green-800" role="alert">
                <i class="fas fa-check-circle mr-2"></i><span class="font-bold">Éxito:</span> <?= htmlspecialchars($data['exito']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>repuestoEnsamblar" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-2">
                    <label for="id_repuesto_padre" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Repuesto Final a Ensamblar <span class="text-red-500">*</span></label>
                    <select name="id_repuesto_padre" id="id_repuesto_padre" required onchange="cargarComponentesReceta(this.value)"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Seleccione el repuesto que va a fabricar --</option>
                        <?php foreach ($data['repuestos_formula'] as $rf): ?>
                            <option value="<?= $rf['id_repuesto'] ?>"><?= htmlspecialchars($rf['nombre_repuesto']) ?> [<?= htmlspecialchars($rf['codigo_referencia']) ?>]</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="cantidad_a_armar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad a Fabricar <span class="text-red-500">*</span></label>
                    <input type="number" name="cantidad_a_armar" id="cantidad_a_armar" min="1" value="1" required oninput="calcularTotalesRequeridos()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div id="contenedorReceta" class="hidden border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-900/30">
                <h3 class="text-md font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                    <i class="fas fa-list-ol text-orange-500 mr-2"></i> Componentes requeridos según fórmula básica:
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-100 dark:bg-gray-700/50">
                            <tr>
                                <th class="py-2 px-3">Componente</th>
                                <th class="py-2 px-3 text-center">Unidades x Ensamble</th>
                                <th class="py-2 px-3 text-center">Total Requerido</th>
                                <th class="py-2 px-3 text-center">Stock Almacén</th>
                                <th class="py-2 px-3 text-center">Disponibilidad</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetalleComponentes" class="divide-y divide-gray-200 dark:divide-gray-700">
                            </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button type="submit" id="btnSubmitEnsamble" class="px-6 py-2.5 bg-orange-500 text-white font-bold rounded-lg shadow-md hover:bg-orange-600 transition-transform transform hover:scale-105">
                    <i class="fas fa-hammer mr-2"></i> Procesar Ensamble
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let componentesActuales = [];

    function cargarComponentesReceta(idPadre) {
        const contenedor = document.getElementById('contenedorReceta');
        const tabla = document.getElementById('tablaDetalleComponentes');
        
        if (!idPadre) {
            contenedor.classList.add('hidden');
            return;
        }

        fetch(`<?= BASE_URL ?>repuestoEnsamblar?obtener_receta=${idPadre}`)
            .then(response => response.json())
            .then(data => {
                componentesActuales = data;
                contenedor.classList.remove('hidden');
                calcularTotalesRequeridos();
            })
            .catch(error => {
                console.error("Error cargando receta:", error);
                alert("Error al cargar la receta del servidor.");
            });
    }

    function calcularTotalesRequeridos() {
        const cantidadArmar = parseInt(document.getElementById('cantidad_a_armar').value) || 0;
        const tabla = document.getElementById('tablaDetalleComponentes');
        const btnSubmit = document.getElementById('btnSubmitEnsamble');
        
        tabla.innerHTML = '';
        let todoOk = true;

        if (componentesActuales.length === 0) {
            tabla.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">Esta pieza no tiene componentes asignados.</td></tr>`;
            btnSubmit.disabled = true;
            return;
        }

        componentesActuales.forEach(c => {
            const totalRequerido = c.cantidad_necesaria * cantidadArmar;
            const stockDisponble = parseInt(c.stock_disponible);
            const cumpleStock = stockDisponble >= totalRequerido;
            
            if(!cumpleStock) todoOk = false;

            const badgeDispo = cumpleStock 
                ? `<span class="px-2 py-0.5 text-[10px] font-bold text-green-700 bg-green-100 dark:bg-green-900/30 rounded-full border border-green-200">OK</span>`
                : `<span class="px-2 py-0.5 text-[10px] font-bold text-red-700 bg-red-100 dark:bg-red-900/30 rounded-full border border-red-200ABC">INSUFICIENTE</span>`;

            const fila = `
                <tr class="border-b dark:border-gray-700 bg-white dark:bg-gray-800/40">
                    <td class="py-3 px-3 font-medium text-gray-900 dark:text-white">
                        ${c.nombre_repuesto} <br><span class="text-xs text-gray-400 font-mono">${c.codigo_referencia}</span>
                    </td>
                    <td class="py-3 px-3 text-center font-bold text-gray-700 dark:text-gray-300">${c.cantidad_necesaria}</td>
                    <td class="py-3 px-3 text-center font-bold text-orange-600 dark:text-orange-400">${totalRequerido}</td>
                    <td class="py-3 px-3 text-center font-bold ${cumpleStock ? 'text-gray-700 dark:text-gray-300' : 'text-red-500 font-black'}">${stockDisponble}</td>
                    <td class="py-3 px-3 text-center">${badgeDispo}</td>
                </tr>
            `;
            tabla.innerHTML += fila;
        });

        // Bloquea el botón de envío si falta stock en alguna pieza
        if(todoOk && cantidadArmar > 0) {
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
        </style>
        } else {
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
</script>