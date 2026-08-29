<?php if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado."); 

$rolId = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;
$esClienteProsegur = ($rolId == 3 || ($_SESSION['nombre_rol'] ?? '') === 'clienteProsegur');
?>

<!-- CHOICES.JS: SELECT CON BÚSQUEDA -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<div class="w-full px-4 md:px-8 pb-8">

    <!-- ENCABEZADO -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center">
                    <i class="fas fa-plus-circle text-blue-600 dark:text-blue-400 mr-3"></i> Nueva Solicitud
                </h1>
                <?php if ($esClienteProsegur): ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 uppercase tracking-wider">
                        Cliente Prosegur
                    </span>
                <?php endif; ?>
            </div>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Diligencia el formulario para registrar una nueva solicitud de repuesto o servicio técnico.
            </p>
        </div>

        <a href="<?= BASE_URL ?>solicitudes"
            class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-5 py-2.5 rounded-xl shadow-sm font-medium transition">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- FORMULARIO DE CREACIÓN -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
        <form action="<?= BASE_URL ?>solicitudes&accion=guardar" method="POST" class="space-y-6">

            <!-- ASUNTO -->
            <div>
                <label for="asunto" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                    Asunto <span class="text-red-500">*</span>
                </label>
                <input type="text" id="asunto" name="asunto" required placeholder="Ej: Solicitud de impresoras adicionales"
                    class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="fas fa-boxes text-blue-500"></i> Ítems del Inventario (Opcional)
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Selecciona los repuestos o productos del almacén que requieres en esta solicitud.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-4 bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-200 dark:border-gray-600">
                    <div class="md:col-span-7">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Buscar / Seleccionar Artículo</label>
                        <select id="select_producto" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Selecciona un artículo del inventario...</option>
                            <?php if (!empty($inventario)): ?>
                                <?php foreach ($inventario as $inv): 
                                    $idVal = $inv['inventario_id'] ?? $inv['producto_id'] ?? $inv['repuesto_id'] ?? '';
                                ?>
                                    <option value="<?= htmlspecialchars($idVal) ?>" 
                                            data-nombre="<?= htmlspecialchars($inv['nombre_producto']) ?>" 
                                            data-categoria="<?= htmlspecialchars($inv['categoria'] ?? 'General') ?>"
                                            data-disponible="<?= $inv['cantidad_disponible'] ?>">
                                        <?= htmlspecialchars($inv['nombre_producto']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Cantidad Solicitar</label>
                        <input type="number" id="cant_producto" min="1" value="1" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2 flex items-end">
                        <button type="button" onclick="agregarItemInventario()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-3 rounded-lg text-sm transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>

                <!-- Tabla de ítems seleccionados -->
                <div id="contenedor_tabla_items" class="hidden overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase font-bold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="px-4 py-2.5">Artículo</th>
                                <th class="px-4 py-2.5 text-center">Categoría</th>
                                <th class="px-4 py-2.5 text-center">Cantidad Solicitada</th>
                                <th class="px-4 py-2.5 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_items" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <!-- Filas dinámicas -->
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="items_json" id="items_json" value="[]">
            </div>

                        <!-- DESCRIPCIÓN / OBSERVACIONES -->
            <div>
                <label for="descripcion" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                    Descripción / Observaciones <span class="text-red-500">*</span>
                </label>
                <textarea id="descripcion" name="descripcion" rows="4" required placeholder="Describe los detalles de tu solicitud..."
                    class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none"></textarea>
            </div>

            <!-- SECCIÓN: ADJUNTAR FOTO (OPCIONAL) -->
            <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide flex items-center justify-between">
                    <span><i class="fas fa-camera text-blue-500 mr-2"></i> Adjuntar Foto / Evidencia (Opcional)</span>
                    <span class="text-xs text-gray-400 font-normal uppercase">Formatos: JPG, PNG, WEBP</span>
                </label>

                <div class="relative">
                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden" onchange="previewFoto(event)">
                    
                    <div id="dropzone_foto" onclick="document.getElementById('foto').click()" class="border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 rounded-2xl p-6 text-center cursor-pointer transition bg-gray-50 dark:bg-gray-700/30 hover:bg-blue-50/50 dark:hover:bg-gray-700/60">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 dark:text-gray-500 mb-2"></i>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Haz clic aquí para adjuntar una fotografía</p>
                        <p class="text-xs text-gray-400 mt-1">Puedes subir una imagen del repuesto requerido o del daño existente (Opcional)</p>
                    </div>

                    <!-- Vista previa -->
                    <div id="preview_container" class="hidden mt-3 relative inline-block">
                        <img id="preview_img" src="#" alt="Vista Previa" class="max-h-48 rounded-xl shadow-md border border-gray-200 dark:border-gray-600 object-cover">
                        <button type="button" onclick="quitarFoto()" class="absolute -top-2 -right-2 bg-red-520600 text-white rounded-full p-1.5 shadow-md hover:bg-red-700 transition" title="Quitar foto">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="<?= BASE_URL ?>solicitudes"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition font-medium">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl shadow-sm font-medium transition transform hover:scale-105">
                    <i class="fas fa-paper-plane"></i> Enviar Solicitud
                </button>
            </div>

        </form>
    </div>
</div>

<!-- ESTILOS PARA QUE CHOICES.JS COMBINE CON EL DISEÑO OSCURO/CLARO -->
<style>
    .choices__inner {
        background-color: inherit;
        border-radius: 0.5rem;
        border-color: inherit;
        min-height: 42px;
        padding: 0.4rem 0.75rem;
    }

    /* Texto del select cerrado */
    .choices__inner,
    .choices__placeholder {
        color: #1f2937; /* gray-800 */
    }

    /* Input de búsqueda dentro del desplegable */
    .choices__input {
        background-color: #ffffff !important;
        color: #1f2937 !important; /* texto en negro */
    }

    /* Lista de opciones */
    .choices__list--dropdown,
    .choices__list[aria-expanded] {
        background-color: #ffffff;
        z-index: 50;
    }
    .choices__list--dropdown .choices__item--selectable {
        color: #1f2937 !important; /* texto negro en cada opción */
    }
    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: #2563eb; /* blue-600 */
        color: #ffffff !important; /* texto blanco solo en la opción resaltada */
    }

    /* Modo oscuro */
    .dark .choices__inner,
    .dark .choices__placeholder {
        color: #ffffff;
    }
    .dark .choices__list--dropdown,
    .dark .choices__list[aria-expanded] {
        background-color: #374151; /* gray-700 */
    }
    .dark .choices__list--dropdown .choices__item--selectable {
        color: #ffffff !important;
    }
    .dark .choices__input {
        background-color: #374151 !important;
        color: #ffffff !important;
    }
