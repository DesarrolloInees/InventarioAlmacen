<?php
date_default_timezone_set('America/Bogota');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Técnico - Solicitudes Inees</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>app/logos/logoIneesSinFondo.png">

    <!-- Tailwind CSS -->
    <script src="js/tailwind.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .animated-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 25%, #1e7e6c 50%, #11998e 75%, #38ef7d 100%);
            background-size: 400% 400%;
            animation: gradientFlow 20s ease infinite;
            z-index: 1;
        }

        @keyframes gradientFlow {
            0%, 100% { background-position: 0% 50%; }
            25% { background-position: 50% 100%; }
            50% { background-position: 100% 50%; }
            75% { background-position: 50% 0%; }
        }

        .particles {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 2;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 15s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(100vh) translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) translateX(100px); opacity: 0; }
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 25%; animation-delay: 2s; }
        .particle:nth-child(3) { left: 40%; animation-delay: 4s; }
        .particle:nth-child(4) { left: 60%; animation-delay: 1s; }
        .particle:nth-child(5) { left: 75%; animation-delay: 3s; }
        .particle:nth-child(6) { left: 90%; animation-delay: 5s; }

        .card-registro {
            position: relative;
            z-index: 10;
            background: rgba(15, 25, 45, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(56, 239, 125, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: cardEntrance 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: scale(0.85) translateY(40px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .input-wrapper {
            position: relative;
        }

        .input-login {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(56, 239, 125, 0.3);
            color: #ffffff;
            padding: 0.85rem 1rem 0.85rem 3.25rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-login::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .input-login:focus {
            background: rgba(56, 239, 125, 0.1);
            border-color: #38ef7d;
            box-shadow: 0 0 0 4px rgba(56, 239, 125, 0.2), 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
            outline: none;
        }

        .input-icon {
            position: absolute;
            left: 1.15rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(56, 239, 125, 0.6);
            font-size: 1rem;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .input-login:focus~.input-icon {
            color: #38ef7d;
            transform: translateY(-50%) scale(1.1);
        }

        .btn-registro {
            position: relative;
            background: linear-gradient(135deg, #1e3c72 0%, #11998e 100%);
            border: none;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px rgba(17, 153, 142, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .btn-registro:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(17, 153, 142, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .text-gradient {
            background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>

    <!-- FONDO ANIMADO -->
    <div class="animated-bg"></div>

    <!-- PARTÍCULAS -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- CARD REGISTRO -->
    <div class="card-registro rounded-3xl p-8 md:p-10 w-full max-w-lg mx-auto text-center my-6">

        <!-- Logo -->
        <div class="mb-6">
            <img src="<?= BASE_URL ?>app/logos/logoIneesFondoBlanco.png" alt="Logo-Inees"
                class="w-24 mx-auto drop-shadow-2xl transform hover:scale-105 transition-transform duration-300">
        </div>

        <!-- Título -->
        <h1 class="text-3xl font-extrabold text-white mb-2 tracking-tight">
            Crear Cuenta de Cliente
        </h1>
        <p class="text-blue-200 mb-6 text-sm font-light">
            Solicitud de registro oficial para <span class="font-semibold text-gradient">Clientes / Empresas</span>
        </p>

        <!-- Badge Rol -->
        <div class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-6">
            <i class="fas fa-user-tie text-emerald-400"></i> Rol: Cliente
        </div>

        <!-- Formulario -->
        <form class="space-y-4 text-left" id="formRegistro" onsubmit="procesarRegistro(event)">
            <input type="hidden" name="accion" value="registrar_cliente">

            <!-- Nombre Completo -->
            <div class="input-wrapper">
                <input type="text" id="nombre" name="nombre" placeholder="Nombre Completo *" required
                    class="input-login w-full rounded-xl text-sm font-medium">
                <i class="input-icon fa-solid fa-id-card"></i>
            </div>

            <!-- Empresa -->
            <div class="input-wrapper">
                <input type="text" id="empresa" name="empresa" placeholder="Empresa a la que pertenece *" required
                    class="input-login w-full rounded-xl text-sm font-medium">
                <i class="input-icon fa-solid fa-building"></i>
            </div>

            <!-- Tipo de Solicitud -->
            <div class="input-wrapper">
                <select id="tipo_solicitud" name="tipo_solicitud" required
                    class="input-login w-full rounded-xl text-sm font-medium appearance-none cursor-pointer">
                    <option value="" disabled selected class="bg-slate-800">Tipo de Solicitud / Motivo de Contacto *</option>
                    <option value="Soporte Técnico" class="bg-slate-800">Soporte Técnico</option>
                    <option value="Mantenimiento / Servicio" class="bg-slate-800">Mantenimiento / Servicio</option>
                    <option value="Cotización / Información Comercial" class="bg-slate-800">Cotización / Información Comercial</option>
                    <option value="Creación de Cuenta / Acceso al Sistema" class="bg-slate-800">Creación de Cuenta / Acceso al Sistema</option>
                    <option value="Otro" class="bg-slate-800">Otro</option>
                </select>
                <i class="input-icon fa-solid fa-clipboard-question"></i>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-emerald-400/60 text-xs pointer-events-none"></i>
            </div>

            <!-- Grid Cédula y Teléfono -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-wrapper">
                    <input type="text" id="cedula" name="cedula" placeholder="Cédula / Documento"
                        class="input-login w-full rounded-xl text-sm font-medium">
                    <i class="input-icon fa-solid fa-address-card"></i>
                </div>
                <div class="input-wrapper">
                    <input type="text" id="celular" name="celular" placeholder="Teléfono / Celular"
                        class="input-login w-full rounded-xl text-sm font-medium">
                    <i class="input-icon fa-solid fa-phone"></i>
                </div>
            </div>

            <!-- Email -->
            <div class="input-wrapper">
                <input type="email" id="email" name="email" placeholder="Correo Electrónico *" required
                    class="input-login w-full rounded-xl text-sm font-medium">
                <i class="input-icon fa-solid fa-envelope"></i>
            </div>

            <!-- Nombre de Usuario -->
            <div class="input-wrapper">
                <input type="text" id="usuario" name="usuario" placeholder="Nombre de Usuario *" required
                    class="input-login w-full rounded-xl text-sm font-medium" autocomplete="username">
                <i class="input-icon fa-solid fa-user"></i>
            </div>

            <!-- Grid Contraseñas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Contraseña *" required
                        class="input-login w-full rounded-xl text-sm font-medium" autocomplete="new-password">
                    <i class="input-icon fa-solid fa-lock"></i>
                </div>
                <div class="input-wrapper">
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirmar Contraseña *" required
                        class="input-login w-full rounded-xl text-sm font-medium" autocomplete="new-password">
                    <i class="input-icon fa-solid fa-check-double"></i>
                </div>
            </div>

            <!-- Mensajes AJAX -->
            <div id="msgContainer" class="hidden text-sm px-4 py-3 rounded-xl font-semibold text-center mt-3"></div>

            <!-- Botón de Registro -->
            <button type="submit" id="btnRegistrar"
                class="btn-registro w-full text-white font-bold py-3.5 rounded-xl text-base tracking-wide mt-4">
                <span id="btnText"><i class="fas fa-paper-plane mr-2"></i> Enviar Solicitud de Registro</span>
                <span id="btnLoading" class="hidden">
                    <span class="spinner"></span>
                    <span class="ml-2">Enviando solicitud...</span>
                </span>
            </button>
        </form>

        <!-- Volver a Login -->
        <div class="mt-6 pt-4 border-t border-white/10">
            <a href="<?= BASE_URL ?>login"
                class="text-sm text-green-300 hover:text-white hover:underline font-semibold inline-flex items-center transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                ¿Ya tienes cuenta? Inicia sesión aquí
            </a>
        </div>
    </div>

    <script src="<?= BASE_URL ?>js/login/registro.js"></script>
</body>

</html>
