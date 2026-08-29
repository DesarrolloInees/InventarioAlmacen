<?php if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado."); 

$rolId = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;
$nombreRol = $_SESSION['nombre_rol'] ?? '';
$puedeGestionar = ($rolId == 1 || $rolId == 2 || $nombreRol === 'superUsuario' || $nombreRol === 'tecnico');

$etiquetasPrioridad = [
    'sin_asignar' => 'Sin Asignar',
    'baja'        => 'Baja',
    'media'       => 'Media',
    'alta'        => 'Alta',
    'urgente'     => 'Urgente',
];

$estilosPrioridad = [
    'sin_asignar' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600',
    'baja'        => 'bg-slate-100 text-slate-700 dark:bg-slate-700/40 dark:text-slate-300 border border-slate-200 dark:border-slate-600',
    'media'       => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800',
    'alta'        => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300 border border-orange-200 dark:border-orange-800',
    'urgente'     => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 border border-red-200 dark:border-red-800',
];

$prioridadActual = $solicitud['prioridad'] ?? 'sin_asignar';

// NUEVO: texto descriptivo del plazo segun la prioridad
$etiquetasPlazo = [
    'baja'        => '3 días',
    'media'       => 'Día y medio',
    'alta'        => '1 día',
    'urgente'     => '12 horas',
];

// NUEVO: calcular si el plazo esta vencido (solo tiene sentido si la solicitud sigue abierta)
$estadosFinales = ['atendida', 'rechazada', 'cancelada'];
$plazoVencido = false;
if (!empty($solicitud['fecha_limite']) && !in_array($solicitud['estado'], $estadosFinales)) {
    $plazoVencido = strtotime($solicitud['fecha_limite']) < time();
}
?>

