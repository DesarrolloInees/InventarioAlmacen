<?php
$nivel = $_SESSION['nivel_acceso'] ?? 0;
?>

<?php if ($nivel == 3): // CLIENTE PROSEGUR ?>
    <a href="<?= BASE_URL ?>inicio"
        class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
        <i class="fas fa-home mr-1"></i> Inicio
    </a>
    <a href="<?= BASE_URL ?>solicitudes"
        class="text-gray-600 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-brand-600/20 hover:text-brand-600 dark:hover:text-brand-400 px-3 py-2 rounded-md text-sm font-medium transition ml-2">
        <i class="fas fa-file-alt mr-1"></i> Mis Solicitudes
    </a>

<?php elseif ($nivel == 2): // TÉCNICO ?>
    <a href="<?= BASE_URL ?>inicio"
        class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
        <i class="fas fa-home mr-1"></i> Inicio
    </a>
    <a href="<?= BASE_URL ?>inventario"
        class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition ml-2">
        <i class="fas fa-boxes mr-1"></i> Inventario 
    </a>
    <a href="<?= BASE_URL ?>solicitudes"
        class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition ml-2">
        <i class="fas fa-history mr-1"></i> Historial y Solicitudes
    </a>

<?php else: // ADMINISTRADORES ($nivel == 1) ?>
    <a href="<?= BASE_URL ?>inicio"
        class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
        <i class="fas fa-home mr-1"></i> Inicio
    </a>

    <a href="<?= BASE_URL ?>dashboard"
        class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
        <i class="fa-solid fa-chart-line mr-1"></i> Dashboard
    </a>

    <a href="<?= BASE_URL ?>inventario"
        class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
        <i class="fa-solid fa-boxes mr-1"></i> Inventario 
    </a>

    <a href="<?= BASE_URL ?>solicitudesRegistro"
        class="text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/30 hover:text-amber-600 dark:hover:text-amber-400 px-3 py-2 rounded-md text-sm font-semibold transition inline-flex items-center gap-1.5 ml-1">
        <i class="fa-solid fa-user-clock text-amber-500"></i> Aprobar Credenciales
    </a>


    

    <!-- Dropdown Salida Simulación -->
    <!--<div class="relative group h-full flex items-center ml-2">
        <button
            class="text-gray-600 dark:text-gray-300 group-hover:bg-gray-100 dark:group-hover:bg-gray-700 group-hover:text-brand-600 dark:group-hover:text-white px-3 py-2 rounded-md text-sm font-medium inline-flex items-center transition">
            <span>Salidas Simulación</span>
            <i class="fas fa-chevron-down ml-2 text-xs opacity-75"></i>
        </button>
        <div
            class="absolute right-0 top-12 w-72 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 hidden group-hover:block border border-gray-200 dark:border-gray-700 z-[9999] max-h-[75vh] overflow-y-auto">

            <div
                class="px-4 py-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase bg-gray-50 dark:bg-gray-800/80 sticky top-0">
                Salida Simulación</div>
            <a href="<?= BASE_URL ?>entradaSalidaVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white">Salida Simulación Ver
            </a>
        </div>
    </div>-->
<?php endif; ?>