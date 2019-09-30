(function ($) {

    $(function () {

        /*------------------------------ WPFM Sorting ---------------------------------*/

        var wpfm_fs_dialog, wpfm_fs_form;

        function wpfm_fs_save_sorting() {

            var $bwpfm_fsa_items = jQuery("#bwpfm_fsa_items");

            var product_id = $(this).data('product_id'),
                    bwl_fsa_faq_items_string = "",
                    bwl_fsa_faq_items_array = [],
                    counter = 0,
                    $notification_box = $(".notification_box"),
                    $bwlpfm_fsa_save_btn = $('.ui-dialog-buttonpane').find("button");

            $bwpfm_fsa_items.find("li").each(function () {
                bwl_fsa_faq_items_array[ counter ] = $(this).attr('id');
                counter++;
            })

            bwl_fsa_faq_items_string = bwl_fsa_faq_items_array.join();
            $bwlpfm_fsa_save_btn.attr("disabled", "disabled");
            $('.save_notification').remove();
            $bwlpfm_fsa_save_btn.before('<span class="save_notification">' + wpfm_saving_string + '</span>');
            $.when(save_wpfm_fs_faq_data(product_id, bwl_fsa_faq_items_string)).done(function (response_data) {

                $bwlpfm_fsa_save_btn.removeAttr("disabled");

                if (response_data == 1) {

                    $('.save_notification').html(wpfm_saved_string);
                    $bwpfm_fsa_items.find("li").attr("class", "");
                    $bwpfm_fsa_items.find("li:even").each(function () {
                        $(this).addClass('wpfm_even_row');
                    });

                    $bwpfm_fsa_items.find("li:odd").each(function () {
                        $(this).addClass('wpfm_odd_row');
                    });

                    setTimeout(function () {
                        $('.save_notification').remove();
                    }, 2500);

                }

            })

        }

        wpfm_fs_dialog = $("#dialog-form").dialog({
            autoOpen: false,
            height: 400,
            width: 600,
            modal: true,
            buttons: {
                "Save": wpfm_fs_save_sorting
            },
            open: function () {

                var product_id = $(this).data('product_id'),
                        $notification_box = $(".notification_box"),
                        $bwpfm_fsa_items = $("#bwpfm_fsa_items"),
                        $bwlpfm_fsa_save_btn = $('.ui-dialog-buttonpane').find("button");

                $notification_box.html(wpfm_loading_string);
                $bwpfm_fsa_items.html("");
                $bwlpfm_fsa_save_btn.attr("disabled", "disabled");

                $.when(get_wpfm_fs_get_faq_data(product_id)).done(function (response_data) {

                    if (response_data.length > 0) {

                        $notification_box.html("");
                        $bwpfm_fsa_items.html(response_data);

                        $bwpfm_fsa_items.find("li:even").each(function () {
                            $(this).addClass('wpfm_even_row');
                        });

                        $bwpfm_fsa_items.find("li:odd").each(function () {
                            $(this).addClass('wpfm_odd_row');
                        });

                        bwpfm_fsa_items_sort('#bwpfm_fsa_items', 'bwpfm_fsa_apply_sort');

                        $bwlpfm_fsa_save_btn.removeAttr("disabled");

                    } else {

                        $notification_box.html(wpfm_no_faq_string);

                    }
                });


            },
            close: function () {
                wpfm_fs_dialog.dialog("close");
            }
        });

        wpfm_fs_form = wpfm_fs_dialog.find("form").on("submit", function (event) {
            event.preventDefault();
            wpfm_fs_save_sorting();
        });

        $(".wpfm_sort").on("click", function () {

            wpfm_fs_dialog
                    .data('product_id', $(this).attr('product_id'))  // The important part .data() method     
                    .dialog("open");

            return false;

        });

        function bwpfm_fsa_items_sort(selector, action) {
            var bwl_fsa_faq_items = jQuery(selector);

            bwl_fsa_faq_items.sortable({

                placeholder: "wpfm-sort-highlight",
                update: function (event, ui) {

                }
            });


        }

        function get_wpfm_fs_get_faq_data(product_id) {

            return $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpfm_fs_get_faq_data', // action will be the function name
                    product_id: product_id
                }
            });

        }

        function save_wpfm_fs_faq_data(product_id, bwl_fsa_faq_items_string) {

            return $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bwpfm_fsa_apply_sort',
                    order: bwl_fsa_faq_items_string.toString(),
                    product_id: product_id
                }
            });

        }

    });


})(jQuery); // jQuery Wrapper!