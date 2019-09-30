<?php if ( ! defined( 'ABSPATH' ) ) exit; 

if(!isset($_GET['pagination']) && !isset($_GET['user_view_points'] ) &&  !isset($_GET['user']) || isset($_GET['pagination']))	
{
	
	$phoen_reward_all_user_obj = new Phoen_Reward_All_User(); ?>

	<div class="wrap">
		
		<h1 class="wp-heading-inline"><?php _e('REWARD POINTS DETAIL','phoen-rewpts'); ?></h1>
		
		<?php 
			
			$phoen_reward_all_user_obj->prepare_items(); 
			
		?>
            <form method="post">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php $phoen_reward_all_user_obj->search_box('search user', 'search_id'); ?>
                <?php $phoen_reward_all_user_obj->display(); ?>
            </form>
	</div>
	<?php
	
}else if(isset($_GET['user']) && $_GET['action']=='view'){
	
	include_once(PHOEN_REWPTSPLUGPATH.'includes/admin/reports/phoen_user_view_point.php');
	
}else if(isset($_GET['user'])  && $_GET['action']=='edit')
{
	include_once(PHOEN_REWPTSPLUGPATH.'includes/admin/reports/phoen_user_edit_points.php');
	
}

function phoen_order_count($id) {
	
	global $woocommerce;
			
	$curr=get_woocommerce_currency_symbol();
	
	$argsm    = array('posts_per_page' => 1, 'post_type' => 'shop_order','post_status'=>array_keys(wc_get_order_statuses()));
	
	$products_order = get_posts( $argsm ); 
	
	$user_detail=get_user_by('id',$id);
		
	$order_count=0;
		
	$customer_orders = get_posts( array(
		'numberposts' => -1,
		'meta_key'    => '_customer_user',
		'meta_value'  => $id,
		'post_type'   => wc_get_order_types(),
		'post_status' => array_keys( wc_get_order_statuses() )
	) );
		
	for($i=0;$i<count($customer_orders);$i++)  	{	
	
		$products_detail=get_post_meta($customer_orders[$i]->ID); 
		$gen_settings=get_post_meta( $customer_orders[$i]->ID, 'phoe_rewpts_order_status', true );
		if(($customer_orders[$i]->post_status=="wc-completed")||($customer_orders[$i]->post_status=="wc-refunded")&&(is_array($gen_settings)))
		{
							
		$order_count++;
		}
					

	}

	return $order_count;
}

?>
<style>

.phoen_reward_pagination {
    margin: 18px 0;
    text-align: center;
    text-decoration: none;
}
.phoen_reward_pagination a {
    background-color: #fff;
    border: 1px solid #ccc;
    font-size: 13px;
    margin: 0 3px;
    padding: 4px 9px;
    text-decoration: none;
}
</style>