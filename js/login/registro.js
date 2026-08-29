// Genera usuario sugerido: primer nombre + inicial del apellido (sin tildes, minúsculas)
function generarUsuario(nombreCompleto) {
    const partes = nombreCompleto
        .trim()
        .replace(/\s+/g, ' ')
        .split(' ')
        .filter(p => p.length > 0);

    if (partes.length === 0) return '';

    const limpiar = (str) => str
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase();

    const primerNombre = limpiar(partes[0]);
    const inicialApellido = partes.length > 1 ? limpiar(partes[1].charAt(0)).toUpperCase() : '';

    return primerNombre + inicialApellido;
}

// Autocompletar el campo usuario al salir del campo nombre
document.addEventListener('DOMContentLoaded', () => {
    const inputNombre = document.getElementById('nombre');
    const inputUsuario = document.getElementById('usuario');

    if (inputNombre && inputUsuario) {
        inputNombre.addEventListener('blur', () => {
            // Solo autocompleta si el usuario no lo ha editado manualmente
            if (!inputUsuario.dataset.editadoManual) {
                inputUsuario.value = generarUsuario(inputNombre.value);
            }
        });

        inputUsuario.addEventListener('input', () => {
            inputUsuario.dataset.editadoManual = 'true';
        });
    }
});

async function procesarRegistro(e) {
    e.preventDefault();

    const form = document.getElementById('formRegistro');
    const btn = document.getElementById('btnRegistrar');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    const msgContainer = document.getElementById('msgContainer');

    const pass = document.getElementById('password').value;
    const passConfirm = document.getElementById('password_confirm').value;

    if (pass !== passConfirm) {
        mostrarMensaje('Las contraseñas no coinciden.', 'error');
        return;
    }

    if (pass.length < 6) {
        mostrarMensaje('La contraseña debe tener al menos 6 caracteres.', 'error');
        return;
    }

    // UI Loading state
    btn.disabled = true;
    btnText.classList.add('hidden');
    btnLoading.classList.remove('hidden');
    msgContainer.classList.add('hidden');

    try {
        const formData = new FormData(form);
        const response = await fetch('registro', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.status === 'success') {
            mostrarMensaje(data.message, 'success');
            form.reset();
            setTimeout(() => {
                window.location.href = data.redirect || 'login';
            }, 4500);
        } else {
            mostrarMensaje(data.message || 'Ocurrió un error al registrar el usuario.', 'error');
            btn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarMensaje('Error de conexión con el servidor. Intenta de nuevo.', 'error');
        btn.disabled = false;
        btnText.classList.remove('hidden');
        btnLoading.classList.add('hidden');
    }
}

function mostrarMensaje(mensaje, tipo) {
    const msgContainer = document.getElementById('msgContainer');
    msgContainer.classList.remove('hidden', 'bg-red-500/20', 'border-red-400/40', 'text-red-300', 'bg-emerald-500/20', 'border-emerald-400/40', 'text-emerald-300');
    
    if (tipo === 'success') {
        msgContainer.classList.add('bg-emerald-500/20', 'border', 'border-emerald-400/40', 'text-emerald-300');
        msgContainer.innerHTML = `<i class="fas fa-check-circle mr-2"></i> ${mensaje}`;
    } else {
        msgContainer.classList.add('bg-red-500/20', 'border', 'border-red-400/40', 'text-red-300');
        msgContainer.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i> ${mensaje}`;
    }
}