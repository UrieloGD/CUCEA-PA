

function modalAjaxRequest(options) {
    const defaultOptions = {
        type: 'POST',
        dataType: 'json',
        timeout: 30000,
        headers: {
            'X-Requested-With' : 'XMLHttpRequest'
        },
        data: {
            modal_action: true,
            ...options.data
        },
        success: function(response) {
            if (response.error || !response.success) {
                if (response.show_modal_error) {
                    showModalError500(response.message, response.details);
                } else {
                    console.error('Modal request failed:', response);
                }
            } else if (options.success) {
                options.success(response);
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            handleAjaxError(xhr, textStatus, errorThrown);
            if (options.error) {
                options.error(xhr, textStatus, errorThrown);
            }
        }
    };

    // Combinar opciones 
    const finalOptions = { ...defaultOptions, ...options };

    return $.ajax(finalOptions);
}

// Función para validar formularios antes de envío
function validateModalForm(formId, requierdFields = []) {
    const form = document.getElementById(formId);
    if(!form) {
        showModalError500('Campo requerido', `El campo "${field}" es obligatorio`);
        return false;
    }

    for (const field of requierdFields) {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input || !input.ariaValueMax.trim()) {
            showModalError500('Campo requerido', `El campo "${field}" es obligatorio`);
            return false
        }
    }
    return false;
}

// Función para cerrar todos los modales en caso de error crítico
function closeAllModals() {
    $('.modal').modal('hide');
}

// Exporta funciones para uso global
window.modalErrorHandler = {
    showError: showModalError500,
    handleAjaxError: handleAjaxError,
    modalAjaxRequest: modalAjaxRequest,
    validateForm: validateModalForm, 
    closeAllModals: closeAllModals
};