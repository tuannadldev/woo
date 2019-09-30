<?php if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode('phoen-total-reward-points','phoen_total_reward_points');
		
function phoen_total_reward_points()
{
		$total_point_reward=phoen_rewpts_user_reward_point();
		
		if($total_point_reward=='')
		{
			$total_point_reward="0";
		}
	?>
	<div class="phoen_total_current_points">
		
		<span class="phoen_balance"><?php _e('Current Points Balance','phoen-rewpts'); ?></span>
	
		<span class="phoen_balance_value">+<?php echo $total_point_reward ; ?></span>
		
	</div>
	<style>
		.phoen_total_current_points {
			border: 1px dashed #ccc;
			display: inline-block;
			padding: 5px 20px;
		}

		.phoen_total_current_points .phoen_balance {
			display: block;
			font-weight: 600;
		}

		.phoen_total_current_points .phoen_balance_value {
			background-color: #eee;
			border-radius: 4px;
			display: inline-block;
			margin-top: 3px;
			padding: 3px 5px;
		}
	</style>
	<?php
		
}