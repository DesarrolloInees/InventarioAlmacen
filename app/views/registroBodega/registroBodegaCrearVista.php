<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

$registro = $data['registro'] ?? [];
$bodegas = $data['bodegas'] ?? [];
$sugerencias = $data['sugerencias'] ?? [];

$fechaValor = !empty($registro['fecha_movimiento'])
    ? date('Y-m-d\TH:i', strtotime($registro['fecha_movimiento']))
    : date('Y-m-d\TH:i');

$tipoActual = strtoupper($registro['tipo_movimiento'] ?? 'ENTRADA');
if (!in_array($tipoActual, ['ENTRADA', 'SALIDA'], true)) {
    $tipoActual = 'ENTRADA';
}
$idBodegaActual = (string) ($registro['id_bodega'] ?? '');
?>

<!-- Select2: búsqueda escribiendo en el campo de artículo -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Adaptación de Select2 a Tailwind (claro / oscuro) */
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single {
        height: 42px !important;
        background-color: #f9fafb !important; /* bg-gray-50 */
        border: 1px solid #d1d5db !important; /* border-gray-300 */
        border-radius: 0.5rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #111827 !important;
        line-height: 40px !important;
        padding-left: 0.75rem !important;
        padding-right: 2rem !important;
        font-size: 0.875rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6b7280 !important; /* text-gray-500 */
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 1.25rem;
        font-size: 1.1rem;
        color: #9ca3af;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #f97316 !important; /* orange-500 */
        box-shadow: 0 0 0 1px #f97316 !important;
    }
    .select2-dropdown {
        border-color: #d1d5db !important;
        border-radius: 0.5rem !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #f97316 !important;
        color: #ffffff !important;
    }

    /* --- MODO OSCURO --- */
    .dark .select2-container .select2-selection--single {
        background-color: #374151 !important; /* bg-gray-700 */
        border-color: #4b5563 !important; /* border-gray-600 */
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #ffffff !important;
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af !important; /* text-gray-400 */
    }
    .dark .select2-dropdown {
        background-color: #111827 !important; /* bg-gray-900 */
        border-color: #374151 !important; /* border-gray-700 */
    }
    .dark .select2-search--dropdown .select2-search__field {
        background-color: #1f2937 !important; /* bg-gray-800 */
        border: 1px solid #4b5563 !important; /* border-gray-600 */
        color: #ffffff !important;
        border-radius: 0.375rem !important;
        outline: none !important;
    }
    .dark .select2-results__option {
        background-color: #111827 !important;
        color: #d1d5db !important; /* text-gray-300 */
    }
    .dark .select2-results__option[aria-selected="true"] {
        background-color: #1f2937 !important; /* bg-gray-800 */
        color: #ffffff !important;
    }
    .dark .select2-results__option--highlighted[aria-selected] {
        background-color: #374151 !important; /* bg-gray-700 */
        color: #ffffff !important;
    }
</style>

