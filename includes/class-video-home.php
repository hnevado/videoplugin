<?php 
if (!defined('ABSPATH')) 
 exit;

 class Video_Home {

    public function __construct() {

        // En el constructor hacemos 2 cosas: Agregamos el menú de administración y registramos el shortcode [video_home]
        // Si queremos agregar estilos personalizados al plugin, habría que añadir un add_action y en la función llamar a wp_enqueue_style

        add_action('admin_menu', array($this, 'vh_add_admin_menu'));
        add_shortcode('video_home', array($this, 'vh_display_video_shortcode'));
    }

    
    public function vh_add_admin_menu() {

        //add_menu_page(string $page_title, string $menu_title, string $capability, string $menu_slug, callable $callback = ”, string $icon_url = ”, 
        //int|float $position = null ): string
        
        add_menu_page(
            'Video Home',             
            'Video Home',             
            'edit_pages',             // Si tiene permisos de Edición, le dejamos acceder al plugin
            'video-home',             
            array($this, 'vh_display_admin_page'), 
            'dashicons-video-alt3',   
            6                         
        );
    }

    public function vh_display_admin_page() {
        if (!current_user_can('edit_pages')) {
            return;
        }

        // Guardamos los datos del formulario
        if (isset($_POST['vh_save_video'])) {

            $video_url = sanitize_text_field($_POST['vh_video_url']);
            $expiry_date = sanitize_text_field($_POST['vh_expiry_date']);

            //Guardamos estos valores sanitizados en la tabla wp_options
            update_option('vh_video_url', $video_url);
            update_option('vh_expiry_date', $expiry_date);
            echo '<div class="updated"><p>Datos guardados correctamente.</p></div>';

        }

        $video_url = get_option('vh_video_url', '');
        $expiry_date = get_option('vh_expiry_date', '');

        ?>
        <div class="wrap">
            <h1>Video Home</h1>
        
            <div class="notice notice-info">
             <p>
                El video que añadas en esta sección, se visualizará hasta la fecha máxima en todos los lugares donde tengas añadido el shortcode [video_home].<br/> 
                <i>Para usarlo dentro del theme, utilizar echo do_shortcode('[video_home]');</i>
             </p>
            </div>
          
            <form method="post">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="vh_video_url">URL del video (Obligatorio formato .mp4)</label></th>
                        <td><input type="text" id="vh_video_url" name="vh_video_url" value="<?php echo esc_attr($video_url); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="vh_expiry_date">Fecha máxima de visualización</label></th>
                        <td><input type="date" id="vh_expiry_date" name="vh_expiry_date" value="<?php echo esc_attr($expiry_date); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button('Guardar', 'primary', 'vh_save_video'); ?>
            </form>
        </div>
        <?php
    }

    
    public function vh_display_video_shortcode() {

        $video_url = get_option('vh_video_url', '');
        $expiry_date = get_option('vh_expiry_date', '');

        // Validamos la fecha actual contra la fecha máxima de visualización y en caso de que se cumpla
        // devolvemos el elemento video de HTML5 con el source en mp4 que es compatible con TODOS los navegadores
        // https://caniuse.com/?search=mp4

        $current_date = date('Y-m-d');
        
        if ($current_date <= $expiry_date && !empty($video_url)) {
            return '<video style="object-fit: cover;" width="100%" height="300" autoplay muted loop>
                        <source src="' . esc_url($video_url) . '" type="video/mp4">
                        Tu navegador no soporta el elemento de video.
                    </video>';
        } 
        
        //Si no hay video que mostrar, devuelve un string vacío
        return '';
        
    }
}

?>