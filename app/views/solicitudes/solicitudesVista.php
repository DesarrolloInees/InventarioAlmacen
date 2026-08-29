<?php if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado."); 

$rolId = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;
$esClienteProsegur = ($rolId == 3 || ($_SESSION['nombre_rol'] ?? '') === 'clienteProsegur');
?>

<div class="w-full px-4 md:px-8 pb-8">

    <!-- ENCABEZADO DEL MÓDULO -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center">
                    <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-400 mr-3"></i> Mis Solicitudes
                </h1>
                <?php if ($esClienteProsegur): ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 uppercase tracking-wider">
                        Cliente Prosegur
                    </span>
                <?php endif; ?>
            </div>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                <?= $esClienteProsegur ? 'Consulta el estado y seguimiento de tus requerimientos en tiempo real.' : 'Gestión y seguimiento de solicitudes del sistema.' ?>
            </p>
        </div>

        <!-- BOTONES DE ACCIÓN Y EXPORTACIÓN -->
        <div class="flex flex-wrap items-center gap-3">
            <?php if (!$esClienteProsegur): ?>
                <!-- Inventario (Solo Admin/Técnico) -->
                <a href="<?= BASE_URL ?>inventario" 
                   class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-gray-200 border border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium shadow-sm transition">
                    <i class="fas fa-boxes text-blue-400"></i> Inventario
                </a>
            <?php endif; ?>

            <!-- Exportar PDF -->
            <a href="<?= BASE_URL ?>solicitudes&accion=exportarPdf" target="_blank"
                class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium shadow-sm transition">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>

            <!-- Exportar Excel -->
            <a href="<?= BASE_URL ?>solicitudes&accion=exportarExcel"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium shadow-sm transition">
                <i class="fas fa-file-excel"></i> Exportar XLSX
            </a>

            <!-- Nueva Solicitud -->
            <a href="<?= BASE_URL ?>solicitudes&accion=crear"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-xl shadow-sm text-sm transition transform hover:scale-105">
                <i class="fas fa-plus"></i> Nueva Solicitud
            </a>
        </div>
    </div>

    <!-- CONTENEDOR DE LISTADO -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

        <?php if (empty($solicitudes)): ?>

            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p class="font-medium">No hay solicitudes registradas todavía.</p>
            </div>

        <?php else: ?>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 uppercase text-xs border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Asunto</th>
                            <th class="px-6 py-3">Solicitante</th>
                            <th class="px-6 py-3">Área</th>
                            <th class="px-6 py-3">Prioridad</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3">Fecha</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($solicitudes as $s): 
                            $id = $s['solicitud_id'] ?? $s['id'] ?? '';
                            $asunto = $s['asunto'] ?? 'Sin asunto';
                            $solicitante = $s['nombre_solicitante'] ?? $s['nombre_usuario'] ?? 'N/A';
                            $area = $s['nombre_area'] ?? 'Sin área';
                            $prioridad = strtolower($s['prioridad'] ?? 'media');
                            $estado = strtolower($s['estado'] ?? 'pendiente');
                            $fecha = $s['fecha_solicitud'] ?? $s['fecha_creacion'] ?? 'now';

                            // Estilos dinámicos para estado
                            $estadoBadge = match($estado) {
                                'aprobada', 'aprobado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'rechazada', 'rechazado' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                            };

                            // Estilos dinámicos para prioridad
                            $prioridadBadge = match($prioridad) {
                                'urgente' => 'text-red-600 dark:text-red-400 font-extrabold',
                                'alta'    => 'text-orange-600 dark:text-orange-400 font-bold',
                                'media'   => 'text-amber-600 dark:text-amber-400 font-medium',
                                'baja'    => 'text-slate-600 dark:text-slate-400 font-normal',
                                default   => 'text-gray-600 dark:text-gray-400',
                            };
                        ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4 font-medium">
                                    <a href="<?= BASE_URL ?>solicitudes&id=<?= htmlspecialchars($id) ?>"
                                        class="text-blue-500 hover:text-blue-600 hover:underline font-semibold">
                                        #<?= htmlspecialchars($id) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200">
                                    <?= htmlspecialchars($asunto) ?>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    <?= htmlspecialchars($solicitante) ?>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    <?= htmlspecialchars($area) ?>
                                </td>
                                <td class="px-6 py-4 capitalize <?= $prioridadBadge ?>">
                                    <?= htmlspecialchars($prioridad) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider <?= $estadoBadge ?>">
                                        <?= htmlspecialchars(str_replace('_', ' ', $estado)) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">
                                    <?= date('d/m/Y H:i', strtotime($fecha)) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?= BASE_URL ?>solicitudes&id=<?= htmlspecialchars($id) ?>"
                                        class="p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </div>
</div>