</style>

<script>
    let itemsSeleccionados = [];

    // INICIALIZAR SELECT CON BÚSQUEDA (CHOICES.JS)
    const choicesProducto = new Choices('#select_producto', {
        searchEnabled: true,
        searchPlaceholderValue: 'Buscar artículo...',
        itemSelectText: '',
        shouldSort: false,
        noResultsText: 'No se encontraron artículos',
        noChoicesText: 'No hay artículos disponibles',
        placeholder: true,
        placeholderValue: 'Selecciona un artículo del inventario...',
        searchResultLimit: 50,
        classNames: {
            containerOuter: 'choices'
        }
    });

    function agregarItemInventario() {
        const select = document.getElementById('select_producto');
        const cantInput = document.getElementById('cant_producto');
        
        // Obtener el valor seleccionado (compatible con Choices.js o el select directo)
        let prodId = choicesProducto ? choicesProducto.getValue(true) : select.value;
        if (!prodId || prodId === '') {
            prodId = select.value;
        }

        const cantidad = parseInt(cantInput.value) || 1;

        if (!prodId) {
            alert('Por favor selecciona un artículo del inventario.');
            return;
        }

        // Buscar la opción correspondiente en el select original
        let selectedOption = null;
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value == prodId) {
                selectedOption = select.options[i];
                break;
            }
        }

        if (!selectedOption) {
            alert('Por favor selecciona un artículo del inventario.');
            return;
        }

        const nombre = selectedOption.getAttribute('data-nombre') || selectedOption.text;
        const categoria = selectedOption.getAttribute('data-categoria') || 'General';

        // Verificar si ya existe en la lista
        const existeIndex = itemsSeleccionados.findIndex(i => i.producto_id == prodId);
        if (existeIndex !== -1) {
            itemsSeleccionados[existeIndex].cantidad += cantidad;
        } else {
            itemsSeleccionados.push({
                producto_id: prodId,
                nombre: nombre,
                categoria: categoria,
                cantidad: cantidad
            });
        }

        renderTablaItems();

        // Reset del select de Choices.js
        if (typeof choicesProducto !== 'undefined' && choicesProducto) {
            choicesProducto.setChoiceByValue('');
            choicesProducto.clearInput();
        }
        select.value = '';
        cantInput.value = 1;
    }

    function removerItem(index) {
        itemsSeleccionados.splice(index, 1);
        renderTablaItems();
    }

    function renderTablaItems() {
        const contenedor = document.getElementById('contenedor_tabla_items');
        const tbody = document.getElementById('tbody_items');
        const jsonInput = document.getElementById('items_json');

        jsonInput.value = JSON.stringify(itemsSeleccionados);

        if (itemsSeleccionados.length === 0) {
            contenedor.classList.add('hidden');
            tbody.innerHTML = '';
            return;
        }

        contenedor.classList.remove('hidden');
        tbody.innerHTML = itemsSeleccionados.map((item, idx) => `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                <td class="px-4 py-2.5 font-semibold text-gray-800 dark:text-white">${escapeHtml(item.nombre)}</td>
                <td class="px-4 py-2.5 text-center text-xs text-gray-500 dark:text-gray-400">${escapeHtml(item.categoria)}</td>
                <td class="px-4 py-2.5 text-center font-bold text-blue-600 dark:text-blue-400">${item.cantidad}</td>
                <td class="px-4 py-2.5 text-center">
                    <button type="button" onclick="removerItem(${idx})" class="text-red-500 hover:text-red-700 text-xs font-bold transition">
                        <i class="fas fa-trash mr-1"></i> Quitar
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function escapeHtml(text) {
        return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function previewFoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview_img').src = e.target.result;
                document.getElementById('preview_container').classList.remove('hidden');
                document.getElementById('dropzone_foto').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function quitarFoto() {
        document.getElementById('foto').value = '';
        document.getElementById('preview_img').src = '#';
        document.getElementById('preview_container').classList.add('hidden');
        document.getElementById('dropzone_foto').classList.remove('hidden');
    }
</script>