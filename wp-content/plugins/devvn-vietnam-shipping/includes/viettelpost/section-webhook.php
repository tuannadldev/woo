<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

?>
<form method="post" action="options.php" novalidate="novalidate">
    <?php
    wp_nonce_field('vtpost_webhook_action', 'vtpost_webhook_nonce');
    settings_fields( $this->_VTPostWebhookGroup );
    ?>
    <h2>Thông số kết nối Webhook</h2>
    <p>
        <strong>Các bước cài đặt webhook.</strong><br>
        1. Đăng nhập vào <a href="https://viettelpost.vn" title="">viettelpost.vn</a><br>
        2. Đến menu 'Cấu hình tài khoản'<br>
        3. Chọn tab 'Thông tin khách hàng'<br>
        4. Đến mục 'Cấu hình cho khách hàng doanh nghiệp'<br>
    </p>
    <?php do_settings_fields($this->_VTPostWebhookGroup, 'default'); ?>
    <?php do_settings_sections($this->_VTPostWebhookGroup, 'default'); ?>
    <?php submit_button();?>
</form>