<?php if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado."); 

$rolId = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;
$esClienteProsegur = ($rolId == 3 || ($_SESSION['nombre_rol'] ?? '') === 'clienteProsegur');
?>

<?php if ($esClienteProsegur): ?>
<!-- ================================================================= -->
<!-- PORTAL CLIENTE PROSEGUR (DISEÑO CORPORATIVO INEES IT SEGURIDAD) -->
<!-- ================================================================= -->
<div class="w-full px-4 md:px-8 pb-10 space-y-10">

    <!-- HERO BANNER CORPORATIVO INEES -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-950 via-indigo-900 to-slate-900 shadow-2xl border border-blue-800/40">
        <!-- Decoraciones de fondo con resplandor en Azul INEES y Verde INEES -->
        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-80 h-80 bg-blue-500 opacity-20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-80 h-80 bg-emerald-500 opacity-20 rounded-full blur-3xl"></div>

        <div class="relative z-10 p-8 sm:p-10 md:p-12 flex flex-col lg:flex-row items-center justify-between gap-8">
            
            <!-- Saludo y Texto Principal -->
            <div class="text-center lg:text-left space-y-4 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 backdrop-blur-md">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-blue-200 text-xs font-extrabold tracking-widest uppercase">
                        Portal Cliente Prosegur - INEES IT Seguridad
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white leading-tight tracking-tight">
                    Bienvenido, <span class="bg-gradient-to-r from-blue-200 via-blue-300 to-emerald-300 bg-clip-text text-transparent"><?= htmlspecialchars($_SESSION['usuario_name'] ?? 'Cliente') ?></span> 👋
                </h1>

                <p class="text-blue-100/90 text-base sm:text-lg font-light leading-relaxed">
                    Gestiona tus solicitudes de repuestos, monitorea el avance de mantenimientos y consulta el historial técnico en una plataforma centralizada.
                </p>

                <!-- Badges Informativos -->
                <div class="pt-2 flex flex-wrap justify-center lg:justify-start gap-3 text-xs text-blue-100">
                    <span class="px-3 py-1.5 rounded-xl bg-slate-900/60 border border-blue-700/50 flex items-center gap-1.5">
                        <i class="fas fa-check-circle text-emerald-400"></i> Servicio Activo
                    </span>
                    <span class="px-3 py-1.5 rounded-xl bg-slate-900/60 border border-blue-700/50 flex items-center gap-1.5">
                        <i class="fas fa-headset text-blue-300"></i> Atención Técnica INEES
                    </span>
                    <span class="px-3 py-1.5 rounded-xl bg-slate-900/60 border border-blue-700/50 flex items-center gap-1.5">
                        <i class="fas fa-shield-alt text-emerald-400"></i> Garantía & Control
                    </span>
                </div>
            </div>

            <!-- CONTENEDOR DE LOGO INEES CORPORATIVO CON EFECTO VIDRIO -->
            <div class="flex-shrink-0 bg-white/10 dark:bg-slate-900/40 backdrop-blur-xl border border-white/20 p-6 sm:p-8 rounded-3xl shadow-2xl flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-[11px] font-black text-blue-200 uppercase tracking-widest">Suministros IT & Seguridad</p>

                <div class="p-4 bg-white rounded-2xl shadow-xl border border-gray-100 flex items-center justify-center h-20 w-44 sm:w-48 transition-transform hover:scale-105">
                    <img src="<?= BASE_URL ?>app/logos/logoInees.jpg" alt="INEES IT Seguridad" class="max-h-14 w-auto object-contain">
                </div>

                <span class="text-[10px] text-emerald-300 font-bold bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-400/30">
                    Electrónica IT Seguridad
                </span>
            </div>

        </div>
    </div>

    <!-- TARJETAS DE MÓDULOS Y ACCESOS RÁPIDOS CLIENTE -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- MÓDULO 1: CREAR NUEVA SOLICITUD (INEES BLUE DEGRADADO) -->
        <a href="<?= BASE_URL ?>solicitudes&accion=crear"
            class="group relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-3xl p-7 shadow-xl hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 transform hover:-translate-y-1.5 overflow-hidden text-white border border-blue-500">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>

            <div class="relative z-10 flex flex-col h-full justify-between space-y-6">
                <div class="flex items-center justify-between">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center text-3xl shadow-lg group-hover:rotate-6 transition-transform duration-300">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span class="bg-white/15 backdrop-blur-md text-white font-extrabold text-xs px-3 py-1 rounded-full uppercase tracking-wider">
                        Nuevo Requerimiento
                    </span>
                </div>

                <div>
                    <h3 class="text-2xl font-black mb-2 tracking-tight text-white">
                        Nueva Solicitud
                    </h3>
                    <p class="text-sm font-medium text-blue-100/90 leading-relaxed">
                        Solicita repuestos, componentes o soporte técnico para tus equipos.
                    </p>
                </div>

                <div class="pt-2 flex items-center text-xs font-extrabold uppercase tracking-widest text-white group-hover:translate-x-2 transition-transform">
                    <span>Crear Solicitud</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>
        </a>

        <!-- MÓDULO 2: MIS SOLICITUDES & ESTADO (INEES BLUE CARD) -->
        <a href="<?= BASE_URL ?>solicitudes"
            class="group relative bg-white dark:bg-slate-800 rounded-3xl p-7 shadow-md hover:shadow-xl border border-slate-200 dark:border-slate-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1.5 overflow-hidden">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-blue-500/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>

            <div class="relative z-10 flex flex-col h-full justify-between space-y-6">
                <div class="flex items-center justify-between">
                    <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-bold text-xs px-3 py-1 rounded-full">
                        Seguimiento
                    </span>
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        Mis Solicitudes
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Revisa el estado de aprobación, despacho y avances de tus requerimientos.
                    </p>
                </div>

                <div class="pt-2 flex items-center text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 group-hover:translate-x-2 transition-transform">
                    <span>Ver Mis Solicitudes</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>
        </a>


        <!-- MÓDULO 4: CATÁLOGO INEES-MOTORIZADOS (PDF) -->
        <a href="https://drive.google.com/file/d/1Bptp2_8enj2LbiwNpfSYgPQnv8UPBTxE/preview"
            target="_blank"
            rel="noopener noreferrer"
            class="group relative bg-white dark:bg-slate-800 rounded-3xl p-7 shadow-md hover:shadow-xl border border-slate-200 dark:border-slate-700 hover:border-amber-500 dark:hover:border-amber-500 transition-all duration-300 transform hover:-translate-y-1.5 overflow-hidden">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-amber-500/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>

            <div class="relative z-10 flex flex-col h-full justify-between space-y-6">
                <div class="flex items-center justify-between">
                    <div class="w-16 h-16 rounded-2xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-3xl shadow-inner group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <span class="bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 font-bold text-xs px-3 py-1 rounded-full">
                        PDF
                    </span>
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                        Catálogo Iness Motrizados
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Explora el catálogo completo de productos y soluciones INEES en formato PDF.
                    </p>
                </div>

                <div class="pt-2 flex items-center text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 group-hover:translate-x-2 transition-transform">
                    <span>Ver Catálogo</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>
        </a>

        <!-- MÓDULO 5: CATÁLOGO INEES (PDF) -->
        <a href="https://drive.google.com/file/d/1M19Xd3XxfYyDx6STSIibXtSQH_0Kq0G5/preview"
            target="_blank"
            rel="noopener noreferrer"
            class="group relative bg-white dark:bg-slate-800 rounded-3xl p-7 shadow-md hover:shadow-xl border border-slate-200 dark:border-slate-700 hover:border-amber-500 dark:hover:border-amber-500 transition-all duration-300 transform hover:-translate-y-1.5 overflow-hidden">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-amber-500/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>

            <div class="relative z-10 flex flex-col h-full justify-between space-y-6">
                <div class="flex items-center justify-between">
                    <div class="w-16 h-16 rounded-2xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-3xl shadow-inner group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <span class="bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 font-bold text-xs px-3 py-1 rounded-full">
                        PDF
                    </span>
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                        Catálogo Iness 
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Explora el catálogo completo de productos y soluciones INEES en formato PDF.
                    </p>
                </div>

                <div class="pt-2 flex items-center text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 group-hover:translate-x-2 transition-transform">
                    <span>Ver Catálogo</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </div>
        </a>

    </div>

    <!-- SECCIÓN DE ATENCIÓN Y SOPORTE DIRECTO INEES -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5 text-center md:text-left">
                <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl flex-shrink-0 border border-blue-200 dark:border-blue-800">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-slate-800 dark:text-white">Línea Directa INEES IT Seguridad</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Soporte técnico especializado y atención de consultas sobre inventario.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="mailto:soporte@inees.co" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-bold text-sm transition flex items-center gap-2">
                    <i class="fas fa-envelope text-blue-500"></i> soporte@inees.co
                </a>
                <a href="tel:6017459000" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-phone-alt"></i> (601) 745-9000
                </a>
            </div>
        </div>
    </div>

