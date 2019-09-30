<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<form method="post" action="options.php" novalidate="novalidate" class="enternosubmit">
    <?php
    wp_nonce_field('vtpost_login_action', 'vtpost_login_nonce');
    settings_fields( $this->_VTPostGroup );
    $vtpost_options = wp_parse_args(get_option($this->_VTPostName),$this->_VTPostDefaultOptions);
    ?>
    <h2>Cài đặt thông tin ViettelPost</h2>
    <p>Đăng ký tài khoản tại <a href="https://viettelpost.vn/" target="_blank">viettelpost.vn</a></p>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label for="username"><?php _e('Username','devvn-vnshipping')?></label></th>
            <td>
                <input type="text" name="<?php echo $this->_VTPostName?>[username]" value="<?php echo $vtpost_options['username'];?>" id="username"/> <br>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="userpass"><?php _e('Password','devvn-vnshipping')?></label></th>
            <td>
                <input type="password" name="<?php echo $this->_VTPostName?>[userpass]" value="<?php echo $vtpost_options['userpass'];?>" id="userpass"/> <br>
            </td>
        </tr>
        </tbody>
    </table>
    <h2>Cấu hình vận chuyển</h2>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label for="type_service"><?php _e('Loại dịch vụ','devvn-vnshipping')?></label></th>
            <td>
                <select name="<?php echo $this->_VTPostName?>[type_service]" id="type_service">
                    <option value="1" <?php selected(1, $vtpost_options['type_service']);?>>1. Chuyển phát nhanh/ Express</option>
                    <option value="2" <?php selected(2, $vtpost_options['type_service']);?>>2. Thương mại điện tử/ E-commerce </option>
                    <option value="3" <?php selected(3, $vtpost_options['type_service']);?>>3. Quốc tế/ International </option>
                    <option value="4" <?php selected(4, $vtpost_options['type_service']);?>>4. Giao voucher thu tiền/ Voucher </option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="all_service"><?php _e('Dịch vụ','devvn-vnshipping')?></label></th>
            <td>
                <?php
                $list_service = $this->api_listService($vtpost_options['type_service']);
                ?>
                <div class="list_all_service" data-choose="<?php echo esc_attr(json_encode($vtpost_options['all_service']));?>">
                    <?php if($list_service):?>
                        <?php foreach($list_service as $service):
                            $SERVICE_CODE = isset($service['SERVICE_CODE']) ? $service['SERVICE_CODE'] : '';
                            $SERVICE_NAME = isset($service['SERVICE_NAME']) ? esc_attr($service['SERVICE_NAME']) : '';
                            ?>
                            <label><input type="checkbox" name="<?php echo $this->_VTPostName?>[all_service][]" <?php echo (in_array($SERVICE_CODE, $vtpost_options['all_service'])) ? 'checked' : '';?> value="<?php echo $SERVICE_CODE;?>"/> <?php echo $SERVICE_NAME;?></label><br>
                        <?php endforeach;?>
                    <?php else:?>
                        <?php _e('Có lỗi khi liệt kê dịch vụ. Vui lòng thử lại sau.');?>
                    <?php endif;?>
                </div>
                <script type="text/template" id="tmpl-vtpost-service">
                    <label><input type="checkbox" name="<?php echo $this->_VTPostName?>[all_service][]" value="{{{data.value}}}" {{{data.checked}}}/> {{{data.name}}}</label><br>
                </script>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="extra_service"><?php _e('Dịch vụ cộng thêm','devvn-vnshipping')?></label></th>
            <td>
                <?php
                $list_service = $this->api_listServiceExtra();
                $extras = isset($vtpost_options['extra_service']) ? (array) $vtpost_options['extra_service'] : array();
                ?>
                <div class="list_all_service_extra" data-choose="<?php echo esc_attr(json_encode($vtpost_options['extra_service']));?>">
                    <?php if($list_service):?>
                        <?php foreach($list_service as $service):
                            $SERVICE_CODE = isset($service['SERVICE_CODE']) ? $service['SERVICE_CODE'] : '';
                            $SERVICE_NAME = isset($service['SERVICE_NAME']) ? esc_attr($service['SERVICE_NAME']) : '';
                            ?>
                            <label><input type="checkbox" name="<?php echo $this->_VTPostName?>[extra_service][]" <?php echo (in_array($SERVICE_CODE, $extras)) ? 'checked' : '';?> value="<?php echo $SERVICE_CODE;?>"/> <?php echo $SERVICE_NAME;?></label><br>
                        <?php endforeach;?>
                            <label><input type="checkbox" name="<?php echo $this->_VTPostName?>[extra_service][]" <?php echo (in_array('GNG', $extras)) ? 'checked' : '';?> value="GNG"/> Gửi hàng tại bưu cục (Giảm 10% cước)</label>
                    <?php else:?>
                        <?php _e('Có lỗi khi liệt kê dịch vụ cộng thêm. Vui lòng thử lại sau.');?>
                    <?php endif;?>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="product_type"><?php _e('Loại hàng hóa','devvn-vnshipping')?></label></th>
            <td>
                <select name="<?php echo $this->_VTPostName?>[product_type]" id="product_type">
                    <option value="TH" <?php selected('TH', $vtpost_options['product_type']);?>>Thư</option>
                    <option value="HH" <?php selected('HH', $vtpost_options['product_type']);?>>Hàng hóa</option>
                </select>
            </td>
        </tr>
        </tbody>
    </table>
    <?php do_settings_sections($this->_VTPostGroup, 'default'); ?>
    <p class="submit">
        <button type="button" class="button button-primary vtpost_login"><?php _e('Đăng nhập và lưu cài đặt','devvn-vnshipping')?></button>
    </p>
    <div class="vn_shipping_hidden">
        <?php submit_button();?>
    </div>
</form>