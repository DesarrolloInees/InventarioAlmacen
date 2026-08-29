<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisa tu Correo - Solicitud Prosegur</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>app/logos/logoIneesSinFondo.png">

    <!-- Tailwind CSS -->
    <script src="<?= BASE_URL ?>js/tailwind.js"></script>

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
            overflow-x: hidden;
            background: #0f172a;
        }

        .animated-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0; left: 0;
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
            width: 100%; height: 100%;
            top: 0; left: 0;
            z-index: 2;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 3px; height: 3px;
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
        .particle:nth-child(2) { left: 30%; animation-delay: 4s; }
        .particle:nth-child(3) { left: 50%; animation-delay: 3s; }
        .particle:nth-child(4) { left: 70%; animation-delay: 2.5s; }
        .particle:nth-child(5) { left: 90%; animation-delay: 1.5s; }

        .card-login {
            position: relative;
            z-index: 10;
            background: rgba(15, 25, 45, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(56, 239, 125, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: cardEntrance 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: scale(0.8) translateY(50px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .btn-login {
            position: relative;
            background: linear-gradient(135deg, #1e3c72 0%, #11998e 100%);
            border: none;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px rgba(17, 153, 142, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(17, 153, 142, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .logo-container {
            position: relative;
            display: inline-block;
        }

        .logo-container::after {
            content: '';
            position: absolute;
            top: -10px; left: -10px; right: -10px; bottom: -10px;
            background: radial-gradient(circle, rgba(56, 239, 125, 0.3) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .text-gradient {
            background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">

    <div class="animated-bg"></div>

    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="card-login rounded-3xl p-10 w-full max-w-md mx-4 text-center">

        <div class="logo-container mb-6">
            <img src="<?= BASE_URL ?>app/logos/logoIneesFondoBlanco.png" alt="Logo-Inees"
                class="w-28 mx-auto drop-shadow-2xl transform hover:scale-110 transition-transform duration-300">
        </div>

        <div class="mb-4 inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-400/30">
            <i class="fa-solid fa-paper-plane text-2xl"></i>
        </div>

        <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">
            ¡Revisa tu Correo!
        </h1>
        <p class="text-blue-200 mb-8 text-sm font-light leading-relaxed">
            Si tu correo está registrado en <span class="font-semibold text-gradient">Solicitud Prosegur</span>, recibirás un código de verificación de 6 dígitos.<br>
            <span class="text-xs text-white/50 mt-2 block">(Recuerda revisar la carpeta de spam o no deseados).</span>
        </p>

        <a href="<?= BASE_URL ?>resetPassword?email=<?= urlencode($_GET['email'] ?? '') ?>"
            class="btn-login w-full text-white font-bold py-4 rounded-xl text-base tracking-wide block relative text-center">
            <i class="fa-solid fa-key mr-2"></i>
            Ya tengo el código
        </a>

        <div class="mt-8 pt-6 border-t border-white border-opacity-10">
            <a href="<?= BASE_URL ?>login"
                class="text-sm text-green-300 hover:text-white hover:underline transition-all duration-300 font-medium inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Volver al Inicio de Sesión
            </a>
            <p class="text-xs text-white text-opacity-50 mt-4">
                <i class="fa-solid fa-shield-halved mr-1"></i>
                Sistema seguro I-Stock &copy; <?= date('Y') ?>
            </p>
        </div>
    </div>

</body>

</html>