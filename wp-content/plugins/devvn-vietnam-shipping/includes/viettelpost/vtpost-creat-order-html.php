<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
global $vtpost_fullhubs, $vtpost_user;
$order = wc_get_order($orderid);
$order_total = vn_shipping()->order_get_total($order);
$listhubs = isset($vtpost_fullhubs['listhubs']) ? $vtpost_fullhubs['listhubs'] : array();
$customer_infor = vn_shipping()->get_customer_address_shipping($orderid);
$product_arg = vn_shipping()->get_product_args($orderid);

$order_payment = 3;
$order_note = '';
$order_amout = $order_total;

$payment_methob = $order->get_payment_method();

if($payment_methob && $payment_methob != 'cod'){
    $order_payment = 1;
    $order_amout = 0;
}

$shipping_methods = $order->get_shipping_methods();
$HubID_Order = '';
$method_id = '';
foreach ( $shipping_methods as $shipping_method ) {
    foreach($shipping_method->get_formatted_meta_data() as $meta_data){
        if($meta_data->key && $meta_data->key == 'HubID' && !$HubID_Order){
            $HubID_Order = $meta_data->value;
        }
    }
    foreach($shipping_method->get_formatted_meta_data() as $meta_data){
        if($meta_data->key && $meta_data->key == 'ServiceID' && !$method_id){
            $method_id = $meta_data->value;
        }
    }
}

$prod_weight = vn_shipping()->convert_weight_to_gram(vn_shipping()->get_order_weight($orderid));
$prod_length = vn_shipping()->convert_dimension_to_cm(vn_shipping()->get_order_weight($orderid, 'length'));
$prod_width = vn_shipping()->convert_dimension_to_cm(vn_shipping()->get_order_weight($orderid, 'width'));
$prod_height = vn_shipping()->convert_dimension_to_cm(vn_shipping()->get_order_weight($orderid, 'height'));
$prod_type = $vtpost_user['product_type'];
$extras = isset($vtpost_user['extra_service']) ? (array) $vtpost_user['extra_service'] : array();

$vtpost_order_full_submit = get_post_meta($orderid,'vtpost_order_full_submit', true);
$vtpost_order_number = get_post_meta($orderid,'vtpost_order_number', true);

if($make == 'update' && $vtpost_order_full_submit && !empty($vtpost_order_full_submit) && $vtpost_order_number){
    $HubID_Order = $vtpost_order_full_submit['GROUPADDRESS_ID'];
    $order_total = $vtpost_order_full_submit['PRODUCT_PRICE'];
    $prod_weight = $vtpost_order_full_submit['PRODUCT_WEIGHT'];
    $prod_length = $vtpost_order_full_submit['PRODUCT_LENGTH'];
    $prod_width = $vtpost_order_full_submit['PRODUCT_WIDTH'];
    $prod_height = $vtpost_order_full_submit['PRODUCT_HEIGHT'];
    $prod_type = $vtpost_order_full_submit['PRODUCT_TYPE'];
    $order_payment = $vtpost_order_full_submit['ORDER_PAYMENT'];
    $method_id = $vtpost_order_full_submit['ORDER_SERVICE'];
    $extras = explode(',', $vtpost_order_full_submit['ORDER_SERVICE_ADD']);
    $order_note = $vtpost_order_full_submit['ORDER_NOTE'];
}

