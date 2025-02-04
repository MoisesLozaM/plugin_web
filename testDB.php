<?php
/*
Plugin Name: shortCode Test
Description: Un plugin para agregar un shortcode personalizado.
Version: 1.0
Author: Moises Loza
*/

// Encolar los estilos CSS y scripts JS
function mi_shortcode_assets() {
    // Encola el CSS
    wp_enqueue_style('mi-shortcode-css', plugin_dir_url(__FILE__) . 'CSS/styles.css');
    // Encola el script JavaScript
    wp_enqueue_script('view_users_js', plugin_dir_url(__FILE__) . 'JS/view_users_js.js', ['jquery'], '1.0', true);
    // Pasar variables de PHP a JavaScript
    wp_localize_script('view_users_js', 'miShortcodeAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'mi_shortcode_assets');

// Shortcode para mostrar la tabla de usuarios
function mostrar_usuarios() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'users_data'; // Reemplaza 'users_data' por el nombre de tu tabla

    // Consulta de datos
    $results = $wpdb->get_results("SELECT id, nombre, apellidos, edad, imagen_url FROM $table_name", ARRAY_A);

    if (!empty($results)) {
        $output = '<table class="mi-shortcode-tabla">';
        $output .= '<tr><th>Nombre</th><th>Apellidos</th><th>Edad</th><th>Imagen</th></tr>';
        foreach ($results as $row) {
            $output .= '<tr>';
            // El nombre será un enlace con un atributo `data-id` para identificar al usuario
            $output .= '<td><a href="#" class="usuario-link" data-id="' . esc_attr($row['id']) . '">' . esc_html($row['nombre']) . '</a></td>';
            $output .= '<td>' . esc_html($row['apellidos']) . '</td>';
            $output .= '<td>' . esc_html($row['edad']) . '</td>';

            // Mostrar la imagen si existe una URL válida
            $ruta_imagen = esc_url($row['imagen_url']);
            if (!empty($ruta_imagen)) {
                $output .= '<td><img src="' . $ruta_imagen . '" alt="Imagen de ' . esc_html($row['nombre']) . '" style="width: 100px; height: auto;"></td>';
            } else {
                $output .= '<td>Sin imagen</td>';
            }

            $output .= '</tr>';
        }
        $output .= '</table>';
        return $output;
    } else {
        return 'No hay datos disponibles.';
    }
}
add_shortcode('mostrar_usuarios', 'mostrar_usuarios');

// AJAX para obtener los detalles del usuario
function obtener_usuario_detalles() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'users_data';

    // Verificar que se envió un ID válido
    $user_id = intval($_POST['user_id']);
    if ($user_id) {
        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $user_id), ARRAY_A);
        if ($user) {
            wp_send_json_success($user);
        } else {
            wp_send_json_error('Usuario no encontrado.');
        }
    } else {
        wp_send_json_error('ID de usuario no válido.');
    }
}
add_action('wp_ajax_obtener_usuario', 'obtener_usuario_detalles');

function mostrar_imagen_fullscreen_variant($atts) {
    // Extraer los atributos del shortcode
    $atts = shortcode_atts([
        'url' => '', // URL de la imagen (vacía por defecto)
        'titulo' => '', // Título en h2
        'subtitulo' => '', // Subtítulo en h3
    ], $atts);

    $imagen_url = esc_url($atts['url']);
    $titulo = esc_html($atts['titulo']);
    $subtitulo = esc_html($atts['subtitulo']);

    if (!empty($imagen_url)) {
        return '
            <div class="imagen-fullscreen-container">
                <img src="' . $imagen_url . '" alt="Imagen en pantalla completa" class="imagen-fullscreen">
                <div class="contenido-centro-variant">
                    <div class="container">
                        <button class="boton-ver-mas">Ver más</button>
                    </div>
                </div>
            </div>';
    } else {
        return '<p>No se proporcionó una URL válida para la imagen.</p>';
    }
}
add_shortcode('imagen_fullscreen_tria', 'mostrar_imagen_fullscreen_variant');

function mostrar_imagen_fullscreen($atts) {
    // Extraer los atributos del shortcode con valores por defecto
    $atts = shortcode_atts([
        'url' => '', // URL de la imagen
        'titulo' => '', // Título en h2
        'subtitulo' => '', // Subtítulo en h3
        'orientacion' => '', // Orientación del contenido
    ], $atts);

    $imagen_url = esc_url($atts['url']);
    $titulo = esc_html($atts['titulo']);
    $subtitulo = esc_html($atts['subtitulo']);
    $orientacion = trim(strtolower($atts['orientacion'])); // Normalizar

    if (!empty($imagen_url)) {
        // Definir clase según orientación
        $clase_orientacion = ($orientacion === "izquierda") ? "izquierda" : "centro";

        return '
            <div class="imagen-fullscreen-container ' . $clase_orientacion . '">
                <img src="' . $imagen_url . '" alt="Imagen en pantalla completa" class="imagen-fullscreen">
                <div class="contenido-' . $clase_orientacion . '">
                    <h2 class="card-title">' . $titulo . '</h2>
                    <h3 class="card-subtitle">' . $subtitulo . '</h3>
                    <button class="boton-ver-mas">Ver más</button>
                </div>
            </div>';
    } else {
        return '<p>No se proporcionó una URL válida para la imagen.</p>';
    }
}
add_shortcode('imagen_fullscreen', 'mostrar_imagen_fullscreen');

