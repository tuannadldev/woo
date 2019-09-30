jQuery(document).ready(function() {
    
    var accordion_search_container = jQuery(".accordion_search_container"),
          bwl_acc_container = jQuery(".bwl_acc_container"); 
          
    var wpfm_container_theme_id = bwl_acc_container.data("theme-id");


//Set default open/close settings
    bwl_acc_container.find('.acc_container').hide(); //Hide/close all containers
    
    bwl_acc_container.find('.acc_trigger').find(".block").css({
        'opacity': 0
     }).removeClass('fadeInDown');
 
    bwl_acc_container.find('.acc_trigger').click(function() {
        
        
        if(jQuery(this).hasClass(wpfm_container_theme_id)) {
            
            jQuery(this).toggleClass(wpfm_container_theme_id).next().slideUp(function(){
                
                jQuery(this).find(".block").css({
                   'opacity': 0
                }).removeClass('fadeInDown');
                
            });
            
        } 
        
        if (jQuery(this).next().is(':hidden')) { //If immediate next container is closed...
             
            bwl_acc_container.find('.acc_trigger').removeClass(wpfm_container_theme_id).next().slideUp(function(){
                
                jQuery(this).find(".block").css({
                   'opacity': 0
                }).removeClass('fadeInDown');
                
            });
            
            
            jQuery(this).toggleClass(wpfm_container_theme_id).next().slideDown(function(){
                jQuery(this).find(".block").css({
                   'opacity': 1
                }).addClass('animated fadeInDown');
            }); 
            
        }
        
        return false; //Prevent the browser jump to the link anchor
        
    });
    
    
    /*------------------------------ SEARCH SECTION ---------------------------------*/

    accordion_search_container.each(function() {
        
        var filter_timeout,
              remove_filter_timeout,
              search_keywords,
              bwl_acc_live_search = accordion_search_container.find(".accordion_search_input_box"),
              search_result_container = bwl_acc_container.find("#search_result_container");

        bwl_acc_live_search.val("").addClass('search_icon');

        bwl_acc_live_search.on("keyup", function() {

            bwl_acc_live_search = jQuery(this);

            bwl_acc_live_search.addClass('load');
            
            clearTimeout(remove_filter_timeout);

            clearTimeout(filter_timeout);

            search_keywords = jQuery.trim( bwl_acc_live_search.val() );

            if (search_keywords.length < 2) {
                 
                bwl_acc_live_search.removeClass('load');

//                bwl_acc_container.find(".acc_trigger:first").addClass(wpfm_container_theme_id).slideDown();
//                bwl_acc_container.find('.acc_container:first').slideDown(function() {
//                              jQuery(this).find(".block").css({
//                                'opacity': 1
//                             }).addClass('animated fadeInDown');
//                        });
                
//                bwl_acc_container.find(".acc_trigger:not(:first)").removeClass(wpfm_container_theme_id).slideDown();
//                bwl_acc_container.find('.acc_container:not(:first)').slideUp(function() {
//
//                    jQuery(this).find(".block").css({
//                        'opacity': 0
//                    }).removeClass('animated fadeInDown');
//
//                });

                bwl_acc_container.find(".acc_trigger").removeClass(wpfm_container_theme_id).slideDown();
                bwl_acc_container.find('.acc_container').slideUp(function() {

                    jQuery(this).find(".block").css({
                        'opacity': 0
                    }).removeClass('animated fadeInDown');

                });
               
               search_result_container.slideUp();

            }
            
            remove_filter_timeout = ( search_keywords.length < 2 ) && setTimeout(function() {
                        
                removeHighlightEvent();

            }, 0);

            filter_timeout = (search_keywords.length >= 2) && setTimeout(function() {
                
                var item_found_count = 0;
                
                removeHighlightEvent();

                bwl_acc_container.find(".acc_trigger").each(function() {
                    
                    var acc_heading = jQuery(this).find('a');
                    var acc_container = jQuery(this).next(".acc_container").find("*");
                    var search_string = jQuery(this).text() + jQuery(this).next(".acc_container").text();
                    
                    /*------------------------------  Start New Code---------------------------------*/

                        highlightEvent(acc_heading, search_keywords);
                        highlightEvent(acc_container, search_keywords);

                    /*------------------------------End New Code  ---------------------------------*/

                    if (search_string.search(new RegExp(search_keywords, "i")) < 0) {

                        jQuery(this).removeClass(wpfm_container_theme_id).slideUp();
                       
                        jQuery(this).next().removeClass(wpfm_container_theme_id).slideUp(function() {

                            jQuery(this).find(".block").css({
                                'opacity': 0
                            }).removeClass('animated fadeInDown');

                        });

                    } else {

                        jQuery(this).addClass(wpfm_container_theme_id).slideDown();
                        jQuery(this).next().addClass(wpfm_container_theme_id).slideDown(function() {
                              jQuery(this).find(".block").css({
                                'opacity': 1
                             }).addClass('animated fadeInDown');
                        });
                        
                        item_found_count =bwl_acc_container.find("i.highlight").length;
                        
                    }

                });
                
                if ( item_found_count == 0 ) {
                    search_result_container.html( bwpfm_txt_nothing_found ).slideDown();
                } else {
                    search_result_container.html( item_found_count + " " + bwpfm_txt_item_found).slideDown();
                }
                
                bwl_acc_live_search.removeClass('load');
                bwl_acc_live_search.addClass('search_icon');

            }, 400);

        });

    });
    
    
    /*------------------------------  Count Total No of FAQs---------------------------------*/
     
    if( jQuery(".bwpfm_tab_tab").length && typeof(bwpfm_display_faq_counter) != 'undefined' && bwpfm_display_faq_counter==1) {
        
        var $wpfm_faq_counter = jQuery(".wpfm_faq_counter"),
            $wpfm_parent_container = jQuery(".bwl_acc_container"),
            $wpfm_total_faqs = $wpfm_parent_container.find("section").length;
            //Fix the title issue of WooCommerce Version 2.3.13
        
            var $wpfm_tab_title_text = jQuery(".bwpfm_tab_tab").find('a').text();
    
            $wpfm_faq_counter.parent('h2').remove();
    
            if( $wpfm_total_faqs > 0 ) {
                jQuery(".bwpfm_tab_tab").find('a').html($wpfm_tab_title_text+" (" + $wpfm_total_faqs + ")");
            } else {
                $wpfm_faq_counter.parent('h2').remove();
                $wpfm_faq_counter.remove();
            }
        
    }
    
       function highlightEvent(acc_content, search_keywords) {
            
            var self = this;
            var regex = new RegExp(search_keywords, "gi");
            
            // Fixed in 1.0.5 version.
                
            acc_content.highlightRegex(regex, {
                highlight_color: bwpfm_highlight_color,
                highlight_bg: bwpfm_highlight_bg
            });
           
            
        }
        
        function removeHighlightEvent() {
            
            var self = this;
            
            jQuery('i.highlight').each(function(){

                    jQuery(this).replaceWith( jQuery(this).text() );    

            });
            
        }
    

});