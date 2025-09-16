<?php
class TCG_Importer_Settings {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_settings_page() {
        add_options_page(
            'TCG Importer Settings',
            'TCG Importer',
            'manage_options',
            'tcg-importer-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'tcg_importer_settings_group', 'tcg_pokemon_api_key' );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>TCG Importer Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'tcg_importer_settings_group' ); ?>
                <?php do_settings_sections( 'tcg_importer_settings_group' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Pokémon TCG API Key</th>
                        <td>
                            <input type="text" name="tcg_pokemon_api_key" value="<?php echo esc_attr( get_option('tcg_pokemon_api_key', '') ); ?>" size="50" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

// Initialize settings page
if ( is_admin() ) {
    new TCG_Importer_Settings();
}
