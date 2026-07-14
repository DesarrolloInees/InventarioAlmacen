<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
?>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 max-w-3xl mx-auto">
        
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-microchip text-orange-500 mr-2"></i> Registrar Nueva Máquina
                </h1>
            </div>
            <a href="<?= BASE_URL ?>maquinaInventarioVer" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">¡Atención!</span> <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>maquinaInventarioCrear" method="POST" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="numero_serie" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Número de Serie <span class="text-red-500">*</span></label>
                    <input type="text" name="numero_serie" id="numero_serie" required placeholder="Ej: SN-90210"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>

                <div>
                    <label for="idTipoMaquina" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo de Máquina <span class="text-red-500">*</span></label>
                    <select name="idTipoMaquina" id="idTipoMaquina" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Seleccione un tipo --</option>
                        <?php foreach ($data['tipos'] as $t): ?>
                            <option value="<?= $t['idTipoMaquina'] ?>"><?= htmlspecialchars($t['nombre_tipo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="id_bodega" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ubicación (Bodega)</label>
                    <select name="id_bodega" id="id_bodega" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Sin asignar (Externa/Cliente) --</option>
                        <?php foreach ($data['bodegas'] as $b): ?>
                            <option value="<?= $b['id_bodega'] ?>"><?= htmlspecialchars($b['nombre_bodega']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="condicion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Condición Inicial</label>
                    <select name="condicion" id="condicion" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="nueva">Nueva</option>
                        <option value="usada">Usada</option>
                        <option value="reparacion">En Reparación</option>
                        <option value="dañada">Dañada</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="estado_remision" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado Adicional (Opcional)</label>
                <input type="text" name="estado_remision" id="estado_remision" placeholder="Ej: Kisan, Desinstalación..."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle text-blue-500"></i> Este campo es opcional y puede usarse para indicar información adicional sobre la máquina, como el estado de remisión, cliente asociado, o cualquier detalle relevante.
                </p>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-bold rounded-lg shadow-md hover:bg-orange-600 transition-transform transform hover:scale-105">
                    <i class="fas fa-save mr-2"></i> Guardar Máquina
                </button>
            </div>
        </form>
    </div>
</div>