<?php if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

// ==============================================================================
// MAPA DE CIUDADES (Sin tocar la Base de Datos)
// Coloca aquí el ID del técnico motorizado (ej: moto_5) y la ciudad que le corresponde.
// Si agregas un técnico nuevo en el futuro, solo lo sumas a esta lista.
// ==============================================================================
$ciudadesMotorizados = [
    '4' => 'Bogotá',
    '6' => 'Bogotá',
    '12' => 'Bogotá',
    '15' => 'Bogotá',
    '16' => 'Bogotá',
    '18' => 'Bogotá',
    '7' => 'Medellin',
    '8' => 'Medellin',
    '9' => 'Cali',
    '13' => 'Cali',
    '10' => 'Barranquilla',
    '11' => 'Barranquilla',
    // 'moto_ID' => 'Ciudad'
];
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        background-color: transparent !important;
        border: 1px solid #d1d5db !important;
        height: 45px !important;
        display: flex;
        align-items: center;
    }

    /* FIX: Color del texto al escribir en el buscador de Select2 (Modo Claro) */
    .select2-search__field {
        color: #1f2937 !important;
        outline: none !important;
    }

    .dark .select2-container--default .select2-selection--single {
        border-color: #4b5563 !important;
        color: white !important;
    }

    .dark .select2-selection__rendered {
        color: #e5e7eb !important;
    }

    .dark .select2-dropdown {
        background-color: #1f2937 !important;
        border-color: #4b5563 !important;
        color: white !important;
    }

    /* FIX: Color del texto y fondo al escribir en el buscador de Select2 (Modo Oscuro) */
    .dark .select2-search__field {
        background-color: #374151 !important;
        color: #ffffff !important;
        border: 1px solid #4b5563 !important;
    }
</style>

