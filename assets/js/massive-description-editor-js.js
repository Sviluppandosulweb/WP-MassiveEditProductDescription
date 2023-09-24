jQuery(document).ready(function($) {
    
    // Initialize TinyMCE editors
    const initializeTinyMCE = (selector) => {
        tinymce.init({ selector: selector });
    };

    initializeTinyMCE('#new_description');
    initializeTinyMCE('#banner_content');
    
    // Switch Tabs
    const switchTab = (showId, hideId, thisTab, otherTab) => {
        $(showId).show();
        $(hideId).hide();
        $(thisTab).css('background-color', '#ccc');
        $(otherTab).css('background-color', '#fff');
    };

    $('#descTab').click((e) => {
        e.preventDefault();
        switchTab('#descContent', '#bannerContent', '#descTab', '#bannerTab');
    });

    $('#bannerTab').click((e) => {
        e.preventDefault();
        switchTab('#bannerContent', '#descContent', '#bannerTab', '#descTab');
    });
    
    // Show SweetAlert2 with progress bar
    const showSweetAlert = (title) => {
        Swal.fire({
            title,
            html: '<div class="progress"><div id="dynamic" class="progress-bar progress-bar-success progress-bar-striped active" style="width:0%"></div></div>',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
    };
    
    // Ajax Function Template
    const performAjax = (url, type, data, successMsg, errorMsg) => {
        $.ajax({
            url,
            type,
            data,
            success: function(response) {
                $('#dynamic').css('width', '100%').attr('aria-valuenow', 100);
                Swal.close();
                Swal.fire({ icon: 'success', title: 'Success', text: successMsg });
            },
            error: function() {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
            }
        });
    };

    // Existing AJAX call for updating product descriptions
    $('#massive-description-editor-form').submit(function(e) {
        e.preventDefault();

        const selectedCategory = $('#product_category').val();
        const newDescription = tinymce.get('new_description').getContent();
        const nonce = $('#massive_desc_editor_nonce_field').val();

        showSweetAlert('Updating descriptions...');
        
        const data = {
            action: 'update_massive_product_descriptions',
            massive_desc_editor_nonce_field: nonce,
            category_id: selectedCategory,
            new_description: newDescription
        };

        performAjax(frontendajax.ajaxurl, 'POST', data, 'Descriptions updated successfully.', 'Error updating descriptions.');
    });

    // New AJAX call for adding timed banners
    $('#massive-banner-editor-form-temp').submit(function(e) {
        e.preventDefault();

        const bannerContent = tinymce.get('banner_content').getContent();
        const bannerStart = $('#banner_start').val();
        const bannerExpiry = $('#banner_expiry').val();
        const selectedCategoryBanner = $('#product_category_banner').val();
        const bannerNonce = $('#massive_banner_editor_nonce_field').val();

        showSweetAlert('Adding timed banners...');
        
        const data = {
            action: 'add_massive_timed_banners',
            massive_banner_editor_nonce_field: bannerNonce,
            bannercontent: bannerContent,
            bannerStart,
            bannerExpiry,
            category_id: selectedCategoryBanner,
            is_temp: true
        };

        performAjax(frontendajax.ajaxurl, 'POST', data, 'Timed banners added successfully.', 'Error adding timed banners.');
    });
});
