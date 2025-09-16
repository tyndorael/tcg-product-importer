<?php
class TCG_Importer_Metabox {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_importer_metabox' ) );
    }

    public function add_importer_metabox() {
        add_meta_box(
            'tcg_importer_metabox',
            'Import TCG Card Data',
            array( $this, 'render_metabox_content' ),
            'product', // WooCommerce post type
            'side',
            'high'
        );
    }

    public function render_metabox_content() {
        // Instead of writing HTML here, we include it from a template file.
        // This keeps the code cleaner and more organized.
        include MY_TCG_IMPORTER_PATH . 'templates/tcg-search-modal.php';
    }
}