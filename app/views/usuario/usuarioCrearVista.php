<?php if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado."); ?>

<div class="w-full max-w-4xl mx-auto px-4 md:px-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 transition-colors">

        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                <i class="fa-solid fa-user-plus text-indigo-600 dark:text-indigo-500 mr-2"></i> Crear Nuevo Usuario
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Completa los datos para registrar un nuevo usuario en el
                sistema.</p>
        </div>

        <?php if (isset($data['mensaje_error'])): ?>
            <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-6 rounded shadow-sm"
                role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <p class="font-bold">¡Error!</p>
                </div>
                <p class="mt-1 text-sm"><?php echo htmlspecialchars($data['mensaje_error']); ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre
                        Completo</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej: Carlos Rodriguez"
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition-colors">
                </div>
                <div>
                    <label for="cedula"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cédula</label>
                    <input type="text" id="cedula" name="cedula" required placeholder="Ej: 123456789"
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition-colors">
                </div>
                <div>
                    <label for="cargo"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cargo</label>
                    <input type="text" id="cargo" name="cargo" required placeholder="Ej: Técnico Especialista"
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition-colors">
                </div>
                <div>
                    <label for="celular"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Celular</label>
                    <input type="tel" id="celular" name="celular" required placeholder="Ej: 3001234567"
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition-colors">
                </div>
                <div class="md:col-span-2">
                    <label for="email"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" id="email" name="email" required placeholder="ejemplo@inees.co"
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition-colors">
                </div>
                <div>
                    <label for="usuario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre
                        de Usuario</label>
                    <input type="text" id="usuario" name="usuario" required placeholder="Ej: crodriguez"
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition-colors">
                </div>
                <div>
                    <label for="pass"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contraseña</label>
                    <input type="password" id="pass" name="pass" required placeholder="••••••••"
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition-colors">
                </div>
                <div class="md:col-span-2">
                    <label for="nivel_acceso"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rol / Nivel de
                        Acceso</label>
                    <select id="nivel_acceso" name="nivel_acceso" required
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition-colors">
                        <option value="">-- Selecciona un rol --</option>

                        <?php if (!empty($tiposUsuario)): ?>
                            <?php foreach ($tiposUsuario as $tipo): ?>
                                <?php
                                $selected = (isset($datosPrevios['nivel_acceso']) && $datosPrevios['nivel_acceso'] == $tipo['idTipoUsuario']) ? 'selected' : '';
                                ?>
                                <option value="<?= htmlspecialchars($tipo['idTipoUsuario']) ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($tipo['nombreTipoUsuario']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay roles definidos en BD</option>
                        <?php endif; ?>

                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-4">
                <a href="<?php echo BASE_URL; ?>usuarioVer"
                    class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-all duration-300 transform hover:scale-105">
                    Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>