<?php

  //include the main class file
  if ( ! class_exists( 'BF_Admin_Page_Class') ) :
    
    require_once("admin-page-class/admin-page-class.php");

 endif;
  
 /*------------------------------ Custom Fields ---------------------------------*/
 
 if ( !function_exists('addHiddenText') ) :
 
  function addHiddenText($id,$args,$repeater=false){
    $new_field = array('type' => 'hidden','id'=> $id,'std' => '','desc' => '','style' =>'','name' => '');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
 
  endif;
  
  /**
   * configure your admin page
   */
  $config = array(    
    'menu'           => 'bwl-woo-faq-manager',             //sub page to settings page
    'page_title'     => __('Option Panel','bwl-wpfm'),       //The name of this page 
    'capability'     => 'activate_plugins',         // The capability needed to view the page 
    'option_group'   => 'bwpfm_options',       //the name of the option to create in the database [do change]
    'id'             => 'bwpfm_admin_page',            // meta box id, unique per page[do change]
    'fields'         => array(),            // list of fields (can be added by field arrays)
    'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );  
  
  /**
   * instantiate your admin page
   */
  $options_panel = new BF_Admin_Page_Class($config);
  $options_panel->OpenTabs_container('');
  
  /**
   * define your admin page tabs listing
   */
  
  $wpfm_option_panel_links = array(
    'links' => array(
      'general_options' =>  __('General Options','bwl-wpfm'),
      'option_custom_theme' =>  __('Custom Themes','bwl-wpfm'),
      'options_advance' =>  __('Advance','bwl-wpfm')
    )
  );
  
  
  /*------------------------------  Start Custom Block For Form Collector Addon --------------------------------*/
  
  if( class_exists('BWL_Wpfm_Fc_Addon') ) {
      
      $wpfm_option_panel_links['links']['faq_form'] = __('FAQ Form Settings', 'bwl-wpfm');
      
  }
  
  $options_panel->TabsListing($wpfm_option_panel_links);
  
  /*------------------------------  End Custom Block For Form Collector Addon --------------------------------*/
  
  
  /**
   * Open admin page first tab
   */
  $options_panel->OpenTab('general_options');

  /**
   * Add fields to your admin page first tab
   * 
   * Simple options:
   * input text, checbox, select, radio 
   * textarea
   */
  //title
  $options_panel->Title(__("General Options","bwl-wpfm"));
  
  //checkbox field
  $options_panel->addCheckbox('bwpfm_load_font_awesome',array('name'=> __('Load Font Awesome?','bwl-wpfm'), 'std' => true, 'desc' => __('If your theme already loaded font-awesome.css, then turn it off.','bwl-wpfm')));
  
  //Text field
  $options_panel->addText('bwpfm_faq_tab_title',array('name'=> __('FAQ Tab Title','bwl-wpfm'), 'std' => __( "FAQ ", 'bwl-wpfm'), 'desc' => __('You can change tab title in here.','bwl-wpfm')));
  
  //checkbox field
  $options_panel->addCheckbox('bwpfm_display_faq_counter',array('name'=> __('Display FAQ Counter?','bwl-wpfm'), 'std' => true, 'desc' => __('If you want to display faq counters in tab title then turn it on.','bwl-wpfm')));

  //Text field
  $options_panel->addText('bwpfm_faq_tab_position',array('name'=> __('FAQ Tab Position','bwl-wpfm'), 'std' => 100, 'desc' => __('Set number like- 1,2,3. Use big number to display FAQ tab last of tab.','bwl-wpfm')));

  //Like Custom Icon.
  $wpfm_search_box_conditinal_fields[] = $options_panel->addText('wpfm_min_faqs_for_sbox',array('name'=> __('Minimum FAQ Items?','bwl-kb'), 'std' => '', 'desc' => 'Set a number of minimum FAQ items to display search box. Leave it blank if you want to hide search box.'), true);
  
 
  //conditinal block
  $options_panel->addCondition('wpfm_search_box_conditinal_fields',
      array(
        'name' => __('Hide Search box? ','bwl-kb'),
        'desc' => '',
        'fields' => $wpfm_search_box_conditinal_fields,
        'std' => false
      ));
  
  /**
   * Close first tab
   */   
  $options_panel->CloseTab();
  
  /**
   * Open admin page 1th tab
   */
  $options_panel->OpenTab('option_custom_theme');
  //title
  $options_panel->Title(__("Custom Themes","bwl-wpfm"));
  
        // Default Theme.
        $bwpfm_data = get_option('bwpfm_options');
        
      $plan_themes_array = array(
          '' => 'Select',
          'red_theme' => 'Red Theme',
          'blue_theme' => 'Blue Theme',
          'green_theme' => 'Green Theme',
          'orange_theme' => 'Orange Theme',
          'yellow_theme' => 'Yellow Theme'
      );

      

      if (isset($bwpfm_data['bwpfm_custom_themes'])) {

          $bwpfm_custom_themes = $bwpfm_data['bwpfm_custom_themes'];
      } else {

          $bwpfm_custom_themes = array(); // initialize.
      }

      if (sizeof($bwpfm_custom_themes) > 0) {
          foreach ($bwpfm_custom_themes as $themes_info) {
              $bptm_theme_title = trim($themes_info['bwpfm_theme_title']);
              $bptm_theme_unique_title = trim($themes_info['bwpfm_theme_unique_title']);
              $plan_themes_array[$bptm_theme_unique_title] = ucwords(( $bptm_theme_title == "" ) ? "untitled" : $bptm_theme_title );
          }
      }


    $options_panel->addSelect('bwpfm_default_theme', $plan_themes_array,
                                                                            array('name'=> __('Set Default Theme:','bwl-wpfm'), 
                                                                            'std'=> array('')
                                                                        ));
  
  //An optionl descrption paragraph
  $options_panel->addParagraph(__("Click Plus(+) button to create new custom theme.","bwl-wpfm"));

  /*
   * To Create a reapeater Block first create an array of fields
   * use the same functions as above but add true as a last param
   */
  $repeater_fields[] = $options_panel->addText('bwpfm_theme_title',array('name'     => __('Theme Title','bwl-wpfm')),true);
  $repeater_fields[] = addHiddenText('bwpfm_theme_unique_title',array('name'     => __('Theme Unique Name','bwl-wpfm'), 'std'=> "bwpfm_ct_". wp_rand()),true);
  
  $repeater_fields[] = $options_panel->addColor('bwpfm_acc_heading_bg',array('name'=> __('Heading BG ','bwl-wpfm'), 'std'=> '#2C2C2C'),true);
  $repeater_fields[] = $options_panel->addColor('bwpfm_acc_heading_color',array('name'=> __('Heading Text ','bwl-wpfm'), 'std'=> '#FFFFFF'),true);
  $repeater_fields[] = $options_panel->addColor('bwpfm_acc_heading_active_bg',array('name'=> __('Heading Active BG ','bwl-wpfm'), 'std'=> '#006666'),true);
  $repeater_fields[] = $options_panel->addColor('bwpfm_acc_heading_active_color',array('name'=> __('Heading Active Text ','bwl-wpfm'), 'std'=> '#F0F0F0'),true);
  
  $repeater_fields[] = $options_panel->addColor('bwpfm_acc_content_bg',array('name'=> __('Content BG ','bwl-wpfm'), 'std'=> '#F1F1F1'),true);
  $repeater_fields[] = $options_panel->addColor('bwpfm_acc_content_color',array('name'=> __('Content Text ','bwl-wpfm'), 'std'=> '#2C2C2C'),true);
  $repeater_fields[] = $options_panel->addColor('bwpfm_highlight_bg',array('name'=> __('Search Highlights BG','bwl-wpfm'), 'std'=> '#FEF5CB'),true);
  $repeater_fields[] = $options_panel->addColor('bwpfm_highlight_color',array('name'=> __('Search Highlights Text ','bwl-wpfm'), 'std'=> '#2C2C2C'),true);
  
  /*
   * Then just add the fields to the repeater block
   */
  //repeater block
  $options_panel->addRepeaterBlock('bwpfm_custom_themes',array('sortable' => true, 
                                                                    'inline' => true, 
                                                                    'name' => __('Custom Theme Block','bwl-wpfm'),
                                                                    'fields' => $repeater_fields, 'desc' => __('You can create unlimited number of custom theme for FAQ accordion.','bwl-wpfm')));
  

  $options_panel->CloseTab();
  
  
  $options_panel->OpenTab('options_advance');
  
  //title
 
  $options_panel->Title(__("Advance Setting",'bwl-wpfm'));

  //Auto Update Notification
  $options_panel->addCheckbox('bwpfm_auto_faq_status',array('name'=> __('Enable Auto FAQ Integration','bwl-wpfm'), 'std' => FALSE, 'desc' => __('Enable this option will allows you to automatically include global FAQ in to every WooCommerce Product.', 'bwl-wpfm')));

  // Custom CSS Panel
  $options_panel->addCode('bwpfm_custom_css',array('name'=> __('Custom CSS ','bwl-wpfm'), 'syntax' => 'css', 'desc' => __('You can write custom css code in here.','bwl-wpfm')));
  
  $options_panel->CloseTab();
  
  /*------------------------------  Start Custom Block For Form Collector Addon --------------------------------*/
  
 if( class_exists( 'BWL_Wpfm_Fc_Addon' ) && class_exists( 'WooCommerce' ) ) {
      
 /**
   * Open admin page first tab
   */
  $options_panel->OpenTab('faq_form');

  /**
   * Add fields to your admin page first tab
   * 
   * Simple options:
   * input text, checbox, select, radio 
   * textarea
   */
  //title
  $options_panel->Title(__("Ask A Question Settings","bwl-wpfm"));
  
  //Text field
  $options_panel->addText('bwpfm_ask_tab_title',array('name'=> __('Ask Question Tab Title','bwl-wpfm'), 'std' => __( "Ask A Question", 'bwl-wpfm'), 'desc' => __('You can change tab title in here.','bwl-wpfm')));
  
  //Ask A Question Tab Position.
  $options_panel->addText('bwpfm_ask_tab_position',array('name'=> __('Ask Tab Position','bwl-wpfm'), 'std' => 101, 'desc' => __('Set any number like: 1,2,3. Use large number to display FAQ Ask TAB at last position and use zero to display at first position in tab menu.','bwl-wpfm')));
  
  //FAQ Question Title Minimum Length
  $options_panel->addText('bwpfm_title_min_length',array('name'=> __('FAQ Question Minimum Length','bwl-wpfm'), 'std' => 3, 'desc' => __('Default: Minimum length 3 characters.','bwl-wpfm')));
  
  //FAQ Question Title Maximum Length
  $options_panel->addText('bwpfm_title_max_length',array('name'=> __('FAQ Question Maximum Length','bwl-wpfm'), 'std' => 100, 'desc' => __('Default: Maximum length 100 characters.','bwl-wpfm')));
  
  //checkbox field
  $options_panel->addCheckbox('bwpfm_captcha_status',array('name'=> __('Disable Captcha?','bwl-wpfm'), 'std' => false, 'desc' => __('Trun it on, if you want to hide captcha section in FAQ form.','bwl-wpfm')));
  
  //Login field
  $options_panel->addCheckbox('bwpfm_login_status',array('name'=> __('Enable Login?','bwl-wpfm'), 'std' => false, 'desc' => ''));
  
  //Login field
  $options_panel->addCheckbox('bwpfm_email_status',array('name'=> __('Disable Email Notification?','bwl-wpfm'), 'std' => false, 'desc' => ''));
  
  //Text field
  $options_panel->addText('bwpfm_admin_email',array('name'=> __('Notification Email','bwl-wpfm'), 'std' => get_bloginfo( 'admin_email' ), 'desc' => __('Set custom email address to get new FAQ submit notification email.','bwl-wpfm')));

  //FAQ Question Form Extra Class.
  $options_panel->addText('fca_container_extra_class',array('name'=> __('FAQ Question Form Extra Class','bwl-wpfm'), 'std' => '', 'desc' => __('You can set extra class for FAQ Question Form.','bwl-wpfm')));
  
  /**
   * Close first tab
   */   
  $options_panel->CloseTab();
      
  }