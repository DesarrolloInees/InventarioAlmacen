<?php if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado."); ?>

<div class="w-full max-w-5xl mx-auto px-4 md:px-6 mt-6">
    <div
        class="bg-white dark:bg-gray-800 p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 transition-all">

        <div class="mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
            <h3 class="text-2xl font-black text-gray-800 dark:text-white flex items-center">
                <i class="fas fa-bell text-yellow-500 mr-3 animate-bounce"></i> Auditoría Automática de Insumos Críticos
            </h3>
        </div>

        <div
            class="bg-amber-50 dark:bg-amber-950/20 border-l-4 border-amber-500 text-amber-900 dark:text-amber-400 p-4 mb-6 rounded-r-xl">
            <h5 class="font-bold text-sm flex items-center"><i class="fas fa-sliders-h mr-2"></i> Regla Operativa de
                Almacén:</h5>
            <p class="text-xs mt-1">El motor del almacén escaneará todas las existencias físicas. Cualquier repuesto
                mecánico o producto consumible que posea **<?= $data['umbral_actual'] ?> unidades o menos** provocará el
                disparo inmediato de alertas multi-canal (WhatsApp + Email).</p>
        </div>

        <div class="text-center py-6">
            <button type="button"
                class="px-8 py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-extrabold rounded-2xl shadow-lg hover:from-amber-600 hover:to-orange-700 transform hover:-translate-y-1 transition-all"
                id="btnEscanearStock">
                <i class="fas fa-search-location mr-2"></i> ESCANEAR ESTANTERÍA Y DISPARAR NOTIFICACIONES
            </button>
        </div>

        <div id="panelResultados" class="hidden mt-8 border-t border-gray-100 dark:border-gray-700 pt-6">
            <div id="bannerEstado" class="p-4 rounded-xl font-bold flex items-center mb-6"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-400 uppercase">Artículos Críticos</p>
                    <h2 class="text-4xl font-black text-red-500 mt-1" id="lblCantCriticos">0</h2>
                </div>
                <div class="p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-400 uppercase">Estado Email</p>
                    <h2 class="text-xl font-black text-gray-700 dark:text-gray-300 mt-2" id="lblEstadoCorreo">--</h2>
                </div>
                <div class="p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-400 uppercase">Estado WhatsApp</p>
                    <h2 class="text-xl font-black text-gray-700 dark:text-gray-300 mt-2" id="lblEstadoWhatsApp">--</h2>
                </div>
            </div>

            <div class="border rounded-xl p-4 bg-gray-50 dark:bg-gray-900 overflow-x-auto" id="previewHtmlCorreo"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#btnEscanearStock').on('click', function () {
            let btn = $(this);
            btn.prop('disabled', true).addClass('opacity-50').html('<i class="fas fa-sync fa-spin mr-2"></i> INSPECCIONANDO ALMACÉN LOCAL...');
            $('#panelResultados').addClass('hidden');

            $.ajax({
                url: 'index.php?pagina=notificacionInventario', // Dejamos que el index.php la reciba
                type: 'POST',
                data: {
                    accion_ajax: 'procesarNotificacionesStock' // Pasamos la acción por el cuerpo del POST
                },
                dataType: 'json',
                success: function (resp) {
                    btn.prop('disabled', false).removeClass('opacity-50').html('<i class="fas fa-search-location mr-2"></i> ESCANEAR ESTANTERÍA Y DISPARAR NOTIFICACIONES');

                    if (resp.exito) {
                        $('#lblCantCriticos').text(resp.items_encontrados);

                        if (resp.items_encontrados > 0) {
                            $('#bannerEstado').removeClass().addClass('p-4 rounded-xl font-bold flex items-center mb-6 bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-200').html('<i class="fas fa-exclamation-circle mr-2 text-xl"></i> Stock Crítico Detectado en Planta');
                            $('#lblEstadoCorreo').text(resp.correo_enviado ? 'ENVIADO' : 'FALLÓ').removeClass().addClass('text-xl font-black mt-2 ' + (resp.correo_enviado ? 'text-green-500' : 'text-red-500'));
                            $('#lblEstadoWhatsApp').text(resp.whatsapp_enviado ? 'ENVIADO' : 'DESACTIVADO/FALLÓ').removeClass().addClass('text-xl font-black mt-2 ' + (resp.whatsapp_enviado ? 'text-green-500' : 'text-gray-400'));

                            $('#previewHtmlCorreo').html(resp.html_generado);
                        } else {
                            $('#bannerEstado').removeClass().addClass('p-4 rounded-xl font-bold flex items-center mb-6 bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border border-green-200').html('<i class="fas fa-check-circle mr-2 text-xl"></i> Almacén en Excelente Estado');
                            $('#lblEstadoCorreo').text('NO REQUERIDO').removeClass().addClass('text-xl font-black text-gray-400 mt-2');
                            $('#lblEstadoWhatsApp').text('NO REQUERIDO').removeClass().addClass('text-xl font-black text-gray-400 mt-2');
                            $('#previewHtmlCorreo').html('<p class="text-xs text-gray-400 italic">No se requirió cuerpo de correo porque todo está por encima de 10 unidades.</p>');
                        }

                        $('#panelResultados').removeClass('hidden');
                    } else {
                        alert("Error interno del sistema: " + resp.error);
                    }
                },
                error: function (xhr, status, error) {
                    btn.prop('disabled', false).removeClass('opacity-50').html('<i class="fas fa-search-location mr-2"></i> ESCANEAR ESTANTERÍA Y DISPARAR NOTIFICACIONES');

                    // Muestra en consola el error real para depurar
                    console.error("XHR Response:", xhr.responseText);

                    // Alerta informativa con el verdadero culpable
                    alert("¡Cuidado bro! El servidor respondió con un error. Revisa la consola (F12) o asegúrate de que PHPMailer esté instalado.");
                }
            });
        });
    });
</script>