(function($) {

    AccountKit_OnInteractive = function() {
        AccountKit.init({
            appId: FBAccountKitLogin.app_id,
            state: FBAccountKitLogin.nonce,
            version: FBAccountKitLogin.version,
            redirect: FBAccountKitLogin.redirect,
            display: FBAccountKitLogin.display,
            fbAppEventsEnabled: true,
        });
    };

    // login callback
    function loginCallback(response) {
        if (response.status === "PARTIALLY_AUTHENTICATED") {

            jQuery('.login-icon-loading').show();

            var first_name = $('#register-form #first_name').val();
            var last_name = $('#register-form #last_name').val();
            var email = $('#register-form #email').val();
            var data = {
                code: response.code,
                csrf: response.state,
                sms_redir: FBAccountKitLogin.sms_redir,
                email_redir: FBAccountKitLogin.email_redir,
                action: 'fbak_fb_account_kit_auth_login',
                'first_name': first_name,
                'last_name': last_name,
                'email': email
            };

            $('.fb-ackit-wrap').addClass('loading');
            $('.fb-ackit-wait').show();

            // Send code to server to exchange for access token
            $.post(FBAccountKitLogin.ajaxurl, data, function(response, textStatus, xhr) {
                window.location.href = response.data.redirect;
            });
        }
    }

    // phone form submission handler
    window.smsLogin = function() {
        var countryCode = '+84';
        var phoneNumber = $("#user_login").val();
        var first_name = $("#first_name").val();
        var last_name = $("#last_name").val();
        var email = $("#email").val();

        var error = false;
        var required_status = false;
        var email_status = false;
        var phone_status = false;
        if (phoneNumber === ''){
            required_status = true;
            error = true;

        }
        else if(first_name === ''){
            required_status = true;
            error = true;
        }
        else if(last_name === ''){
            required_status = true;
            error = true;
        }
        // else if(email === ''){
        //     required_status = true;
        //     error = true;
        // }

        if (!required_status){
            // var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            // if (!emailReg.test( email )){
            //     email_status = true;
            //     error = true;
            // }
            if (isNaN(phoneNumber)){
                phone_status = true;
                error = true;
            }
        }

        if (required_status){
            $('.required-error').removeClass('hidden');
        }
        else{
            $('.required-error').addClass('hidden');
        }

        // if (email_status){
        //     $('.email-error').removeClass('hidden');
        // }
        // else{
        //     $('.email-error').addClass('hidden');
        // }

        if (phone_status){
            $('.phone-error').removeClass('hidden');
        }
        else{
            $('.phone-error').addClass('hidden');
        }

        if (!error){
            AccountKit.login(
                'PHONE',
                {countryCode: countryCode, phoneNumber: phoneNumber}, // will use default values if not specified
                loginCallback
            );
        }
        else {
            return false;
        }
    }

    // email form submission handler
    window.emailLogin = function() {
        AccountKit.login('EMAIL',{},loginCallback);
    }

})(jQuery);