</div>

<?php else: ?>
<!-- ================================================================= -->
<!-- PORTAL ESTÁNDAR ADMINISTRATIVO / TÉCNICO -->
<!-- ================================================================= -->
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Dashboard -->
        <a href="<?= BASE_URL ?>dashboard"
            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-blue-500 dark:hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-125">
            </div>
            <div class="relative z-10 flex items-start space-x-4">
                <div
                    class="w-14 h-14 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 text-2xl shadow-inner group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h3
                        class="text-xl font-bold text-gray-800 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        Dashboard</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Panel de control, analítica y métricas clave en tiempo real.</p>
                </div>
            </div>
        </a>

        <!-- Inventario -->
        <a href="<?= BASE_URL ?>inventario"
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

        <!-- Historial / Solicitudes -->
        <a href="<?= BASE_URL ?>solicitudes"
            class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:border-indigo-500 dark:hover:border-indigo-500 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 dark:bg-indigo-900/20 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-125">
            </div>
            <div class="relative z-10 flex items-start space-x-4">
                <div
                    class="w-14 h-14 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-2xl shadow-inner group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <h3
                        class="text-xl font-bold text-gray-800 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        Historial</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Auditoría, trazabilidad y
                        registro general de movimientos.</p>
                </div>
            </div>
        </a>

    </div>
</div>

    <!-- SECCIÓN DE ATENCIÓN Y SOPORTE DIRECTO INEES -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-200 dark:border-slate-700 mt-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5 text-center md:text-left">
                <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl flex-shrink-0 border border-blue-200 dark:border-blue-800">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-slate-800 dark:text-white">Línea Directa INEES IT Seguridad</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Soporte técnico especializado y atención de consultas sobre inventario.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="mailto:soporte@inees.co" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-bold text-sm transition flex items-center gap-2">
                    <i class="fas fa-envelope text-blue-500"></i> soporte@inees.co
                </a>
                <a href="tel:6017459000" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-phone-alt"></i> (601) 745-9000
                </a>
            </div>
        </div>
    </div>

</div>
<?php endif; ?>