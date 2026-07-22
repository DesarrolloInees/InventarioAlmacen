<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
?>

<!-- Estilos para adaptar Select2 a Tailwind y Modo Oscuro -->
<style>
    /* Estilos generales (altura y bordes para que coincida con Tailwind) */
    .select2-container .select2-selection--single {
        height: 42px !important; /* Adaptado al p-2.5 de este form */
        border: 1px solid #d1d5db !important; /* border-gray-300 */
        border-radius: 0.5rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }

    /* --- MODO OSCURO --- */
    .dark .select2-container .select2-selection--single {
        background-color: #1f2937 !important; /* bg-gray-800 */
        border-color: #4b5563 !important; /* border-gray-600 */
    }
    .dark .select2-container .select2-selection__rendered {
        color: #ffffff !important;
    }
    .dark .select2-dropdown {
        background-color: #1f2937 !important; /* bg-gray-800 */
        border-color: #4b5563 !important; /* border-gray-600 */
    }
    .dark .select2-search--dropdown .select2-search__field {
        background-color: #374151 !important; /* bg-gray-700 */
        border: 1px solid #4b5563 !important; /* border-gray-600 */
        color: #ffffff !important;
        border-radius: 0.375rem !important;
        outline: none !important;
    }
    .dark .select2-results__option {
        background-color: #1f2937 !important;
        color: #d1d5db !important; /* text-gray-300 */
    }
    .dark .select2-results__option[aria-selected="true"] {
        background-color: #374151 !important; /* bg-gray-700 */
        color: #ffffff !important;
    }
    .dark .select2-results__option--highlighted[aria-selected] {
        background-color: #4b5563 !important; /* bg-gray-600 */
        color: #ffffff !important;
    }
</style>

