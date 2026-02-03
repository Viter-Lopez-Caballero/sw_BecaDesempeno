import Swal from 'sweetalert2';
import './alerts.css';

/**
 * Alerta personalizada básica
 */
export const customAlert = (title, text, icon) => {
    return Swal.fire({
        title,
        text,
        icon,
        confirmButtonColor: "#1B396A",
        confirmButtonText: 'Aceptar',
    });
};

/**
 * Alerta de éxito
 */
export const alertaExito = (titulo, mensaje) => {
    Swal.fire({
        icon: 'success',
        title: titulo,
        text: mensaje,
        iconColor: '#10B981',
        showConfirmButton: false,
        timer: 1500,
        customClass: {
            popup: 'custom-alert-success',
        }
    });
};

/**
 * Alerta de error
 */
export const alertaError = (titulo, mensaje) => {
    Swal.fire({
        icon: 'error',
        title: titulo,
        text: mensaje,
        iconColor: '#DC2626',
        showConfirmButton: false,
        timer: 2000,
        customClass: {
            popup: 'custom-alert-error',
        }
    });
};

/**
 * Alerta de advertencia
 */
export const alertaAdvertencia = (titulo, mensaje) => {
    Swal.fire({
        icon: 'warning',
        title: titulo,
        text: mensaje,
        iconColor: '#F59E0B',
        showConfirmButton: false,
        timer: 2000,
        customClass: {
            popup: 'custom-alert-warning',
        }
    });
};

/**
 * Alerta de información
 */
export const alertaInfo = (titulo, mensaje) => {
    Swal.fire({
        icon: 'info',
        title: titulo,
        text: mensaje,
        iconColor: '#1B396A',
        confirmButtonColor: '#1B396A',
        confirmButtonText: 'Aceptar',
    });
};

/**
 * Alerta de cargando
 */
export const alertaCargando = (titulo, mensaje) => {
    Swal.fire({
        html: `
            <div class="flex flex-col items-center">
                <div class="loader spinner-border mt-5 mb-5"></div>
                <h2 class="swal2-title custom-alert-title mb-4">${titulo}</h2>
                <p class="mt-4">${mensaje}</p>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false,
        customClass: {
            popup: 'custom-alert-loading',
        }
    });
};

/**
 * Cerrar alerta de cargando
 */
export const cerrarAlerta = () => {
    Swal.close();
};

/**
 * Alerta de confirmación/pregunta
 */
export const alertaPregunta = (titulo, mensaje) => {
    return Swal.fire({
        icon: 'question',
        title: titulo,
        text: mensaje,
        iconColor: '#1B396A',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        buttonsStyling: false,
        reverseButtons: true,
        customClass: {
            popup: 'custom-alert-pregunta',
            confirmButton: 'custom-blue-bottom hover:bg-blue-900 text-white px-4 py-2 rounded ml-2 cursor-pointer',
            cancelButton: 'bg-gray-100 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded mr-2 cursor-pointer',
        }
    }).then((result) => {
        return result.isConfirmed;
    });
};
