<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

$pendientes = $data['pendientes'] ?? [];
$aprobados  = $data['aprobados']  ?? [];
$denegados  = $data['denegados']  ?? [];

/**
 * Devuelve el HTML de la insignia (badge) para el Tipo de Solicitud,
 * con color e icono según el motivo seleccionado por el cliente.
 */
function badgeTipoSolicitud($tipo)
{
    $tipo = trim((string) $tipo);

    if ($tipo === '') {
        return '<span class="bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5"><i class="fa-solid fa-question"></i> No especificado</span>';
    }

    $estilos = [
        'Soporte Técnico' => ['bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300', 'fa-solid fa-headset'],
        'Mantenimiento / Servicio' => ['bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300', 'fa-solid fa-screwdriver-wrench'],
        'Cotización / Información Comercial' => ['bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300', 'fa-solid fa-file-invoice-dollar'],
        'Creación de Cuenta / Acceso al Sistema' => ['bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300', 'fa-solid fa-user-plus'],
        'Otro' => ['bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300', 'fa-solid fa-ellipsis'],
    ];

    [$clases, $icono] = $estilos[$tipo] ?? ['bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300', 'fa-solid fa-clipboard-question'];

    return '<span class="' . $clases . ' px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5"><i class="' . $icono . '"></i> ' . htmlspecialchars($tipo) . '</span>';
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

<div class="w-full px-4 md:px-6 space-y-6">
    <!-- TITULO DE PÁGINA -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 transition-colors">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-user-shield"></i> Panel de Super Usuario
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Aprobación de Credenciales de Clientes
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                Revisa y autoriza o deniega el acceso a los clientes que han solicitado una cuenta en la plataforma.
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.location.reload()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition flex items-center gap-2 text-sm shadow-sm">
                <i class="fa-solid fa-rotate"></i> Actualizar Lista
            </button>
        </div>
    </div>

    <!-- TARJETAS METRICAS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card Pendientes -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-amber-200 dark:border-amber-900/50 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-1">Solicitudes Pendientes</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white"><?= count($pendientes) ?></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Esperando tu aprobación</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <!-- Card Aprobados -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-emerald-200 dark:border-emerald-900/50 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1">Clientes Aprobados</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white"><?= count($aprobados) ?></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Con acceso activo al sistema</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <!-- Card Denegados -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-rose-200 dark:border-rose-900/50 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 mb-1">Solicitudes Denegadas</p>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white"><?= count($denegados) ?></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Acceso rechazado o inactivo</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-user-xmark"></i>
            </div>
        </div>
    </div>

    <!-- PESTAÑAS Y TABLA PRINCIPAL -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
        
        <!-- Pestañas de Navegación -->
        <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
            <button onclick="cambiarTab('pendientes')" id="tabBtn-pendientes" class="tab-btn px-4 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 transition bg-amber-500 text-white shadow-md">
                <i class="fa-solid fa-clock"></i> Pendientes por Aprobar
                <span class="bg-white/20 px-2 py-0.5 rounded-full text-xs"><?= count($pendientes) ?></span>
            </button>
            <button onclick="cambiarTab('aprobados')" id="tabBtn-aprobados" class="tab-btn px-4 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2 transition text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fa-solid fa-circle-check"></i> Historial Aprobadas
                <span class="bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded-full text-xs"><?= count($aprobados) ?></span>
            </button>
            <button onclick="cambiarTab('denegados')" id="tabBtn-denegados" class="tab-btn px-4 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2 transition text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fa-solid fa-circle-xmark"></i> Historial Denegadas
                <span class="bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded-full text-xs"><?= count($denegados) ?></span>
            </button>
        </div>

        <!-- TAB 1: PENDIENTES -->
        <div id="tabContent-pendientes" class="tab-content">
            <?php if (!empty($pendientes)): ?>
                <div class="overflow-x-auto">
                    <table class="tabla-solicitudes w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="text-xs text-gray-800 dark:text-gray-200 uppercase bg-amber-50 dark:bg-amber-950/40 border-b dark:border-gray-700">
                            <tr>
                                <th class="py-3.5 px-4">ID</th>
                                <th class="py-3.5 px-4">Cliente / Empresa</th>
                                <th class="py-3.5 px-4">Cédula / Doc.</th>
                                <th class="py-3.5 px-4">Contacto</th>
                                <th class="py-3.5 px-4">Nombre de Usuario</th>
                                <th class="py-3.5 px-4">Tipo de Solicitud</th>
                                <th class="py-3.5 px-4 text-center">Acciones Super Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($pendientes as $p): ?>
                                <tr class="hover:bg-amber-50/30 dark:hover:bg-gray-750 transition-colors">
                                    <td class="py-4 px-4 font-bold text-gray-500">#<?= htmlspecialchars($p['usuario_id']) ?></td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-gray-900 dark:text-white text-base"><?= htmlspecialchars($p['nombre']) ?></div>
                                        <div class="text-indigo-600 dark:text-indigo-400 font-semibold text-xs mt-0.5 flex items-center gap-1">
                                            <i class="fa-solid fa-building"></i> <?= htmlspecialchars($p['empresa'] ?? 'No especificada') ?>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-mono text-gray-700 dark:text-gray-300"><?= htmlspecialchars($p['cedula'] ?? 'N/A') ?></td>
                                    <td class="py-4 px-4 text-xs">
                                        <div class="font-medium text-gray-800 dark:text-gray-200"><i class="fa-solid fa-envelope text-gray-400 mr-1"></i><?= htmlspecialchars($p['email']) ?></div>
                                        <?php if (!empty($p['celular'])): ?>
                                            <div class="text-gray-500 mt-1"><i class="fa-solid fa-phone text-gray-400 mr-1"></i><?= htmlspecialchars($p['celular']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-4 font-mono font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($p['usuario']) ?></td>
                                    <td class="py-4 px-4"><?= badgeTipoSolicitud($p['tipo_solicitud'] ?? '') ?></td>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <div class="flex justify-center items-center gap-2">
                                            <button onclick="procesarCredenciales(<?= $p['usuario_id'] ?>, 'aprobar', '<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>')" 
                                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition transform hover:scale-105">
                                                <i class="fa-solid fa-check"></i> Aprobar Credenciales
                                            </button>
                                            <button onclick="procesarCredenciales(<?= $p['usuario_id'] ?>, 'denegar', '<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>')" 
                                                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition transform hover:scale-105">
                                                <i class="fa-solid fa-xmark"></i> Denegar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl bg-gray-50/50 dark:bg-gray-800/50">
                    <i class="fa-solid fa-clipboard-check text-5xl text-emerald-400 dark:text-emerald-500 mb-3"></i>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">¡No hay solicitudes pendientes!</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Todas las credenciales de clientes han sido procesadas.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- TAB 2: APROBADAS -->
        <div id="tabContent-aprobados" class="tab-content hidden">
            <?php if (!empty($aprobados)): ?>
                <div class="overflow-x-auto">
                    <table class="tabla-solicitudes w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="text-xs text-gray-800 dark:text-gray-200 uppercase bg-gray-100 dark:bg-gray-700 border-b dark:border-gray-600">
                            <tr>
                                <th class="py-3.5 px-4">ID</th>
                                <th class="py-3.5 px-4">Cliente / Empresa</th>
                                <th class="py-3.5 px-4">Cédula</th>
                                <th class="py-3.5 px-4">Contacto</th>
                                <th class="py-3.5 px-4">Usuario</th>
                                <th class="py-3.5 px-4">Tipo de Solicitud</th>
                                <th class="py-3.5 px-4 text-center">Estado</th>
                                <th class="py-3.5 px-4 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($aprobados as $a): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                    <td class="py-4 px-4 font-bold text-gray-400">#<?= htmlspecialchars($a['usuario_id']) ?></td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($a['nombre']) ?></div>
                                        <div class="text-indigo-600 dark:text-indigo-400 text-xs font-semibold"><i class="fa-solid fa-building mr-1"></i><?= htmlspecialchars($a['empresa'] ?? 'N/A') ?></div>
                                    </td>
                                    <td class="py-4 px-4 font-mono"><?= htmlspecialchars($a['cedula'] ?? 'N/A') ?></td>
                                    <td class="py-4 px-4 text-xs"><?= htmlspecialchars($a['email']) ?></td>
                                    <td class="py-4 px-4 font-mono font-bold text-gray-800 dark:text-gray-200"><?= htmlspecialchars($a['usuario']) ?></td>
                                    <td class="py-4 px-4"><?= badgeTipoSolicitud($a['tipo_solicitud'] ?? '') ?></td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 px-3 py-1 rounded-full text-xs font-bold">Aprobado</span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <button onclick="procesarCredenciales(<?= $a['usuario_id'] ?>, 'denegar', '<?= htmlspecialchars($a['nombre'], ENT_QUOTES) ?>')" 
                                            class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition">
                                            Denegar Acceso
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">No hay registros de solicitudes aprobadas.</div>
            <?php endif; ?>
        </div>

        <!-- TAB 3: DENEGADAS -->
        <div id="tabContent-denegados" class="tab-content hidden">
            <?php if (!empty($denegados)): ?>
                <div class="overflow-x-auto">
                    <table class="tabla-solicitudes w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="text-xs text-gray-800 dark:text-gray-200 uppercase bg-gray-100 dark:bg-gray-700 border-b dark:border-gray-600">
                            <tr>
                                <th class="py-3.5 px-4">ID</th>
                                <th class="py-3.5 px-4">Cliente / Empresa</th>
                                <th class="py-3.5 px-4">Cédula</th>
                                <th class="py-3.5 px-4">Contacto</th>
                                <th class="py-3.5 px-4">Usuario</th>
                                <th class="py-3.5 px-4">Tipo de Solicitud</th>
                                <th class="py-3.5 px-4 text-center">Estado</th>
                                <th class="py-3.5 px-4 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($denegados as $d): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                    <td class="py-4 px-4 font-bold text-gray-400">#<?= htmlspecialchars($d['usuario_id']) ?></td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($d['nombre']) ?></div>
                                        <div class="text-indigo-600 dark:text-indigo-400 text-xs font-semibold"><i class="fa-solid fa-building mr-1"></i><?= htmlspecialchars($d['empresa'] ?? 'N/A') ?></div>
                                    </td>
                                    <td class="py-4 px-4 font-mono"><?= htmlspecialchars($d['cedula'] ?? 'N/A') ?></td>
                                    <td class="py-4 px-4 text-xs"><?= htmlspecialchars($d['email']) ?></td>
                                    <td class="py-4 px-4 font-mono font-bold text-gray-800 dark:text-gray-200"><?= htmlspecialchars($d['usuario']) ?></td>
                                    <td class="py-4 px-4"><?= badgeTipoSolicitud($d['tipo_solicitud'] ?? '') ?></td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 px-3 py-1 rounded-full text-xs font-bold">Denegado</span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <button onclick="procesarCredenciales(<?= $d['usuario_id'] ?>, 'aprobar', '<?= htmlspecialchars($d['nombre'], ENT_QUOTES) ?>')" 
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                                            Re-Aprobar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">No hay registros de solicitudes denegadas.</div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
function cambiarTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-amber-500', 'text-white', 'shadow-md', 'bg-emerald-600', 'bg-rose-600');
        btn.classList.add('text-gray-600', 'dark:text-gray-400');
    });

    const targetContent = document.getElementById('tabContent-' + tabName);
    const targetBtn = document.getElementById('tabBtn-' + tabName);

    if (targetContent) targetContent.classList.remove('hidden');
    if (targetBtn) {
        targetBtn.classList.remove('text-gray-600', 'dark:text-gray-400');
        if (tabName === 'pendientes') targetBtn.classList.add('bg-amber-500', 'text-white', 'shadow-md');
        if (tabName === 'aprobados') targetBtn.classList.add('bg-emerald-600', 'text-white', 'shadow-md');
        if (tabName === 'denegados') targetBtn.classList.add('bg-rose-600', 'text-white', 'shadow-md');
    }
}

async function procesarCredenciales(userId, accion, clienteNombre) {
    const accionTexto = accion === 'aprobar' ? 'APROBAR' : 'DENEGAR';
    const confirmacion = confirm(`¿Estás seguro de que deseas ${accionTexto} las credenciales de "${clienteNombre}"?`);

    if (!confirmacion) return;

    try {
        const formData = new FormData();
        formData.append('accion_solicitud', accion);
        formData.append('usuario_id', userId);

        const response = await fetch('solicitudesRegistro', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.status === 'success') {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'Error al procesar la solicitud.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de comunicación con el servidor.');
    }
}
</script>