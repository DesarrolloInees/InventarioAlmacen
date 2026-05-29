<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

// Capturamos el nivel del usuario para los permisos
$nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
$esAdmin = ($nivelUsuario == 1 || $nivelUsuario == 2);
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<style>
    /* Personalización para DataTables en Modo Claro */
    .dataTables_length select,
    .dataTables_filter input {
        background-color: white !important;
        color: #374151 !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        margin: 0 0.5rem;
        outline: none;
    }

    .dataTables_length label,
    .dataTables_filter label {
        color: #4b5563 !important;
        font-weight: 500;
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    #usuariosTable tbody tr {
        background-color: white !important;
        color: #374151 !important;
    }

    #usuariosTable tbody tr:hover {
        background-color: #f9fafb !important;
    }

    .dataTables_info {
        color: #6b7280 !important;
    }

    .dataTables_paginate .paginate_button {
        color: #4b5563 !important;
        background-color: #f3f4f6 !important;
        border: 1px solid #d1d5db;
    }

    .dataTables_paginate .paginate_button.current,
    .dataTables_paginate .paginate_button:hover {
        background-color: #4f46e5 !important;
        color: white !important;
        border-color: #4f46e5 !important;
    }

    /* MODO OSCURO PARA DATATABLES */
    .dark .dataTables_length select,
    .dark .dataTables_filter input {
        background-color: #374151 !important;
        /* gray-700 */
        color: #f3f4f6 !important;
        /* gray-100 */
        border: 1px solid #4b5563 !important;
        /* gray-600 */
    }

    .dark .dataTables_length label,
    .dark .dataTables_filter label,
    .dark .dataTables_info,
    .dark .dataTables_paginate .paginate_button {
        color: #9ca3af !important;
        /* gray-400 */
    }

    .dark #usuariosTable tbody tr {
        background-color: #1f2937 !important;
        color: #d1d5db !important;
    }

    .dark #usuariosTable tbody tr:hover {
        background-color: #374151 !important;
    }

    .dark .dataTables_paginate .paginate_button {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
    }

    .dark .dataTables_paginate .paginate_button.current {
        background-color: #4f46e5 !important;
        color: white !important;
        border-color: #4f46e5 !important;
    }

    /* Controles responsive */
    .dataTables_wrapper>div:first-child,
    .dataTables_wrapper>div:last-of-type {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        margin-top: 1.5rem;
    }
</style>

<div class="w-full px-4 md:px-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-users text-indigo-600 dark:text-indigo-500 mr-2"></i> Gestión de Usuarios
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Crea, edita o elimina los usuarios del sistema.</p>
            </div>

            <!-- SOLO ADMINS PUEDEN VER EL BOTÓN DE CREAR -->
            <?php if ($esAdmin): ?>
                <a href="<?php echo BASE_URL; ?>usuarioCrear"
                    class="mt-4 sm:mt-0 px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Crear Nuevo Usuario</span>
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($data['usuarios'])): ?>
            <div class="overflow-x-auto">
                <table id="usuariosTable" class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead
                        class="text-xs text-gray-800 dark:text-gray-300 uppercase bg-gray-100 dark:bg-gray-700 border-b dark:border-gray-600">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Nombre</th>
                            <th class="py-3 px-4">Cédula</th>
                            <th class="py-3 px-4">Cargo</th>
                            <th class="py-3 px-4">Rol</th>
                            <?php if ($esAdmin): ?>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($data['usuarios'] as $usuario): ?>
                            <tr class="border-b dark:border-gray-700 transition-colors">
                                <td class="py-4 px-4 font-bold text-gray-600 dark:text-gray-400">
                                    #<?php echo htmlspecialchars($usuario['usuario_id']); ?></td>
                                <td class="py-4 px-4 font-medium text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                <td class="py-4 px-4 font-mono text-indigo-600 dark:text-indigo-400">
                                    <?php echo htmlspecialchars($usuario['cedula']); ?></td>
                                <td class="py-4 px-4"><?php echo htmlspecialchars($usuario['cargo']); ?></td>
                                <td class="py-4 px-4">
                                    <span
                                        class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs font-semibold px-2.5 py-0.5 rounded border border-gray-200 dark:border-gray-600">
                                        <?php echo htmlspecialchars($usuario['rol']); ?>
                                    </span>
                                </td>

                                <!-- SOLO ADMINS PUEDEN VER LOS BOTONES DE ACCIÓN -->
                                <?php if ($esAdmin): ?>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <div class="flex justify-center items-center space-x-2">
                                            <a href="<?php echo BASE_URL . 'usuarioEditar/' . htmlspecialchars($usuario['usuario_id']); ?>"
                                                class="p-2 w-9 h-9 flex items-center justify-center bg-yellow-400 dark:bg-yellow-900/30 text-white dark:text-yellow-500 rounded-full hover:bg-yellow-500 dark:hover:bg-yellow-900/50 transition-colors border border-transparent dark:border-yellow-800"
                                                title="Editar">
                                                <i class="fa-solid fa-user-pen text-xs"></i>
                                            </a>
                                            <button data-modal-trigger
                                                data-id="<?php echo htmlspecialchars($usuario['usuario_id']); ?>"
                                                class="p-2 w-9 h-9 flex items-center justify-center bg-red-600 dark:bg-red-900/30 text-white dark:text-red-400 rounded-full hover:bg-red-700 dark:hover:bg-red-900/50 transition-colors border border-transparent dark:border-red-800"
                                                title="Eliminar">
                                                <i class="fa-solid fa-user-minus text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div
                class="text-center p-8 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                <i class="fa-solid fa-users-slash text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay usuarios activos para mostrar.</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">¡Crea el primer usuario para empezar!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Eliminar Adaptado a Dark Mode -->
<div id="confirmModal"
    class="fixed inset-0 z-[9999] overflow-y-auto hidden flex items-center justify-center bg-gray-900 bg-opacity-75 transition-opacity duration-300 backdrop-blur-sm">
    <div
        class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-sm mx-auto shadow-xl transform transition-all duration-300 scale-95 border dark:border-gray-700">
        <div class="text-center">
            <div
                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
                <i class="fa-solid fa-triangle-exclamation text-xl text-red-600 dark:text-red-500"></i>
            </div>
            <h2 class="text-xl font-bold my-4 text-gray-800 dark:text-white">Confirmar Eliminación</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">¿Estás seguro de que quieres eliminar este usuario?
                Esta acción no se puede deshacer.</p>
        </div>
        <div class="flex justify-center space-x-4">
            <button data-modal-close
                class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-transparent dark:border-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 font-medium transition-colors">Cancelar</button>
            <a id="confirmButton" href="#"
                class="px-6 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-colors">Sí,
                eliminar</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    // Esto pasa la variable de PHP a JavaScript
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>
<script src="<?php echo BASE_URL; ?>js/usuario/usuarioVer.js"></script>