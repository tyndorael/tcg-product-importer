<?php
/**
 * Plugin Name: My TCG Product Importer
 * Description: A plugin to import TCG (Trading Card Game) card data into WooCommerce products.
 * Version: 1.0.0
 * Author: Tyndorael
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define constants for plugin paths and URLs
define( 'MY_TCG_IMPORTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'MY_TCG_IMPORTER_URL', plugin_dir_url( __FILE__ ) );

// Include necessary classes and files
require_once MY_TCG_IMPORTER_PATH . 'includes/class-tcg-importer-api.php';
require_once MY_TCG_IMPORTER_PATH . 'includes/class-tcg-importer-metabox.php';

// Initialize the plugin's classes
function my_tcg_importer_init() {
    new TCG_Importer_API();
    new TCG_Importer_Metabox();
}
add_action( 'plugins_loaded', 'my_tcg_importer_init' );

/**
 * Enqueue scripts and styles for the admin metabox interface.
 */
function my_tcg_importer_admin_scripts() {
    global $post_type;

    // Load scripts and styles only on the WooCommerce product page
    if ( 'product' === $post_type ) {
        // Enqueue the plugin's CSS
        wp_enqueue_style( 'tcg-importer-style', MY_TCG_IMPORTER_URL . 'assets/css/tcg-importer.css', array(), '1.0.0' );

        // Enqueue the plugin's JS.
        // wp_localize_script passes data from PHP to JavaScript.
        wp_enqueue_script( 'tcg-importer-script', MY_TCG_IMPORTER_URL . 'assets/js/tcg-importer.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'tcg-importer-script', 'tcg_importer_data', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'tcg_importer_nonce' ),
        ) );
    }
}
add_action( 'admin_enqueue_scripts', 'my_tcg_importer_admin_scripts' );