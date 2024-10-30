<?php
/*
  Plugin Name: MazingAR for Wordpress
  Plugin URI: https://mazingxr.com
  description: Plugin to embed Mazing Augmented Reality Experiences to your website / shop
  Version: 1.0.2
  Author: Mazing G.m.b.H.
  Author URI: https://mazingxr.com
*/


// Register various scripts and necessary files
function mazgar_register_script()
{

    // CSS Custom Mazing
    wp_register_style('base_style', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');
    wp_enqueue_style('base_style');

    // JS Bootstrap
    wp_register_script('prefix_bootstrap_js', plugins_url('/lib/bootstrap.bundle.5.1.3.min.js', __FILE__));
    wp_enqueue_script('prefix_bootstrap_js');

    // CSS Bootstrap
    wp_register_style('prefix_bootstrap_css', plugins_url('/css/bootstrap.5.1.3.min.css', __FILE__));
    wp_enqueue_style('prefix_bootstrap_css');

    // JQUERY
    wp_enqueue_script('jquery');
}


// Creation of Database tables
function mazing_ar_shortcode_table()
{
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $tablename = $wpdb->prefix . "mazing_ar_shortcode";
    $sql = "CREATE TABLE $tablename (
	  id mediumint(11) NOT NULL AUTO_INCREMENT,
	  name varchar(80) NOT NULL,
	  url varchar(200) NOT NULL,
	  image varchar(200) NOT NULL,
	  ratio_left mediumint(11) DEFAULT 4,
	  ratio_right mediumint(11) DEFAULT 3,	  
	  max_width mediumint(11) DEFAULT 600,
	  max_height mediumint(11) DEFAULT 450,
	  PRIMARY KEY  (id)
	) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'mazing_ar_shortcode_table');

// Add side menu to wp admin panel
function mazgar_set_plugin_menu()
{
    mazgar_register_script();

    add_menu_page('MazingAR', 'MazingAR', 'manage_options', 'mazgar_displayList', 'mazgar_displayList', plugins_url('/img/icon.png', __FILE__), 10);
    add_submenu_page('mazgar_displayList', 'Submenu Page Title', 'Dashboard', 'manage_options', 'mazgar_displayList');
}

add_action("admin_menu", "mazgar_set_plugin_menu");


// main entry
function mazgar_displayList()
{
    include "mazgar_displaylist.php";
}


// short code generation and query for existing project
function mazgar_shortcode_generation($atts)
{

    // Attributes
    $atts = shortcode_atts(
        array(
            'id' => '',
        ),
        $atts,
        'mazing'
    );

    global $wpdb;
    $tablename = $wpdb->prefix . "mazing_ar_shortcode";
    $id = $atts['id'];

    $specget = $wpdb->prepare("SELECT * FROM " . $tablename . " WHERE id = " . $id);
    $entriesList = $wpdb->get_results($specget);

    if (count($entriesList) > 0) {
        foreach ($entriesList as $entry) {
            $id = $entry->id;
            $url = $entry->url;

            $ratioLeft = $entry->ratio_left;
            $ratioRight = $entry->ratio_right;
            $maxWidth = $entry->max_width;
            $maxHeight = $entry->max_height;

            return "<div mazing-id='" . $id . "' mazing-url='" . $url . "' mazing-ratio-left='" . $ratioLeft . "' mazing-ratio-right='" . $ratioRight . "' mazing-max-width='" . $maxWidth . "' mazing-max-height='" . $maxHeight . "'></div> <script>generateMazingFrame()</script>";
        }
    } else {
        return "<br>MAZING ID " . $id . " NOT FOUND, CHECK MAZINGAR PLUGIN IN WP-ADMIN<br>";
    }
    return '';

}

add_shortcode('mazing', 'mazgar_shortcode_generation');

function mazgar_check_shortcode_existence()
{
    // JS Mazing frame lazy operation
    wp_register_script('prefix_generator', plugins_url('/lib/mazgar_generator.js', __FILE__));
    wp_enqueue_script('prefix_generator');
}

add_action('wp_enqueue_scripts', 'mazgar_check_shortcode_existence');



