<?php
$nivel = $_SESSION['nivel_acceso'] ?? 0;
?>

<div class="p-4 space-y-1">
    <?php if ($nivel == 3): // CLIENTE PROSEGUR (ID 3) ?>
        <a href="<?= BASE_URL ?>inicio"
            class="flex items-center text-gray-800 dark:text-gray-300 py-3 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 border-b border-gray-200 dark:border-gray-700 font-medium transition-colors">
            <i class="fas fa-home w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
            <span>Inicio</span>
        </a>

        <a href="<?= BASE_URL ?>solicitudes"
            class="flex items-center text-gray-800 dark:text-gray-300 py-3 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 border-b border-gray-200 dark:border-gray-700 font-medium transition-colors">
            <i class="fas fa-file-alt w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
            <span>Solicitudes</span>
        </a>

        <a href="<?= BASE_URL ?>historial"
            class="flex items-center text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/20 py-3 px-3 rounded-lg hover:bg-brand-100 dark:hover:bg-brand-900/40 font-bold mt-2 transition-colors">
            <i class="fas fa-history w-5 text-center mr-3"></i>
            <span>Historial</span>
        </a>

    <?php elseif ($nivel == 2): // TÉCNICO ?>
        <a href="<?= BASE_URL ?>inicio"
            class="flex items-center text-gray-800 dark:text-gray-300 py-3 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 border-b border-gray-200 dark:border-gray-700 font-medium transition-colors">
            <i class="fas fa-home w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
            <span>Inicio</span>
        </a>

        <a href="<?= BASE_URL ?>inventario"
            class="flex items-center text-gray-800 dark:text-gray-300 py-3 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 border-b border-gray-200 dark:border-gray-700 font-medium transition-colors">
            <i class="fas fa-boxes w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
            <span>Inventario Almacén</span>
        </a>

        <a href="<?= BASE_URL ?>solicitudes"
            class="flex items-center text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/20 py-3 px-3 rounded-lg hover:bg-brand-100 dark:hover:bg-brand-900/40 font-bold mt-2 transition-colors">
            <i class="fas fa-history w-5 text-center mr-3"></i>
            <span>Historial y Solicitudes</span>
        </a>

    <?php else: // ADMINISTRADOR ?>
        <a href="<?= BASE_URL ?>inicio"
            class="flex items-center text-gray-800 dark:text-gray-300 py-3 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 border-b border-gray-200 dark:border-gray-700 font-medium transition-colors">
            <i class="fas fa-home w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
            <span>Inicio</span>
        </a>

        <a href="<?= BASE_URL ?>dashboard"
            class="flex items-center text-gray-800 dark:text-gray-300 py-3 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 border-b border-gray-200 dark:border-gray-700 font-medium transition-colors">
            <i class="fas fa-chart-line w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
            <span>Dashboard</span>
        </a>

        <!-- Acordeón: Categorías -->
        <details class="group">
            <summary class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="flex items-center font-medium">
                    <i class="fas fa-tags w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
                    Categorías
                </span>
                <span class="transition-transform duration-200 group-open:rotate-180 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </span>
            </summary>
            <div class="mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                <a href="<?= BASE_URL ?>categoriaCrear" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Crear Categoría
                </a>
                <a href="<?= BASE_URL ?>categoriaVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Ver Categorías
                </a>
            </div>
        </details>

        <!-- Acordeón: Compras -->
        <details class="group">
            <summary class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="flex items-center font-medium">
                    <i class="fas fa-shopping-cart w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
                    Compras
                </span>
                <span class="transition-transform duration-200 group-open:rotate-180 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </span>
            </summary>
            <div class="mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                <a href="<?= BASE_URL ?>compraCrear" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Agregar una Compra (Crear)
                </a>
                <a href="<?= BASE_URL ?>compraVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Ver Compras
                </a>
            </div>
        </details>

        <!-- Acordeón: Repuestos -->
        <details class="group">
            <summary class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="flex items-center font-medium">
                    <i class="fas fa-tools w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
                    Repuestos
                </span>
                <span class="transition-transform duration-200 group-open:rotate-180 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </span>
            </summary>
            <div class="mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                <a href="<?= BASE_URL ?>repuestoCrear" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Nuevo Repuesto
                </a>
                <a href="<?= BASE_URL ?>repuestoVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Gestionar Repuestos
                </a>
                <a href="<?= BASE_URL ?>repuestoFormulaVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Ensamblar Repuestos
                </a>
                <a href="<?= BASE_URL ?>repuestoEnsamblar" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Gestionar Repuestos Reparados
                </a>
            </div>
        </details>

        <!-- Acordeón: Productos -->
        <details class="group">
            <summary class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="flex items-center font-medium">
                    <i class="fas fa-box w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
                    Productos
                </span>
                <span class="transition-transform duration-200 group-open:rotate-180 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </span>
            </summary>
            <div class="mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                <a href="<?= BASE_URL ?>productoCrear" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Nuevo Producto
                </a>
                <a href="<?= BASE_URL ?>productoVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Gestionar Productos
                </a>
            </div>
        </details>

        <!-- Acordeón: Logística -->
        <details class="group">
            <summary class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="flex items-center font-medium">
                    <i class="fa-solid fa-motorcycle w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
                    Logística
                </span>
                <span class="transition-transform duration-200 group-open:rotate-180 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </span>
            </summary>
            <div class="mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                <a href="<?= BASE_URL ?>inventario" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Inventario Almacén
                </a>
                <a href="<?= BASE_URL ?>bodegaVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Administrador Bodegas
                </a>
                <a href="<?= BASE_URL ?>maquinaInventarioVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Inventario Bodega
                </a>
            </div>
        </details>

        <!-- Acordeón: Salidas -->
        <details class="group">
            <summary class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="flex items-center font-medium">
                    <i class="fas fa-truck-loading w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
                    Salidas
                </span>
                <span class="transition-transform duration-200 group-open:rotate-180 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </span>
            </summary>
            <div class="mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                <a href="<?= BASE_URL ?>salidaCrear" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Crear Salida
                </a>
                <a href="<?= BASE_URL ?>salidaVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">
                    Ver Salidas
                </a>
            </div>
        </details>

        <!-- Acordeón: Administración -->
        <details class="group">
            <summary class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="flex items-center font-medium">
                    <i class="fas fa-shield-alt w-5 text-center text-gray-500 dark:text-gray-400 mr-3"></i>
                    Administración
                </span>
                <span class="transition-transform duration-200 group-open:rotate-180 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </span>
            </summary>
            <div class="mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                <p class="px-4 py-1 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Accesos</p>
                <a href="<?= BASE_URL ?>solicitudesRegistro" class="block py-2 px-4 text-amber-600 dark:text-amber-400 font-bold hover:text-brand-600 dark:hover:text-white rounded transition-colors"><i class="fa-solid fa-user-clock mr-1"></i> Aprobar Credenciales</a>
                <a href="<?= BASE_URL ?>usuarioVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">Usuarios</a>
                <a href="<?= BASE_URL ?>clienteVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">Clientes</a>

                <p class="px-4 py-1 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mt-2">Maestros</p>
                <a href="<?= BASE_URL ?>maquinaVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">Máquinas</a>
                <a href="<?= BASE_URL ?>validadorVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">Validadores</a>
                <a href="<?= BASE_URL ?>tipoUsuarioVer" class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded transition-colors">Tipos de Usuario</a>
            </div>
        </details>

    <?php endif; ?>

    <!-- Botón Cerrar Sesión -->
    <div class="pt-4 mt-6 border-t border-gray-200 dark:border-gray-700">
        <a href="<?= BASE_URL ?>logout"
            class="flex items-center justify-center text-red-600 dark:text-red-400 py-3 px-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 border border-red-200 dark:border-red-900/50 font-bold transition-colors">
            <i class="fas fa-sign-out-alt mr-2"></i>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</div>