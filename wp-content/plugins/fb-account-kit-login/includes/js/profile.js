(function($) {

    AccountKit_OnInteractive = function() {
        AccountKit.init({
            appId: FBAccountKitLogin.app_id,
            state: FBAccountKitLogin.nonce,
            version: FBAccountKitLogin.version,
            display: FBAccountKitLogin.display,
            fbAppEventsEnabled: true
        });
    };

    // login callback
    function loginCallback(response) {
        if (response.status === "PARTIALLY_AUTHENTICATED") {
            var userid = $("span#fbak-user-id").text();
            var data = {
                code: response.code,
                csrf: response.state,
                user_id: userid,
                action: 'fbak_fb_account_kit_associate'
            };

            $('.fb-ackit-wait').addClass('is-active');
            
            // Send code to server to exchange for access token
            $.post(FBAccountKitLogin.ajaxurl, data, function() {
                window.location.reload();
            });
        }
    }

    // phone form submission handler
    window.smsLogin = function() {
        var countryCode = '+84';
        var phoneNumber = document.getElementById("user_login").value;
        AccountKit.login(
            'PHONE',
            {countryCode: countryCode, phoneNumber: phoneNumber}, // will use default values if not specified
            loginCallback
        );
    }

    // email form submission handler
    window.emailLogin = function() {
        AccountKit.login('EMAIL',{},loginCallback);
    }

    window.fbAcDisconnect = function() {

        var userid = $("span#fbak-user-id").text();
        var message = $("span#fbak-check-msg").text();
        var chcek = confirm( message );

        if ( chcek == true ) {
            var data = {
                user_id: userid,
                action: 'fbak_fb_account_kit_disconnect'
            };

            $('.fb-ackit-wait').addClass('is-active');

            $.post(FBAccountKitLogin.ajaxurl, data, function() {
                window.location.reload();
            });
        }
    }

})(jQuery);
