<?php
$nivel = $_SESSION['nivel_acceso'] ?? 0;
?>

<div class="p-4">
    <?php if ($nivel == 3): // TÉCNICO ?>
        <a href="<?= BASE_URL ?>inicio"
            class="block text-gray-800 dark:text-gray-300 py-3 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 border-b border-gray-200 dark:border-gray-700 font-medium mb-1 transition-colors">
            <i class="fas fa-home mr-3 w-5 text-center text-gray-500 dark:text-gray-400"></i> Inicio
        </a>
        <a href="<?= BASE_URL ?>ordenMovil"
            class="block text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/20 py-3 px-3 rounded-lg hover:bg-brand-100 dark:hover:bg-brand-900/40 font-bold mt-2 transition-colors">
            <i class="fas fa-search mr-3 w-5 text-center"></i> Consultar Historial
        </a>

    <?php else: // ADMINISTRADOR ?>
        <a href="<?= BASE_URL ?>inicio"
            class="block text-gray-800 dark:text-gray-300 py-3 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 border-b border-gray-200 dark:border-gray-700 font-medium mb-2 transition-colors">
            <i class="fas fa-home mr-3 w-5 text-center text-gray-500 dark:text-gray-400"></i> Inicio
        </a>

        <a href="<?= BASE_URL ?>dashboard"
            class="block text-gray-800 dark:text-gray-300 py-3 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 border-b border-gray-200 dark:border-gray-700 font-medium mb-2 transition-colors">
            <i class="fas fa-home mr-3 w-5 text-center text-gray-500 dark:text-gray-400"></i> Dashboard
        </a>

        <!-- Acordeón: Categorias -->
        <details class="group mb-1">
            <summary
                class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="font-medium">
                    <i class="fas fa-tags mr-3 w-5 text-center text-gray-500 dark:text-gray-400"></i> Categorías
                </span>
                <span class="transition-transform group-open:rotate-180">
                    <i class="fas fa-chevron-down text-sm"></i>
                </span>
            </summary>
            <div class="text-gray-600 dark:text-gray-400 mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2">
                <a href="<?= BASE_URL ?>categoriaCrear"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Crear Categoría
                </a>
                <a href="<?= BASE_URL ?>categoriaVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Ver Categorías
                </a>
            </div>
        </details>

        <!-- Acordeón: Compras -->
        <details class="group mb-1">
            <summary
                class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="font-medium"><i class="fas fa-cogs mr-3 w-5 text-center text-gray-500 dark:text-gray-400"></i>
                    Compras</span>
                <span class="transition-transform group-open:rotate-180">
                    <i class="fas fa-chevron-down text-sm"></i>
                </span>
            </summary>
            <div class="text-gray-600 dark:text-gray-400 mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2">
                <a href="<?= BASE_URL ?>compraCrear"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Agregar una Compra (Crear)
                </a>
                <a href="<?= BASE_URL ?>compraVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Ver Compras
                </a>
            </div>
        </details>

        <!-- Acordeón: Repuestos -->
        <details class="group mb-1">
            <summary
                class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="font-medium"><i class="fas fa-truck mr-3 w-5 text-center text-gray-500 dark:text-gray-400"></i>
                    Repuestos</span>
                <span class="transition-transform group-open:rotate-180">
                    <i class="fas fa-chevron-down text-sm"></i>
                </span>
            </summary>
            <div class="text-gray-600 dark:text-gray-400 mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2">
                <a href="<?= BASE_URL ?>repuestoCrear"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Nuevo Repuesto
                </a>
                <a href="<?= BASE_URL ?>repuestoVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Gestionar Repuestos
                </a>
            </div>
        </details>

        <!-- Acordeón: Productos -->
        <details class="group mb-1">
            <summary
                class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="font-medium"><i class="fas fa-box mr-3 w-5 text-center text-gray-500 dark:text-gray-400"></i>
                    Productos</span>
                <span class="transition-transform group-open:rotate-180">
                    <i class="fas fa-chevron-down text-sm"></i>
                </span>
            </summary>
            <div class="text-gray-600 dark:text-gray-400 mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2">
                <a href="<?= BASE_URL ?>productoCrear"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Nuevo Producto
                </a>
                <a href="<?= BASE_URL ?>productoVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Gestionar Productos
                </a>
            </div>
        </details>

        <!-- Acordeón: Logistica -->
        <details class="group mb-1">
            <summary
                class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="font-medium">
                    <i class="fa-solid fa-motorcycle mr-3 w-5 text-center text-gray-500 dark:text-gray-400">
                    </i>
                    Logística
                </span>
                <span class="transition-transform group-open:rotate-180">
                    <i class="fas fa-chevron-down text-sm"></i>
                </span>
            </summary>
            <div class="text-gray-600 dark:text-gray-400 mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2">
                <a href="<?= BASE_URL ?>inventarioVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Inventario Almacén
                </a>
            </div>
        </details>

        <!-- Acordeón: Salida -->
        <details class="group mb-1">
            <summary
                class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="font-medium">
                    <i class="fas fa-chart-bar mr-3 w-5 text-center text-gray-500 dark:text-gray-400"></i> Salidas
                </span>
                <span class="transition-transform group-open:rotate-180">
                    <i class="fas fa-chevron-down text-sm"></i>
                </span>
            </summary>
            <div class="text-gray-600 dark:text-gray-400 mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2">
                <a href="<?= BASE_URL ?>salidaCrear"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Crear Salida
                </a>
                <a href="<?= BASE_URL ?>salidaVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Ver Salidas
                </a>
            </div>
        </details>

        <!-- Acordeón: Administración -->
        <details class="group mb-1">
            <summary
                class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="font-medium">
                    <i class="fas fa-shield-alt mr-3 w-5 text-center text-gray-500 dark:text-gray-400"></i>
                    Administración
                </span>
                <span class="transition-transform group-open:rotate-180">
                    <i class="fas fa-chevron-down text-sm"></i>
                </span>
            </summary>
            <div class="text-gray-600 dark:text-gray-400 mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2">
                <p class="px-4 py-1 text-[10px] font-bold text-gray-400 uppercase mt-1">Accesos</p>
                <a href="<?= BASE_URL ?>usuarioVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Usuarios</a>
                <a href="<?= BASE_URL ?>clienteVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Clientes</a>

                <p class="px-4 py-1 text-[10px] font-bold text-gray-400 uppercase mt-2">Maestros</p>
                <a href="<?= BASE_URL ?>maquinaVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Máquinas</a>
                <a href="<?= BASE_URL ?>validadorVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Validadores</a>
                <a href="<?= BASE_URL ?>tipoUsuarioVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Tipos de Usuario</a>
            </div>
        </details>

        <!-- Acordeón: Salidas Simulación -->
        <details class="group mb-1">
            <summary
                class="flex justify-between items-center cursor-pointer list-none text-gray-800 dark:text-gray-300 py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg select-none transition-colors">
                <span class="font-medium">
                    <i class="fas fa-shield-alt mr-3 w-5 text-center text-gray-500 dark:text-gray-400"></i>
                    Simulación Salidas
                </span>
                <span class="transition-transform group-open:rotate-180">
                    <i class="fas fa-chevron-down text-sm"></i>
                </span>
            </summary>
            <div class="text-gray-600 dark:text-gray-400 mt-1 mb-2 pl-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg py-2">
                <p class="px-4 py-1 text-[10px] font-bold text-gray-400 uppercase mt-1">Salidas</p>
                <a href="<?= BASE_URL ?>entradaSalidaVer"
                    class="block py-2 px-4 hover:text-brand-600 dark:hover:text-white rounded">Salida Simulación
                </a>
            </div>
        </details>
    <?php endif; ?>

    <!-- Botón Cerrar Sesión Móvil -->
    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="<?= BASE_URL ?>logout"
            class="block text-red-600 dark:text-red-400 py-3 px-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-center border border-red-200 dark:border-red-900/50 font-bold transition-colors">
            <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
        </a>
    </div>
</div>