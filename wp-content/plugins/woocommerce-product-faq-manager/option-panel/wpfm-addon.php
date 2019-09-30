<style type="text/css">
    
    .wpfm-addon-wrap{
        font-family: "verdana", sans-serif, serif;
    }

    .wpfm-addon-wrap ul,
    .wpfm-addon-wrap li,
    .wpfm-addon-wrap p {

        font-size: 13px;

    }
    .wpfm-addon-wrap h2 {

        margin: 0;
        padding: 0;

    }

    .wpfmm_addon_install{
         margin-top: 32px;
        font-size: 12px;
        display: inline-block;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 10px;
        background: #ff003a;
        color: #FFFFFF;
        font-weight: normal;
    }
    
    .wpfmm_addon_installed{
        margin-top: 32px;
        font-size: 12px;
        display: inline-block;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 10px;
        background: #47b642;
        color: #FFFFFF;
        font-weight: normal;
    }
    
    .wpfmm_addon_install a,
    .wpfmm_addon_installed a{
        text-decoration: none;
        color: #FFFFFF;
        display: block;
    }
    
    ul.addon-lists{
        
    }
    
    ul.addon-lists li{
        clear: both;
        line-height: 32px;
       width: 100%;
       float: left;
    }
    
    ul.addon-lists li:nth-child(2n){
        
        background: #EEE;
       /*margin-bottom: 24px;*/
    }
    
    ul.addon-lists li:nth-child(2n+1){
        margin-top: 0px;
        margin-bottom: 0px;
        background: #fafafa;
    }
    
    ul.addon-lists li.lists-heading{
       line-height: 4em;
       font-size: 16px;
       font-weight: bold;
       width: 100%;
       float: left;
       border-bottom: 1px solid #FFF;
       margin-bottom: 0px;
       background: #fafafa;
    }
    
    .addon_id{
        width: 2%;
        float: left;
        text-align: center;
    }
    .addon_title{
        width: 25%;
        float: left;
    }
    .addon_details{
        width: 50%;
        float: left;
    }
    .addon_purchase{
        width: 22%;
        float: left;
        text-align: right;
    }
    
</style>

<div class="wrap wpfm-addon-wrap">
        
    <h2><?php _e('Available Premium Add-ons', 'bwl-wpfm')?></h2>
    
    <ul class="addon-lists">
        
        <li class="lists-heading">
            
            <div class="addon_id">
                #
            </div>
            
            <div class="addon_title">
                Name
            </div>
            
            <div class="addon_details">
                Key Features & Why it's useful ?
            </div>
            
            <div class="addon_purchase">
                Get It Now :)
            </div>
            
        </li>
        
        
        <li>
            
            <div class="addon_id">
                1.
            </div>
            
            <div class="addon_title">
                FAQ Collector - Addon For Product Faq Manager 
            </div>
            
            <div class="addon_details">
                - Responsive searchable accordion.<br />
                - Automatic integration in WooCommerce product details Tab with show/hide option.<br />
                - Easy Drag & drop sorting feature.
            </div>
            
            <div class="addon_purchase">
          
                <?php  if( class_exists( 'BWL_Wpfm_Fc_Addon' ) && class_exists( 'WooCommerce' ) ) { ?>
                    <span class="wpfmm_addon_installed">  
                        <?php _e('Installed', 'bwl-wpfm')?>
                    </span>   
                <?php } else { ?>
                    <span class="wpfmm_addon_install">
                        <a href="http://codecanyon.net/item/faq-collector-addon-for-product-faq-manager/9992576?ref=xenioushk" target="_blank"><?php _e('Install Now', 'bwl-wpfm')?> <strong>($15)</strong></a>
                    </span>
                <?php } ?>
                
                <span class="wpfmm_addon_install">
                    <a href="http://projects.bluewindlab.net/wpplugin/wpfm/product/woo-ninja-2/" target="_blank"><?php _e('Demo', 'bwl-wpfm')?></a>
                </span>
                
            </div>
            
        </li>
        
        
        <!--  end. -->
        
    </ul>
    
</div> 