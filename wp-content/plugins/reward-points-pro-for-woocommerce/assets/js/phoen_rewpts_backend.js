jQuery(document).ready(function() {
				
	jQuery('.btn_color_picker').wpColorPicker();
	
	jQuery('#phoen_rewpts_point_type').on('change',function(){
		
		if(jQuery(this).val()=='percentage_price'){
			
			jQuery('#phoen_rewpts_point_type_div2').hide();
			
			jQuery('#phoen_rewpts_point_type_div3').show();
			
			jQuery('#phoen_rewpts_point_type_div').show();
		
		}else{
			jQuery('#phoen_rewpts_point_type_div2').show();
			
			jQuery('#phoen_rewpts_point_type_div3').show();
			
			jQuery('#phoen_rewpts_point_type_div').hide();
		}
	});
	jQuery(".phoen_points_expiry_month,.phoen_points_assignment_dates").datepicker({
		dateFormat : "dd-mm-yy"
		}
	);	
	jQuery('.phoen_reword_select_to').select2();
});
