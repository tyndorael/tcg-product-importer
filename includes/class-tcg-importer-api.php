<?php
class TCG_Importer_API {

    public function __construct() {
        // AJAX hook to handle card search
        add_action( 'wp_ajax_tcg_search_cards', array( $this, 'search_cards_callback' ) );
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