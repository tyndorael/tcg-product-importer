jQuery(document).ready(function($) {

    var modal = $('#tcg-importer-modal');
    var button = $('#tcg-importer-button');
    var closeButton = $('.close-button');
    var searchButton = $('#tcg-search-button');
    var searchInput = $('#tcg-search-input');
    var searchResults = $('#tcg-search-results');

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

    // Display the search results
    function displayResults(cards) {
        searchResults.empty();
        if (cards.length > 0) {
            cards.forEach(function(card) {
                var cardHtml = '<div class="tcg-importer-card-result" data-card-name="' + card.name + '" data-card-image="' + card.image + '" data-card-description="' + card.description + '">' +
                                   '<img src="' + card.image + '" alt="' + card.name + '">' +
                                   '<div>' +
                                       '<h4>' + card.name + ' (' + card.game + ')</h4>' +
                                       '<p><strong>Set:</strong> ' + card.set + '</p>' +
                                       '<p><strong>Rarity:</strong> ' + card.rarity + '</p>' +
                                   '</div>' +
                               '</div>';
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
        console.log('Selected card:', cardName, cardImage, cardDescription);

        // Autocomplete the WooCommerce product fields
        $('#title').val(cardName);
        $('#content').val(cardDescription);
        $('#_thumbnail_id').val(''); // Clear the existing featured image
        $('.product-image').html('<img src="' + cardImage + '" />'); // Display the new image

        // Close the modal
        modal.hide();

        // Note: For a real plugin, you'd also need an AJAX call to upload the image
        // to the WordPress Media Library and set it as the product's featured image.
    });
});