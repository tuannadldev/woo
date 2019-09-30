<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$current_section = isset($_REQUEST['section']) ? esc_html($_REQUEST['section']) : 'general';
?>
<div class="wrap">

    <ul class="subsubsub">
        <li>
            <a href="?page=devvn-vietnam-shipping&tab=viettelpost&section=general" class="<?php echo ($current_section == 'general') ? 'current' : '' ?>"> <?php _e('Login', 'devvn-vnshipping'); ?></a> |
        </li>
        <?php if($this->is_vtpost_login()):?>
        <li>
            <a href="?page=devvn-vietnam-shipping&tab=viettelpost&section=hubs" class="<?php echo ($current_section == 'hubs') ? 'current' : '' ?>"> <?php _e('Quản lý kho', 'devvn-vnshipping'); ?></a>
        </li>
        <!--<li>
            <a href="?page=devvn-vietnam-shipping&tab=viettelpost&section=webhook" class="<?php /*echo ($current_section == 'webhook') ? 'current' : '' */?>"> <?php /*_e('Cập nhật trạng thái tự động (Webhook)', 'devvn-vnshipping'); */?></a>
        </li>-->
        <?php endif;?>
    </ul>
    <br class="clear">
    <?php
    switch ($current_section) {
        case 'general': include('section-login.php');
            break;
        case 'hubs': include('section-hubs.php');
            break;
        case 'webhook': include('section-webhook.php');
            break;
    }
    ?>
</div>