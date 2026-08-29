// js/usuario/usuarioVer.js

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar DataTable si existe la tabla usuariosTable
    if (window.jQuery && $.fn.DataTable && $('#usuariosTable').length) {
        $('#usuariosTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });
    }

    // Modal de confirmación de eliminación
    const confirmModal = document.getElementById('confirmModal');
    const confirmButton = document.getElementById('confirmButton');
    
    document.querySelectorAll('[data-modal-trigger]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const userId = btn.getAttribute('data-id');
            if (confirmButton) {
                confirmButton.setAttribute('onclick', `eliminarUsuario(${userId})`);
            }
            if (confirmModal) {
                confirmModal.classList.remove('hidden');
            }
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (confirmModal) confirmModal.classList.add('hidden');
        });
    });
});

async function procesarSolicitudUsuario(userId, accion) {
    const titulo = accion === 'aprobar' ? '¿Aprobar esta cuenta?' : '¿Rechazar esta solicitud?';
    const texto = accion === 'aprobar' 
        ? 'El usuario podrá iniciar sesión en el sistema inmediatamente.' 
        : 'La solicitud será rechazada y el usuario no podrá acceder.';

    if (!confirm(`${titulo}\n\n${texto}`)) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('accion_usuario', accion);
        formData.append('usuario_id', userId);

        const response = await fetch('usuarioVer', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.status === 'success') {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'Ocurrió un error al procesar la solicitud.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión con el servidor.');
    }
}

async function eliminarUsuario(userId) {
    const confirmModal = document.getElementById('confirmModal');
    if (confirmModal) confirmModal.classList.add('hidden');

    try {
        const formData = new FormData();
        formData.append('accion_usuario', 'eliminar');
        formData.append('usuario_id', userId);

        const response = await fetch('usuarioVer', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.status === 'success') {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'Error al eliminar usuario.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión al eliminar.');
    }
}
