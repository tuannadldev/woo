<?php
/**
 * Template Name: Register
 */
include('header_register.php');
?>
<?php
if (!is_user_logged_in()) {?>
  <?php
  if (isset($_GET['code']) && isset($_GET['csrf'])){
    // call API with loginWithAccountKit
    $oms_user = oms_login_with_accountkit($_GET['code'],$_GET['first_name'],$_GET['last_name']);

    if($oms_user['data']['loginWithAccountKit'] != null) {
      $redirect_url = home_url('signin?active_account=success');
      wp_safe_redirect($redirect_url);
      exit;
    }
  }
  ?>
    <div class="signin-wrapper" style="background-image:url('<?php echo get_field('background_image', 'option');?>')">
        <div id="register-form" class="login-form-container row content-row mb-0">
            <h3 class="signin-text">Đăng nhập tài khoản EXTRACARE</h3>
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
                    <input type="submit" href="#" onclick="smsLogin();return false;" name="wp-submit" id="wp-submit" class="Button Button--primary" value="Đăng nhập ExtraCare">
                </p>
            </form>
        </div>
    </div>

  <?php
}
?>
<div id="content" role="main" class="content-area">

  <?php while ( have_posts() ) : the_post(); ?>

    <?php the_content(); ?>

  <?php endwhile; // end of the loop. ?>

</div>
<?php include('footer_login.php'); ?>

