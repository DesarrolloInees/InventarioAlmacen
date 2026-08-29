<!DOCTYPE html>
<html lang="es" class="light">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titulo) ? $titulo . ' - Sistema' : 'Dashboard - Sistema' ?></title>
    
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>app/logos/logoIneesSinFondo.png">
 
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 
    <!-- Configuración de Tailwind para Dark Mode -->
    <script>
        tailwind.config = {
            darkMode: 'class', // Habilita el modo oscuro basado en clases
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#3b82f6', // Azul principal
                            600: '#2563eb',
                        }
                    }
                }
            }
        }
    </script>
 
    <!-- Script para manejar el Dark/Light Mode al cargar -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
 
<body
    class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300 font-sans min-h-screen flex flex-col">
 
    <!-- BARRA SUPERIOR ACCENTUADA CON COLORES CORPORATIVOS INEES (AZUL Y VERDE) -->
    <div class="h-1.5 w-full bg-gradient-to-r from-blue-600 via-blue-700 to-emerald-600"></div>
 
    <!-- NAVBAR ARRIBA -->
    <nav
        class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50 transition-colors duration-300">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo INEES Corporativo -->
                <div class="flex items-center">
                    <a href="<?= BASE_URL ?>inicio"
                        class="flex-shrink-0 flex items-center mr-6 hover:opacity-80 transition">
                        <img src="<?= BASE_URL ?>app/logos/logoIneesFondoBlanco.png" alt="Logo-Inees"
                            class="h-10 w-auto object-contain invert dark:invert-0 transition-all duration-300">
                    </a>
 
                    <!-- MENÚ ESCRITORIO (Visible solo en XL) -->
                    <div class="hidden xl:flex space-x-1 items-center h-full">
                        <?php
                        if (file_exists(__DIR__ . '/../partials/navbar_menu.php')) {
                            include __DIR__ . '/../partials/navbar_menu.php';
                        }
                        ?>
                    </div>
                </div>
 
                <!-- Opciones Derecha -->
                <div class="flex items-center space-x-2">
                    <!-- BOTÓN MENÚ MÓVIL (Visible solo en pantallas menores a XL) -->
                    <div class="xl:hidden flex items-center">
                        <button id="mobile-menu-button"
                            class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
 
 
 
                    <!-- NOTIFICACIONES CAMPANITA INTERACTIVA -->
                    <?php
                    $cantNotif = 0;
                    $listaNotificaciones = [];
                    if (isset($_SESSION['usuario_id']) && class_exists('Conexion')) {
                        try {
                            // Conexión a solicitudprosegur (usuarios, notificaciones...)
                            $dbNotif = (new Conexion())->getConexion();
 
                            // ====================================================================
                            // AJUSTA ESTA LÍNEA a como realmente obtienes la conexión a
                            // "inventario-almacen" en tu conexion.php.
                            // Ejemplos según tu caso real:
                            //   $dbInventario = (new Conexion('inventario'))->getConexion();
                            //   $dbInventario = (new ConexionInventario())->getConexion();
                            //   $dbInventario = Conexion::getInventario();
                            // ====================================================================
                            $dbInventario = (new Conexion())->getConexionRemota();
 
                            require_once __DIR__ . '/../models/notificacion/notificacionInventarioModelo.php';
                            $modeloNotif = new NotificacionInventarioModelo($dbNotif, $dbInventario);
                            $listaNotificaciones = $modeloNotif->obtenerNotificacionesNoLeidas($_SESSION['usuario_id']);
                            $cantNotif = count($listaNotificaciones);
                        } catch (Exception $e) {
                            error_log('Error cargando notificaciones en plantillaVista: ' . $e->getMessage());
                            $cantNotif = 0;
                        }
                    }
                    ?>
                    <div class="relative">
                        <button id="notif-bell-btn" type="button"
                            class="relative text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none rounded-lg text-sm p-2.5 transition">
                            <i class="fas fa-bell text-lg"></i>
                            <span id="notif-badge" class="<?= $cantNotif > 0 ? '' : 'hidden' ?> absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full animate-pulse">
                                <?= $cantNotif ?>
                            </span>
                        </button>
 
                        <!-- Dropdown de notificaciones -->
                        <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 z-[99999] overflow-hidden">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800/80">
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-bell text-blue-500"></i> Notificaciones
                                </h4>
                                <span id="notif-count-text" class="text-xs bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 font-bold px-2 py-0.5 rounded-full">
                                    <?= $cantNotif ?> sin leer
                                </span>
                            </div>
 
                            <div id="notif-list-container" class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                                <?php if (empty($listaNotificaciones)): ?>
                                    <div id="notif-empty-msg" class="p-6 text-center text-gray-400 dark:text-gray-500 text-sm">
                                        <i class="fas fa-check-circle text-2xl text-emerald-400 mb-2 block"></i>
                                        ¡Estás al día! No tienes notificaciones pendientes.
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($listaNotificaciones as $n): ?>
                                        <div id="notif-item-<?= $n['notificacion_id'] ?>" class="p-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition flex items-start justify-between gap-3">
                                            <div class="space-y-1 pr-2 cursor-pointer" onclick="marcarNotificacionLeida(event, <?= $n['notificacion_id'] ?>)">
                                                <p class="text-xs font-bold text-gray-800 dark:text-white">
                                                    <?= htmlspecialchars($n['titulo']) ?>
                                                </p>
                                                <p class="text-xs text-gray-600 dark:text-gray-300">
                                                    <?= htmlspecialchars($n['mensaje']) ?>
                                                </p>
                                                <span class="text-[10px] text-gray-400">
                                                    <?= date('d/m/Y H:i', strtotime($n['fecha_creacion'])) ?>
                                                </span>
                                            </div>
                                            <button type="button" onclick="marcarNotificacionLeida(event, <?= $n['notificacion_id'] ?>)" 
                                                    title="Marcar como leída"
                                                    class="text-gray-400 hover:text-emerald-500 dark:hover:text-emerald-400 text-xs p-1.5 transition flex-shrink-0 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/30">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
 
                    <!-- Botón Dark/Light Mode -->
                    <button id="theme-toggle" type="button"
                        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none rounded-lg text-sm p-2.5 transition">
                        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon text-lg"></i>
                        <i id="theme-toggle-light-icon" class="hidden fas fa-sun text-lg"></i>
                    </button>
 
                    <!-- Contenedor del Perfil con Dropdown -->
                    <div class="relative group">
                        <!-- Botón del Perfil -->
 
 
                        <!-- Botón de Cerrar Sesión directo y claro -->
                        <div class="flex items-center gap-3">
                            <!-- Avatar y nombre (solo visual) -->
                            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 rounded-full py-1.5 px-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                    <i class="fas fa-user-circle text-xl"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-bold text-gray-800 dark:text-white leading-tight">
                                        <?= $_SESSION['usuario_name'] ?? 'Usuario' ?>
                                    </p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">
                                        <?= $_SESSION['usuario_cargo'] ?? 'Cargo' ?>
                                    </p>
                                </div>
                            </div>
 
                            <!-- Botón de cerrar sesión visible -->
                            <a href="<?= BASE_URL ?>logout"
                                class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition text-sm font-medium">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="hidden sm:inline">Salir</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- MENÚ DESPLEGABLE MÓVIL -->
    <div id="mobile-menu"
        class="hidden xl:hidden bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <?php
            if (file_exists(__DIR__ . '/../partials/navbar_menu_mobile.php')) {
                include __DIR__ . '/../partials/navbar_menu_mobile.php';
            } else {
                echo "<span class='text-xs text-red-400'>Falta navbar_menu_mobile.php</span>";
            }
            ?>
        </div>
    </div>
 
    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-1 w-full max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 z-0">
        <?php
        // Aquí se inyecta la vista correspondiente (ej. inicioVista.php)
        if (isset($contenido)):
            echo $contenido;
        elseif (isset($vistaContenido) && file_exists($vistaContenido)):
            include $vistaContenido;
        endif;
        ?>
    </main>
 
    <!-- FOOTER -->
    <footer
        class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4 mt-auto transition-colors duration-300">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500"> &copy; <?= date('Y') ?>
            I-Stock. Todos los derechos reservados.
        </div>
    </footer>
 
    <!-- Script Funcionalidad Dark Mode -->
    <script>
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
 
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }
 
        const themeToggleBtn = document.getElementById('theme-toggle');
 
        themeToggleBtn.addEventListener('click', function () {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');
 
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
 
    <script>
        // Usamos un event listener para asegurar que el DOM esté listo
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuBtn = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
 
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', () => {
                    // Toggle de la clase hidden para mostrar/ocultar
                    mobileMenu.classList.toggle('hidden');
 
                    // Cambiar el ícono
                    const icon = mobileMenuBtn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-bars');
                        icon.classList.toggle('fa-times');
                    }
                });
            }
        });
 
        // Script Notificaciones Campanita
        document.getElementById('notif-bell-btn')?.addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('notif-dropdown')?.classList.toggle('hidden');
        });
 
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('notif-dropdown');
            const bellBtn = document.getElementById('notif-bell-btn');
            if (dropdown && !dropdown.contains(e.target) && !bellBtn?.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
 
        function marcarNotificacionLeida(event, id) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }

            const item = document.getElementById('notif-item-' + id);
            if (item) {
                item.style.transition = 'all 0.2s ease-out';
                item.style.transform = 'translateX(20px)';
                item.style.opacity = '0';
                setTimeout(() => {
                    item.remove();
                    
                    const container = document.getElementById('notif-list-container');
                    if (container && container.querySelectorAll('[id^="notif-item-"]').length === 0) {
                        container.innerHTML = `
                            <div id="notif-empty-msg" class="p-6 text-center text-gray-400 dark:text-gray-500 text-sm">
                                <i class="fas fa-check-circle text-2xl text-emerald-400 mb-2 block"></i>
                                ¡Estás al día! No tienes notificaciones pendientes.
                            </div>`;
                    }
                }, 200);
            }

            const badge = document.getElementById('notif-badge');
            if (badge) {
                let current = parseInt(badge.innerText) || 1;
                current = Math.max(0, current - 1);
                badge.innerText = current;
                if (current === 0) badge.classList.add('hidden');
            }

            const sinLeerSpan = document.getElementById('notif-count-text');
            if (sinLeerSpan) {
                let current = parseInt(sinLeerSpan.innerText) || 1;
                current = Math.max(0, current - 1);
                sinLeerSpan.innerText = current + ' sin leer';
            }

            // Notificar a otras pestañas/pantallas en el mismo navegador
            try {
                localStorage.setItem('notif_last_read_id', id);
                localStorage.setItem('notif_updated_at', Date.now());
            } catch(e) {}

            fetch('<?= BASE_URL ?>index.php?pagina=notificacion&accion=marcarLeida', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_notificacion=' + encodeURIComponent(id)
            })
            .catch(err => console.error('Error al persistir notificación leída:', err));
        }

        // Sincronización en tiempo real entre pestañas y al cambiar de pantalla
        function sincronizarNotificaciones() {
            fetch('<?= BASE_URL ?>index.php?pagina=notificacion&accion=obtenerNoLeidas')
            .then(res => res.json())
            .then(data => {
                if (data && data.ok) {
                    const badge = document.getElementById('notif-badge');
                    const sinLeerSpan = document.getElementById('notif-count-text');
                    const container = document.getElementById('notif-list-container');

                    const total = data.total || 0;

                    if (badge) {
                        badge.innerText = total;
                        if (total > 0) {
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    }

                    if (sinLeerSpan) {
                        sinLeerSpan.innerText = total + ' sin leer';
                    }

                    if (container) {
                        if (total === 0) {
                            container.innerHTML = `
                                <div id="notif-empty-msg" class="p-6 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    <i class="fas fa-check-circle text-2xl text-emerald-400 mb-2 block"></i>
                                    ¡Estás al día! No tienes notificaciones pendientes.
                                </div>`;
                        } else {
                            let html = '';
                            data.notificaciones.forEach(n => {
                                html += `
                                <div id="notif-item-${n.notificacion_id}" class="p-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition flex items-start justify-between gap-3">
                                    <div class="space-y-1 pr-2 cursor-pointer" onclick="marcarNotificacionLeida(event, ${n.notificacion_id})">
                                        <p class="text-xs font-bold text-gray-800 dark:text-white">
                                            ${escapeHtml(n.titulo)}
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300">
                                            ${escapeHtml(n.mensaje)}
                                        </p>
                                        <span class="text-[10px] text-gray-400">
                                            ${n.fecha_creacion}
                                        </span>
                                    </div>
                                    <button type="button" onclick="marcarNotificacionLeida(event, ${n.notificacion_id})" 
                                            title="Marcar como leída"
                                            class="text-gray-400 hover:text-emerald-500 dark:hover:text-emerald-400 text-xs p-1.5 transition flex-shrink-0 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/30">
                                        <i class="fas fa-check text-sm"></i>
                                    </button>
                                </div>`;
                            });
                            container.innerHTML = html;
                        }
                    }
                }
            })
            .catch(err => console.error('Error sincronizando notificaciones:', err));
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        // Escuchar cambios de estado en otras pestañas
        window.addEventListener('storage', function(e) {
            if (e.key === 'notif_updated_at' || e.key === 'notif_last_read_id') {
                sincronizarNotificaciones();
            }
        });

        // Sincronizar al volver a enfocar la ventana o cambiar de vista
        window.addEventListener('focus', sincronizarNotificaciones);

        // Polling cada 25 segundos para mantener notificaciones sincronizadas en tiempo real
        setInterval(sincronizarNotificaciones, 25000);
    </script>
</body>
 
</html>