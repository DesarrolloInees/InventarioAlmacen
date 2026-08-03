<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
?>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 max-w-3xl mx-auto">
        
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-exchange-alt text-blue-500 mr-2"></i> Mover Máquina
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

        <!-- Cuadro informativo de la máquina seleccionada -->
        <div class="mb-6 bg-blue-50 dark:bg-gray-700 border-l-4 border-blue-500 p-4 rounded-r-lg">
            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300">Máquina Seleccionada</h3>
            <ul class="mt-2 space-y-1 text-sm text-gray-700 dark:text-gray-200">
                <li><span class="font-bold">Serial:</span> <?= htmlspecialchars($data['maquina']['numero_serie']) ?></li>
                <li><span class="font-bold">Tipo:</span> <?= htmlspecialchars($data['maquina']['nombreTipoMaquina']) ?></li>
                <li><span class="font-bold">Ubicación Actual:</span> <?= $data['maquina']['nombre_bodega'] ? htmlspecialchars($data['maquina']['nombre_bodega']) : '<span class="text-red-500">Sin Bodega / Externa</span>' ?></li>
            </ul>
        </div>

        <form action="<?= BASE_URL ?>maquinaMovimiento/<?= $data['maquina']['id_maquina'] ?>" method="POST" class="space-y-6">
            
            <!-- Input oculto con el ID de la máquina -->
            <input type="hidden" name="id_maquina" value="<?= $data['maquina']['id_maquina'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tipo_movimiento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo de Movimiento <span class="text-red-500">*</span></label>
                    <select name="tipo_movimiento" id="tipo_movimiento" required onchange="toggleBodegaDestino()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Seleccione Acción --</option>
                        <option value="TRASLADO">Traslado (De bodega a bodega)</option>
                        <option value="INGRESO">Ingreso (Entra a bodega)</option>
                        <option value="SALIDA_REMISION">Salida Bodega</option>
                    </select>
                </div>

                <div>
                    <label for="id_bodega_destino" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bodega de Destino</label>
                    <select name="id_bodega_destino" id="id_bodega_destino" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Seleccione bodega destino --</option>
                        <?php foreach ($data['bodegas'] as $b): ?>
                            <option value="<?= $b['id_bodega'] ?>"><?= htmlspecialchars($b['nombre_bodega']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p id="texto-ayuda-bodega" class="mt-1 text-xs text-gray-500 hidden">Para salidas, la bodega destino no aplica.</p>
                </div>
            </div>

            <div>
                <label for="observacion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Observaciones / Motivo</label>
                <textarea name="observacion" id="observacion" rows="3" placeholder="Ej: Se traslada para revisión técnica, o Sale para instalación en cliente X..."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"></textarea>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition-transform transform hover:scale-105">
                    <i class="fas fa-check-circle mr-2"></i> Confirmar Movimiento
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleBodegaDestino() {
    const tipo = document.getElementById('tipo_movimiento').value;
    const selectBodega = document.getElementById('id_bodega_destino');
    const textoAyuda = document.getElementById('texto-ayuda-bodega');

    if (tipo === 'SALIDA_REMISION') {
        selectBodega.value = '';
        selectBodega.disabled = true;
        selectBodega.classList.add('opacity-50', 'cursor-not-allowed');
        textoAyuda.classList.remove('hidden');
    } else {
        selectBodega.disabled = false;
        selectBodega.classList.remove('opacity-50', 'cursor-not-allowed');
        textoAyuda.classList.add('hidden');
    }
}
</script>