<div class="w-full px-4 md:px-6">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 max-w-4xl mx-auto transition-colors">

        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-clipboard-list text-orange-500 mr-2"></i> Registrar Entrada / Salida de Bodega
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                    Libro manual de novedades de bodega. No modifica el stock del sistema.
                </p>
            </div>
            <a href="<?= BASE_URL ?>registroBodegaVer"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">¡Error!</span> <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>registroBodegaCrear" method="POST" class="space-y-6" id="formRegistroBodega">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="tipo_movimiento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Tipo de movimiento <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo_movimiento" id="tipo_movimiento" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="ENTRADA" <?= $tipoActual === 'ENTRADA' ? 'selected' : '' ?>>ENTRADA (ingresa a bodega)</option>
                        <option value="SALIDA" <?= $tipoActual === 'SALIDA' ? 'selected' : '' ?>>SALIDA (egresa de bodega)</option>
                    </select>
                </div>

                <div>
                    <label for="fecha_movimiento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Fecha y hora <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="fecha_movimiento" id="fecha_movimiento" required
                        value="<?= htmlspecialchars($fechaValor) ?>"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div>
                <label for="nombre_articulo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nombre del artículo <span class="text-red-500">*</span>
                </label>
                <?php
                $nombreArticuloActual = trim((string) ($registro['nombre_articulo'] ?? ''));
                $nombreArticuloNorm = mb_strtolower($nombreArticuloActual, 'UTF-8');
                $articuloSeleccionado = false;
                ?>
                <select name="nombre_articulo" id="nombre_articulo" aria-required="true"
                    class="select2-articulo bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">— Escribe o selecciona un artículo —</option>
                    <?php foreach ($sugerencias as $s): ?>
                        <?php
                        $nombreOpcion = trim((string) ($s['nombre'] ?? ''));
                        if ($nombreOpcion === '') {
                            continue;
                        }
                        $codigoOpcion = trim((string) ($s['codigo'] ?? ''));
                        $esActual = !$articuloSeleccionado && $nombreArticuloNorm !== ''
                            && mb_strtolower($nombreOpcion, 'UTF-8') === $nombreArticuloNorm;
                        if ($esActual) {
                            $articuloSeleccionado = true;
                        }
                        $textoOpcion = $nombreOpcion . ($codigoOpcion !== '' ? ' — ' . $codigoOpcion : '');
                        ?>
                        <option value="<?= htmlspecialchars($nombreOpcion) ?>"
                            data-nombre="<?= htmlspecialchars($nombreOpcion) ?>"
                            data-codigo="<?= htmlspecialchars($codigoOpcion) ?>"
                            <?= $esActual ? 'selected' : '' ?>><?= htmlspecialchars($textoOpcion) ?></option>
                    <?php endforeach; ?>
                    <?php if ($nombreArticuloActual !== '' && !$articuloSeleccionado): ?>
                        <option value="<?= htmlspecialchars($nombreArticuloActual) ?>"
                            data-nombre="<?= htmlspecialchars($nombreArticuloActual) ?>"
                            data-codigo="<?= htmlspecialchars((string) ($registro['codigo_referencia'] ?? '')) ?>" selected><?= htmlspecialchars($nombreArticuloActual) ?></option>
                    <?php endif; ?>
                </select>
                <p id="error_articulo" class="mt-1 text-xs font-medium text-red-500 hidden">
                    Selecciona un artículo de la lista o escribe uno nuevo.
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Escribe para filtrar la lista. Para un artículo nuevo, digita su nombre y presiona <strong>Enter</strong>.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="codigo_referencia" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Código / Serial</label>
                    <input type="text" name="codigo_referencia" id="codigo_referencia" maxlength="100"
                        value="<?= htmlspecialchars($registro['codigo_referencia'] ?? '') ?>"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="cantidad" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Cantidad <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="cantidad" id="cantidad" required min="0.01" step="0.01"
                        value="<?= htmlspecialchars($registro['cantidad'] ?? '1') ?>"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="id_bodega" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bodega</label>
                    <select name="id_bodega" id="id_bodega"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">— Sin bodega —</option>
                        <?php foreach ($bodegas as $b): ?>
                            <option value="<?= (int) $b['id_bodega'] ?>" <?= $idBodegaActual === (string) $b['id_bodega'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($b['nombre_bodega']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="destino" id="label_destino" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Procedencia / Proveedor</label>
                    <input type="text" name="destino" id="destino" maxlength="200"
                        value="<?= htmlspecialchars($registro['destino'] ?? '') ?>"
                        placeholder="Ej: Proveedor XYZ"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="remision" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remisión</label>
                    <input type="text" name="remision" id="remision" maxlength="100"
                        value="<?= htmlspecialchars($registro['remision'] ?? '') ?>"
                        placeholder="Ej: REM-2026-001"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="cotizacion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cotización</label>
                    <input type="text" name="cotizacion" id="cotizacion" maxlength="100"
                        value="<?= htmlspecialchars($registro['cotizacion'] ?? '') ?>"
                        placeholder="Ej: COT-8891"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div>
                <label for="novedad" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Novedad</label>
                <input type="text" name="novedad" id="novedad" maxlength="255"
                    value="<?= htmlspecialchars($registro['novedad'] ?? '') ?>"
                    placeholder="Ej: Equipo con golpe leve, faltó un accesorio..."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label for="observacion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Observación</label>
                <textarea name="observacion" id="observacion" rows="3"
                    placeholder="Detalle adicional del movimiento..."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"><?= htmlspecialchars($registro['observacion'] ?? '') ?></textarea>
            </div>
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-end gap-3">
                <a href="<?= BASE_URL ?>registroBodegaVer"
                    class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white font-bold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-center">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-orange-500 text-white font-bold rounded-lg shadow-md hover:bg-orange-600 transition-transform transform hover:scale-105">
                    <i class="fas fa-save mr-2"></i> Guardar Registro
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function actualizarEtiquetaDestino() {
        const tipo = document.getElementById('tipo_movimiento').value;
        const label = document.getElementById('label_destino');
        const input = document.getElementById('destino');

        if (tipo === 'SALIDA') {
            label.textContent = 'Destino / Área que recibe';
            input.placeholder = 'Ej: Taller Norte, Técnico Juan Pérez';
        } else {
            label.textContent = 'Procedencia / Proveedor';
            input.placeholder = 'Ej: Proveedor XYZ';
        }
    }

    document.getElementById('tipo_movimiento').addEventListener('change', actualizarEtiquetaDestino);
    actualizarEtiquetaDestino();

    document.addEventListener('DOMContentLoaded', function () {
        if (!window.jQuery || !jQuery.fn.select2) return;

        const $articulo = jQuery('#nombre_articulo');
        if (!$articulo.length) return;

        const normalizarTexto = function (texto) {
            return String(texto || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        };

        // Las coincidencias existentes deben aparecer antes que la opción "(nuevo)",
        // así Enter selecciona el artículo real y no crea un duplicado.
        try {
            const AdaptadorTags = jQuery.fn.select2.amd.require('select2/data/tags');
            AdaptadorTags.prototype.insertTag = function (_, datos, etiqueta) {
                datos.push(etiqueta);
            };
        } catch (e) {
            // Si cambia la versión de Select2 se conserva el orden por defecto.
        }

        $articulo.select2({
            width: '100%',
            placeholder: 'Escribe para buscar un artículo...',
            allowClear: true,
            tags: true,
            maximumInputLength: 200,
            language: {
                noResults: function () {
                    return 'Sin coincidencias. Presiona Enter para usar este nombre.';
                },
                searching: function () {
                    return 'Buscando...';
                }
            },
            matcher: function (params, data) {
                const termino = normalizarTexto(params.term);
                if (termino === '') return data;
                return normalizarTexto(data.text).indexOf(termino) > -1 ? data : null;
            },
            createTag: function (params) {
                const texto = jQuery.trim(params.term);
                if (texto === '') return null;
                return { id: texto, text: texto, newTag: true };
            },
            templateResult: function (data) {
                return data.newTag ? data.text + ' (nuevo)' : data.text;
            },
            templateSelection: function (data) {
                if (!data.id) return data.text;
                const opcion = data.element;
                const nombre = opcion && opcion.getAttribute ? opcion.getAttribute('data-nombre') : '';
                return nombre || data.text;
            }
        });

        $articulo.on('select2:select', function (evento) {
            const codigo = document.getElementById('codigo_referencia');
            const opcion = evento.params.data.element;
            const sugerido = opcion && opcion.getAttribute ? (opcion.getAttribute('data-codigo') || '') : '';

            if (codigo && codigo.value.trim() === '' && sugerido !== '') {
                codigo.value = sugerido;
            }

            const error = document.getElementById('error_articulo');
            if (error) error.classList.add('hidden');
        });

        const formulario = document.getElementById('formRegistroBodega');
        if (formulario) {
            formulario.addEventListener('submit', function (evento) {
                const error = document.getElementById('error_articulo');
                if (!String($articulo.val() || '').trim()) {
                    evento.preventDefault();
                    if (error) error.classList.remove('hidden');
                    $articulo.select2('open');
                } else if (error) {
                    error.classList.add('hidden');
                }
            });
        }
    });
</script>


