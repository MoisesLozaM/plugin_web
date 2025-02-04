jQuery(document).ready(function ($) {
    // Capturar el clic en el nombre del usuario
    $(document).on('click', '.usuario-link', function (e) {
        e.preventDefault();

        // Obtener el ID del usuario
        const userId = $(this).data('id');

        // Enviar solicitud AJAX
        $.ajax({
            url: miShortcodeAjax.ajax_url, // URL para la solicitud AJAX
            method: 'POST',
            data: {
                action: 'obtener_usuario', // Acción AJAX definida en PHP
                user_id: userId,
            },
            success: function (response) {
                if (response.success) {
                    const user = response.data;
                    // Reemplazar la tabla con el perfil del usuario
                    const profileHtml = `
                        <div class="usuario-perfil">
                            <h2>Perfil de ${user.nombre} ${user.apellidos}</h2>
                            <p><strong>Edad:</strong> ${user.edad}</p>
                            <p><strong>Descripción:</strong> ${user.descripcion || 'Sin descripción disponible'}</p>
                            <img src="${user.imagen_url}" alt="Imagen de ${user.nombre}" style="width: 150px; height: auto;">
                            <button class="volver-tabla">Volver</button>
                        </div>
                    `;
                    $('.mi-shortcode-tabla').replaceWith(profileHtml);
                } else {
                    alert(response.data || 'Error al obtener los datos del usuario.');
                }
            },
        });
    });

    // Volver a la tabla original
    $(document).on('click', '.volver-tabla', function () {
        location.reload(); // Recargar la página para mostrar la tabla original
    });
});


document.addEventListener('DOMContentLoaded', function () {
    // Agrega un evento de clic a los botones con la clase 'tarjeta-boton'
    document.querySelectorAll('.tarjeta-boton').forEach(function (boton) {
        boton.addEventListener('click', function (e) {
            console.log('Botón clicado: ' + e.target.innerText);
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const imagen = document.querySelector(".imagen");
    const mensaje = document.querySelector(".mensaje");

    imagen.addEventListener("animationend", function () {
        mensaje.classList.remove("oculto");
    });
});
