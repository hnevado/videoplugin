<?php
/*
Plugin Name: Video Home Plugin
Description: Plugin para mostrar un video con límite de visualización en una fecha específica usando un shortcode.
Version: 1.0
Author: Héctor Nevado
*/

// Evitamos accesos no autorizados
if (!defined('ABSPATH')) 
 exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-video-home.php';


function vh_plugin_init() {
    $video_home = new Video_Home();
}

add_action('plugins_loaded', 'vh_plugin_init');
?>