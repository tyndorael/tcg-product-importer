jQuery(document).ready(function($) {

    var modal = $('#tcg-importer-modal');
    var button = $('#tcg-importer-button');
    var closeButton = $('.close-button');
    var searchButton = $('#tcg-search-button');
    var searchInput = $('#tcg-search-input');
    var searchResults = $('#tcg-search-results');
    var searchPokemonButton = $('#tcg-search-pokemon');
    var searchOnePieceButton = $('#tcg-search-onepiece');

    // Open the modal
    button.on('click', function() {
        modal.show();
    });

    // Close the modal
    closeButton.on('click', function() {
        modal.hide();
    });

    // Close the modal if the user clicks outside of it
    $(window).on('click', function(event) {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
    });

    // Handle the card search
    searchButton.on('click', function(e) {
        e.preventDefault();
        var searchTerm = searchInput.val();
        if (searchTerm.length < 3) {
            alert('Please enter at least 3 characters.');
            return;
        }

        // Make the AJAX call
        $.ajax({
            url: tcg_importer_data.ajax_url,
            type: 'POST',
            data: {
                action: 'tcg_search_cards',
                search_term: searchTerm,
                nonce: tcg_importer_data.nonce
            },
            beforeSend: function() {
                searchResults.html('Searching...');
            },
            success: function(response) {
                if (response.success) {
                    displayResults(response.data);
                } else {
                    searchResults.html('<p>No results found.</p>');
                }
            },
            error: function() {
                searchResults.html('<p>An error occurred while searching. Please try again.</p>');
            }
        });
    });

    // Handle Pokémon card search
    searchPokemonButton.on('click', function(e) {
        console.log('Pokémon search button clicked');
        e.preventDefault();
        var searchTerm = searchInput.val();
        if (searchTerm.length < 3) {
            alert('Please enter at least 3 characters.');
            return;
        }
        $.ajax({
            url: tcg_importer_data.ajax_url,
            type: 'POST',
            data: {
                action: 'tcg_search_cards',
                search_term: searchTerm,
                game: 'pokemon',
                nonce: tcg_importer_data.nonce
            },
            beforeSend: function() {
                searchResults.html('Searching Pokémon...');
            },
            success: function(response) {
                if (response.success) {
                    displayResults(response.data);
                } else {
                    searchResults.html('<p>No results found.</p>');
                }
            },
            error: function() {
                searchResults.html('<p>An error occurred while searching. Please try again.</p>');
            }
        });
    });

    // Handle One Piece card search
    searchOnePieceButton.on('click', function(e) {
        console.log('One Piece search button clicked');
        e.preventDefault();
        var searchTerm = searchInput.val();
        if (searchTerm.length < 3) {
            alert('Please enter at least 3 characters.');
            return;
        }
        $.ajax({
            url: tcg_importer_data.ajax_url,
            type: 'POST',
            data: {
                action: 'tcg_search_cards',
                search_term: searchTerm,
                game: 'onepiece',
                nonce: tcg_importer_data.nonce
            },
            beforeSend: function() {
                searchResults.html('Searching One Piece...');
            },
            success: function(response) {
                if (response.success) {
                    displayResults(response.data);
                } else {
                    searchResults.html('<p>No results found.</p>');
                }
            },
            error: function() {
                searchResults.html('<p>An error occurred while searching. Please try again.</p>');
            }
        });
    });

    // Display the search results
    function displayResults(cards) {
        searchResults.empty();
        if (cards.length > 0) {
            cards.forEach(function(card) {
                var image = card.image_small || card.image || '';
                var name = card.name || '';
                var game = card.game || '';
                var set = card.set_name || card.set || '';
                var rarity = card.rarity || '';
                // Build a rich description for One Piece cards
                var description = '';
                if (card.game === 'One Piece TCG') {
                    description += '<strong>Name:</strong> ' + (card.name || '') + '<br>';
                    description += '<strong>Type:</strong> ' + (card.type || '') + '<br>';
                    description += '<strong>Rarity:</strong> ' + (card.rarity || '') + '<br>';
                    description += '<strong>Set:</strong> ' + (card.set_name || '') + '<br>';
                    description += '<strong>Cost:</strong> ' + (card.cost || '') + '<br>';
                    description += '<strong>Attribute:</strong> ' + (card.attribute_name || '') + '<br>';
                    description += '<strong>Power:</strong> ' + (card.power || '') + '<br>';
                    description += '<strong>Counter:</strong> ' + (card.counter || '') + '<br>';
                    description += '<strong>Color:</strong> ' + (card.color || '') + '<br>';
                    description += '<strong>Family:</strong> ' + (card.family || '') + '<br>';
                    description += '<strong>Ability:</strong> ' + (card.ability || '') + '<br>';
                    description += '<strong>Trigger:</strong> ' + (card.trigger || '') + '<br>';
                } else {
                    description = card.description || '';
                }
                var cardHtml = '<div class="tcg-importer-card-result" '
                    + 'data-card-name="' + name + '" '
                    + 'data-card-image="' + image + '" '
                    + 'data-card-description="' + description.replace(/"/g, '&quot;') + '" '
                    + '>'
                    + '<img src="' + image + '" alt="' + name + '">' 
                    + '<div>'
                    + '<h4>' + name + ' (' + game + ')</h4>'
                    + '<p><strong>Set:</strong> ' + set + '</p>'
                    + '<p><strong>Rarity:</strong> ' + rarity + '</p>'
                    + '</div>'
                    + '</div>';
                searchResults.append(cardHtml);
            });
        } else {
            searchResults.html('<p>No results found.</p>');
        }
    }

    // Handle the card selection
    searchResults.on('click', '.tcg-importer-card-result', function() {
        // Get card data from the clicked element's data attributes
        var cardName = $(this).data('card-name');
        var cardImage = $(this).data('card-image');
        var cardDescription = $(this).data('card-description');

        console.log('name:', cardName);
        console.log('image:', cardImage);
        console.log('description:', cardDescription);

        // Autocomplete the WooCommerce product fields
        $('#title').trigger('focus').val(cardName).trigger('change');
        if (typeof tinymce !== 'undefined' && tinymce.get('content') && !tinymce.get('content').hidden) {
            tinymce.get('content').setContent(cardDescription);
        } else {
            $('#content').val(cardDescription);
        }
        $('#_thumbnail_id').val(''); // Clear the existing featured image
        $('.product-image').html('<img src="' + cardImage + '" />'); // Display the new image

        // Get product ID from the product edit page
        var productId = $('#post_ID').val();

        // AJAX call to upload image from URL and set as featured image
        $.ajax({
            url: tcg_importer_data.ajax_url,
            type: 'POST',
            data: {
                action: 'tcg_upload_card_image',
                image_url: cardImage,
                product_id: productId,
                nonce: tcg_importer_data.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#_thumbnail_id').val(response.data.attachment_id);
                    console.log('Image uploaded and set as featured image for product:', productId);
                } else {
                    alert('Image upload AJAX error: ' + (response.data ? response.data : 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                alert('Image upload AJAX error: ' + error);
            }
        });

        // Close the modal
        modal.hide();
    });
});