<div class="w-full px-4 md:px-8 pb-8">

    <!-- ENCABEZADO -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="<?= BASE_URL ?>solicitudes"
                class="inline-flex items-center gap-2 text-sm text-blue-500 hover:text-blue-600 mb-3">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
            <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white">
                Solicitud #<?= htmlspecialchars($solicitud['solicitud_id']) ?>
            </h1>
        </div>
    </div>

    <!-- TARJETA DE DETALLE -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold mb-1">Solicitante</p>
                <p class="text-gray-800 dark:text-white font-medium">
                    <?= htmlspecialchars($solicitud['nombre_solicitante']) ?>
                </p>
            </div>
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold mb-1">Área</p>
                <p class="text-gray-800 dark:text-white font-medium">
                    <?= htmlspecialchars($solicitud['nombre_area'] ?? 'Sin área') ?>
                </p>
            </div>
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold mb-1">Prioridad</p>
                <p>
                    <span id="badge_prioridad" class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider <?= $estilosPrioridad[$prioridadActual] ?? $estilosPrioridad['sin_asignar'] ?>">
                        <?= htmlspecialchars($etiquetasPrioridad[$prioridadActual] ?? ucfirst($prioridadActual)) ?>
                    </span>
                </p>
            </div>
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold mb-1">Estado</p>
                <p class="text-gray-800 dark:text-white font-medium">
                    <?php
                    $etiquetasEstado = [
                        'pendiente'    => 'Pendiente por Evaluar',
                        'en_revision'  => 'En Revisión',
                        'aprobada'     => 'Aprobada',
                        'rechazada'    => 'Rechazada',
                        'en_proceso'   => 'En Proceso',
                        'atendida'     => 'Listo para Entrega',
                        'cancelada'    => 'Cancelada',
                    ];
                    echo htmlspecialchars($etiquetasEstado[$solicitud['estado']] ?? ucfirst($solicitud['estado']));
                    ?>
                </p>
            </div>
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold mb-1">Fecha de solicitud</p>
                <p class="text-gray-800 dark:text-white font-medium">
                    <?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?>
                </p>
            </div>
            <?php if (!empty($solicitud['fecha_actualizacion'])): ?>
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold mb-1">Última actualización</p>
                <p class="text-gray-800 dark:text-white font-medium">
                    <?= date('d/m/Y H:i', strtotime($solicitud['fecha_actualizacion'])) ?>
                </p>
            </div>
            <?php endif; ?>
            <?php if (!empty($solicitud['fecha_limite'])): ?>
            <div>
                <p class="text-xs uppercase text-gray-400 font-bold mb-1">Fecha Límite</p>
                <p class="font-medium <?= $plazoVencido ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-800 dark:text-white' ?>">
                    <?= date('d/m/Y H:i', strtotime($solicitud['fecha_limite'])) ?>
                    <?php if ($plazoVencido): ?>
                        <span class="inline-flex items-center gap-1 ml-2 text-xs bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 px-2 py-0.5 rounded-full">
                            <i class="fas fa-exclamation-triangle"></i> Vencida
                        </span>
                    <?php endif; ?>
                </p>
                <?php if (isset($etiquetasPlazo[$prioridadActual])): ?>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    Plazo: <?= htmlspecialchars($etiquetasPlazo[$prioridadActual]) ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="mb-6">
            <p class="text-xs uppercase text-gray-400 font-bold mb-1">Asunto</p>
            <p class="text-gray-800 dark:text-white font-medium text-lg">
                <?= htmlspecialchars($solicitud['asunto']) ?>
            </p>
        </div>

        <div class="mb-6">
            <p class="text-xs uppercase text-gray-400 font-bold mb-1">Descripción</p>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                <?= nl2br(htmlspecialchars($solicitud['descripcion'])) ?>
            </p>
        </div>

        <?php if (!empty($solicitud['observaciones'])): ?>
        <div class="mb-6">
            <p class="text-xs uppercase text-gray-400 font-bold mb-1">Observaciones</p>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                <?= nl2br(htmlspecialchars($solicitud['observaciones'])) ?>
            </p>
        </div>
        <?php endif; ?>

        <!-- ÍTEMS SOLICITADOS DEL INVENTARIO -->
        <?php if (!empty($detalles)): ?>
        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-boxes text-blue-500"></i> Ítems del Inventario Solicitados
            </h3>
            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-bold text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3">Artículo</th>
                            <th class="px-4 py-3 text-center">Cantidad Solicitada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($detalles as $det): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white">
                                    <?= htmlspecialchars($det['nombre_producto']) ?>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-blue-600 dark:text-blue-400">
                                    <?= htmlspecialchars($det['cantidad_solicitada']) ?> <?= htmlspecialchars($det['unidad_medida'] ?? 'unidades') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- FOTO / EVIDENCIA ADJUNTA -->
        <?php if (!empty($solicitud['foto']) && file_exists(__DIR__ . '/../../../' . $solicitud['foto'])): ?>
        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-camera text-blue-500"></i> Foto / Evidencia Adjunta
            </h3>
            <div class="inline-block relative group">
                <img src="<?= BASE_URL . htmlspecialchars($solicitud['foto']) ?>" 
                     alt="Foto adjunta de solicitud" 
                     class="max-h-72 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-md object-cover">
                <div class="mt-2">
                    <a href="<?= BASE_URL . htmlspecialchars($solicitud['foto']) ?>" 
                       target="_blank" 
                       class="inline-flex items-center gap-1.5 text-xs text-blue-500 hover:text-blue-600 hover:underline font-medium">
                        <i class="fas fa-external-link-alt"></i> Ver imagen en tamaño completo
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ASIGNACIÓN DE PRIORIDAD (SOLO ADMIN / TÉCNICO) -->
        <?php if ($puedeGestionar): ?>
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
            <p class="text-xs uppercase text-gray-400 font-bold mb-3">Asignar Prioridad (Administrador / Técnico)</p>

            <div class="flex flex-wrap items-center gap-3">
                <select id="select_prioridad" class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="baja" <?= $prioridadActual === 'baja' ? 'selected' : '' ?>>Baja</option>
                    <option value="media" <?= $prioridadActual === 'media' ? 'selected' : '' ?>>Media</option>
                    <option value="alta" <?= $prioridadActual === 'alta' ? 'selected' : '' ?>>Alta</option>
                    <option value="urgente" <?= $prioridadActual === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                </select>

                <button onclick="asignarPrioridad(<?= $solicitud['solicitud_id'] ?>)"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-sm text-sm">
                    <i class="fas fa-flag"></i> Guardar Prioridad
                </button>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($puedeGestionar && !in_array($solicitud['estado'], ['atendida', 'rechazada', 'cancelada'])): ?>
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
            <p class="text-xs uppercase text-gray-400 font-bold mb-3">Gestión de Estado de Solicitud (Administrador / Técnico)</p>

            <div class="flex flex-wrap gap-3 items-center">
                <?php if (in_array($solicitud['estado'], ['pendiente', 'en_revision'])): ?>
                    <button onclick="aprobarSolicitud(<?= $solicitud['solicitud_id'] ?>)"
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-sm">
                        <i class="fas fa-check"></i> Aprobar Solicitud
                    </button>
                    <button onclick="mostrarRechazo(<?= $solicitud['solicitud_id'] ?>)"
                        class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-sm">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                <?php elseif ($solicitud['estado'] === 'aprobada'): ?>
                    <button onclick="iniciarProceso(<?= $solicitud['solicitud_id'] ?>)"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-sm">
                        <i class="fas fa-cogs"></i> Iniciar Proceso
                    </button>
                    <button onclick="mostrarRechazo(<?= $solicitud['solicitud_id'] ?>)"
                        class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm transition">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                <?php elseif ($solicitud['estado'] === 'en_proceso'): ?>
                    <button onclick="finalizarSolicitud(<?= $solicitud['solicitud_id'] ?>)"
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-sm">
                        <i class="fas fa-box-open"></i> Marcar como Listo para Entrega
                    </button>
                <?php endif; ?>
            </div>

            <!-- Formulario de motivo de rechazo -->
            <div id="rechazoForm" class="hidden mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Motivo de rechazo <span class="text-red-500">*</span>
                </label>
                <textarea id="motivoRechazo" rows="3"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 text-sm"
                    placeholder="Explica por qué se rechaza esta solicitud..."></textarea>
                <div class="flex gap-2 mt-3">
                    <button onclick="confirmarRechazo(<?= $solicitud['solicitud_id'] ?>)"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Confirmar rechazo
                    </button>
                    <button onclick="document.getElementById('rechazoForm').classList.add('hidden')"
                        class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

