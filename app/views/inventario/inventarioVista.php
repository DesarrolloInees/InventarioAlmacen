<?php if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado."); 

$rolUsuario = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;
$esSuperUsuario = ($rolUsuario == 1);
?>

<div class="w-full px-4 md:px-8 pb-8">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center">
                <i class="fas fa-boxes text-blue-500 mr-3"></i> Inventario Disponible
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Stock actual de productos disponibles para la gestión.
            </p>
        </div>

        <div>
            <button type="button" onclick="abrirModalSolicitudAlmacen()"
                class="inline-flex items-center gap-2.5 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm rounded-2xl shadow-md hover:shadow-lg transition transform hover:scale-105">
                <i class="fas fa-paper-plane text-base"></i>
                <span>Crear Solicitud de Actualización de Almacén</span>
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <?php if (empty($inventario)): ?>
            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <i class="fas fa-box-open text-4xl mb-3"></i>
                <p class="font-medium">No hay productos disponibles en inventario actualmente.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 uppercase text-xs border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Producto</th>
                            <th class="px-6 py-3">Cant. Disponible</th>
                            <th class="px-6 py-3">Ubicación</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3">Última Actualización</th>
                            <?php if ($esSuperUsuario): ?>
                                <th class="px-6 py-3">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($inventario as $item): 
                            // Prioriza 'estado_inventario' si viene de la consulta con JOIN, o cae a 'estado'
                            $estado = strtolower($item['estado_inventario'] ?? $item['estado'] ?? 'disponible');
                            
                            $badge = match($estado) {
                                'disponible' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'stock_bajo' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                'agotado'    => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                default      => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                            };

                            // Datos adicionales desde la tabla productos
                            $nombreProducto = $item['nombre_producto'] ?? ('Producto #' . ($item['producto_id'] ?? ''));
                            $categoria = $item['categoria'] ?? null;
                            $unidadMedida = $item['unidad_medida'] ?? 'unidades';
                            $fechaActualizacion = !empty($item['fecha_actualizacion']) 
                                ? date('d/m/Y H:i', strtotime($item['fecha_actualizacion'])) 
                                : 'Sin registro';
                        ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4 font-medium text-gray-500">
                                    #<?= htmlspecialchars($item['inventario_id'] ?? '') ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-800 dark:text-white">
                                    <?= htmlspecialchars($nombreProducto) ?>
                                    <?php if ($categoria): ?>
                                        <span class="block text-xs font-normal text-gray-400 mt-0.5">
                                            Categoría: <?= htmlspecialchars($categoria) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">
                                    <span class="text-base text-blue-600 dark:text-blue-400 font-bold">
                                        <?= htmlspecialchars($item['cantidad_disponible'] ?? 0) ?>
                                    </span> 
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        <?= htmlspecialchars($unidadMedida) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <?= htmlspecialchars($item['ubicacion'] ?? 'Sin asignar') ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider <?= $badge ?>">
                                        <?= htmlspecialchars(str_replace('_', ' ', $estado)) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">
                                    <?= htmlspecialchars($fechaActualizacion) ?>
                                </td>
                                <?php if ($esSuperUsuario): ?>
                                    <td class="px-6 py-4">
                                        <a href="<?= BASE_URL ?>inventario&editar=<?= htmlspecialchars($item['inventario_id']) ?>"
                                            class="inline-flex items-center gap-1.5 text-blue-500 hover:text-blue-600 hover:underline text-sm font-medium">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL: CREAR SOLICITUD DE ACTUALIZACIÓN DE ALMACÉN -->
<div id="modalSolicitudAlmacen" class="hidden fixed inset-0 z-[99999] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-lg overflow-hidden transform transition-all">
        <!-- ENCABEZADO DEL MODAL -->
        <div class="bg-gradient-to-r from-blue-900 to-indigo-900 p-6 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/30 flex items-center justify-center text-blue-200">
                    <i class="fas fa-warehouse text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Solicitud de Actualización de Almacén</h3>
                    <p class="text-xs text-blue-200">Se enviará al correo predeterminado del almacén</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalSolicitudAlmacen()" class="text-blue-200 hover:text-white transition text-lg p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- FORMULARIO DEL MODAL -->
        <form id="formSolicitudAlmacen" onsubmit="enviarSolicitudAlmacen(event)" class="p-6 space-y-5">
            <!-- CORREO DESTINO (PREDETERMINADO) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5 flex items-center gap-2">
                    <i class="fas fa-envelope text-blue-500"></i> Correo Destino (Predeterminado)
                </label>
                <input type="email" value="almacen@inees.co" readonly disabled
                    class="w-full bg-slate-100 dark:bg-slate-700/80 border border-slate-300 dark:border-slate-600 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-200 cursor-not-allowed shadow-inner">
            </div>

            <!-- ASUNTO DE LA SOLICITUD -->
            <div>
                <label for="asunto_almacen" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                    Asunto de la Solicitud <span class="text-red-500">*</span>
                </label>
                <input type="text" id="asunto_almacen" name="asunto" required placeholder="Ej: Solicitud de actualización de stock / ingreso de productos"
                    class="w-full bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <!-- DETALLE / MENSAJE DE LA SOLICITUD -->
            <div>
                <label for="detalle_almacen" class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                    Detalle / Mensaje de la Solicitud <span class="text-red-500">*</span>
                </label>
                <textarea id="detalle_almacen" name="detalle" rows="4" required placeholder="Escribe detalladamente los ítems, cantidades o cambios a actualizar en el almacén..."
                    class="w-full bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none"></textarea>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="cerrarModalSolicitudAlmacen()"
                    class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-medium text-sm transition">
                    Cancelar
                </button>
                <button type="submit" id="btnEnviarSolicitudAlmacen"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium text-sm shadow-sm transition">
                    <i class="fas fa-paper-plane"></i> Enviar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalSolicitudAlmacen() {
    const modal = document.getElementById('modalSolicitudAlmacen');
    if (modal) modal.classList.remove('hidden');
}

function cerrarModalSolicitudAlmacen() {
    const modal = document.getElementById('modalSolicitudAlmacen');
    if (modal) modal.classList.add('hidden');
}

function enviarSolicitudAlmacen(e) {
    e.preventDefault();

    const btn = document.getElementById('btnEnviarSolicitudAlmacen');
    const asunto = document.getElementById('asunto_almacen').value.trim();
    const detalle = document.getElementById('detalle_almacen').value.trim();

    if (!asunto || !detalle) {
        alert('Por favor completa todos los campos requeridos.');
        return;
    }

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    }

    const formData = new FormData();
    formData.append('accion', 'solicitud_actualizacion_almacen');
    formData.append('asunto', asunto);
    formData.append('detalle', detalle);

    fetch('<?= BASE_URL ?>inventario', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.ok) {
            alert('✅ ' + data.mensaje);
            cerrarModalSolicitudAlmacen();
            document.getElementById('formSolicitudAlmacen').reset();
        } else {
            alert('⚠️ ' + (data.mensaje || 'Ocurrió un error al enviar la solicitud.'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('❌ Error al enviar la solicitud. Revisa tu conexión.');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Solicitud';
        }
    });
}
</script>