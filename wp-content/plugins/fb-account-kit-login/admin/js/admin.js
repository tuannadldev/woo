jQuery(document).ready(function($) {

    $("#btn1").click(function () {
        $("#fbak-general").fadeIn("slow");
        $("#fbak-sms").hide();
        $("#fbak-email").hide();
        $("#fbak-display").hide();
        $("#fbak-woo").hide();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").hide();
    });

    $("#btn2").click(function () {
        $("#fbak-general").hide();
        $("#fbak-sms").fadeIn("slow");
        $("#fbak-email").hide();
        $("#fbak-display").hide();
        $("#fbak-woo").hide();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").hide();
    });

    $("#btn3").click(function () {
        $("#fbak-general").hide();
        $("#fbak-sms").hide();
        $("#fbak-email").fadeIn("slow");
        $("#fbak-display").hide();
        $("#fbak-woo").hide();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").hide();
    });

    $("#btn4").click(function () {
        $("#fbak-general").hide();
        $("#fbak-sms").hide();
        $("#fbak-email").hide();
        $("#fbak-display").fadeIn("slow");
        $("#fbak-woo").hide();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").hide();
    });

    $("#btn5").click(function () {
        $("#fbak-general").hide();
        $("#fbak-sms").hide();
        $("#fbak-email").hide();
        $("#fbak-display").hide();
        $("#fbak-woo").fadeIn("slow");
        $("#fbak-misc").hide();
        $("#fbak-shortcode").hide();
    });

    $("#btn6").click(function () {
        $("#fbak-general").hide();
        $("#fbak-sms").hide();
        $("#fbak-email").hide();
        $("#fbak-display").hide();
        $("#fbak-woo").hide();
        $("#fbak-misc").fadeIn("slow");
        $("#fbak-shortcode").hide();
    });

    $("#btn7").click(function () {
        $("#fbak-general").hide();
        $("#fbak-sms").hide();
        $("#fbak-email").hide();
        $("#fbak-display").hide();
        $("#fbak-woo").hide();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").fadeIn("slow");
    });

    $("#fbak-sms-reg").change(function () {
        if ($('#fbak-sms-reg').is(':checked')) {
            $('.fbak-sms-user-type').show();
        }
        if (!$('#fbak-sms-reg').is(':checked')) {
            $('.fbak-sms-user-type').hide();
        }
    });
    $("#fbak-sms-reg").trigger('change');

    $("#fbak-email-reg").change(function () {
        if ($('#fbak-email-reg').is(':checked')) {
            $('.fbak-email-user-type').show();
        }
        if (!$('#fbak-email-reg').is(':checked')) {
            $('.fbak-email-user-type').hide();
        }
    });
    $("#fbak-email-reg").trigger('change');

    $("#fbak-sms-redirect").change(function() {
        if ($('#fbak-sms-redirect').val() == 'custom_url') {
            $('.custom-sms-redir-url').show();
            $('#fbak-sms-redir-url').attr('required', 'required');
        }
        if ($('#fbak-sms-redirect').val() != 'custom_url') {
            $('.custom-sms-redir-url').hide();
            $('#fbak-sms-redir-url').removeAttr('required');
        }
    });
    $("#fbak-sms-redirect").trigger('change');

    $("#fbak-email-redirect").change(function() {
        if ($('#fbak-email-redirect').val() == 'custom_url') {
            $('.custom-email-redir-url').show();
            $('#fbak-email-redir-url').attr('required', 'required');
        }
        if ($('#fbak-email-redirect').val() != 'custom_url') {
            $('.custom-email-redir-url').hide();
            $('#fbak-email-redir-url').removeAttr('required');
        }
    });
    $("#fbak-email-redirect").trigger('change');

    $("#fbak-email-success").change(function() {
        if ($('#fbak-email-success').val() == 'custom') {
            $('.custom-email-url').show();
            $('#fbak-email-success-url').attr('required', 'required');
        }
        if ($('#fbak-email-success').val() != 'custom') {
            $('.custom-email-url').hide();
            $('#fbak-email-success-url').removeAttr('required');
        }
    });
    $("#fbak-email-success").trigger('change');

    $("#fbak-form").change(function() {
        if ($('#fbak-form').val() == 'enable') {
            $('.fbak-loginform').show();
        }
        if ($('#fbak-form').val() != 'enable') {
            $('.fbak-loginform').hide();
        }
    });
    $("#fbak-form").trigger('change');

    $("#fbak-woo-cus").change(function () {
        if ($('#fbak-woo-cus').is(':checked')) {
            $('.fbak-woo-ep').show();
            $('.fbak-woo-ep-label').show();
            $('.fbak-woo-des').show();
        }
        if (!$('#fbak-woo-cus').is(':checked')) {
            $('.fbak-woo-ep').hide();
            $('.fbak-woo-ep-label').hide();
            $('.fbak-woo-des').hide();
        }
    });
    $("#fbak-woo-cus").trigger('change');

    $(".coffee-amt").change(function() {
        var btn = $('.buy-coffee-btn');
        btn.attr('href', btn.data('link') + $(this).val());
    });
    $(".coffee-amt").trigger('change');

    if ( location.href.match(/page\=fb-account-kit-login#general/ig) ) {
        $("#fbak-general").show();
        $("#fbak-sms").hide();
        $("#fbak-email").hide();
        $("#fbak-display").hide();
        $("#fbak-woo").hide();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").hide();
    } else if ( location.href.match(/page\=fb-account-kit-login#sms/ig) ) {
        $("#btn1").removeClass("active");
        $("#btn2").addClass("active");
        $("#fbak-general").hide();
        $("#fbak-sms").show();
        $("#fbak-email").hide();
        $("#fbak-display").hide();
        $("#fbak-woo").hide();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").hide();
    } else if( location.href.match(/page\=fb-account-kit-login#email/ig) ) {
        $("#btn1").removeClass("active");
        $("#btn3").addClass("active");
        $("#fbak-general").hide();
        $("#fbak-sms").hide();
        $("#fbak-email").show();
        $("#fbak-display").hide();
        $("#fbak-woo").hide();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").hide();
    } else if( location.href.match(/page\=fb-account-kit-login#display/ig) ) {
        $("#btn1").removeClass("active");
        $("#btn4").addClass("active");
        $("#fbak-general").hide();
        $("#fbak-sms").hide();
        $("#fbak-email").hide();
        $("#fbak-display").show();
        $("#fbak-woo").hide();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").hide();
    } else if( location.href.match(/page\=fb-account-kit-login#woocommerce/ig) ) {
        $("#btn1").removeClass("active");
        $("#btn5").addClass("active");
        $("#fbak-general").hide();
        $("#fbak-sms").hide();
        $("#fbak-email").hide();
        $("#fbak-display").hide();
        $("#fbak-woo").show();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").hide();
    } else if( location.href.match(/page\=fb-account-kit-login#others/ig) ) {
        $("#btn1").removeClass("active");
        $("#btn6").addClass("active");
        $("#fbak-general").hide();
        $("#fbak-sms").hide();
        $("#fbak-email").hide();
        $("#fbak-display").hide();
        $("#fbak-woo").hide();
        $("#fbak-misc").show();
        $("#fbak-shortcode").hide();
    } else if( location.href.match(/page\=fb-account-kit-login#shortcode/ig) ) {
        $("#btn1").removeClass("active");
        $("#btn7").addClass("active");
        $("#fbak-general").hide();
        $("#fbak-sms").hide();
        $("#fbak-email").hide();
        $("#fbak-display").hide();
        $("#fbak-woo").hide();
        $("#fbak-misc").hide();
        $("#fbak-shortcode").show();
    } 
});