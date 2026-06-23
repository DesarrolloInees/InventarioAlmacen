<?php if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado."); ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        background-color: transparent !important;
        border: 1px solid #d1d5db !important;
        height: 45px !important;
        display: flex;
        align-items: center;
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
</style>

<div class="w-full max-w-6xl mx-auto px-4">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">

        <div class="mb-6 border-b dark:border-gray-700 pb-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-dolly text-indigo-600 mr-2"></i> Despacho de Inventario
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- CONTROLES IZQUIERDOS -->
            <div class="lg:col-span-1 space-y-5 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl">
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-gray-300">1. Seleccionar Técnico</label>
                    <select id="id_tecnico" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:text-white">
                        <option value="">-- Buscar Técnico --</option>
                        <?php foreach ($data['tecnicos'] as $t): ?>
                            <option value="<?= $t['usuario_id'] ?>"><?= htmlspecialchars($t['nombre']) ?>
                                (<?= $t['cargo'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-gray-300">2. Destino / Ruta</label>
                    <select id="tipo_asignacion" class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:text-white">
                        <option value="interno">Taller / Armado Interno</option>
                        <option value="motorizado">Ruta / Motorizado (Sincroniza App Externa)</option>
                    </select>
                </div>

                <div class="lg:col-span-1 space-y-5 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl">

                    <div>
                        <label class="block text-sm font-bold mb-1 text-indigo-600 dark:text-indigo-400">
                            <i class="fas fa-barcode"></i> 3. Escanear con Pistola
                        </label>
                        <input type="text" id="lector_barras" autofocus
                            class="w-full p-3 border-2 border-indigo-400 rounded-lg dark:bg-gray-800 dark:text-white shadow-inner focus:outline-none focus:ring-2 focus:ring-indigo-600"
                            placeholder="Dispara el código aquí...">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1 dark:text-gray-300">O buscar manualmente</label>
                        <select id="buscador_general" class="w-full">
                            <option value="">Escribe código o nombre...</option>
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
                        </select>
                    </div>
                </div>
            </div>

            <!-- TABLA DEL CARRITO -->
            <div class="lg:col-span-2">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-sm text-left border dark:border-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                            <tr>
                                <th class="p-3">Artículo / Clasificación</th>
                                <th class="p-3 text-center">Cant.</th>
                                <th class="p-3 text-center">Stock</th>
                                <th class="p-3 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="carrito_body" class="divide-y dark:divide-gray-700">
                            <tr>
                                <td colspan="4" class="p-10 text-center text-gray-400">Escanea o busca
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
                        <i class="fas fa-check-double mr-2"></i> PROCESAR SALIDA
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let carrito = [];

    $(document).ready(function () {
        $('#buscador_general').select2({ width: '100%' });

        $('#buscador_general').on('select2:select', function (e) {
            let el = e.params.data.element.dataset;

            agregarAlCarrito({
                id: e.params.data.id,
                tipo: el.tipo,
                nombre: el.nombre,
                codigo: el.codigo,
                condicion: el.condicion,
                stock: parseInt(el.stock)
            });

            $(this).val(null).trigger('change');
            setTimeout(() => { $(this).select2('open'); }, 100);
        });

        // Capturar el disparo de la pistola de código de barras
        $('#lector_barras').on('keypress', function (e) {
            // Si la tecla presionada es Enter (código 13)
            if (e.which === 13) {
                e.preventDefault(); // Evitamos que el formulario haga submit (si lo hubiera)

                let codigoEscaneado = $(this).val().trim();
                if (codigoEscaneado === '') return;

                // Buscamos dentro de las opciones de tu select el que tenga ese data-codigo
                let opcionEncontrada = $('#buscador_general option').filter(function () {
                    return $(this).data('codigo') == codigoEscaneado;
                }).first();

                if (opcionEncontrada.length > 0) {
                    let el = opcionEncontrada[0].dataset;

                    // Reutilizamos tu función actual
                    agregarAlCarrito({
                        id: opcionEncontrada.val(),
                        tipo: el.tipo,
                        nombre: el.nombre,
                        codigo: el.codigo,
                        condicion: el.condicion,
                        stock: parseInt(el.stock)
                    });

                    // Limpiamos el input para el siguiente escaneo
                    $(this).val('');
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No encontrado',
                        text: `El artículo con código [${codigoEscaneado}] no existe o no tiene stock disponible.`,
                        timer: 2000 // Opcional: que se cierre solo para no interrumpir el flujo
                    });
                    $(this).val(''); // Limpiamos aunque falle
                }
            }
        });
    });

    function agregarAlCarrito(item) {
        let existe = carrito.find(x => x.id == item.id && x.tipo == item.tipo);
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
            let etiqueta = item.tipo === 'repuesto'
                ? `<span class="text-[10px] bg-blue-100 text-blue-800 font-bold px-1.5 py-0.5 rounded uppercase">Repuesto — ${item.condicion}</span>`
                : `<span class="text-[10px] bg-purple-100 text-purple-800 font-bold px-1.5 py-0.5 rounded uppercase">Consumible</span>`;

            html += `
        <tr class="dark:text-gray-300">
            <td class="p-3">
                <b>[${item.codigo || 'S/C'}]</b> ${item.nombre}<br>${etiqueta}
            </td>
            <td class="p-3 text-center">
                <input type="number" value="${item.cantidad}" min="1" max="${item.stock}" 
                onchange="actualizarCantidad(${index}, this.value)"
                class="w-16 p-1 border rounded text-center dark:bg-gray-800">
            </td>
            <td class="p-3 text-center text-gray-400">${item.stock}</td>
            <td class="p-3 text-center">
                <button onclick="eliminarDelCarrito(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times-circle"></i>
                </button>
            </td>
        </tr>`;
        });
        $('#carrito_body').html(html || '<tr><td colspan="4" class="p-10 text-center text-gray-400">Escanea o busca repuestos/productos para comenzar...</td></tr>');
    }

    function actualizarCantidad(index, valor) {
        let cant = parseInt(valor);
        if (cant > carrito[index].stock) {
            Swal.fire('Stock insuficiente', 'La cantidad máxima disponible es ' + carrito[index].stock, 'error');
            carrito[index].cantidad = carrito[index].stock;
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
        let tipoAsignacion = $('#tipo_asignacion').val();

        if (!idTecnico) return Swal.fire('Error', 'Selecciona un técnico primero', 'error');
        if (carrito.length === 0) return Swal.fire('Error', 'El carrito de despacho está vacío', 'error');

        $('#btn_procesar').prop('disabled', true).text('Procesando Despacho...');

        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: {
                id_tecnico: idTecnico,
                tipo_asignacion: tipoAsignacion,
                items: JSON.stringify(carrito)
            },
            success: function (response) {
                let res = JSON.parse(response);
                // CAMBIAMOS RES.STATUS POR RES.EXITO
                if (res.exito) {
                    Swal.fire('¡Despacho Exitoso!', res.msg, 'success')
                        .then(() => { location.reload(); });
                } else {
                    Swal.fire('Error de Procesamiento', res.msg, 'error');
                    $('#btn_procesar').prop('disabled', false).text('PROCESAR SALIDA');
                }
            }
        });
    }
</script>