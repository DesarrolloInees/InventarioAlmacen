<?php if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado."); ?>
<style>
.select2-container .select2-selection--single{height:48px!important;border:1px solid #e5e7eb!important;border-radius:.5rem!important;display:flex!important;align-items:center!important;background-color:#fff!important}
.select2-container--default .select2-selection--single .select2-selection__rendered{color:#111827!important}
.dark .select2-container .select2-selection--single{background-color:#111827!important;border-color:#374151!important}
.dark .select2-container .select2-selection__rendered{color:#fff!important}
.dark .select2-dropdown{background-color:#111827!important;border-color:#374151!important}
.dark .select2-search--dropdown .select2-search__field{background-color:#1f2937!important;color:#fff!important;border:1px solid #4b5563!important}
.dark .select2-results__option{background-color:#111827!important;color:#d1d5db!important}
.dark .select2-results__option--highlighted[aria-selected]{background-color:#374151!important;color:#fff!important}
.dark input,.dark select,.dark textarea{color-scheme:dark}
/* Los <option> nativos los pinta el SO: hay que forzarlos en dark o se ven blancos */
.dark select option,.dark select optgroup{background-color:#111827!important;color:#f9fafb!important}
select option,select optgroup{color:#111827}
</style>
<div class="w-full max-w-5xl mx-auto px-4 md:px-6">
<div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700">
<div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
<div>
<h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Entrada de Recuperado</h1>
<p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Repuestos recuperados por tecnicos. No es compra.</p>
</div>
<a href="<?= BASE_URL ?>recuperacionVer" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-lg text-sm">Ver historial</a>
</div>
<?php if (!empty($errores)): ?>
<div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-6 rounded"><ul class="list-disc list-inside text-sm"><?php foreach ($errores as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<?php if (!empty($exito) && $exito): ?>
<div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 mb-6 rounded"><p class="font-bold">Entrada recuperada guardada.</p></div>
<?php endif; ?>
<?php if (empty($tecnicos)): ?>
<div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-4 mb-6 rounded text-sm">No hay tecnicos disponibles.</div>
<?php endif; ?>
<form action="<?= BASE_URL ?>recuperacionCrear" method="POST" class="space-y-6">
<div class="bg-gray-50 dark:bg-gray-900/60 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
<label class="block text-sm font-black mb-2 text-gray-700 dark:text-gray-200">Que vas a ingresar?</label>
<div class="grid grid-cols-3 gap-3">
<?php $ti = $datosPrevios['tipo_item'] ?? 'repuesto'; ?>
<label class="flex items-center justify-center p-3 rounded-lg border-2 cursor-pointer bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700"><input type="radio" name="tipo_item" value="repuesto" class="hidden" onchange="conmutarTipoItem('repuesto')" <?= $ti==='repuesto'?'checked':'' ?>><span class="text-sm font-bold text-gray-800 dark:text-white">Repuesto</span></label>
<label class="flex items-center justify-center p-3 rounded-lg border-2 cursor-pointer bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700"><input type="radio" name="tipo_item" value="producto" class="hidden" onchange="conmutarTipoItem('producto')" <?= $ti==='producto'?'checked':'' ?>><span class="text-sm font-bold text-gray-800 dark:text-white">Producto</span></label>
<label class="flex items-center justify-center p-3 rounded-lg border-2 cursor-pointer bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700"><input type="radio" name="tipo_item" value="manual" class="hidden" onchange="conmutarTipoItem('manual')" <?= $ti==='manual'?'checked':'' ?>><span class="text-sm font-bold text-gray-800 dark:text-white">Otro/Manual</span></label>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div id="wrapper_repuestos" class="md:col-span-2">
<label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-200">Repuesto del catalogo *</label>
<select id="id_repuesto" name="id_repuesto" class="w-full p-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white select2-elemento">
<option value="">-- Elige el repuesto --</option>
<?php foreach ($repuestosActivos as $rep): ?><option value="<?= $rep['id_repuesto'] ?>" <?= (isset($datosPrevios['id_repuesto']) && $datosPrevios['id_repuesto']==$rep['id_repuesto'])?'selected':'' ?>>[<?= htmlspecialchars($rep['codigo_referencia']) ?>] <?= htmlspecialchars($rep['nombre_repuesto']) ?></option><?php endforeach; ?>
</select>
</div>
<div id="wrapper_productos" class="md:col-span-2 hidden">
<label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-200">Producto *</label>
<select id="id_producto" name="id_producto" class="w-full p-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white select2-elemento">
<option value="">-- Elige el producto --</option>
<?php foreach ($productosActivos as $pro): ?><option value="<?= $pro['id_producto'] ?>" <?= (isset($datosPrevios['id_producto']) && $datosPrevios['id_producto']==$pro['id_producto'])?'selected':'' ?>>[<?= htmlspecialchars($pro['codigo_interno']) ?>] <?= htmlspecialchars($pro['nombre_producto']) ?></option><?php endforeach; ?>
</select>
</div>
<div id="wrapper_manual" class="md:col-span-2 hidden">
<label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-200">Nombre (manual) *</label>
<input type="text" name="repuesto_manual" maxlength="150" value="<?= htmlspecialchars($datosPrevios['repuesto_manual'] ?? '') ?>" class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white">
</div>
<div>
<label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-200">Tecnico que recupero *</label>
<select name="id_tecnico_origen" required class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white">
<option value="">-- Selecciona el tecnico --</option>
<?php $tecSel = $datosPrevios['id_tecnico_origen'] ?? ''; ?>
<optgroup label="Tecnicos locales (Taller)">
<?php $hayLoc=false; foreach($tecnicos as $t): if(strpos($t['value'],'local_')===0): $hayLoc=true; ?>
<option value="<?= htmlspecialchars($t['value']) ?>" <?= $tecSel===$t['value']?'selected':'' ?>><?= htmlspecialchars($t['nombre']) ?> (<?= htmlspecialchars($t['cargo']) ?>)</option>
<?php endif; endforeach; if(!$hayLoc): ?><option value="" disabled>No hay locales activos</option><?php endif; ?>
</optgroup>
<optgroup label="Motorizados (otra app)">
<?php $hayMot=false; foreach($tecnicos as $t): if(strpos($t['value'],'moto_')===0): $hayMot=true; ?>
<option value="<?= htmlspecialchars($t['value']) ?>" <?= $tecSel===$t['value']?'selected':'' ?>><?= htmlspecialchars($t['nombre']) ?> (<?= htmlspecialchars($t['cargo']) ?>)</option>
<?php endif; endforeach; if(!$hayMot): ?><option value="" disabled>No hay motorizados</option><?php endif; ?>
</optgroup>
</select>
</div>
<div>
<label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-200">Serial</label>
<input type="text" name="serial_recuperado" maxlength="100" value="<?= htmlspecialchars($datosPrevios['serial_recuperado'] ?? '') ?>" class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white">
</div>
<div>
<label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-200">Fecha *</label>
<input type="date" name="fecha_movimiento" required max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($datosPrevios['fecha_movimiento'] ?? date('Y-m-d')) ?>" class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white">
</div>
<div>
<label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-200">Cantidad *</label>
<input type="number" name="cantidad" min="1" required value="<?= htmlspecialchars($datosPrevios['cantidad'] ?? '1') ?>" class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white">
</div>
<div class="md:col-span-2">
<label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-200">Novedad</label>
<textarea name="novedad" rows="2" class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white"><?= htmlspecialchars($datosPrevios['novedad'] ?? '') ?></textarea>
</div>
<div class="md:col-span-2">
<label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-200">Observacion</label>
<textarea name="observacion" rows="2" class="mt-1 block w-full px-3 py-3 bg-white dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white"><?= htmlspecialchars($datosPrevios['observacion'] ?? '') ?></textarea>
</div>
</div>
<div class="pt-6 flex justify-end">
<button type="submit" class="px-8 py-3 bg-amber-500 text-white font-bold rounded-lg">Guardar recuperado</button>
</div>
</form>
</div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function conmutarTipoItem(t){
var a=document.getElementById('wrapper_repuestos');
var b=document.getElementById('wrapper_productos');
var c=document.getElementById('wrapper_manual');
a.classList.add('hidden');b.classList.add('hidden');c.classList.add('hidden');
if(t==='repuesto')a.classList.remove('hidden');
if(t==='producto')b.classList.remove('hidden');
if(t==='manual')c.classList.remove('hidden');
}
document.addEventListener("DOMContentLoaded",function(){
if(window.jQuery){jQuery('.select2-elemento').select2({width:'100%'});}
var ch=document.querySelector('input[name="tipo_item"]:checked');
conmutarTipoItem(ch?ch.value:'repuesto');
});
</script>
