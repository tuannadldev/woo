(function ($) {

    $(function () {
        
        if ( $(".wpfm_cmb_repeat_field_container").length > 0 ) {
        
            // Implementing dialog Box Idea in here. 

            function wpfm_update_counter() {

                var $wpfm_counter_field = $("#wpfm_row_string"),
                      wpfm_row_string = "";
              
                $wpfm_counter_field.val("");

                $('ul.wpfm_cmb_repeat_field_container').find('li').each(function(){

                    var $row_count = $(this).data('row_count');
                           wpfm_row_string+=$row_count+','
                });

                $wpfm_counter_field.val(wpfm_row_string);

            }

            wpfm_update_counter();

            var wpfm_faq_settings_dialog;
            var $wpfm_faq_settings_form = $('.wpfm-faq-form-container') ; 
            var $wpfm_cmb_repeat_field_container = $(".wpfm_cmb_repeat_field_container");


            function wpfm_set_editor_value( editor_id, set_value ) {

                    if( typeof (set_value) == 'undefined' ) {

                        set_value = "";

                    }
    //                alert(" "+editor_id);
    //                alert(" "+set_value);
                    var $wpfm_editor_content;

                    var editor = tinyMCE.get(editor_id);

                    if (editor) {
                        // Ok, the active tab is Visual
                        $wpfm_editor_content = editor.setContent(set_value);
                    } else {
                        // The active tab is HTML, so just query the textarea
                        $wpfm_editor_content = $('#'+editor_id).val(set_value);
                    }

            }

            function wpfm_get_editor_value( editor_id ) {

                 var $wpfm_editor_content;
                    var editor = tinyMCE.get(editor_id);
                    if (editor) {
                        // Ok, the active tab is Visual
                        $wpfm_editor_content = editor.getContent();
                    } else {
                        // The active tab is HTML, so just query the textarea
                        $wpfm_editor_content = $('#'+editor_id).val();
                    }
                    return $wpfm_editor_content;

            }

            function get_wpfm_faq_row_html( $wpfm_row_id ) { 

                var $count_val;

                if ( typeof ($wpfm_row_id) != 'undefined' && $wpfm_row_id !="" ) {

                    $count_val = $wpfm_row_id;

                } else {

                    var $next_row_id = 0;

                    if ( $wpfm_cmb_repeat_field_container.find("li.custom_wpfm_faq_rows").length > 0 ) {

                        $next_row_id = get_new_row_id() + parseInt(1);

                    }

                     $count_val = $next_row_id;   

                }

                 var $add_new_row = $("#add_new_row");
                 var $wpfm_cmb_data_set = $("#wpfm_cmb_data_set");


                 // Get Value From the Form Fields.


               var $val_wpfm_faq_type = $wpfm_faq_settings_form.find('.wpfm_faq_type:checked').val(),
                        $val_wpfm_faq_title= $wpfm_faq_settings_form.find('.wpfm_faq_title').val(),
                        $val_wpfm_faq_details= wpfm_get_editor_value('faq_desc'),
                        $val_wpfm_global_faq_id= $wpfm_faq_settings_form.find('.wpfm_global_faq_id').val();

    //             alert(" "+$val_wpfm_faq_details);

                // Get Translated Texts.
                var $text_wpfm_faq_type = $add_new_row.data('text_wpfm_faq_type'),
                      $text_wpfm_faq_title = $add_new_row.data('text_wpfm_faq_title'),
                      $text_wpfm_faq_details = $add_new_row.data('text_wpfm_faq_details'),
                      $text_wpfm_new_faq = $add_new_row.data('text_wpfm_new_faq'),
                      $text_wpfm_ready_made_faq = $add_new_row.data('text_wpfm_ready_made_faq'),
                      $text_wpfm_select_faq = $add_new_row.data('text_wpfm_select_faq');


                var $repeat_row ='',
                      $select_options = "",
                      $selected_global_faq,
                      $selected_global_faq_heading="";

                    var $parse_default_value = $.parseJSON($wpfm_cmb_data_set.val());

                        $.each($parse_default_value, function(index, element) {

                                if( $val_wpfm_global_faq_id == index ) {
                                    $selected_global_faq="selected=selected";
                                    $selected_global_faq_heading = element;
                                } else {
                                    $selected_global_faq=""
                                }

                                 $select_options += '<option value="'+index+'" '+$selected_global_faq+'>'+element+'</option>';

                        });

                // Check box.
                var $display_wpfm_faq_title;
                var $wpfm_new_faq_checked = "",
                      $wpfm_global_faq_checked = "";

                if ( $val_wpfm_faq_type == 1 ) {
                    $wpfm_global_faq_checked += "checked=checked";
                    $display_wpfm_faq_title = $selected_global_faq_heading;
                } else {
                    $wpfm_new_faq_checked += "checked=checked";
                    $display_wpfm_faq_title = $val_wpfm_faq_title;
                }

    //'<p class="cmb_repeat_row cont_wpfm_faq_details"><label>' + $text_wpfm_faq_details + ' :</label><input class="widefat" type="text" value="" id="faq_desc_'+$count_val+'" name="faq_desc_'+$count_val+'"></p>'+

                    $repeat_row+= '<li class="wpfm_cmb_repeat_row custom_wpfm_faq_rows wpfm_row_id_'+$count_val+'" data-row_count="'+$count_val+'">'+
                                                    '<p class="cmb_wpfm_title_container"><span class="cmb_wpfm_post_title">' + $display_wpfm_faq_title + '</span><span class="cmb_wpfm_ctrl_btns"><a class="wpfm_edit" title="Edit" href="#">E</a><a class="wpfm_copy" title="Copy" href="#">C</a><a class="wpfm_delete" title="Delete" href="#">X</a></span></p>'+
                                                    '<p class="cmb_repeat_row cont_wpfm_faq_type"><label>' + $text_wpfm_faq_type + ' : </label><input type="radio" ' + $wpfm_new_faq_checked + ' value="0" name="wpfm_faq_type['+$count_val+']">' + $text_wpfm_new_faq+ ' <input type="radio"  ' + $wpfm_global_faq_checked + ' value="1" name="wpfm_faq_type['+$count_val+']"> ' + $text_wpfm_ready_made_faq + '</p>'+
                                                    '<p class="cmb_repeat_row cont_wpfm_faq_title"><label>' + $text_wpfm_faq_title + ' :</label><input class="widefat" type="text" value="' + $val_wpfm_faq_title + '" name="faq_title['+$count_val+']"></p>'+

                                                    '<p class="cmb_repeat_row cont_wpfm_faq_details"><label>' + $text_wpfm_faq_details + ' :</label><textarea class="widefat" id="faq_desc_'+$count_val+'" name="faq_desc_'+$count_val+'">' + $val_wpfm_faq_details + '</textarea></p>'+
                                                    '<p class="cmb_repeat_row cont_wpfm_global_faqs"><label>' + $text_wpfm_ready_made_faq + ' :</label>'+
                                                    '<select name="wpfm_global_faq_id['+$count_val+']">'+
                                                    '<option value="" selected="selected">- ' + $text_wpfm_select_faq + ' -</option>'+
                                                    $select_options+
                                                    '</select></p>'+
                                                '</li>';



                return $repeat_row;


            }

            function wpfm_add_faq_settings() {

              var $this = $('.wpfm-faq-form-container') ; // store form container ID.

              var $wpfm_row_id = $('#wpfm_row_id').val();
//              var $wpfm_row_id = get_new_row_id() + parseInt(1);

              var $wpfm_faq_row_html = get_wpfm_faq_row_html( $wpfm_row_id );

    //          console.log($wpfm_faq_row_html);

              if( $wpfm_row_id !="" ) {

                    $( "li.wpfm_row_id_"+$wpfm_row_id ).replaceWith( $wpfm_faq_row_html );
                    $( "li.wpfm_row_id_"+$wpfm_row_id ).addClass('wpfm_highlight');

                   setTimeout(function(){

                        $( "li.wpfm_row_id_"+$wpfm_row_id ).removeClass('wpfm_highlight');

                   },1500)

              } else if ( $wpfm_cmb_repeat_field_container.find('li').length> 0 ) {

                  $wpfm_cmb_repeat_field_container.find('li:last').after($wpfm_faq_row_html);

                  var $wpfm_new_field = $wpfm_cmb_repeat_field_container.find('li:last');

                        $wpfm_new_field.addClass('wpfm_highlight');

                   setTimeout(function(){

                        $wpfm_new_field.removeClass('wpfm_highlight');

                   },1500)

              } else {

                  $wpfm_cmb_repeat_field_container.html($wpfm_faq_row_html);

              }


              wpfm_faq_settings_dialog.dialog("close");

              wpfm_update_counter();

            }


            wpfm_faq_settings_dialog = $("#wpfm-dialog-form").dialog({
                autoOpen: false,
                height: 500,
                width: 1024,
                modal: true,
                buttons: {
                    "Save": wpfm_add_faq_settings
                },
                open: function() {

                   $('#wpfm_row_id').val("");

                    var row_id = $(this).data('row_id');
    //                alert(" "+row_id);
                    var $this = $('.wpfm-faq-form-container') ; // store form container ID.
    //                alert(" "+$this.length);
                    // Findout all form items container.
                    var $modal_cont_wpfm_faq_type = $this.find('.cont_wpfm_faq_type'),
                            $modal_cont_wpfm_faq_title= $this.find('.cont_wpfm_faq_title'),
                            $modal_cont_wpfm_faq_details= $this.find('.cont_wpfm_faq_details'),
                            $modal_cont_wpfm_faq_details_editor= $this.find('#wp-faq_desc-wrap'),
                            $modal_cont_wpfm_global_faqs= $this.find('.cont_wpfm_global_faqs');

    //                alert(" "+$modal_cont_wpfm_faq_type.length);
                    // Seperate New & Global FAQ Container.

                    var $modal_new_faq_fields = $([]).add($modal_cont_wpfm_faq_title).add($modal_cont_wpfm_faq_details).add($modal_cont_wpfm_faq_details_editor);
                    var $modal_global_faq_fields = $([]).add($modal_cont_wpfm_global_faqs);

                    // Now it's time to Findout all the fields.

                    var $field_wpfm_faq_type = $modal_cont_wpfm_faq_type.find('.wpfm_faq_type'),
                        $field_wpfm_faq_title= $modal_cont_wpfm_faq_title.find('.wpfm_faq_title'),
                        $field_wpfm_faq_details= $this.find('#faq_desc'),
                        $field_wpfm_global_faq_id= $modal_cont_wpfm_global_faqs.find('.wpfm_global_faq_id');

                    // Reset all the field values when open modal window for the first time.    

                    var $all_modal_fields = $([]).add($field_wpfm_faq_title).add($field_wpfm_faq_details).add($field_wpfm_global_faq_id);
                    //Clearing All the field values.
                        $all_modal_fields.val("");

                    if ( row_id < 0 ) {

                        // Added FAQ Item.

                        
                        // Set Editor Value.
                        // Make editor value blank (Add FAQ section)
                        wpfm_set_editor_value( "faq_desc", '');

                        // Set default value 0 for faq type.
                        $modal_cont_wpfm_faq_type.find('.wpfm_faq_type[value=0]').prop( "checked", true );


                        // Now show new faq fields.
                        $modal_cont_wpfm_faq_type.show();
                        $modal_new_faq_fields.show();
                        $modal_global_faq_fields.hide();

                    } else {

                        $('#wpfm_row_id').val(row_id);

                        // Edit existing FAQ Item.b
                        // Set old values in to form field.
                        $modal_cont_wpfm_faq_type.show();
                        var $wpfm_row_parent = $(".wpfm_row_id_"+row_id);
    //                    console.log($wpfm_row_parent.find("input[type=radio]:checked").val());

                        var $wpfm_faq_title = $wpfm_row_parent.find('input[type=text]').val();
//                        console.log($wpfm_faq_title);
                        var $faq_desc = $('#faq_desc_'+row_id).val();
//                        console.log($faq_desc);
                        var $wpfm_global_faq_id = $wpfm_row_parent.find('select').val();
//                        console.log($wpfm_global_faq_id);


                        if ( $wpfm_row_parent.find("input[type=radio]:checked").val() == 1 ) {

                            $modal_cont_wpfm_faq_type.find('.wpfm_faq_type[value=1]').prop( "checked", true );
                            $field_wpfm_global_faq_id.val($wpfm_global_faq_id);
                            $modal_new_faq_fields.hide();
                            $modal_global_faq_fields.show();

                        } else {
                            $modal_cont_wpfm_faq_type.find('.wpfm_faq_type[value=0]').prop( "checked", true );
                            $field_wpfm_faq_title.val($wpfm_faq_title);
                            wpfm_set_editor_value( "faq_desc", $faq_desc);
                            $modal_new_faq_fields.show();
                            $modal_global_faq_fields.hide();
                        }




                    }


                    // Show/Hide Event.

                    $field_wpfm_faq_type.on('click', function(){

                        if ( $modal_cont_wpfm_faq_type.find('.wpfm_faq_type:checked').val() == 1 ) {

                            // Hide New FAQ Fields.
                            // Show Global FAQ Field.
                            $modal_new_faq_fields.hide();
                            $modal_global_faq_fields.show();

                        } else {

                            // Hide New FAQ Fields.
                            // Show Global FAQ Field.
                            $modal_new_faq_fields.show();
                            $modal_global_faq_fields.hide();

                        }

                    });

                },
                close: function() {
                    wpfm_faq_settings_dialog.dialog("close");
                }
            });

            $("#add_new_row").on("click", function() {

                wpfm_faq_settings_dialog
                        .data('row_id', '')  // The important part .data() method     
                        .dialog("open");

                return false;

            });

            // Edit Rows.

             $(document).on('click', '.wpfm_edit', function () {

                 var $row_id = $(this).parents('.custom_wpfm_faq_rows').data('row_count');

                    wpfm_faq_settings_dialog
                            .data('row_id',  $row_id )  // The important part .data() method     
                            .dialog("open");

                return false;
            });

            // Clone Rows.

            function get_new_row_id() {


                var new_row_id=0;

                $wpfm_cmb_repeat_field_container.find("li.custom_wpfm_faq_rows").each(function(){

                    if ( $(this).data('row_count') > new_row_id ) {
                        new_row_id =  $(this).data('row_count');
                    }

                });

                return new_row_id;


            }

             $(document).on('click', '.wpfm_copy', function () {


                 var $new_row_id = get_new_row_id() + parseInt(1);

                 var $parent_row = $(this).parents('.custom_wpfm_faq_rows');

                 var $new_row_content = $parent_row.clone();

    //             alert($new_row_id+" "+$new_row_content.html().replace( '['+$parent_row_id+']','['+$new_row_id+']'));
    //        var $new_parent_class = "wpfm_cmb_repeat_row custom_wpfm_faq_rows wpfm_row_id_0".replace();


                $new_row_content.attr('class', 'wpfm_cmb_repeat_row custom_wpfm_faq_rows wpfm_row_id_'+$new_row_id).attr('data-row_count', $new_row_id).addClass('wpfm_dn');
                $new_row_content.find('input[type=radio]').attr('name','wpfm_faq_type['+$new_row_id+']');
                $new_row_content.find('input[type=text]').attr('name','faq_title['+$new_row_id+']');
                $new_row_content.find('textarea').attr('id','faq_desc_'+$new_row_id).attr('name','faq_desc_'+$new_row_id);
                $new_row_content.find('select').attr('name','wpfm_global_faq_id['+$new_row_id+']');
                $parent_row.after($new_row_content);

                // Add highlighting Animation.

                 $('.wpfm_row_id_'+$new_row_id).addClass('wpfm_highlight').fadeIn(1000, function () {

                        $(this).removeClass('wpfm_highlight');

                });

    //                 alert(" "+$row_id.html());
                wpfm_update_counter();
                return false;
            });


            // Remove Rows.

             $(document).on('click', '.wpfm_delete', function (e) {

                $(this).parents('.custom_wpfm_faq_rows').addClass('wpfm_cmb_row_deleted').fadeOut(500, function () {
                    $(this).remove();
                    wpfm_update_counter();
                });

                e.preventDefault();
            });

            // Sortable Things.

            $(".wpfm_cmb_repeat_field_container").sortable({
                placeholder: "bwl-cmb-sort-highlight",
                update: function(event, ui){
                    setTimeout(function(){
                        wpfm_update_counter();
                    },0);
                }
            });
        
        } // End of checking.
        
    });
   

})(jQuery);