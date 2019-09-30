<?php
do_action( 'wc_deposits_enqueue_deposit_button_scripts' );
?>
<tr class="deposit-option">
    <td colspan="2" style="text-align: left;">

        <div class="checkbox-deposit">
            <input class="<?php if($deposit_amount < 0 || !is_user_logged_in() ) echo 'not-validate'?>" type="checkbox" name="checkbox-deposit" value="1" id="checkbox-deposit" <?php if($default_checked == 'deposit' && $deposit_amount>0) echo 'checked'?> <?php if($deposit_amount<=0) echo 'disabled';?> >
            <label class='deposit-option' for="checkbox-deposit" style="cursor: pointer;">

                <span id='deposit-amount'>
                    <?php if($deposit_amount>0){?>
                    <span class="user-logged">
                        <div><?php _e( 'Thanh toán bằng điểm Extracare', 'woocommerce-dosits')?></div>
                        <div class="em-not-logged">
                            <?php $user_points = get_user_meta(get_current_user_id(), 'reward_point');
                            $user_points = $user_points[0];?>
                            <div><?php _e( 'Bạn đang có ', 'woocommerce-dosits')?><?php echo  number_format($user_points); ?><?php _e( ' điểm.', 'woocommerce-dosits')?></div>
                            <div>
                                <?php echo $deposit_option_text; ?> <?php echo  number_format($deposit_amount); ?><?php _e( ' (ứng với ', 'woocommerce-deposits')?><?php echo wc_price( $deposit_amount );?><?php _e( ') để thanh toán.', 'woocommerce-deposits'); ?>

                            </div>
                        </div>
                     </span>
                    <?php } else{?>
                        <?php
                            if ( is_user_logged_in() ) {
                                $link = 'https://www.pharmacity.vn/quy-dinh-su-dung/';
                            } else {
                                $link = 'https://www.pharmacity.vn/signup/';
                            }

                        ?>

                        <span class="not-logged">
                            <?php if ( is_user_logged_in() ) {?>
                                <div><?php _e( 'Thanh toán bằng điểm Extracare', 'woocommerce-dosits')?></div>
                                <div class="em-not-logged"> <?php _e( 'Bạn chưa có điểm ExtraCare. Tiếp tục tích luỹ.', 'woocommerce-dosits')?> </div>
                            <?php } else{?>
                                <div><?php _e( 'Thanh toán bằng điểm Extracare', 'woocommerce-dosits')?></div>
                                <div class="em-not-logged">
                                    <?php _e( 'Bạn chưa là thành viên Extracare. ', 'woocommerce-dosits')?><a href="<?php echo $link?>" target="_blank" style="color:#1b74e7 "><?php _e( 'Đăng ký ngay', 'woocommerce-dosits')?></a>
                                </div>

                            <?php }?>
                        </span>
                    <?php }?>
                </span>
            </label>

        </div>
    </td>

</tr>
<tr class="deposit-buttons" style="display: none;">
    <td colspan="2">

        <div id='<?php echo $basic_buttons ? 'basic-wc-deposits-options-form' : 'wc-deposits-options-form'; ?>'>
            <div class="<?php echo $basic_buttons ? 'basic-switch-woocommerce-deposits' : 'deposit-options switch-toggle switch-candy switch-woocommerce-deposits'; ?>">
                    <input id='pay-deposit' name='deposit-radio'
                           type='radio' <?php echo checked( $default_checked , 'deposit' ); ?> class='input-radio'
                           value='deposit'>
                    <label id="pay-deposit-label"
                           for='pay-deposit'><?php _e( $deposit_text , 'woocommerce-deposits' ); ?></label>
					<?php if( isset( $force_deposit ) && $force_deposit === 'yes' ){ ?>
                        <input id='pay-full-amount' name='deposit-radio' type='radio'
                               class='input-radio'
                               disabled>
                        <label id="pay-full-amount-label" for='pay-full-amount'
                               onclick=''><?php _e( $full_text , 'woocommerce-deposits' ); ?></label>
					<?php } else{ ?>
                        <input id='pay-full-amount' name='deposit-radio'
                               type='radio' <?php echo checked( $default_checked , 'full' );; ?> class='input-radio'
                               value='full'>
                        <label id="pay-full-amount-label" for='pay-full-amount'
                               onclick=''><?php _e( $full_text , 'woocommerce-deposits' ); ?></label>
					<?php } ?>
                    <a class='wc-deposits-switcher'></a>
            </div>
            <span class='deposit-message' id='wc-deposits-notice'></span>

        </div>
    </td>
</tr>
<script>
    jQuery(document).ready(function($) {
        $("#checkbox-deposit").change(function() {
            if(this.checked) {
                $('#pay-deposit').trigger('click');
            }
            else{
                $('#pay-full-amount').trigger('click');
            }
        });

        <?php if($full_amount && $default_checked == 'deposit'){?>
             $('#payment_method_cod').trigger('click');
             $('.wc_payment_method.payment_method_epay').hide();
        <?php } else{?>
            $('.wc_payment_method.payment_method_epay').show();
        <?php }?>


    });
</script>
