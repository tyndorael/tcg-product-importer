<?php
class TCG_Importer_API {

    public function __construct() {
        // AJAX hook to handle card search
        add_action( 'wp_ajax_tcg_search_cards', array( $this, 'search_cards_callback' ) );
        // AJAX hook to handle image upload from URL
        add_action( 'wp_ajax_tcg_upload_card_image', array( $this, 'upload_card_image_callback' ) );
    }
    
    /**
     * AJAX callback to upload image from URL and set as featured image
     */
    public function upload_card_image_callback() {
        check_ajax_referer( 'tcg_importer_nonce', 'nonce' );

        $image_url = isset( $_POST['image_url'] ) ? esc_url_raw( $_POST['image_url'] ) : '';
        if ( empty( $image_url ) ) {
            wp_send_json_error( 'Image URL not provided.' );
        }

        // Validate file extension
        $allowed_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp' );
        $ext = strtolower( pathinfo( parse_url( $image_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
        if ( ! in_array( $ext, $allowed_extensions ) ) {
            wp_send_json_error( 'Invalid image file type: ' . $ext );
        }

        // Download image to temp location
        $tmp = download_url( $image_url );
        if ( is_wp_error( $tmp ) ) {
            wp_send_json_error( 'Failed to download image: ' . $tmp->get_error_message() );
        }

        // Check file type
        $filetype = wp_check_filetype( basename( $image_url ) );
        if ( empty( $filetype['type'] ) ) {
            @unlink( $tmp );
            wp_send_json_error( 'Downloaded file is not a valid image.' );
        }

        // Get the file name and type
        $file_array = array();
        $file_array['name'] = basename( $image_url );
        $file_array['tmp_name'] = $tmp;

        // Upload to media library
        $attachment_id = media_handle_sideload( $file_array, 0 );
        if ( is_wp_error( $attachment_id ) ) {
            $error_message = $attachment_id->get_error_message();
            @unlink( $tmp );
            wp_send_json_error( 'Failed to upload image: ' . $error_message );
        }

        // Clean up temp file
        @unlink( $tmp );

        wp_send_json_success( array( 'attachment_id' => $attachment_id ) );
    }

    public function search_cards_callback() {
        check_ajax_referer( 'tcg_importer_nonce', 'nonce' );

        $search_term = isset( $_POST['search_term'] ) ? sanitize_text_field( $_POST['search_term'] ) : '';

        if ( empty( $search_term ) ) {
            wp_send_json_error( 'Search term not provided.' );
        }

        // Fetch data from Pokémon TCG and One Piece TCG APIs.
        // For simplicity, we are simulating the API response here.
        $pokemon_data = $this->fetch_from_pokemon_api( $search_term );
        $onepiece_data = $this->fetch_from_onepiece_api( $search_term );

        // Combine the results from both APIs
        $results = array_merge( $pokemon_data, $onepiece_data );

        if ( ! empty( $results ) ) {
            wp_send_json_success( $results );
        } else {
            wp_send_json_error( 'No cards found.' );
        }
    }

    private function fetch_from_pokemon_api( $search_term ) {
        $endpoint = 'https://api.pokemontcg.io/v2/cards?q=name:' . urlencode( $search_term );
        $api_key = get_option( 'tcg_pokemon_api_key', '' );

        $args = array(
            'timeout' => 30
        );
        if ( ! empty( $api_key ) ) {
            $args['headers'] = array(
                'X-Api-Key' => $api_key
            );
        }

        $response = wp_remote_get( $endpoint, $args );

        if ( is_wp_error( $response ) ) {
            return array();
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( empty( $data['data'] ) || !is_array( $data['data'] ) ) {
            return array();
        }

        $results = array();
        foreach ( $data['data'] as $card ) {
            $results[] = array(
                'id'          => isset($card['id']) ? $card['id'] : '',
                'name'        => isset($card['name']) ? $card['name'] : '',
                'image'       => isset($card['images']['small']) ? $card['images']['small'] : '',
                'description' => isset($card['flavorText']) ? $card['flavorText'] : '',
                'set'         => isset($card['set']['name']) ? $card['set']['name'] : '',
                'rarity'      => isset($card['rarity']) ? $card['rarity'] : '',
                'game'        => 'Pokemon TCG'
            );
        }
        return $results;
    }
    
    private function fetch_from_onepiece_api( $search_term ) {
        $endpoint = 'https://apitcg.com/api/one-piece/cards?name=' . urlencode( $search_term );
        $api_key = get_option( 'tcg_onepiece_api_key', '' );

        $args = array(
            'timeout' => 30
        );
        if ( ! empty( $api_key ) ) {
            $args['headers'] = array(
                'x-api-key' => $api_key
            );
        }

        $response = wp_remote_get( $endpoint, $args );

        if ( is_wp_error( $response ) ) {
            return array();
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( empty( $data['data'] ) || !is_array( $data['data'] ) ) {
            return array();
        }

        $results = array();
        foreach ( $data['data'] as $card ) {
            $results[] = array(
                'id'              => isset($card['id']) ? $card['id'] : '',
                'code'            => isset($card['code']) ? $card['code'] : '',
                'rarity'          => isset($card['rarity']) ? $card['rarity'] : '',
                'type'            => isset($card['type']) ? $card['type'] : '',
                'name'            => isset($card['name']) ? $card['name'] : '',
                'image_small'     => isset($card['images']['small']) ? $card['images']['small'] : '',
                'image_large'     => isset($card['images']['large']) ? $card['images']['large'] : '',
                'cost'            => isset($card['cost']) ? $card['cost'] : '',
                'attribute_name'  => isset($card['attribute']['name']) ? $card['attribute']['name'] : '',
                'attribute_image' => isset($card['attribute']['image']) ? $card['attribute']['image'] : '',
                'power'           => isset($card['power']) ? $card['power'] : '',
                'counter'         => isset($card['counter']) ? $card['counter'] : '',
                'color'           => isset($card['color']) ? $card['color'] : '',
                'family'          => isset($card['family']) ? $card['family'] : '',
                'ability'         => isset($card['ability']) ? $card['ability'] : '',
                'trigger'         => isset($card['trigger']) ? $card['trigger'] : '',
                'set_name'        => isset($card['set']['name']) ? $card['set']['name'] : '',
                'notes'           => isset($card['notes']) ? $card['notes'] : array(),
                'game'            => 'One Piece TCG'
            );
        }
        return $results;
    }

    public function handle_image_upload() {
        // ... (Logic to upload the image to the product)
    }
}