<div class="signin-wrapper" style="background-image:url('<?php echo get_field('background_image', 'option');?>')">
    <div id="register-form" class="login-form-container row content-row mb-0">
        <h3 class="signin-text">Đăng ký tài khoản EXTRACARE</h3>
        <?php
        // Error.
        if (isset($_GET['fbak_login_error']) && 'true' == $_GET['fbak_login_error']) {
            echo '<p class="form-submit-error">Lỗi: Hệ thống xảy ra lỗi, vui lòng thử lại.</p>';
        }
        ?>
        <form id="register_accountkit_form" method="post" novalidate>

            <p class="form-submit-error required-error hidden">Lỗi: Vui lòng nhập các trường bắt buộc (*).</p>
            <p class="form-submit-error email-error hidden">Lỗi: Vui lòng nhập email đúng.</p>
            <p class="form-submit-error phone-error hidden">Lỗi: Vui lòng nhập số điện thoại đúng.</p>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="user_login"><?php _e( 'Số điện thoại', 'personalize-login' ); ?><strong>*</strong></label>
                <input type="text" name="user_login" required="required" id="user_login">
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="first_name"><?php _e( 'Tên', 'personalize-login' ); ?><strong>*</strong></label>
                <input type="text" name="first_name" required="required" id="first_name">
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="last_name"><?php _e( 'Họ', 'personalize-login' ); ?> <strong>*</strong></label>
                <input type="text" name="last_name" required="required" id="last_name"
            </p>
            <!--            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">-->
            <!--                <label for="email">--><?php //_e( 'Email', 'personalize-login' ); ?><!-- <strong>*</strong></label>-->
            <!--                <input type="text" name="user_email" required="required" id="email">-->
            <!--            </p>-->
            <!--            <div class="field last">-->
            <!--                <label for="pass1">--><?php //_e( 'Password', 'personalize-login' ) ?><!--<strong>*</strong></label>-->
            <!--                <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />-->
            <!--            </div>-->
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                Tôi đồng ý với các <a target="_blank" href="https://www.pharmacity.vn/quy-dinh-su-dung/">điều khoản sử dụng</a> của Pharmacity và cho phép Pharmacity sử dụng thông tin của tôi khi hoàn tất thao tác này.
            </p>
            <p class="form-row">
                <input type="submit" href="#" onclick="smsLogin();return false;" name="wp-submit" id="wp-submit" class="Button Button--primary" value="Đăng ký ExtraCare">
            </p>
        </form>
        <div class="register-link">
            Bạn đã có tài khoản? <a rel="nofollow" href="<?php echo home_url('/signin/');?>">Đăng nhập </a>
        </div>
    </div>
</div>