<div class="w-full max-w-6xl mx-auto px-4">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">

        <div class="mb-6 border-b dark:border-gray-700 pb-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-dolly text-indigo-600 mr-2"></i> Movimientos de Inventario
            </h1>
            <?php if (!empty($data['precargue'])): ?>
                <div class="mt-3 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-sm text-amber-800 dark:text-amber-300">
                    <i class="fas fa-robot mr-2"></i>
                    <b><?= count($data['precargue']) ?> máquina(s) terminadas en inees3</b> pendientes de salida.
                    Selecciónalas abajo en el grupo “Máquinas terminadas (inees3)” y confirma.
                </div>
            <?php endif; ?>
        </div>

        <!-- ===================== SALIDA MASIVA DE MÁQUINAS ===================== -->
        <?php $maquinasEnBodega = $data['maquinasEnBodega'] ?? []; ?>
        <?php if (!empty($maquinasEnBodega)): ?>
        <div class="mt-6 mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-bold text-blue-800 dark:text-blue-200">
                    <i class="fas fa-server mr-2"></i>Salida Masiva de Máquinas en Bodega
                    <span class="ml-2 px-2 py-0.5 bg-blue-200 dark:bg-blue-800 text-blue-900 dark:text-blue-100 rounded-full text-xs font-bold">
                        <?= count($maquinasEnBodega) ?> disponibles
                    </span>
                </h2>
                <div class="flex gap-2">
                    <button id="btnSeleccionarTodo" type="button"
                        class="px-3 py-1.5 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check-double mr-1"></i>Seleccionar Todo
                    </button>
                    <button id="btnLimpiarSeleccion" type="button"
                        class="px-3 py-1.5 bg-gray-500 text-white text-sm font-bold rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times mr-1"></i>Limpiar
                    </button>
                </div>
            </div>

            <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">
                Selecciona las máquinas que salen de bodega y confirma. Se registrará la salida (SALIDA_REMISION)
                y se quitarán del inventario. Si ya tienen salida registrada, el sistema lo bloqueará automáticamente.
            </p>

            <div class="max-h-72 overflow-y-auto rounded-lg border border-blue-200 dark:border-blue-800 bg-white dark:bg-gray-800">
                <table class="w-full text-sm" id="tablaSalidaMasiva">
                    <thead class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 sticky top-0">
                        <tr>
                            <th class="p-2 text-center"><input type="checkbox" id="checkAllMaq"></th>
                            <th class="p-2 text-left">Serial</th>
                            <th class="p-2 text-left">Tipo de Máquina</th>
                            <th class="p-2 text-left">Bodega Actual</th>
                            <th class="p-2 text-left">Condición</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($maquinasEnBodega as $mq): ?>
                        <tr class="border-b border-blue-100 dark:border-blue-900/40 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                            <td class="p-2 text-center">
                                <input type="checkbox" class="checkMaquina" value="<?= (int)$mq['id_maquina'] ?>"
                                       data-serial="<?= htmlspecialchars($mq['numero_serie']) ?>">
                            </td>
                            <td class="p-2 font-mono text-xs"><?= htmlspecialchars($mq['numero_serie']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($mq['nombreTipoMaquina']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($mq['nombre_bodega']) ?></td>
                            <td class="p-2 text-xs"><?= htmlspecialchars($mq['condicion']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 border-t border-blue-200 dark:border-blue-800 pt-3 space-y-3">
                <p class="text-sm font-bold text-blue-800 dark:text-blue-200">
                    <i class="fas fa-file-alt mr-1"></i>Datos de la Salida
                </p>
                <div>
                    <input type="text" id="masiva_destino" placeholder="Destino específico (Opcional)"
                        class="w-full p-2 border rounded-lg text-sm dark:bg-gray-800 dark:text-white dark:border-gray-600">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" id="masiva_remision" placeholder="Nº Remisión"
                        class="w-full p-2 border rounded-lg text-sm dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    <input type="text" id="masiva_novedad" placeholder="Novedad"
                        class="w-full p-2 border rounded-lg text-sm dark:bg-gray-800 dark:text-white dark:border-gray-600">
                </div>
                <div>
                    <input type="text" id="masiva_observacion" placeholder="Observación (opcional)..."
                        class="w-full p-2 border rounded-lg text-sm dark:bg-gray-800 dark:text-white dark:border-gray-600">
                </div>
            </div>

            <div class="mt-3 flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                <div class="flex gap-2 flex-wrap">
                    <button id="btnConfirmarSalidaMasiva" type="button"
                        class="px-5 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span id="textoBotonSalida">Confirmar Salida (<span id="contadorSeleccion">0</span>)</span>
                    </button>
                </div>
            </div>

            <!-- Resultado de la operación -->
            <div id="resultadoSalidaMasiva" class="hidden mt-3 p-3 rounded-lg text-sm"></div>
        </div>
        <?php endif; ?>
        <!-- ===================== FIN SALIDA MASIVA ===================== -->

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- CONTROLES IZQUIERDOS -->
            <div class="lg:col-span-1 space-y-5 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl">

                <div id="bloque_tecnico">
                    <label class="block text-sm font-bold mb-1 dark:text-gray-300">1. Seleccionar Técnico
                        (Opcional)</label>
                    <select id="id_tecnico" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:text-white">
                        <option value="">-- Sin Técnico (Salida General) --</option>
                        <?php foreach ($data['tecnicos'] as $t): ?>
                            <?php
                            // Lógica para agregar la ciudad si es motorizado
                            $textoCiudad = "";
                            if (strpos($t['usuario_id'], 'moto_') === 0) {
                                // Quitamos el prefijo 'moto_' para que coincida con tus IDs numéricos (4, 6, 12...)
                                $idLimpio = str_replace('moto_', '', $t['usuario_id']);

                                $textoCiudad = isset($ciudadesMotorizados[$idLimpio])
                                    ? ' - ' . $ciudadesMotorizados[$idLimpio]
                                    : ' - Ciudad N/A';
                            }
                            ?>
                            <option value="<?= $t['usuario_id'] ?>">
                                <?= htmlspecialchars($t['nombre']) ?> (<?= $t['cargo'] ?><?= $textoCiudad ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="bloque_asignacion">
                    <label class="block text-sm font-bold mb-1 dark:text-gray-300">2. Destino / Ruta</label>
                    <select id="tipo_asignacion" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:text-white">
                        <option value="interno">Taller / Armado Interno</option>
                        <option value="motorizado">Ruta / Motorizado (Sincroniza App Externa)</option>
                    </select>
                </div>

                <!-- Datos Globales del Documento -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <label class="block text-sm font-bold mb-3 text-gray-700 dark:text-gray-300">3. Datos de la
                        Salida</label>
                    <div class="space-y-3">
                        <div>
                            <input type="text" id="global_destino" placeholder="Destino específico (Opcional)"
                                class="w-full p-2 text-sm border rounded-lg dark:bg-gray-800 dark:text-white">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" id="global_remision" placeholder="Nº Remisión"
                                class="w-full p-2 text-sm border rounded-lg dark:bg-gray-800 dark:text-white">
                            <input type="text" id="global_cotizacion" placeholder="Nº Cotización"
                                class="w-full p-2 text-sm border rounded-lg dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <input type="text" id="global_novedad" placeholder="Novedad / Observación"
                                class="w-full p-2 text-sm border rounded-lg dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-indigo-600 dark:text-indigo-400">
                            <i class="fas fa-barcode"></i> 4. Escanear con Pistola
                        </label>
                        <input type="text" id="lector_barras" autofocus
                            class="w-full p-3 border-2 border-indigo-400 rounded-lg dark:bg-gray-800 dark:text-white shadow-inner focus:outline-none focus:ring-2 focus:ring-indigo-600"
                            placeholder="Dispara el código aquí...">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1 dark:text-gray-300">O buscar manualmente</label>
                        <select id="buscador_general" class="w-full">
                            <option value="">Escribe código o nombre...</option>
                            <option value="OTRO" data-tipo="manual" class="font-bold text-orange-600">➕ Otro (Manual, no
                                está en catálogo)</option>
                            <optgroup label="Inventario con Stock">
                                <?php foreach ($data['inventario'] as $inv): ?>
                                    <option value="<?= $inv['id_interno'] ?>" data-tipo="<?= $inv['tipo'] ?>"
                                        data-nombre="<?= htmlspecialchars($inv['nombre']) ?>"
                                        data-codigo="<?= $inv['codigo'] ?>" data-condicion="<?= $inv['condicion'] ?>"
                                        data-stock="<?= $inv['stock'] ?>">
                                        [<?= strtoupper($inv['tipo']) ?><?= $inv['tipo'] === 'repuesto' ? ' - ' . $inv['condicion'] : '' ?>]
                                        [<?= $inv['codigo'] ?: 'S/C' ?>] <?= htmlspecialchars($inv['nombre']) ?> (Stock:
                                        <?= $inv['stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <?php if (!empty($data['precargue'])): ?>
                                <optgroup label="Máquinas terminadas (inees3) — precargue">
                                    <?php foreach ($data['precargue'] as $pm): ?>
                                        <option value="MAQ-<?= htmlspecialchars($pm['serial']) ?>"
                                            data-tipo="maquina"
                                            data-nombre="Máquina <?= htmlspecialchars($pm['nombre_tipo_maquina'] ?? '') ?>"
                                            data-codigo="<?= htmlspecialchars($pm['serial']) ?>"
                                            data-condicion="terminada"
                                            data-stock="1"
                                            data-orden="<?= (int)$pm['orden_id'] ?>"
                                            data-ubicacion="<?= htmlspecialchars($pm['nombre_bodega'] ?? 'Sin bodega') ?>">
                                            [MÁQUINA] [<?= htmlspecialchars($pm['serial']) ?>]
                                            <?= htmlspecialchars($pm['nombre_tipo_maquina'] ?? '') ?>
                                            — Orden #<?= (int)$pm['orden_id'] ?>
                                            (<?= htmlspecialchars($pm['nombre_bodega'] ?? 'Sin bodega') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLA DEL CARRITO -->
    <div class="lg:col-span-2 mt-6 lg:mt-0">
        <div class="overflow-x-auto min-h-[300px]">
            <table class="w-full text-sm text-left border dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                    <tr>
                        <th class="p-3">Artículo / Clasificación</th>
                        <th class="p-3">Novedad / Serial</th>
                        <th class="p-3 text-center">Cant.</th>
                        <th class="p-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody id="carrito_body" class="divide-y dark:divide-gray-700">
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-400">Escanea o busca
                            repuestos/productos para comenzar...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <button onclick="limpiarCarrito()"
                class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-bold">Limpiar</button>
            <button onclick="procesarSalida()" id="btn_procesar"
                class="px-10 py-3 bg-indigo-600 text-white rounded-lg font-bold shadow-lg hover:bg-indigo-700 transition-all">
                <i class="fas fa-check-double mr-2"></i> PROCESAR MOVIMIENTOS
            </button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>

<script>
    let carrito = [];

    $(document).ready(function () {
        // Inicializamos ambos Select2
        $('#buscador_general').select2({ width: '100%' });
        $('#id_tecnico').select2({ width: '100%' }); // <-- NUEVO: Select2 para Técnicos

        $('#buscador_general').on('select2:select', function (e) {
            let el = e.params.data.element.dataset;
            if (e.params.data.id === 'OTRO') {
                agregarManual();
                $(this).val(null).trigger('change');
                return;
            }

            agregarAlCarrito({
                id: e.params.data.id,
                tipo: el.tipo,
                nombre: el.nombre,
                codigo: el.codigo,
                condicion: el.condicion,
                stock: parseInt(el.stock),
                orden_id: el.orden ? parseInt(el.orden) : null
            });

            $(this).val(null).trigger('change');
            setTimeout(() => { $(this).select2('open'); }, 100);
        });

        $('#lector_barras').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                let codigoEscaneado = $(this).val().trim();
                if (codigoEscaneado === '') return;

                let opcionEncontrada = $('#buscador_general option').filter(function () {
                    return $(this).data('codigo') == codigoEscaneado;
                }).first();

                if (opcionEncontrada.length > 0) {
                    let el = opcionEncontrada[0].dataset;
                    agregarAlCarrito({
                        id: opcionEncontrada.val(),
                        tipo: el.tipo,
                        nombre: el.nombre,
                        codigo: el.codigo,
                        condicion: el.condicion,
                        stock: parseInt(el.stock),
                        orden_id: el.orden ? parseInt(el.orden) : null
                    });
                    $(this).val('');
                } else {
                    Swal.fire({ icon: 'warning', title: 'No encontrado', text: `El código [${codigoEscaneado}] no tiene stock o no existe.`, timer: 2000 });
                    $(this).val('');
                }
            }
        });
    });

    function toggleTipoMovimiento() {
        const tipo = $('#tipo_movimiento').val();
        if (tipo === 'ENTRADA') {
            $('#bloque_tecnico, #bloque_asignacion').hide();
            $('#grupo_stock').hide();
            $('#grupo_catalogo').show();
        } else {
            $('#bloque_tecnico, #bloque_asignacion').show();
            $('#grupo_stock').show();
            $('#grupo_catalogo').hide();
        }
    }

    function agregarManual() {
        Swal.fire({
            title: 'Ítem manual',
            input: 'text',
            inputLabel: 'Nombre del repuesto/producto (no está en catálogo)',
            showCancelButton: true,
            confirmButtonText: 'Agregar'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                agregarAlCarrito({
                    id: 'OTRO',
                    tipo: 'repuesto',
                    nombre: result.value,
                    repuesto_manual: result.value,
                    codigo: '',
                    condicion: 'N/A',
                    stock: 999999
                });
            }
        });
    }

    function agregarAlCarrito(item) {
        item.novedad = item.novedad || '';

        // Las máquinas son unitarias: nunca fusionar, stock siempre 1.
        if (item.tipo === 'maquina') {
            if (carrito.some(x => x.tipo === 'maquina' && x.codigo === item.codigo)) {
                Swal.fire('Atención', 'Esa máquina ya está en el carrito.', 'warning');
                return;
            }
            item.cantidad = 1;
            item.stock = 1;
            carrito.push(item);
            renderizarCarrito();
            return;
        }

        // Solo fusiona cantidad si ya existe el mismo ítem Y aún no tiene novedad/serial escrito.
        // Si ya tiene novedad, se crea una fila nueva (para poder distinguir seriales).
        let existe = item.id !== 'OTRO'
            ? carrito.find(x => x.id == item.id && x.tipo == item.tipo && !x.novedad)
            : null;

        if (existe) {
            if (existe.cantidad < item.stock) {
                existe.cantidad++;
            } else {
                Swal.fire('Atención', 'No hay más stock disponible en estantería.', 'warning');
            }
        } else {
            item.cantidad = 1;
            carrito.push(item);
        }
        renderizarCarrito();
    }

    function renderizarCarrito() {
        let html = '';
        carrito.forEach((item, index) => {
            let etiqueta = item.id === 'OTRO'
                ? `<span class="text-[10px] bg-orange-100 text-orange-800 font-bold px-1.5 py-0.5 rounded uppercase">Manual</span>`
                : (item.tipo === 'maquina'
                    ? `<span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-1.5 py-0.5 rounded uppercase">Máquina inees3${item.orden_id ? ' — Orden #' + item.orden_id : ''}</span>`
                    : (item.tipo === 'repuesto'
                    ? `<span class="text-[10px] bg-blue-100 text-blue-800 font-bold px-1.5 py-0.5 rounded uppercase">Repuesto — ${item.condicion}</span>`
                    : `<span class="text-[10px] bg-purple-100 text-purple-800 font-bold px-1.5 py-0.5 rounded uppercase">Consumible</span>`));

            html += `
        <tr class="dark:text-gray-300">
            <td class="p-3">
                <b>[${item.codigo || 'S/C'}]</b> ${item.nombre}<br>${etiqueta}
            </td>
            <td class="p-3">
                <input type="text" value="${item.novedad || ''}" placeholder="Ej: Serial BV-1000..."
                onchange="actualizarNovedad(${index}, this.value)"
                class="w-full p-1 border rounded text-xs dark:bg-gray-800 dark:text-white">
            </td>
            <td class="p-3 text-center">
                <input type="number" value="${item.cantidad}" min="1" max="${item.stock}" 
                onchange="actualizarCantidad(${index}, this.value)"
                class="w-16 p-1 border rounded text-center dark:bg-gray-800">
            </td>
            <td class="p-3 text-center">
                <button onclick="eliminarDelCarrito(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times-circle text-lg"></i>
                </button>
            </td>
        </tr>`;
        });
        $('#carrito_body').html(html || '<tr><td colspan="4" class="p-10 text-center text-gray-400">Escanea o busca repuestos para comenzar...</td></tr>');
    }

    function actualizarNovedad(index, valor) {
        carrito[index].novedad = valor;
    }

    function actualizarCantidad(index, valor) {
        let cant = parseInt(valor);
        let maxCant = carrito[index].stock;
        if (cant > maxCant) {
            Swal.fire('Stock insuficiente', 'Máximo disponible: ' + maxCant, 'error');
            carrito[index].cantidad = maxCant;
        } else {
            carrito[index].cantidad = cant;
        }
        renderizarCarrito();
    }

    function eliminarDelCarrito(index) {
        carrito.splice(index, 1);
        renderizarCarrito();
    }

    function limpiarCarrito() {
        carrito = [];
        renderizarCarrito();
    }

    function procesarSalida() {
        let idTecnico = $('#id_tecnico').val();
        if (carrito.length === 0) return Swal.fire('Error', 'El carrito está vacío.', 'error');

        $('#btn_procesar').prop('disabled', true).text('Procesando...');

        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: {
                id_tecnico: idTecnico,
                tipo_asignacion: $('#tipo_asignacion').val(),
                destino: $('#global_destino').val(),
                remision: $('#global_remision').val(),
                cotizacion: $('#global_cotizacion').val(),
                novedad: $('#global_novedad').val(),
                items: JSON.stringify(carrito)
            },
            success: function (response) {
                try {
                    let res = JSON.parse(response);
                    if (res.exito) {
                        Swal.fire('¡Movimiento Exitoso!', res.msg, 'success').then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', res.msg, 'error');
                        $('#btn_procesar').prop('disabled', false).text('PROCESAR MOVIMIENTOS');
                    }
                } catch (error) {
                    Swal.fire('Error del Servidor', `<textarea class="w-full text-xs p-2 text-red-700" rows="5" readonly>${response}</textarea>`, 'error');
                    $('#btn_procesar').prop('disabled', false).text('PROCESAR MOVIMIENTOS');
                }
            }
        });
    }

    // ================================================================
    // SALIDA MASIVA DE MÁQUINAS
    // ================================================================
    (function() {
        // Contador de seleccionadas
        function actualizarContador() {
            const n = $('.checkMaquina:checked').length;
            $('#contadorSeleccion').text(n);
        }

        // Seleccionar / deseleccionar todas
        $('#checkAllMaq').on('change', function() {
            $('.checkMaquina').prop('checked', $(this).is(':checked'));
            actualizarContador();
        });

        $(document).on('change', '.checkMaquina', actualizarContador);

        // Botones auxiliares
        $('#btnSeleccionarTodo').on('click', function() {
            $('#checkAllMaq').prop('checked', true);
            $('.checkMaquina').prop('checked', true);
            actualizarContador();
        });

        $('#btnLimpiarSeleccion').on('click', function() {
            $('#checkAllMaq').prop('checked', false);
            $('.checkMaquina').prop('checked', false);
            actualizarContador();
        });

        // Confirmar salida masiva
        $('#btnConfirmarSalidaMasiva').on('click', function() {
            const ids = [];
            $('.checkMaquina:checked').each(function() {
                ids.push($(this).val());
            });

            if (ids.length === 0) {
                Swal.fire('Sin selección', 'Selecciona al menos una máquina.', 'warning');
                return;
            }

            const destino = $('#masiva_destino').val().trim();
            const remision = $('#masiva_remision').val().trim();
            const novedad = $('#masiva_novedad').val().trim();
            const observacion = $('#masiva_observacion').val().trim();

            Swal.fire({
                title: '¿Confirmar salida de ' + ids.length + ' máquina(s)?',
                text: 'Se registrará la salida y se quitarán del inventario de bodega.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar salida',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $('#btnConfirmarSalidaMasiva').prop('disabled', true).text('Procesando...');

                $.ajax({
                    url: '<?= BASE_URL ?>salidaCrear?accion=marcarSalidasManuales',
                    method: 'POST',
                    data: {
                        ids_maquinas: ids,
                        destino: destino,
                        remision: remision,
                        novedad: novedad,
                        observacion: observacion
                    },
                    success: function(res) {
                        try {
                            if (res.exito) {
                                Swal.fire('¡Éxito!', res.msg, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', res.msg, 'error');
                                $('#btnConfirmarSalidaMasiva').prop('disabled', false).text('Confirmar Salida');
                            }
                        } catch(e) {
                            Swal.fire('Error del Servidor', res, 'error');
                            $('#btnConfirmarSalidaMasiva').prop('disabled', false).text('Confirmar Salida');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo conectar al servidor.', 'error');
                        $('#btnConfirmarSalidaMasiva').prop('disabled', false).text('Confirmar Salida');
                    }
                });
            });
        });
    })();
</script>