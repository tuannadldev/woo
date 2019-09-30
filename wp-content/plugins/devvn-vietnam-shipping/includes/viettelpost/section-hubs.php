<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if(!$this->is_vtpost_login()) return;
global $vtpost_fullhubs, $tinh_thanhpho;
$hubs = $this->get_allhubs();
?>
<p>
<button type="button" class="button button-primary vtpost_sync_hubs"><?php _e('Đồng bộ kho từ ViettelPost', 'devvn-vnshipping');?></button>
</p>
<?php
wp_nonce_field('vtpost_sync_hubs_action', 'vtpost_sync_hubs_nonce');
if($hubs && !empty($hubs)){
    ?>
    <form method="post" action="options.php" novalidate="novalidate">
        <?php
        settings_fields( $this->_VTPostHubsGroup );
        ?>
        <div class="devvn_option_2col devvn_options_style">
            <?php foreach($hubs as $hub):
                $GROUPADDRESS_ID = isset($hub['GROUPADDRESS_ID']) ? $hub['GROUPADDRESS_ID'] : '';
                $CUS_ID = isset($hub['CUS_ID']) ? $hub['CUS_ID'] : '';
                $NAME = isset($hub['NAME']) ? $hub['NAME'] : '';
                $ADDRESS = isset($hub['ADDRESS']) ? $hub['ADDRESS'] : '';
                $PHONE = isset($hub['PHONE']) ? $hub['PHONE'] : '';
                $POST_ID = isset($hub['POST_ID']) ? $hub['POST_ID'] : '';
                $PROVINCE_ID = isset($hub['PROVINCE_ID']) ? $hub['PROVINCE_ID'] : '';
                $DISTRICT_ID = isset($hub['DISTRICT_ID']) ? $hub['DISTRICT_ID'] : '';
                $WARDS_ID = isset($hub['WARDS_ID']) ? $hub['WARDS_ID'] : '';
                $TEN_TINH = isset($hub['TEN_TINH']) ? $hub['TEN_TINH'] : '';
                $TEN_HUYEN = isset($hub['TEN_HUYEN']) ? $hub['TEN_HUYEN'] : '';
                $TEN_XA = isset($hub['TEN_XA']) ? $hub['TEN_XA'] : '';

                $is_main = isset($vtpost_fullhubs['listhubs'][$GROUPADDRESS_ID]['ismain']) ? $vtpost_fullhubs['listhubs'][$GROUPADDRESS_ID]['ismain'] : 0;
                $this_hub_district = isset($vtpost_fullhubs['listhubs'][$GROUPADDRESS_ID]['hub_district']) ? $vtpost_fullhubs['listhubs'][$GROUPADDRESS_ID]['hub_district'] : array();
                ?>
                <div class="devvn_option_col" data-hubs="<?php echo esc_attr(json_encode($hub));?>">
                    <div class="devvn_option_box">
                        <table class="devvn_hubs_table widefat" cellspacing="0">
                            <thead>
                            <tr>
                                <th colspan="2">
                                    <h2><?php _e('Kho', 'devvn-vnshipping'); ?> #<?php echo $GROUPADDRESS_ID;?></h2>
                                    <input type="hidden" name="<?php echo $this->_VTPostHubsName?>[listhubs][<?php echo $GROUPADDRESS_ID;?>][hubid]" value="<?php echo $GROUPADDRESS_ID;?>"/>
                                    <input type="hidden" name="<?php echo $this->_VTPostHubsName?>[listhubs][<?php echo $GROUPADDRESS_ID;?>][city]" value="<?php echo $PROVINCE_ID;?>"/>
                                    <input type="hidden" name="<?php echo $this->_VTPostHubsName?>[listhubs][<?php echo $GROUPADDRESS_ID;?>][district]" value="<?php echo $DISTRICT_ID;?>"/>
                                    <input type="hidden" name="<?php echo $this->_VTPostHubsName?>[listhubs][<?php echo $GROUPADDRESS_ID;?>][wards]" value="<?php echo $WARDS_ID;?>"/>
                                    <input type="hidden" name="<?php echo $this->_VTPostHubsName?>[listhubs][<?php echo $GROUPADDRESS_ID;?>][phone]" value="<?php echo $PHONE;?>"/>
                                    <input type="hidden" name="<?php echo $this->_VTPostHubsName?>[listhubs][<?php echo $GROUPADDRESS_ID;?>][address]" value="<?php echo $ADDRESS;?>"/>
                                    <input type="hidden" name="<?php echo $this->_VTPostHubsName?>[listhubs][<?php echo $GROUPADDRESS_ID;?>][cus_id]" value="<?php echo $CUS_ID;?>"/>
                                    <input type="hidden" name="<?php echo $this->_VTPostHubsName?>[listhubs][<?php echo $GROUPADDRESS_ID;?>][name]" value="<?php echo $NAME;?>"/>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Tên chủ kho:</strong> <?php echo $NAME;?></td>
                                    <td><strong>Xã/Phường:</strong> <?php echo vn_shipping()->get_name_village($WARDS_ID);?></td>
                                </tr>
                                <tr>
                                    <td><strong>SĐT:</strong> <?php echo $PHONE;?></td>
                                    <td><strong>Quận/Huyện:</strong> <?php echo vn_shipping()->get_name_district($DISTRICT_ID);?></td>
                                </tr>
                                <tr>
                                    <td><strong>Địa chỉ:</strong> <?php echo $ADDRESS;?></td>
                                    <td><strong>Tỉnh/Thành phố:</strong> <?php echo $this->get_name_city_vt($PROVINCE_ID);?></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <label><input type="checkbox" name="<?php echo $this->_VTPostHubsName?>[listhubs][<?php echo $GROUPADDRESS_ID;?>][ismain]" <?php checked('1',$is_main);?> value="1" class="pick_ismain_onlyone" onclick="selectOnlyThis(this)"/> <strong><?php _e('Đặt làm kho chính','devvn-vnshipping');?></strong></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <strong><?php _e('Khu vực bán hàng', 'devvn-vnshipping');?></strong>
                                        <div class="vtpost_all_state_checkbox">
                                            <?php
                                            foreach($tinh_thanhpho as $k=>$v){?>
                                                <div class="ghn_all_state_checkbox_item">
                                                    <label><input type="checkbox" name="<?php echo $this->_VTPostHubsName?>[listhubs][<?php echo $GROUPADDRESS_ID;?>][hub_district][]" value="<?php echo $k;?>" <?php echo (in_array($k, $this_hub_district)) ? 'checked="checked"' : '';?>> <?php echo $v;?></label>
                                                </div>
                                            <?php }?>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach;?>
            <?php do_settings_fields($this->_VTPostHubsGroup, 'default'); ?>
        </div>
        <?php do_settings_sections($this->_VTPostHubsGroup, 'default'); ?>
        <?php submit_button();?>
    </form>
    <?php
}else{

}