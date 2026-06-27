//#region IMPORTACIONES Y VARIABLES GLOBALES
// El chatbot usa jQuery puro para AJAX, no se requiere pedir_datos_ajax() 
// porque interactúa con un microservicio externo y no llena DataTables
//#endregion

//#region FUNCIONES PROPIAS DEL MÓDULO
// Si se requieren funciones adicionales, se añaden aquí
//#endregion

//#region DELEGACIÓN DE EVENTOS
$(document).ready(function() {
    
    // Toggle chat interface visibility
    $(document).off('click', '#chatbot-toggle-btn').on('click', '#chatbot-toggle-btn', function() {
        $('#chatbot-window').removeClass('d-none');
        $(this).addClass('d-none');
    });

    // Close chat modal
    $(document).off('click', '#chatbot-close-btn').on('click', '#chatbot-close-btn', function() {
        $('#chatbot-window').addClass('d-none');
        $('#chatbot-toggle-btn').removeClass('d-none');
    });

    // Quick suggestions buttons handler
    $(document).off('click', '#chatbot-suggestions button').on('click', '#chatbot-suggestions button', function() {
        let texto = $(this).text();
        $('#chatbot-input').val(texto);
        $('#chatbot-form').submit(); 
    });

    // Submit form (AJAX handler)
    $(document).off('submit', '#chatbot-form').on('submit', '#chatbot-form', function(e) {
        e.preventDefault();

        let inputElement = $('#chatbot-input');
        let textoMensaje = inputElement.val().trim(); 
        let botonEnviar = $(this).find('button[type="submit"]');

        if (textoMensaje !== "") {
            // Render user message bubble
            let horaActual = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            let htmlMensajeUsuario = `
                <div class="d-flex mb-3">
                    <div class="user-msg-bubble bg-primary text-white p-3 shadow-sm">
                        ${textoMensaje}
                        <div class="text-end mt-1" style="font-size: 0.65rem; color: rgba(255,255,255,0.7);">${horaActual}</div>
                    </div>
                </div>
            `;
            $('#chatbot-messages').append(htmlMensajeUsuario);
            
            // Disable input pending response
            inputElement.val('').prop('disabled', true);
            botonEnviar.prop('disabled', true);
            
            // Auto-scroll
            let cajaMensajes = $('#chatbot-messages');
            cajaMensajes.scrollTop(cajaMensajes[0].scrollHeight);

            // Render typing indicator
            let idTyping = 'typing-' + Date.now();
            let htmlTyping = `
                <div class="d-flex mb-3" id="${idTyping}">
                    <div class="bot-msg-bubble bg-white border p-3 shadow-sm text-muted">
                        <i class="fi fi-rr-menu-dots"></i> Pensando...
                    </div>
                </div>
            `;
            cajaMensajes.append(htmlTyping);
            cajaMensajes.scrollTop(cajaMensajes[0].scrollHeight);

            // AJAX request to PHP controller
            $.ajax({
                url: '?views=chatbot',
                type: 'POST',
                dataType: 'json',
                data: {
                    accion: 'enviarMensaje',
                    mensaje: textoMensaje
                },
                success: function(respuesta) {
                    $('#' + idTyping).remove();

                    if (typeof respuesta === 'string') {
                        try {
                            respuesta = JSON.parse(respuesta);
                        } catch(e) {
                            console.error("Error parseando JSON:", e, respuesta);
                        }
                    }

                    let textoBot = "";
                    if(respuesta && respuesta.status === 'error') {
                        textoBot = respuesta.mensaje; 
                    } else if(respuesta.respuesta) {
                        textoBot = respuesta.respuesta; 
                    } else {
                        textoBot = "Error al procesar el formato de respuesta.";
                    }

                    // Render bot message bubble
                    let horaBot = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    let htmlMensajeBot = `
                        <div class="d-flex mb-3">
                            <div class="bot-msg-bubble bg-white border p-3 shadow-sm rounded">
                                ${textoBot}
                                <div class="text-end text-muted mt-1" style="font-size: 0.65rem;">${horaBot}</div>
                            </div>
                        </div>
                    `;
                    cajaMensajes.append(htmlMensajeBot);
                    cajaMensajes.scrollTop(cajaMensajes[0].scrollHeight);
                },
                error: function(xhr) {
                    $('#' + idTyping).remove();
                    let htmlError = `
                        <div class="d-flex mb-3">
                            <div class="bot-msg-bubble bg-danger text-white border p-3 shadow-sm">
                                Error del servidor PHP. Revisa la consola o los logs.
                            </div>
                        </div>
                    `;
                    cajaMensajes.append(htmlError);
                    cajaMensajes.scrollTop(cajaMensajes[0].scrollHeight);
                    console.error("Error AJAX ChatBot:", xhr.responseText);
                },
                complete: function() {
                    // Re-enable input
                    inputElement.prop('disabled', false).focus();
                    botonEnviar.prop('disabled', false);
                }
            });
        }
    });
});
//#endregion