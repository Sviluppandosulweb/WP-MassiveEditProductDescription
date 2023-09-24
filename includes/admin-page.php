<?php
// Function to register the admin menu for the Massive Description Editor
function register_massive_description_editor_menu() {
    add_menu_page(
        'Massive Description Editor',
        'Massive Description Editor',
        'manage_options',
        'massive-description-editor',
        'massive_description_editor_page_html'
    );
}

// Function to generate the dropdown list for product categories
function generate_taxonomy_dropdown($taxonomy, $select_id) {
    $args = array(
        'show_option_all'   => '',
        'show_option_none'  => '',
        'option_none_value' => '-1',
        'orderby'           => 'ID',
        'order'             => 'ASC',
        'show_count'        => 0,
        'hide_empty'        => 0,
        'child_of'          => 0,
        'exclude'           => '',
        'include'           => '',
        'echo'              => 1,
        'selected'          => 0,
        'hierarchical'      => 1,
        'name'              => 'product_category',
        'id'                => $select_id,
        'class'             => 'postform',
        'depth'             => 0,
        'tab_index'         => 0,
        'taxonomy'          => $taxonomy,
        'hide_if_empty'     => false,
    );

    wp_dropdown_categories($args);
}


// Function to render the HTML for the admin page
function massive_description_editor_page_html() {
    // Initialize WordPress editor
    wp_enqueue_editor();

    ob_start(); // Inizia la cattura dell'output

    ?>
    <div class="wrap">
        <h1>Massive Description Editor</h1>

        <div id="tabs" style="margin-bottom: 20px;">
            <a href="#" id="descTab" style="margin-right: 20px; font-weight: bold;">Sovrascrivi Descrizioni</a>
            <a href="#" id="bannerTab" style="font-weight: bold;">Aggiungi Banner Temporizzati</a>
        </div>

        <div id="descContent">
            <form id="massive-description-editor-form">
                <input type="hidden" id="massive_desc_editor_nonce_field" name="massive_desc_editor_nonce_field" value="<?php echo wp_create_nonce('massive_desc_editor_nonce'); ?>">
                <div>
                    <label for="product_category">Select Product Category:</label>
                    <?php generate_taxonomy_dropdown('product_cat', 'product_category'); ?>
                </div>
                <div>
                    <label for="new_description">New Description:</label>
                    <?php wp_editor('', 'new_description', array('textarea_name' => 'new_description')); ?>
                </div>
                <button type="submit">Update Descriptions</button>
            </form>
        </div>

        <div id="bannerContent" style="display:none;">
            <form id="massive-banner-editor-form-temp">
                <input type="hidden" id="massive_banner_editor_nonce_field" name="massive_banner_editor_nonce_field" value="<?php echo wp_create_nonce('massive_banner_editor_nonce'); ?>">
                <div>
                    <label for="product_category_banner">Select Product Category:</label>
                    <?php generate_taxonomy_dropdown('product_cat', 'product_category_banner'); ?>
                </div>
                <div>
                    <label for="banner_content">Contenuto del Banner:</label>
                    <?php wp_editor('', 'banner_content', array('textarea_name' => 'banner_content')); ?>
                </div>
                <div>
                    <label for="banner_start">Inizio del Banner:</label>
                    <input type="date" name="banner_start" id="banner_start">
                </div>
                <div>
                    <label for="banner_expiry">Scadenza del Banner:</label>
                    <input type="date" name="banner_expiry" id="banner_expiry">
                </div>
                <button type="submit">Aggiungi Banner</button>
            </form>
        </div>
    </div>
    <?php

    $output = ob_get_clean(); // Termina la cattura dell'output
    echo $output; // Stampa tutto l'output catturato
}

// Hook to register the admin menu
add_action('admin_menu', 'register_massive_description_editor_menu');