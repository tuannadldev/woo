<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$current_tab = isset($_REQUEST['tab']) ? esc_html($_REQUEST['tab']) : 'general';
?>
<div class="wrap devvn_vietnam_shipping_wrap">
	<h1><?php _e('Master Shipping by DevVN','devvn-vnshipping');?></h1>

	<p>Plugin được viết và phát triển bởi <a href="<?php echo DEVVN_AUTHOR_URL;?>" target="_blank" title="Đến web của Toản">Lê Văn Toản</a></p>
    <p>Mọi thắc mắc và góp ý về plugin hãy liên hệ với tôi <a href="http://m.me/levantoan.wp" target="_blank">tại đây</a></p>

    <h2 class="nav-tab-wrapper devvn-nav-tab-wrapper">
        <?php do_action('before_vn_shipping_tabs_nav', $current_tab);?>
        <a href="?page=devvn-vietnam-shipping&tab=general" class="nav-tab <?php echo ($current_tab == 'general') ? 'nav-tab-active' : '' ?>"> <?php _e('General', 'devvn-vnshipping'); ?></a>
        <?php do_action('vn_shipping_tabs_nav', $current_tab);?>
        <!--<a href="?page=devvn-vietnam-shipping&tab=license" class="nav-tab <?php /*echo ($current_tab == 'license') ? 'nav-tab-active' : '' */?>"> <?php /*_e('License', 'devvn-vnshipping'); */?></a>-->
        <?php do_action('after_vn_shipping_tabs_nav', $current_tab);?>
    </h2>

    <?php
    switch ($current_tab) {
        case 'general': include('options-generals.php');
            break;
        case 'license': include('options-license.php');
            break;
        default:
            do_action('vn_shipping_tabs_content_' . $current_tab);
            break;
    }
    ?>
</div>