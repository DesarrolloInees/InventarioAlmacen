<?php if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado."); ?>

<div class="w-full px-4 md:px-8 pb-8">

    <!-- HERO SECTION (Banner Principal) -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-900 via-blue-800 to-blue-900 shadow-2xl mb-10">
        <!-- Decoraciones de fondo -->
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-48 h-48 bg-blue-400 opacity-10 rounded-full blur-2xl">
        </div>

        <div class="relative z-10 px-8 py-12 md:py-16 md:px-12 flex flex-col md:flex-row items-center justify-between">
            <div class="text-center md:text-left mb-6 md:mb-0">
                <span
                    class="inline-block py-1 px-4 rounded-full bg-blue-500/30 text-blue-200 text-xs font-bold tracking-wider uppercase mb-4 border border-blue-400/30">
                    <i class="fas fa-rocket mr-1"></i> Sistema de Almacén v2.0
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-3">
                    Bienvenido, <?= htmlspecialchars($_SESSION['usuario_name'] ?? 'Equipo') ?> 👋
                </h1>
                <p class="text-blue-200 text-lg max-w-xl font-light">
                    Gestiona el inventario, controla las entradas y salidas, y mantén el almacén operando al máximo
                    nivel desde un solo lugar.
                </p>
            </div>

            <div
                class="hidden md:flex items-center justify-center bg-white/10 p-6 rounded-2xl backdrop-blur-md border border-white/10 shadow-inner">
                <div class="text-center">
                    <p class="text-blue-200 text-xs font-medium uppercase tracking-widest mb-1">Hoy es</p>
                    <p class="text-4xl font-black text-white"><?= date('d') ?></p>
                    <p class="text-lg font-medium text-blue-300"><?= date('M, Y') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- TÍTULO DE MÓDULOS -->
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
            <i class="fas fa-th-large text-blue-500 mr-3"></i> Portal de Módulos
        </h2>
    </div>

    <!-- GRID DE ACCESOS RÁPIDOS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Dashboard -->
        <a href="<?= BASE_URL ?>dashboard"
            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-blue-500 dark:hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-125">
            </div>
            <div class="relative z-10 flex items-start space-x-4">
                <div
                    class="w-14 h-14 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 text-2xl shadow-inner group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <h3
                        class="text-xl font-bold text-gray-800 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        Dashboard</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Métricas, gráficos y alertas de
                        stock en tiempo real.</p>
                </div>
            </div>
        </a>

        <!-- Inventario -->
        <a href="<?= BASE_URL ?>inventarioVer"
            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-emerald-500 dark:hover:border-emerald-500 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/20 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-125">
            </div>
            <div class="relative z-10 flex items-start space-x-4">
                <div
                    class="w-14 h-14 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-2xl shadow-inner group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div>
                    <h3
                        class="text-xl font-bold text-gray-800 dark:text-white mb-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        Inventario</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Consulta el stock actual,
                        precios y valoración del almacén.</p>
                </div>
            </div>
        </a>

        <!-- Compras (Entradas) -->
        <a href="<?= BASE_URL ?>compraVer"
            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-green-500 dark:hover:border-green-500 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-green-50 dark:bg-green-900/20 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-125">
            </div>
            <div class="relative z-10 flex items-start space-x-4">
                <div
                    class="w-14 h-14 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400 text-2xl shadow-inner group-hover:bg-green-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-cart-arrow-down"></i>
                </div>
                <div>
                    <h3
                        class="text-xl font-bold text-gray-800 dark:text-white mb-1 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                        Compras</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Registra entradas de nueva
                        mercancía y aumenta el stock.</p>
                </div>
            </div>
        </a>

        <!-- Salidas (Asignaciones) -->
        <a href="<?= BASE_URL ?>salidaVer"
            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-red-500 dark:hover:border-red-500 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-red-50 dark:bg-red-900/20 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-125">
            </div>
            <div class="relative z-10 flex items-start space-x-4">
                <div
                    class="w-14 h-14 rounded-xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center text-red-600 dark:text-red-400 text-2xl shadow-inner group-hover:bg-red-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-dolly"></i>
                </div>
                <div>
                    <h3
                        class="text-xl font-bold text-gray-800 dark:text-white mb-1 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                        Salidas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Asigna repuestos a técnicos o a
                        la flota de motorizados.</p>
                </div>
            </div>
        </a>

        <!-- Catálogo y Categorías -->
        <a href="<?= BASE_URL ?>categoriaVer"
            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-purple-50 dark:bg-purple-900/20 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-125">
            </div>
            <div class="relative z-10 flex items-start space-x-4">
                <div
                    class="w-14 h-14 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center text-purple-600 dark:text-purple-400 text-2xl shadow-inner group-hover:bg-purple-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <h3
                        class="text-xl font-bold text-gray-800 dark:text-white mb-1 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                        Categorías</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Administra los grupos y tipos de
                        repuestos del sistema.</p>
                </div>
            </div>
        </a>

        <!-- Proveedores -->
        <a href="<?= BASE_URL ?>proveedorVer"
            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-yellow-500 dark:hover:border-yellow-500 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-yellow-50 dark:bg-yellow-900/20 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-125">
            </div>
            <div class="relative z-10 flex items-start space-x-4">
                <div
                    class="w-14 h-14 rounded-xl bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center text-yellow-600 dark:text-yellow-500 text-2xl shadow-inner group-hover:bg-yellow-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-truck-loading"></i>
                </div>
                <div>
                    <h3
                        class="text-xl font-bold text-gray-800 dark:text-white mb-1 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors">
                        Proveedores</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Directorio de empresas,
                        contactos y suministros.</p>
                </div>
            </div>
        </a>

    </div>
</div>