function imagen_lateral_content($atts) {
    $atts = shortcode_atts([
        'subtitulo' => '',
        'url' => '',
        'titulo' => '',
    ], $atts);

    $imagen_url = esc_url($atts['url']);
    $titulo = esc_html($atts['titulo']); 
    $subtitulo = esc_html($atts['subtitulo']); // Cambia a wp_kses_post si necesitas HTML

    if (!empty($imagen_url)) {
        return 
        '
            <div class="imagen-lateral-container">
                <img src="' . $imagen_url . '" alt="Imagen en pantalla completa" class="imagen-lateral">
                <div class="contenido-lateral">
                    <h2 class="card-title">' . $titulo . '</h2>
                    <h3 class="card-content">' . $subtitulo . '</h3>
                    <button class="boton-ver-mas">Ver más</button>
                </div>
            </div>
        
        ';
    } else {
        return '<p> No se inserto correctamente la imagen </p>' ;
    }
}
add_shortcode('imagen_lateral', 'imagen_lateral_content');

function tres_imagenes($atts){
    $atts = shortcode_atts([
        'img1' => '',
        'titulo1' => '',
        'subtitulo1' => '',
        'img2' => '',
        'titulo2' => '',
        'subtitulo2' => '',
        'img3' => '',
        'titulo3' => '',
        'subtitulo3' => '',
    ], $atts);

    $img1 = esc_url($atts['img1']);
    $img2 = esc_url($atts['img2']);
    $img3 = esc_url($atts['img3']);

    $titulo1 = esc_html($atts['titulo1']);
    $titulo2 = esc_html($atts['titulo2']);
    $titulo3 = esc_html($atts['titulo3']);
    
    $subtitulo1 = esc_html($atts['subtitulo1']);
    $subtitulo2 = esc_html($atts['subtitulo2']);
    $subtitulo3 = esc_html($atts['subtitulo3']);

    if (!empty($img1) && !empty($img2) && !empty($img3)) {
        return 
        '
            <div class="tres-imagenes-container">
                <div class="imagen-container">
                <img src="' . $img1 . '" alt="Imagen 1" class="imagen-tres">
                    <div class="contenido-wrapper">
                        <div class="barra-blanca"></div> <!-- Nueva barra blanca -->
                            <div class="contenido-tres">
                            <h2 class="card-title-imgs">' . $titulo1 . '</h2>
                            <h3 class="card-content-imgs">' . $subtitulo1 . '</h3>
                            <button class="boton-ver-mas">Ver más</button>
                        </div>
                    </div>
                </div>
                <div class="imagen-container">
                    <img src="' . $img2 . '" alt="Imagen 2" class="imagen-tres">
                        <div class="contenido-wrapper">
                        <div class="barra-blanca"></div> <!-- Nueva barra blanca -->
                            <div class="contenido-tres">
                            <h2 class="card-title-imgs">' . $titulo2 . '</h2>
                            <h3 class="card-content-imgs">' . $subtitulo2 . '</h3>
                            <button class="boton-ver-mas">Ver más</button>
                        </div>
                    </div>
                </div>
                <div class="imagen-container">
                    <img src="' . $img3 . '" alt="Imagen 3" class="imagen-tres">
                    <div class="contenido-wrapper">
                        <div class="barra-blanca"></div> <!-- Nueva barra blanca -->
                            <div class="contenido-tres">
                            <h2 class="card-title-imgs">' . $titulo3 . '</h2>
                            <h3 class="card-content-imgs">' . $subtitulo3 . '</h3>
                            <button class="boton-ver-mas">Ver más</button>
                        </div>
                    </div>
                </div>
            </div>
        ';
    } else {
        return '<p> No se inserto correctamente la imagen, error en algun lado </p>' ;
    }
}
add_shortcode('tres_imagenes', 'tres_imagenes');

function video_img($atts){
    $atts = shortcode_atts([
        'video' => '',
        'imagen' => '',
    ], $atts);

    $video = esc_url($atts['video']);
    $imagen = esc_url($atts['imagen']);

    if (!empty($video) && !empty($imagen)) {
        return 
        '
            <div class="video-img-container">
                <video src="' . $video . '" class="video" autoplay loop muted></video>
                <img src="' . $imagen . '" alt="Imagen" class="imagen">
                <div class="mensaje oculto">
                    <p>¡Animación terminada!</p>
                    <button onclick="alert(\'¡Botón clickeado!\')">Continuar</button>
                </div>
            </div>
        ';
    } else {
        return '<p>No se insertó correctamente la imagen, error en algún lado.</p>';
    }
}
add_shortcode('video_img', 'video_img');