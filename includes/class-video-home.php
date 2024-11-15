<?php 
if (!defined('ABSPATH')) 
 exit;

 class Video_Home {

    public function __construct() {

        // En el constructor hacemos 2 cosas: Agregamos el menú de administración y 
        // registramos los shortcodes [video_home] y [video_home_en]
        // Si queremos agregar estilos personalizados al plugin, habría que añadir un add_action y en la función llamar a wp_enqueue_style

        add_action('admin_menu', array($this, 'vh_add_admin_menu'));

        add_shortcode('video_home', function() {
            return $this->vh_display_video_shortcode('es');
        });

        add_shortcode('video_home_en', function() {
            return $this->vh_display_video_shortcode('en');
        });
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
            wp_die('No tienes permiso para acceder a esta página.');
        }

        // Guardamos los datos del formulario validando con check_admin_referer para evitar ataques CSRF
        if (isset($_POST['vh_save_video']) && check_admin_referer('vh_save_video_action', 'vh_save_video_nonce')) {

            $video_url_es = sanitize_text_field($_POST['vh_video_url_es']);
            $expiry_date_es = sanitize_text_field($_POST['vh_expiry_date_es']);
            $video_width_es = sanitize_text_field($_POST['vh_video_width_es']);
            $video_height_es = sanitize_text_field($_POST['vh_video_height_es']);

            $video_url_en = sanitize_text_field($_POST['vh_video_url_en']);
            $expiry_date_en = sanitize_text_field($_POST['vh_expiry_date_en']);
            $video_width_en = sanitize_text_field($_POST['vh_video_width_en']);
            $video_height_en = sanitize_text_field($_POST['vh_video_height_en']);

            if (!empty($video_url_es) && pathinfo($video_url_es, PATHINFO_EXTENSION) !== 'mp4') {
                echo '<div class="error">
                        <p>
                            <strong>ERROR</strong> - La URL del video en español debe ser válida y terminar en .mp4
                        </p>
                        <p><a href="' . esc_url(admin_url('admin.php?page=video-home')) . '" class="button">Volver</a></p>
                      </div>';
                return false;
            }

            if (!empty($video_url_en) && pathinfo($video_url_en, PATHINFO_EXTENSION) !== 'mp4') {
                echo '<div class="error">
                        <p>
                            <strong>ERROR</strong> - La URL del video en inglés debe ser válida y terminar en .mp4
                        </p>
                        <p><a href="' . esc_url(admin_url('admin.php?page=video-home')) . '" class="button">Volver</a></p>
                      </div>';
                return false;
            }

            //Guardamos estos valores sanitizados en la tabla wp_options
            update_option('vh_video_url_es', $video_url_es);
            update_option('vh_expiry_date_es', $expiry_date_es);
            update_option('vh_video_width_es', $video_width_es);
            update_option('vh_video_height_es', $video_height_es);

            update_option('vh_video_url_en', $video_url_en);
            update_option('vh_expiry_date_en', $expiry_date_en);
            update_option('vh_video_width_en', $video_width_en);
            update_option('vh_video_height_en', $video_height_en);

            echo '<div class="updated"><p>Datos guardados correctamente.</p></div>';

        }

        $video_url_es = get_option('vh_video_url_es', '');
        $expiry_date_es = get_option('vh_expiry_date_es', '');
        $video_width_es = get_option('vh_video_width_es', '');
        $video_height_es = get_option('vh_video_height_es', '');

        $video_url_en = get_option('vh_video_url_en', '');
        $expiry_date_en = get_option('vh_expiry_date_en', '');
        $video_width_en = get_option('vh_video_width_en', '');
        $video_height_en = get_option('vh_video_height_en', '');

        ?>
        <div class="wrap">
            <h1>Video Home</h1>

            <div class="notice notice-info">
                <p>
                    Los videos que añadas en esta sección se visualizarán según el idioma correspondiente 
                    usando los shortcodes [video_home] para español y [video_home_en] para inglés.<br/> 
                    <i>Para usarlo dentro del theme, utiliza echo do_shortcode('[video_home]') o echo do_shortcode('[video_home_en]').</i>
                </p>
            </div>
          
            <form method="post">
                <?php wp_nonce_field('vh_save_video_action', 'vh_save_video_nonce'); ?>
                <h2>Versión en Español</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="vh_video_url_es">URL del video (Obligatorio formato .mp4)</label></th>
                        <td><input type="text" id="vh_video_url_es" name="vh_video_url_es" value="<?php echo esc_attr($video_url_es); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="vh_expiry_date_es">Fecha máxima de visualización</label></th>
                        <td><input type="date" id="vh_expiry_date_es" name="vh_expiry_date_es" value="<?php echo esc_attr($expiry_date_es); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="vh_video_width_es">Ancho (por defecto 100%)</label></th>
                        <td><input type="text" id="vh_video_width_es" name="vh_video_width_es" value="<?php echo esc_attr($video_width_es); ?>" /></td> 
                    </tr>

                    <tr>
                        <th scope="row"><label for="vh_video_height_es">Alto (por defecto 300)</label></th>
                        <td><input type="text" id="vh_video_height_es" name="vh_video_height_es" value="<?php echo esc_attr($video_height_es); ?>"/></td> 
                    </tr>

                </table>

                <h2>Versión en Inglés</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="vh_video_url_en">URL del video (Obligatorio formato .mp4)</label></th>
                        <td><input type="text" id="vh_video_url_en" name="vh_video_url_en" value="<?php echo esc_attr($video_url_en); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="vh_expiry_date_en">Fecha máxima de visualización</label></th>
                        <td><input type="date" id="vh_expiry_date_en" name="vh_expiry_date_en" value="<?php echo esc_attr($expiry_date_en); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="vh_video_width_en">Ancho (por defecto 100%)</label></th>
                        <td><input type="text" id="vh_video_width_en" name="vh_video_width_en" value="<?php echo esc_attr($video_width_en); ?>" /></td> 
                    </tr>

                    <tr>
                        <th scope="row"><label for="vh_video_height_en">Alto (por defecto 300)</label></th>
                        <td><input type="text" id="vh_video_height_en" name="vh_video_height_en" value="<?php echo esc_attr($video_height_en); ?>"/></td> 
                    </tr>
                </table>
                <?php submit_button('Guardar', 'primary', 'vh_save_video'); ?>
            </form>
        </div>
        <?php
    }

    
    public function vh_display_video_shortcode($lang) {

        $video_url = get_option("vh_video_url_{$lang}", '');
        $expiry_date = get_option("vh_expiry_date_{$lang}", '');
        $video_width = get_option("vh_video_width_{$lang}", '');
        $video_height = get_option("vh_video_height_{$lang}", '');

        // Validamos la fecha actual contra la fecha máxima de visualización y en caso de que se cumpla
        // devolvemos el elemento video de HTML5 con el source en mp4 que es compatible con TODOS los navegadores
        // https://caniuse.com/?search=mp4

        $current_date = date('Y-m-d');
        
        if ($current_date <= $expiry_date && !empty($video_url)) {

            if (empty($video_width))
             $video_width="100%";

             if (empty($video_height))
              $video_height="300";

            return '<video style="object-fit: cover;" width="'.$video_width.'" height="'.$video_height.'" autoplay muted loop>
                        <source src="' . esc_url($video_url) . '" type="video/mp4">
                        Tu navegador no soporta el elemento de video.
                    </video>';
        } 
        
        //Si no hay video que mostrar, devuelve un string vacío
        return '';
        
    }
}

?>