<div class="w-full px-4 md:px-6 mb-8">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 max-w-6xl mx-auto transition-colors">

        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-exchange-alt text-orange-500 mr-2"></i> Registrar Movimientos
                </h1>
            </div>
            <a href="<?= BASE_URL ?>entradaSalidaVer"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
                <span class="font-medium">¡Error!</span> <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <!-- Panel de Captura -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-transparent dark:border-gray-700">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Tipo Movimiento</label>
                <select id="tmp_tipo_movimiento"
                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="SALIDA">Salida (Descarga)</option>
                    <option value="ENTRADA">Entrada (Ingreso)</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Repuesto</label>
                <!-- Aplicamos la clase select2-elemento -->
                <select id="tmp_id_repuesto"
                    class="select2-elemento bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 outline-none">
                    <option value="">Seleccione...</option>
                    <option value="OTRO" class="font-bold text-orange-600">➕ Otro (Manual)</option>
                    <?php foreach ($data['repuestos'] as $repuesto): ?>
                        <option value="<?= $repuesto['id_repuesto'] ?>"
                            data-nombre="<?= htmlspecialchars($repuesto['codigo_referencia'] . ' - ' . $repuesto['nombre_repuesto']) ?>">
                            <?= htmlspecialchars($repuesto['codigo_referencia'] . ' - ' . $repuesto['nombre_repuesto']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="div_manual" class="hidden">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Repuesto Manual</label>
                <input type="text" id="tmp_repuesto_manual"
                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Cantidad</label>
                <input type="number" id="tmp_cantidad" min="1" value="1"
                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Destino</label>
                <input type="text" id="tmp_destino"
                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Nº Remisión</label>
                    <input type="text" id="tmp_remision"
                        class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Nº Cotiz.</label>
                    <input type="text" id="tmp_cotizacion"
                        class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Novedad / Obs.</label>
                <input type="text" id="tmp_novedad"
                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div class="md:col-span-1 flex items-end">
                <button type="button" onclick="agregarFila()"
                    class="w-full bg-green-600 text-white font-bold py-2.5 rounded-lg shadow hover:bg-green-700 transition">
                    <i class="fas fa-plus"></i> Agregar a Lista
                </button>
            </div>
        </div>

        <!-- Tabla Temporal (Carrito) -->
        <div class="overflow-x-auto mb-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Artículo</th>
                        <th class="px-4 py-3 text-center">Cant.</th>
                        <th class="px-4 py-3">Destino / Docs / Novedad</th>
                        <th class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla_body_movimientos">
                    <tr id="fila_vacia" class="bg-white dark:bg-gray-800">
                        <td colspan="5" class="text-center py-6 text-gray-400 dark:text-gray-500">No hay movimientos agregados.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Formulario Real que se envía al PHP -->
        <form action="<?= BASE_URL ?>entradaSalidaCrear" method="POST" id="form_final"
            class="flex justify-end border-t border-gray-200 dark:border-gray-700 pt-4">
            <input type="hidden" name="movimientos_json" id="movimientos_json" value="[]">
            <button type="button" onclick="guardarTodo()"
                class="px-6 py-2.5 bg-orange-500 text-white font-bold rounded-lg shadow-md hover:bg-orange-600 transition-transform transform hover:scale-105">
                <i class="fas fa-save mr-2"></i> Guardar Todos los Movimientos
            </button>
        </form>

    </div>
</div>

<!-- CDNs de jQuery y Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    let listaMovimientos = [];

    // Inicializar Select2 y sus eventos al cargar
    $(document).ready(function() {
        $('.select2-elemento').select2({
            width: '100%',
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            }
        });

        // Usamos el evento de Select2 para detectar el cambio
        $('#tmp_id_repuesto').on('change', function() {
            toggleManual();
        });
    });

    function toggleManual() {
        const id_rep = document.getElementById('tmp_id_repuesto').value;
        if (id_rep === 'OTRO') {
            document.getElementById('div_manual').classList.remove('hidden');
        } else {
            document.getElementById('div_manual').classList.add('hidden');
            document.getElementById('tmp_repuesto_manual').value = '';
        }
    }

    function agregarFila() {
        const selectRepuesto = document.getElementById('tmp_id_repuesto');
        const id_repuesto = selectRepuesto.value;
        const repuesto_manual = document.getElementById('tmp_repuesto_manual').value;

        if (!id_repuesto || (id_repuesto === 'OTRO' && !repuesto_manual)) {
            alert('Por favor selecciona un repuesto o escribe uno manual.');
            return;
        }

        let nombreVisual = (id_repuesto === 'OTRO') ? repuesto_manual : selectRepuesto.options[selectRepuesto.selectedIndex].getAttribute('data-nombre');

        const nuevoMovimiento = {
            id: Date.now(), // ID temporal para borrar
            tipo_movimiento: document.getElementById('tmp_tipo_movimiento').value,
            id_repuesto: id_repuesto,
            repuesto_manual: repuesto_manual,
            cantidad: document.getElementById('tmp_cantidad').value,
            destino: document.getElementById('tmp_destino').value || 'N/A',
            n_remision: document.getElementById('tmp_remision').value || 'N/A',
            n_cotizacion: document.getElementById('tmp_cotizacion').value || 'N/A',
            novedad: document.getElementById('tmp_novedad').value || 'N/A',
            nombreVisual: nombreVisual
        };

        listaMovimientos.push(nuevoMovimiento);
        renderTabla();

        // Limpiar campos y Select2 para el siguiente registro rápido
        $('#tmp_id_repuesto').val('').trigger('change'); 
        document.getElementById('tmp_cantidad').value = 1;
        document.getElementById('tmp_repuesto_manual').value = '';
        document.getElementById('tmp_destino').value = '';
        document.getElementById('tmp_remision').value = '';
        document.getElementById('tmp_cotizacion').value = '';
        document.getElementById('tmp_novedad').value = '';
    }

    function eliminarFila(id) {
        listaMovimientos = listaMovimientos.filter(m => m.id !== id);
        renderTabla();
    }

    function renderTabla() {
        const tbody = document.getElementById('tabla_body_movimientos');
        tbody.innerHTML = '';

        if (listaMovimientos.length === 0) {
            tbody.innerHTML = '<tr id="fila_vacia" class="bg-white dark:bg-gray-800"><td colspan="5" class="text-center py-6 text-gray-400 dark:text-gray-500">No hay movimientos agregados.</td></tr>';
            return;
        }

        listaMovimientos.forEach(mov => {
            const tr = document.createElement('tr');
            // Agregadas clases de Tailwind para dark mode en las filas
            tr.className = "bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors";

            const colorTipo = mov.tipo_movimiento === 'ENTRADA' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
            const detalles = `<span class="text-xs text-gray-600 dark:text-gray-400"><b>Destino:</b> ${mov.destino} | <b>Rem:</b> ${mov.n_remision} | <b>Cot:</b> ${mov.n_cotizacion}<br><b>Nov:</b> ${mov.novedad}</span>`;

            tr.innerHTML = `
                <td class="px-4 py-3 font-bold ${colorTipo}">${mov.tipo_movimiento}</td>
                <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">${mov.nombreVisual}</td>
                <td class="px-4 py-3 text-center text-gray-900 dark:text-white font-bold">${mov.cantidad}</td>
                <td class="px-4 py-3">${detalles}</td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="eliminarFila(${mov.id})" class="text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function guardarTodo() {
        if (listaMovimientos.length === 0) {
            alert('Debes agregar al menos un movimiento a la lista.');
            return;
        }
        document.getElementById('movimientos_json').value = JSON.stringify(listaMovimientos);
        document.getElementById('form_final').submit();
    }
</script>