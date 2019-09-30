<?php
/**
 * Template Name: LoginOTP
 */
include('header.php');
?>
<?php
if (!is_user_logged_in()) {?>
    <div class="signin-wrapper" style="background-image:url('<?php echo get_field('background_image', 'option');?>')">
        <div class="login-form-container row content-row mb-0">
            <h3 class="signin-text">Đăng nhập</h3>
            <?php
            // Success actions.
            if (isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail']) {
                echo '<p class="form-submit-success">Check your email for a link to reset your password.</p>';
            }
            if (isset($_GET['password']) && 'changed' == $_GET['password']) {
                echo '<p class="form-submit-success">Your password has been changed. You can sign in now.</p>';
            }
            if (isset($_GET['logged_out']) && 'true' == $_GET['logged_out']) {
                echo '<p class="form-submit-success">Bạn đã đăng xuất bạn có muốn đăng nhập lại không?</p>';
            }
            if (isset($_GET['registered']) && 'success' == $_GET['registered']) {
                echo '<p class="form-submit-success">Tài khoản của bạn đã đăng ký thành công. Vui lòng đăng nhập với mật khẩu được gửi qua số điện thoại của bạn để tiếp tục .</p>';
            }
            // Error.
            if (isset($_GET['login_error'])) {
                echo '<p class="form-submit-error"  data-error="true" data-error-type="empty_username">Lỗi: '.base64_decode($_GET['login_error']).'</p>';
            }
            // Display WordPress login form:
            ?>
            <form id="forgotpassword" method="post" class="woocommerce-form woocommerce-form-login custom-login custom-forgot" action="<?php echo site_url('/wp-otp-login.php'); ?>">
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="user_login">Số điên thoại<strong>*</strong></label>
                    <input type="text" name="log" id="user_login" class="woocommerce-Input woocommerce-Input--text input-text" value="" size="20">
                    <input type="button" value="Lấy mã xác nhận" onclick="sendOTP()" class="Button Button--primary" id="btnGetOtp">
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="user_pass">Mã xác nhận<strong>*</strong></label>
                    <input type="password" name="pwd" id="user_pass" class="woocommerce-Input woocommerce-Input--text input-text" value="" size="20">
                </p>
                <p class="form-row">
                    <input type="submit" name="wp-submit" id="wp-submit" class="Button Button--primary" value="Đăng nhập">
                    <!--                    <a class="forgot-pass-link" href="--><?php //echo home_url('/forgot-password/');?><!--">Quên mật khẩu?</a>-->
                    <!--<input type="hidden" name="redirect_to" value="http://dev.a2milk.local/wp-admin/">-->
                    <input type="hidden" name="form" value="forgot_password">
                    <input type="hidden" name="testcookie" value="1">
                </p>
            </form>
        </div>
    </div>
    <?php
}
?>
<?php include('footer_login.php'); ?>
