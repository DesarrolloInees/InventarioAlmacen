<?php
$nivel = $_SESSION['nivel_acceso'] ?? 0;
?>

<?php if ($nivel == 3): // TÉCNICO ?>
    <a href="<?= BASE_URL ?>inicio"
        class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
        <i class="fas fa-home mr-1"></i> Inicio
    </a>
    <a href="<?= BASE_URL ?>ordenMovil"
        class="text-gray-600 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-brand-600/20 hover:text-brand-600 dark:hover:text-brand-400 px-3 py-2 rounded-md text-sm font-medium transition ml-2">
        <i class="fas fa-search mr-1"></i> Consultar Historial
    </a>


<?php else: // ADMINISTRADORES ?>
    <a href="<?= BASE_URL ?>inicio"
        class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
        <i class="fas fa-home mr-1"></i> Inicio
    </a>

    <a href="<?= BASE_URL ?>dashboard"
        class="text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
        <i class="fa-solid fa-chart-line mr-1"></i> Dashboard
    </a>


    <!-- Dropdown Salida -->
    <div class="relative group h-full flex items-center ml-2">
        <button
            class="text-gray-600 dark:text-gray-300 group-hover:bg-gray-100 dark:group-hover:bg-gray-700 group-hover:text-brand-600 dark:group-hover:text-white px-3 py-2 rounded-md text-sm font-medium inline-flex items-center transition">
            <span>Categorias</span>
            <i class="fas fa-chevron-down ml-2 text-xs opacity-75"></i>
        </button>
        <div
            class="absolute left-0 top-12 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 hidden group-hover:block border border-gray-200 dark:border-gray-700 z-[9999]">
            <a href="<?= BASE_URL ?>categoriaCrear"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-plus w-5 text-center mr-1 text-yellow-600"></i> Crear Categoría
            </a>
            <a href="<?= BASE_URL ?>categoriaVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-hammer w-5 text-center mr-1 text-blue-500"></i> Ver Categorías
            </a>
        </div>
    </div>


    <!-- Dropdown Compras -->
    <div class="relative group h-full flex items-center ml-2">
        <button
            class="text-gray-600 dark:text-gray-300 group-hover:bg-gray-100 dark:group-hover:bg-gray-700 group-hover:text-brand-600 dark:group-hover:text-white px-3 py-2 rounded-md text-sm font-medium inline-flex items-center transition">
            <span>Compras</span>
            <i class="fas fa-chevron-down ml-2 text-xs opacity-75"></i>
        </button>
        <div
            class="absolute left-0 top-12 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 hidden group-hover:block border border-gray-200 dark:border-gray-700 z-[9999]">
            <a href="<?= BASE_URL ?>compraCrear"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-dollar-sign w-5 text-center mr-1 "></i>Agregar una Compra (Crear)
            </a>
            <a href="<?= BASE_URL ?>compraVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-hand-holding-dollar w-5 text-center mr-1 text-emerald-500"></i>Ver Compras
            </a>
        </div>
    </div>

    <!-- Dropdown Repuestos -->
    <div class="relative group h-full flex items-center ml-2">
        <button
            class="text-gray-600 dark:text-gray-300 group-hover:bg-gray-100 dark:group-hover:bg-gray-700 group-hover:text-brand-600 dark:group-hover:text-white px-3 py-2 rounded-md text-sm font-medium inline-flex items-center transition">
            <span>Repuestos</span>
            <i class="fas fa-chevron-down ml-2 text-xs opacity-75"></i>
        </button>
        <div
            class="absolute left-0 top-12 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 hidden group-hover:block border border-gray-200 dark:border-gray-700 z-[9999]">
            <a href="<?= BASE_URL ?>repuestoCrear"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-wrench w-5 text-center mr-1 text-green-500"></i> Nuevo Repuesto
            </a>
            <a href="<?= BASE_URL ?>repuestoVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-list-ul w-5 text-center mr-1 text-blue-500"></i> Gestionar Repuestos
            </a>
        </div>
    </div>

    <!-- Dropdown Productos -->
    <div class="relative group h-full flex items-center ml-2">
        <button
            class="text-gray-600 dark:text-gray-300 group-hover:bg-gray-100 dark:group-hover:bg-gray-700 group-hover:text-brand-600 dark:group-hover:text-white px-3 py-2 rounded-md text-sm font-medium inline-flex items-center transition">
            <span>Productos</span>
            <i class="fas fa-chevron-down ml-2 text-xs opacity-75"></i>
        </button>
        <div
            class="absolute left-0 top-12 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 hidden group-hover:block border border-gray-200 dark:border-gray-700 z-[9999]">
            <a href="<?= BASE_URL ?>productoCrear"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-box w-5 text-center mr-1 text-red-500"></i> Nuevo Producto
            </a>
            <a href="<?= BASE_URL ?>productoVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-list-ul w-5 text-center mr-1 text-blue-500"></i> Gestionar Productos
            </a>
        </div>
    </div>

    <!-- Dropdown Logística -->
    <div class="relative group h-full flex items-center ml-2">
        <button
            class="text-gray-600 dark:text-gray-300 group-hover:bg-gray-100 dark:group-hover:bg-gray-700 group-hover:text-brand-600 dark:group-hover:text-white px-3 py-2 rounded-md text-sm font-medium inline-flex items-center transition">
            <span>Logística</span>
            <i class="fas fa-chevron-down ml-2 text-xs opacity-75"></i>
        </button>
        <div
            class="absolute left-0 top-12 w-60 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 hidden group-hover:block border border-gray-200 dark:border-gray-700 z-[9999]">
            <a href="<?= BASE_URL ?>inventarioVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-warehouse w-5 text-center mr-1 text-yellow-500"></i> Inventario Almacén
            </a>
        </div>
    </div>



    <!-- Dropdown Salida -->
    <div class="relative group h-full flex items-center ml-2">
        <button
            class="text-gray-600 dark:text-gray-300 group-hover:bg-gray-100 dark:group-hover:bg-gray-700 group-hover:text-brand-600 dark:group-hover:text-white px-3 py-2 rounded-md text-sm font-medium inline-flex items-center transition">
            <span>Salidas</span>
            <i class="fas fa-chevron-down ml-2 text-xs opacity-75"></i>
        </button>
        <div
            class="absolute left-0 top-12 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 hidden group-hover:block border border-gray-200 dark:border-gray-700 z-[9999]">
            <a href="<?= BASE_URL ?>salidaCrear"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-plus w-5 text-center mr-1 text-yellow-600"></i> Crear Salida
            </a>
            <a href="<?= BASE_URL ?>salidaVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white transition">
                <i class="fa-solid fa-hammer w-5 text-center mr-1 text-blue-500"></i> Ver Salidas
            </a>
        </div>
    </div>

    <!-- Dropdown Administración -->
    <div class="relative group h-full flex items-center ml-2">
        <button
            class="text-gray-600 dark:text-gray-300 group-hover:bg-gray-100 dark:group-hover:bg-gray-700 group-hover:text-brand-600 dark:group-hover:text-white px-3 py-2 rounded-md text-sm font-medium inline-flex items-center transition">
            <span>Administración</span>
            <i class="fas fa-chevron-down ml-2 text-xs opacity-75"></i>
        </button>
        <div
            class="absolute right-0 top-12 w-72 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 hidden group-hover:block border border-gray-200 dark:border-gray-700 z-[9999] max-h-[75vh] overflow-y-auto">

            <div
                class="px-4 py-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase bg-gray-50 dark:bg-gray-800/80 sticky top-0">
                Accesos y Personas</div>
            <a href="<?= BASE_URL ?>usuarioVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white">Usuarios</a>

            <a href="<?= BASE_URL ?>proveedorVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white">Proveedores</a>

            <div
                class="px-4 py-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase bg-gray-50 dark:bg-gray-800/80 sticky top-0 mt-1">
                Maestros</div>
            <a href="<?= BASE_URL ?>tipoMaquinaVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white">Tipos
                de Máquinas
            </a>
            <a href="<?= BASE_URL ?>tipoValidadorVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white">Tipos
                de Validadores
            </a>
            <a href="<?= BASE_URL ?>tipoUsuarioVer"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-white">Tipos
                de Usuarios
            </a>
            <div
                class="px-4 py-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase bg-gray-50 dark:bg-gray-800/80 sticky top-0 mt-1">
                Importaciones</div>
        </div>
    </div>


    <!-- Dropdown Salida Simulación -->
    <div class="relative group h-full flex items-center ml-2">
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
    </div>
<?php endif; ?>