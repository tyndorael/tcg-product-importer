<p>Click the button to search for a card and import its data into the product.</p>
<button id="tcg-importer-button" class="button button-primary" type="button">Search & Import Card</button>

<div id="tcg-importer-modal" class="tcg-importer-modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Search for a Card</h2>
        <div class="search-form">
            <input type="text" id="tcg-search-input" placeholder="Card name...">
            <button id="tcg-search-pokemon" class="button button-secondary" type="button">Search Pokémon</button>
            <button id="tcg-search-onepiece" class="button button-secondary" type="button">Search One Piece</button>
        </div>
        <div id="tcg-search-results">
            </div>
    </div>
</div>