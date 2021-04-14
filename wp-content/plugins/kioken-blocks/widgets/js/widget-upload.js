jQuery(document).ready(function ($) {
    "use strict";

    var body = $('body');

    var upl_btn = $('.btn_remove');
    upl_btn.each(function() {
      if ($(this).siblings('.image_src').val()) {
        $(this).show();
      }
    });

    /**
     * Init Media Manager
     */
    body.on('click', '.image_upl', function (e) {
        e.preventDefault();

        var button = $(this);

        // Create new media manager instance
        var frame = wp.media.frames.file_frame = wp.media({
            title: translation.select_image,
            button: {
                text: translation.use_selected_image
            },
            library: {
                type: 'image'
            },
            multiple: false
        });

        // Run callback when image selected
        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();

            button.siblings('.image_src').val(attachment.id).trigger("change");
            button.siblings('.kt_img_preview').attr('src', attachment.url);

            button.siblings('.btn_remove').show();
        });

        // Open media manager
        frame.open();
    });

    /**
     * Remove selected image
     */

    body.on('click', '.btn_remove', function (e) {
        e.preventDefault();

        var button = $(this);

        button.siblings('.image_src').val('').trigger('change');
        button.siblings('.kt_img_preview').attr('src', '');

        button.hide();
    });

});