</div>

<script>
function cambiarEstadoDirecto(id, estado) {
    if (!confirm('¿Confirmas que deseas cambiar el estado de esta solicitud a ' + estado + '?')) return;

    fetch('<?= BASE_URL ?>solicitudes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=' + encodeURIComponent(estado) + '&id=' + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert(data.message || 'Ocurrió un error');
        }
    })
    .catch(() => alert('Error de conexión con el servidor'));
}

function asignarPrioridad(id) {
    const prioridad = document.getElementById('select_prioridad').value;

    if (!confirm('¿Confirmas asignar la prioridad "' + prioridad + '" a esta solicitud?')) return;

    fetch('<?= BASE_URL ?>solicitudes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=asignar_prioridad&id=' + encodeURIComponent(id) + '&prioridad=' + encodeURIComponent(prioridad)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert(data.message || 'Ocurrió un error');
        }
    })
    .catch(() => alert('Error de conexión con el servidor'));
}

function aprobarSolicitud(id) {
    if (!confirm('¿Confirmas que deseas aprobar esta solicitud?')) return;

    fetch('<?= BASE_URL ?>solicitudes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=aprobar&id=' + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert(data.message || 'Ocurrió un error');
        }
    })
    .catch(() => alert('Error de conexión con el servidor'));
}

function mostrarRechazo(id) {
    document.getElementById('rechazoForm').classList.remove('hidden');
}

function confirmarRechazo(id) {
    const motivo = document.getElementById('motivoRechazo').value.trim();

    if (motivo === '') {
        alert('Debes indicar un motivo de rechazo');
        return;
    }

    fetch('<?= BASE_URL ?>solicitudes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=rechazar&id=' + encodeURIComponent(id) + '&motivo=' + encodeURIComponent(motivo)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert(data.message || 'Ocurrió un error');
        }
    })
    .catch(() => alert('Error de conexión con el servidor'));
}

function iniciarProceso(id) {
    if (!confirm('¿Confirmas que deseas iniciar el proceso de esta solicitud?')) return;

    fetch('<?= BASE_URL ?>solicitudes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=iniciar_proceso&id=' + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert(data.message || 'Ocurrió un error');
        }
    })
    .catch(() => alert('Error de conexión con el servidor'));
}

function finalizarSolicitud(id) {
    if (!confirm('¿Confirmas que esta solicitud está lista para entrega?')) return;

    fetch('<?= BASE_URL ?>solicitudes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=finalizar&id=' + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert(data.message || 'Ocurrió un error');
        }
    })
    .catch(() => alert('Error de conexión con el servidor'));
}
</script>