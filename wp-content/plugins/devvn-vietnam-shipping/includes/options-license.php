<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
global $tinh_thanhpho;
?>
<form method="post" action="options.php" novalidate="novalidate">
    <?php
    settings_fields( $this->_licenseGroup );
    $license_options = wp_parse_args(get_option($this->_licenseName),$this->_licenseDefaultOptions);
    ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label for="license_key"><?php _e('License Key','devvn-vnshipping')?></label></th>
            <td>
                <input type="text" name="<?php echo $this->_licenseName?>[license_key]" value="<?php echo $license_options['license_key'];?>" id="license_key"/> <br>
                <small>Điền key để tự động update khi có phiên bản mới.</small>
            </td>
        </tr>
        </tbody>
    </table>
    <?php do_settings_sections($this->_licenseGroup, 'default'); ?>
    <?php submit_button();?>
</form>