?>
<div id="creat_order_<?php echo $orderid;?>" class="creat_order_wrap">
    <p class="devvn_creat_order_box">
        <form method="POST" id="form_creat_order">
        <table class="devvn_creat_order_table widefat" cellspacing="0">
            <thead>
                <tr>
                    <th colspan="2"><h2><?php _e('Đăng đơn hàng lên ViettelPost','devvn-vnshipping');?></h2></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?php if($listhubs):?>
                        <div class="title_box">Lựa chọn kho</div>
                        <select name="order_hubsid" id="order_hubsid">
                            <option value="">Chọn kho lấy hàng</option>
                            <?php foreach($listhubs as $k=>$hubs):
                                $id = isset($hubs['hubid']) ? $hubs['hubid'] : '';
                                $address = isset($hubs['address']) ? $hubs['address'] : '';
                                $city = isset($hubs['city']) ? $hubs['city'] : '';
                                ?>
                                <option value="<?php echo $k;?>" <?php selected($k, $HubID_Order);?>><?php echo '#'.$id.' - '.$address . ' - '. VTPost()->get_name_city_vt($city);?></option>
                            <?php endforeach;?>
                        </select>
                        </p>
                        <?php endif;?>
                        <p>
                            <div class="title_box">Thông tin người nhận:</div>
                            <strong>Họ tên:</strong> <?php echo $customer_infor['name'];?><br>
                            <strong>Phone:</strong> <?php echo $customer_infor['phone'];?><br>
                            <strong>Địa chỉ:</strong> <?php echo $customer_infor['address'];?>,
                            <?php if($customer_infor['ward']):?><?php echo vn_shipping()->get_name_village($customer_infor['ward']);?>,<?php endif;?>
                            <?php if($customer_infor['disrict']):?><?php echo vn_shipping()->get_name_district($customer_infor['disrict']);?>,<?php endif;?>
                            <?php if($customer_infor['province']):?><?php echo vn_shipping()->get_name_city($customer_infor['province']);?><?php endif;?>
                        </p>
                        <div class="title_box">Ngày giao hàng:<br>
                            <small>(Ví dụ: 20/07/2018 08:00:00)</small></div>
                        <p>
                            <input type="text" name="delivery_date" id="delivery_date" value="<?php echo date_i18n('d/m/Y H:i:s', strtotime( 'NOW + 1 hour', current_time( 'timestamp' ) ));?>"/>
                        </p>
                    </td>
                    <td>
                        <div class="title_box">Thông tin sản phẩm</div>
                        <div class="list_input_box">
                            <div class="devvn_creat_order_input_box">
                                <div class="devvn_creat_order_label">
                                    <label for="prod_price">Giá trị gói hàng:</label>
                                </div>
                                <div class="devvn_creat_order_input">
                                    <input type="number" name="prod_price" id="prod_price" value="<?php echo $order_total;?>"/> <?php echo get_woocommerce_currency_symbol();?>
                                </div>
                            </div>
                            <div class="devvn_creat_order_input_box">
                                <div class="devvn_creat_order_label">
                                    <label for="prod_price">Khối lượng:</label>
                                </div>
                                <div class="devvn_creat_order_input">
                                    <input type="number" name="prod_weight" id="prod_weight" value="<?php echo $prod_weight;?>"/> gram
                                </div>
                            </div>
                            <div class="devvn_creat_order_input_box">
                                <div class="devvn_creat_order_label">
                                    <label for="prod_price">Kích thước (cm):</label>
                                </div>
                                <div class="devvn_creat_order_input devvn_creat_order_input_inline">
                                    <input type="number" name="prod_length" id="prod_length" value="<?php echo $prod_length;?>"/> dài
                                    <input type="number" name="prod_width" id="prod_width" value="<?php echo $prod_width;?>"/> rộng
                                    <input type="number" name="prod_height" id="prod_height" value="<?php echo $prod_height;?>"/> cao
                                </div>
                            </div>
                            <div class="devvn_creat_order_input_box dwas_hidden">
                                <div class="devvn_creat_order_label">
                                    <label for="prod_price">Loại hàng hóa:</label>
                                </div>
                                <div class="devvn_creat_order_input">
                                    <select name="prod_type" id="prod_type">
                                        <option value="TH" <?php selected('TH', $prod_type);?>>Thư</option>
                                        <option value="HH" <?php selected('HH', $prod_type);?>>Hàng hóa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="devvn_creat_order_input_box">
                                <div class="devvn_creat_order_label">
                                    <label for="prod_price">Loại vận đơn:</label>
                                </div>
                                <div class="devvn_creat_order_input">
                                    <select name="order_payment" id="order_payment">
                                        <option value="1" <?php selected(1, $order_payment);?>>1: Không thu tiền</option>
                                        <option value="2" <?php selected(2, $order_payment);?>>2: Thu hộ tiền cước và tiền hàng</option>
                                        <option value="3" <?php selected(3, $order_payment);?>>3: Thu hộ tiền hàng</option>
                                        <option value="4" <?php selected(4, $order_payment);?>>4: Thu hộ tiền cước</option>
                                    </select>
                                </div>
                            </div>
                            <div class="devvn_creat_order_input_box">
                                <div class="devvn_creat_order_label">
                                    <label for="prod_price">Dịch vụ:<br>
                                    <small>(Tiền cước)</small>
                                    </label>
                                </div>
                                <div class="devvn_creat_order_input list_service">
                                    <?php
                                    $shipping = $this->order_findAvailableShipping($orderid, $HubID_Order, array(
                                        "PRODUCT_WEIGHT" => vn_shipping()->convert_weight_to_gram(vn_shipping()->get_order_weight($orderid)),
                                        "PRODUCT_PRICE" => $order_total,
                                        "MONEY_COLLECTION" => $order_amout,
                                        "PRODUCT_QUANTITY" => 1,
                                    ));
                                    if($shipping && !empty($shipping)){
                                        echo '<div id="service_id_list" data-value="'.$method_id.'">';
                                        foreach($shipping as $rate){
                                            $service_id = isset($rate['service_id']) ? $rate['service_id'] : '';
                                            $hubid = isset($rate['hubid']) ? $rate['hubid'] : '';
                                            $service_name = isset($rate['service_name']) ? $rate['service_name'] : '';
                                            foreach($rate as $item){
                                                if(isset($item['SERVICE_CODE']) && $item['SERVICE_CODE'] == 'ALL'){
                                                    echo '<label><input type="radio" class="service_id" name="service_id" data-price="'.$item['PRICE'].'" value="'.$service_id.'" '.checked($service_id, $method_id,false).'/>'. wc_price($item['PRICE']) .' - '.$service_name.'</label>';
                                                    continue;
                                                }
                                            }
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="devvn_creat_order_input_box">
                                <div class="devvn_creat_order_label">
                                    <label for="order_service_extra">Dịch vụ cộng thêm:</label>
                                </div>
                                <div class="devvn_creat_order_input">
                                    <?php
                                    $list_service = $this->api_listServiceExtra();
                                    ?>
                                    <div class="list_all_service_extra" data-choose="<?php echo esc_attr(json_encode($vtpost_user['extra_service']));?>">
                                        <?php if($list_service):?>
                                            <?php foreach($list_service as $service):
                                                $SERVICE_CODE = isset($service['SERVICE_CODE']) ? $service['SERVICE_CODE'] : '';
                                                $SERVICE_NAME = isset($service['SERVICE_NAME']) ? esc_attr($service['SERVICE_NAME']) : '';
                                                ?>
                                                <label><input type="checkbox" name="order_service_extra[]" class="order_service_extra" <?php echo (in_array($SERVICE_CODE, $extras)) ? 'checked' : '';?> value="<?php echo $SERVICE_CODE;?>"/> <?php echo $SERVICE_NAME;?></label>
                                            <?php endforeach;?>
                                                <label><input type="checkbox" name="order_service_extra[]" class="order_service_extra" <?php echo (in_array('GNG', $extras)) ? 'checked' : '';?> value="GNG"/> Gửi hàng tại bưu cục (Giảm 10% cước)</label>
                                        <?php else:?>
                                            <?php _e('Có lỗi khi liệt kê dịch vụ cộng thêm. Vui lòng thử lại sau.');?>
                                        <?php endif;?>
                                    </div>
                                </div>
                            </div>
                            <div class="devvn_creat_order_input_box">
                                <div class="devvn_creat_order_label">
                                    <label for="order_note">Ghi chú:</label>
                                </div>
                                <div class="devvn_creat_order_input">
                                    <textarea name="order_note" id="order_note"><?php echo esc_textarea($order_note)?></textarea>
                                </div>
                            </div>
                            <div class="devvn_creat_order_input_box">
                                <div class="devvn_creat_order_label">
                                    <label for="cod_amout">Tiền thu hộ:</label>
                                </div>
                                <div class="devvn_creat_order_input">
                                    <input type="number" name="cod_amout" id="cod_amout" value=""/> <?php echo get_woocommerce_currency_symbol();?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text_alignright">
                        <div class="vtpost_msg"></div>
                        <?php if($make == 'update' && $vtpost_order_full_submit && !empty($vtpost_order_full_submit) && $vtpost_order_number){?>
                        <a href="#" class="button button-primary devvn_float_right devvn_vtpost_update_order"><?php _e('Cập nhật đơn hàng', 'devvn-vnshipping')?></a>
                        <?php }else{?>
                        <a href="#" class="button button-primary devvn_float_right devvn_vtpost_creat_order"><?php _e('Tạo đơn hàng', 'devvn-vnshipping')?></a>
                        <?php }?>
                        <a href="#" class="button close_popup devvn_float_right"><?php _e('Hủy tạo','devvn-vnshipping');?></a>
                    </td>
                </tr>
            </tfoot>
        </table>
        <input type="hidden" value="<?php echo $orderid;?>" name="order_id" class="order_id">
        <input type="hidden" value="0" name="allow_creat_order" class="allow_creat_order">
        <?php if($make == 'update' && $vtpost_order_full_submit && !empty($vtpost_order_full_submit) && $vtpost_order_number){?>
            <input type="hidden" value="<?php echo $vtpost_order_number;?>" name="vtpost_order_number" class="vtpost_order_number">
        <?php }?>
        <?php wp_nonce_field('creat_order_action', 'creat_order_nonce');?>
        </form>
    </div>
</div>