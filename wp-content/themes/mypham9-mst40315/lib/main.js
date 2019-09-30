function submit_builder_form(lat,long,idElement){


	var dataPass = {
			action: 'get_ajax_store',
			long: long,
			lat: lat,
			element_id:idElement
		};
	jQuery.ajax({
		url : ajax_url,
		method : 'post',
		dataType: 'JSON',
		data : dataPass,
		success : function( data ) {
			console.log(data);
			jQuery( "body" ).append( data.result );
		}
	});
}

function openCity(evt, cityName,lat,long) {
	// Declare all variables
	var i, tabcontent, tablinks;

	// Get all elements with class="tabcontent" and hide them
	tabcontent = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}

	// Get all elements with class="tablinks" and remove the class "active"
	tablinks = document.getElementsByClassName("tablinks");
	for (i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace(" active", "");
	}

	// Show the current tab, and add an "active" class to the link that opened the tab
	document.getElementById(cityName).style.display = "block";
	evt.currentTarget.className += " active";

	submit_builder_form(lat,long,cityName);
}

function forgotPassword(){
	console.log(11111);
    var phone = jQuery('.custom-forgot #user_login').val();
    var data = {
        phone: phone,
        action: 'ajax_forgot_password'
    };

    // Send code to server to exchange for access token
	if(FBAccountKitLogin.ajaxurl){
        jQuery.post(FBAccountKitLogin.ajaxurl, data, function(response, textStatus, xhr) {
            jQuery('.form-submit-success').remove();
            jQuery('.form-submit-error').remove();
            if(response.success === true){
				jQuery('#forgotpassword').prepend('<p class="form-submit-success">'+response.data.message+'</p>');
			}
			else{
                jQuery('#forgotpassword').prepend('<p class="form-submit-error">'+response.data.message+'</p>');
			}
        });
	}
	return false;
}


function sendOTP(){
    var phone = jQuery('.custom-forgot #user_login').val();
    var data = {
        phone: phone,
        action: 'ajax_request_otp'
    };
    // Send code to server to exchange for access token
    jQuery.ajax({
        url : ajax_url,
        method : 'post',
        dataType: 'JSON',
        data : data,
        success : function( response ) {
            jQuery('.form-submit-success').remove();
            jQuery('.form-submit-error').remove();
            if(response.success === true){
                jQuery("#btnGetOtp").remove();
                jQuery('#forgotpassword').prepend('<p class="form-submit-success">'+'Mã OTP đã được gửi về số điện thoại của bạn'+'</p>');
            }
            else{
                jQuery('#forgotpassword').prepend('<p class="form-submit-error">'+response.data.message+'</p>');
            }
        }
    });
    return false;
}
