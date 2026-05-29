<!DOCTYPE html>
<html lang="es" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titulo) ? $titulo . ' - Sistema' : 'Dashboard - Sistema' ?></title>

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

    <!-- NAVBAR ARRIBA -->
    <nav
        class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50 transition-colors duration-300">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
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
    </script>
</body>

</html>